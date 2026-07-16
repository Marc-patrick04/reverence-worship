import { NextRequest, NextResponse } from "next/server";
import { createSession } from "@/lib/auth";
import { exchangeGoogleCode, GOOGLE_OAUTH_COOKIE, parseGoogleOAuthFlow } from "@/lib/google-oauth";
import { notifyUsers, userIdsWithPermission } from "@/lib/notifications";
import { prisma } from "@/lib/prisma";
import { isRegistrationEnabled } from "@/lib/system-settings";

export const runtime = "nodejs";

function authRedirect(request: NextRequest, path: string, error?: string) {
  const url = new URL(path, request.url);
  if (error) url.searchParams.set("oauth_error", error);
  const response = NextResponse.redirect(url);
  response.cookies.delete({ name: GOOGLE_OAUTH_COOKIE, path: "/api/auth/google" });
  return response;
}

export async function GET(request: NextRequest) {
  const flow = parseGoogleOAuthFlow(request.cookies.get(GOOGLE_OAUTH_COOKIE)?.value);
  const fallbackPath = flow?.intent === "register" ? "/register" : "/login";
  const state = request.nextUrl.searchParams.get("state");
  const code = request.nextUrl.searchParams.get("code");
  const providerError = request.nextUrl.searchParams.get("error");

  if (!flow || !state || state !== flow.state) return authRedirect(request, fallbackPath, "invalid_state");
  if (providerError || !code) return authRedirect(request, fallbackPath, providerError === "access_denied" ? "cancelled" : "google_failed");

  try {
    const profile = await exchangeGoogleCode(request.nextUrl.origin, code, flow);
    const byGoogleId = await prisma.user.findUnique({ where: { googleId: profile.googleId } });
    const byEmail = byGoogleId ? null : await prisma.user.findUnique({ where: { email: profile.email } });
    let user = byGoogleId ?? byEmail;

    if (user) {
      if (user.googleId && user.googleId !== profile.googleId) return authRedirect(request, fallbackPath, "account_conflict");
      user = await prisma.user.update({
        where: { id: user.id },
        data: {
          googleId: profile.googleId,
          emailVerifiedAt: user.emailVerifiedAt ?? new Date(),
          avatarUrl: user.avatarUrl ?? profile.avatarUrl,
        },
      });
      await prisma.activityLog.create({ data: { userId: user.id, action: "auth.google-linked", module: "security" } });
    } else {
      if (flow.intent !== "register") return authRedirect(request, "/register", "account_not_found");
      if (!(await isRegistrationEnabled())) return authRedirect(request, "/login", "registration_disabled");

      const userCount = await prisma.user.count();
      const firstUser = userCount === 0;
      const role = await prisma.role.findUniqueOrThrow({ where: { name: firstUser ? "super-admin" : "member" } });
      user = await prisma.user.create({
        data: {
          name: profile.name,
          email: profile.email,
          googleId: profile.googleId,
          avatarUrl: profile.avatarUrl,
          emailVerifiedAt: new Date(),
          membershipType: "permanent",
          status: firstUser ? "active" : "pending",
          roles: { create: { roleId: role.id } },
        },
      });
      await notifyUsers({
        userIds: [user.id], type: "account", title: "Registration submitted",
        message: firstUser ? "Your Google account was registered and activated." : "Your Google registration was received and is awaiting administrator approval.",
        link: "/admin/dashboard", sourceType: "user", sourceId: user.id, dedupeKey: `registration:${user.id}:google`,
      });
      if (!firstUser) {
        await notifyUsers({
          userIds: await userIdsWithPermission("users", "change-status"), type: "account", title: "New account awaiting approval",
          message: `${user.name} registered with Google and is awaiting approval.`, link: "/admin/users",
          sourceType: "user", sourceId: user.id, dedupeKey: `registration:${user.id}:google-approval`,
        });
      }
      await prisma.activityLog.create({ data: { userId: user.id, action: "auth.google-registered", module: "security" } });
    }

    if (user.status !== "active") return authRedirect(request, "/login", user.status === "pending" ? "approval_pending" : "account_inactive");
    await createSession(user.id);
    return authRedirect(request, "/admin/dashboard");
  } catch (error) {
    console.error("Google OAuth callback failed", error);
    return authRedirect(request, fallbackPath, "google_failed");
  }
}
