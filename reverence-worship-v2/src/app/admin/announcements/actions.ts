"use server";

import { revalidatePath } from "next/cache";
import { requirePermission } from "@/lib/auth";
import { prisma } from "@/lib/prisma";

function readString(formData: FormData, key: string) {
  const value = formData.get(key);
  return typeof value === "string" ? value.trim() : "";
}

function dateOnly(value: string) {
  return new Date(`${value}T12:00:00.000Z`);
}

function parseIdList(value: string) {
  if (!value) return [];
  try {
    const parsed = JSON.parse(value) as unknown;
    return Array.isArray(parsed)
      ? Array.from(new Set(parsed.map(Number).filter((item) => Number.isInteger(item) && item > 0)))
      : [];
  } catch {
    return [];
  }
}

function normalizeStatus(status: string, scheduledDate: string) {
  if (status === "draft") return "draft";
  if (status === "scheduled" || scheduledDate) return "scheduled";
  return "active";
}

function normalizeTarget(formData: FormData) {
  const targetType = readString(formData, "targetType") || "all";
  const targetRoles = parseIdList(readString(formData, "targetRoles"));
  const targetUsers = parseIdList(readString(formData, "targetUsers"));

  if (!["all", "roles", "users"].includes(targetType)) {
    return { ok: false as const, message: "Please select a valid recipient type." };
  }

  if (targetType === "roles" && targetRoles.length === 0) {
    return { ok: false as const, message: "Select at least one role." };
  }

  if (targetType === "users" && targetUsers.length === 0) {
    return { ok: false as const, message: "Select at least one user." };
  }

  return {
    ok: true as const,
    targetType,
    targetRoles: targetType === "roles" ? JSON.stringify(targetRoles) : null,
    targetUsers: targetType === "users" ? JSON.stringify(targetUsers) : null,
  };
}

export async function saveAnnouncement(formData: FormData) {
  const id = Number(readString(formData, "id"));
  const user = await requirePermission("announcements", id ? "edit" : "create", "/admin/announcements");
  const title = readString(formData, "title");
  const content = readString(formData, "content");
  const statusValue = readString(formData, "status") || "active";
  const type = readString(formData, "type") || "general";
  const scheduledDateValue = readString(formData, "scheduledDate");
  const expiryDateValue = readString(formData, "expiryDate");
  const target = normalizeTarget(formData);

  if (!title || !content) {
    return { ok: false, message: "Subject and message are required." };
  }

  if (!target.ok) return { ok: false, message: target.message };

  const status = normalizeStatus(statusValue, scheduledDateValue);
  const publishedAt = status === "active" ? new Date() : null;

  if (Number.isFinite(id) && id > 0) {
    await prisma.announcement.update({
      where: { id },
      data: {
        title,
        content,
        type,
        status,
        scheduledDate: scheduledDateValue ? dateOnly(scheduledDateValue) : null,
        expiryDate: expiryDateValue ? dateOnly(expiryDateValue) : null,
        targetType: target.targetType,
        targetRoles: target.targetRoles,
        targetUsers: target.targetUsers,
        targetAudience: target.targetType === "all" ? "All Users" : null,
        publishedBy: status === "active" ? user.id : null,
        publishedAt,
      },
    });
  } else {
    await prisma.announcement.create({
      data: {
        title,
        content,
        type,
        status,
        scheduledDate: scheduledDateValue ? dateOnly(scheduledDateValue) : null,
        expiryDate: expiryDateValue ? dateOnly(expiryDateValue) : null,
        targetType: target.targetType,
        targetRoles: target.targetRoles,
        targetUsers: target.targetUsers,
        targetAudience: target.targetType === "all" ? "All Users" : null,
        createdBy: user.id,
        publishedBy: status === "active" ? user.id : null,
        publishedAt,
        emailSent: false,
      },
    });
  }

  revalidatePath("/admin/announcements");
  return { ok: true, message: id ? "Announcement updated successfully." : "Announcement created successfully." };
}

export async function deleteAnnouncement(id: number) {
  await requirePermission("announcements", "delete", "/admin/announcements");

  if (!Number.isInteger(id) || id <= 0) {
    return { ok: false, message: "Announcement not found." };
  }

  await prisma.announcement.delete({ where: { id } });
  revalidatePath("/admin/announcements");
  return { ok: true, message: "Announcement deleted successfully." };
}

export async function toggleAnnouncementStatus(id: number) {
  const user = await requirePermission("announcements", "publish", "/admin/announcements");

  if (!Number.isInteger(id) || id <= 0) {
    return { ok: false, message: "Announcement not found." };
  }

  const announcement = await prisma.announcement.findUnique({
    where: { id },
    select: { status: true },
  });

  if (!announcement) {
    return { ok: false, message: "Announcement not found." };
  }

  const nextStatus = announcement.status === "active" ? "draft" : "active";
  await prisma.announcement.update({
    where: { id },
    data: {
      status: nextStatus,
      publishedBy: nextStatus === "active" ? user.id : null,
      publishedAt: nextStatus === "active" ? new Date() : null,
    },
  });

  revalidatePath("/admin/announcements");
  return { ok: true, message: nextStatus === "active" ? "Announcement published." : "Announcement moved to draft." };
}

export async function markAnnouncementSent(id: number) {
  const user = await requirePermission("announcements", "publish", "/admin/announcements");

  if (!Number.isInteger(id) || id <= 0) {
    return { ok: false, message: "Announcement not found." };
  }

  await prisma.announcement.update({
    where: { id },
    data: {
      emailSent: true,
      status: "active",
      publishedBy: user.id,
      publishedAt: new Date(),
    },
  });

  revalidatePath("/admin/announcements");
  return { ok: true, message: "Announcement marked as sent." };
}
