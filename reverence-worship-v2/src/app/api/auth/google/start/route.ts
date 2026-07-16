import { NextRequest, NextResponse } from "next/server";
import { createGoogleOAuthFlow, GOOGLE_OAUTH_COOKIE, GOOGLE_OAUTH_MAX_AGE_SECONDS, googleAuthorizationUrl, type GoogleOAuthIntent } from "@/lib/google-oauth";

export const runtime = "nodejs";

export async function GET(request: NextRequest) {
  const intent: GoogleOAuthIntent = request.nextUrl.searchParams.get("intent") === "register" ? "register" : "login";
  const returnPath = intent === "register" ? "/register" : "/login";
  try {
    const { flow, cookieValue } = createGoogleOAuthFlow(intent);
    const response = NextResponse.redirect(googleAuthorizationUrl(request.nextUrl.origin, flow));
    response.cookies.set(GOOGLE_OAUTH_COOKIE, cookieValue, {
      httpOnly: true,
      sameSite: "lax",
      secure: process.env.NODE_ENV === "production",
      maxAge: GOOGLE_OAUTH_MAX_AGE_SECONDS,
      path: "/api/auth/google",
    });
    return response;
  } catch (error) {
    console.error("Unable to start Google OAuth", error);
    return NextResponse.redirect(new URL(`${returnPath}?oauth_error=not_configured`, request.url));
  }
}
