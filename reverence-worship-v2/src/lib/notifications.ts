import "server-only";

import nodemailer from "nodemailer";
import { prisma } from "@/lib/prisma";

type NotifyUsersInput = {
  userIds: number[];
  type: string;
  title: string;
  message: string;
  link?: string;
  sourceType?: string;
  sourceId?: number;
  dedupeKey?: string;
  emailSubject?: string;
  emailText?: string;
  sendEmail?: boolean;
};

let transporter: ReturnType<typeof nodemailer.createTransport> | null = null;

function uniqueIds(ids: number[]) {
  return [...new Set(ids.filter((id) => Number.isInteger(id) && id > 0))];
}

function smtpConfigured() {
  return Boolean(process.env.SMTP_HOST && process.env.SMTP_FROM);
}

function mailTransport() {
  if (transporter) return transporter;
  transporter = nodemailer.createTransport({
    host: process.env.SMTP_HOST,
    port: Number(process.env.SMTP_PORT || 587),
    secure: process.env.SMTP_SECURE === "true",
    auth: process.env.SMTP_USER
      ? { user: process.env.SMTP_USER, pass: process.env.SMTP_PASSWORD }
      : undefined,
  });
  return transporter;
}

function escapeHtml(value: string) {
  return value.replace(/[&<>"']/g, (character) => ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#039;" })[character] ?? character);
}

function emailHtml(title: string, message: string, link?: string) {
  const appUrl = (process.env.APP_URL || process.env.NEXT_PUBLIC_APP_URL || "").replace(/\/$/, "");
  const actionUrl = link && appUrl ? `${appUrl}${link}` : null;
  return `<div style="font-family:Arial,sans-serif;max-width:620px;margin:auto;color:#1f2937"><h2 style="color:#1d4ed8">${escapeHtml(title)}</h2><p style="line-height:1.6">${escapeHtml(message)}</p>${actionUrl ? `<p><a href="${escapeHtml(actionUrl)}" style="display:inline-block;background:#2563eb;color:white;text-decoration:none;padding:10px 16px;border-radius:8px">Open Reverence Worship</a></p>` : ""}<p style="font-size:12px;color:#6b7280">This is an automated message from Reverence Worship.</p></div>`;
}

export async function deliverEmail(deliveryId: number) {
  const delivery = await prisma.emailDelivery.findUnique({ where: { id: deliveryId } });
  if (!delivery || delivery.status === "sent") return;

  if (!smtpConfigured()) {
    await prisma.emailDelivery.update({
      where: { id: delivery.id },
      data: { status: "pending", lastError: "SMTP is not configured.", nextAttemptAt: new Date(Date.now() + 60 * 60 * 1000) },
    });
    return;
  }

  try {
    await mailTransport().sendMail({
      from: process.env.SMTP_FROM,
      to: delivery.recipient,
      subject: delivery.subject,
      text: delivery.text,
      html: delivery.html ?? undefined,
    });
    await prisma.emailDelivery.update({
      where: { id: delivery.id },
      data: { status: "sent", attempts: { increment: 1 }, sentAt: new Date(), lastError: null, nextAttemptAt: null },
    });
  } catch (error) {
    const attempts = delivery.attempts + 1;
    await prisma.emailDelivery.update({
      where: { id: delivery.id },
      data: {
        status: attempts >= 3 ? "failed" : "pending",
        attempts,
        lastError: error instanceof Error ? error.message.slice(0, 1000) : String(error).slice(0, 1000),
        nextAttemptAt: attempts >= 3 ? null : new Date(Date.now() + attempts * 15 * 60 * 1000),
      },
    });
  }
}

export async function notifyUsers(input: NotifyUsersInput) {
  const ids = uniqueIds(input.userIds);
  if (!ids.length) return [];

  const users = await prisma.user.findMany({ where: { id: { in: ids } }, select: { id: true, email: true } });
  const notificationIds: number[] = [];

  for (const user of users) {
    const dedupeKey = input.dedupeKey ? `${input.dedupeKey}:user:${user.id}` : null;
    const existing = dedupeKey ? await prisma.notification.findUnique({ where: { dedupeKey } }) : null;
    if (existing) {
      notificationIds.push(existing.id);
      continue;
    }

    const notification = await prisma.notification.create({
      data: {
        userId: user.id,
        type: input.type,
        title: input.title,
        message: input.message,
        link: input.link,
        sourceType: input.sourceType,
        sourceId: input.sourceId,
        dedupeKey,
      },
    });
    notificationIds.push(notification.id);

    if (input.sendEmail !== false && user.email) {
      const delivery = await prisma.emailDelivery.create({
        data: {
          userId: user.id,
          notificationId: notification.id,
          recipient: user.email,
          subject: input.emailSubject ?? input.title,
          text: input.emailText ?? input.message,
          html: emailHtml(input.title, input.emailText ?? input.message, input.link),
        },
      });
      await deliverEmail(delivery.id);
    }
  }

  return notificationIds;
}

export async function notifyEmailAddress(recipient: string, subject: string, message: string) {
  if (!recipient) return;
  const delivery = await prisma.emailDelivery.create({
    data: { recipient, subject, text: message, html: emailHtml(subject, message) },
  });
  await deliverEmail(delivery.id);
}

export async function sendCriticalSystemEmail(subject: string, message: string) {
  const recipients = (process.env.SYSTEM_ALERT_EMAIL ?? "").split(",").map((item) => item.trim()).filter(Boolean);
  if (!recipients.length || !smtpConfigured()) return false;
  await mailTransport().sendMail({
    from: process.env.SMTP_FROM,
    to: recipients,
    subject,
    text: message,
    html: emailHtml(subject, message),
  });
  return true;
}

export async function userIdsForRoles(roleNames: string[]) {
  const users = await prisma.user.findMany({
    where: { roles: { some: { role: { name: { in: roleNames } } } } },
    select: { id: true },
  });
  return users.map((user) => user.id);
}

export async function userIdsWithPermission(pageName: string, featureName: string) {
  const users = await prisma.user.findMany({
    where: {
      roles: {
        some: {
          role: {
            permissions: { some: { page: { name: pageName }, feature: { name: featureName } } },
          },
        },
      },
    },
    select: { id: true },
  });
  return users.map((user) => user.id);
}

export async function userIdsForAnnouncement(targetType: string, targetRoles: string | null, targetUsers: string | null) {
  if (targetType === "users") {
    try {
      return uniqueIds((JSON.parse(targetUsers ?? "[]") as unknown[]).map(Number));
    } catch {
      return [];
    }
  }

  if (targetType === "roles") {
    try {
      const roleIds = uniqueIds((JSON.parse(targetRoles ?? "[]") as unknown[]).map(Number));
      if (!roleIds.length) return [];
      const users = await prisma.user.findMany({
        where: { status: "active", roles: { some: { roleId: { in: roleIds } } } },
        select: { id: true },
      });
      return users.map((user) => user.id);
    } catch {
      return [];
    }
  }

  const users = await prisma.user.findMany({ where: { status: "active" }, select: { id: true } });
  return users.map((user) => user.id);
}

export async function notifySuperAdmins(input: Omit<NotifyUsersInput, "userIds">) {
  return notifyUsers({ ...input, userIds: await userIdsForRoles(["super-admin"]) });
}

export async function processPendingEmailDeliveries(limit = 50) {
  const deliveries = await prisma.emailDelivery.findMany({
    where: { status: "pending", OR: [{ nextAttemptAt: null }, { nextAttemptAt: { lte: new Date() } }] },
    orderBy: { createdAt: "asc" },
    take: limit,
    select: { id: true },
  });
  for (const delivery of deliveries) await deliverEmail(delivery.id);
  return deliveries.length;
}
