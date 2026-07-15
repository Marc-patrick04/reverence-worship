import { NextRequest, NextResponse } from "next/server";
import { notifySuperAdmins, sendCriticalSystemEmail } from "@/lib/notifications";

export const runtime = "nodejs";

export async function POST(request: NextRequest) {
  const secret = process.env.CRON_SECRET;
  if (!secret || request.headers.get("authorization") !== `Bearer ${secret}`) return NextResponse.json({ error: "Unauthorized" }, { status: 401 });
  const body = (await request.json().catch(() => null)) as { title?: unknown; message?: unknown; eventId?: unknown } | null;
  if (!body || typeof body.title !== "string" || typeof body.message !== "string") return NextResponse.json({ error: "Title and message are required." }, { status: 400 });
  const eventId = typeof body.eventId === "string" ? body.eventId : `${body.title}:${Date.now()}`;
  const title = body.title.slice(0, 160);
  const message = body.message.slice(0, 2000);
  try {
    await notifySuperAdmins({ type: "system", title, message, link: "/admin/settings", dedupeKey: `system-alert:${eventId}` });
  } catch (error) {
    await sendCriticalSystemEmail(title, `${message}\n\nNotification database error: ${error instanceof Error ? error.message : String(error)}`);
  }
  return NextResponse.json({ ok: true });
}
