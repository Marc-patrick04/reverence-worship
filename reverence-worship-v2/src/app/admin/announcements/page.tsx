import { AnnouncementsClient } from "@/components/announcements-client";
import { requireAdminUser } from "@/lib/auth";
import { prisma } from "@/lib/prisma";

function formatDate(date: Date | null) {
  if (!date) return "-";
  return new Intl.DateTimeFormat("en", { month: "short", day: "2-digit", year: "numeric" }).format(date);
}

function dateValue(date: Date | null) {
  return date ? date.toISOString().slice(0, 10) : "";
}

function parseIdList(value: string | null) {
  if (!value) return [];
  try {
    const parsed = JSON.parse(value) as unknown;
    return Array.isArray(parsed) ? parsed.map(Number).filter((item) => Number.isInteger(item) && item > 0) : [];
  } catch {
    return [];
  }
}

export default async function AnnouncementsPage() {
  await requireAdminUser();

  const [announcements, roles, users, stats] = await Promise.all([
    prisma.announcement.findMany({
      orderBy: { createdAt: "desc" },
      include: {
        creator: { select: { id: true, name: true } },
        publisher: { select: { id: true, name: true } },
      },
    }),
    prisma.role.findMany({
      where: { name: { not: "super-admin" } },
      orderBy: { displayName: "asc" },
      select: { id: true, name: true, displayName: true },
    }),
    prisma.user.findMany({
      where: { status: "active" },
      orderBy: { name: "asc" },
      select: { id: true, name: true, email: true },
    }),
    Promise.all([
      prisma.announcement.count(),
      prisma.announcement.count({ where: { status: "active" } }),
      prisma.announcement.count({ where: { status: "scheduled" } }),
      prisma.announcement.count({ where: { status: "draft" } }),
      prisma.announcement.count({ where: { expiryDate: { lt: new Date() } } }),
    ]),
  ]);

  const roleNameById = new Map(roles.map((role) => [role.id, role.displayName]));
  const userById = new Map(users.map((user) => [user.id, user]));

  const recipientCounts = await Promise.all(
    announcements.map(async (announcement) => {
      if (announcement.targetType === "all") return users.length;
      if (announcement.targetType === "users") {
        const ids = parseIdList(announcement.targetUsers);
        return ids.filter((id) => userById.has(id)).length;
      }
      if (announcement.targetType === "roles") {
        const ids = parseIdList(announcement.targetRoles);
        if (!ids.length) return 0;
        return prisma.user.count({
          where: {
            status: "active",
            roles: { some: { roleId: { in: ids } } },
          },
        });
      }
      return 0;
    }),
  );

  const [total, active, scheduled, draft, expired] = stats;

  return (
    <AnnouncementsClient
      stats={{ total, active, scheduled, draft, expired }}
      roles={roles}
      users={users}
      announcements={announcements.map((announcement, index) => {
        const targetRoleIds = parseIdList(announcement.targetRoles);
        const targetUserIds = parseIdList(announcement.targetUsers);
        const roleNames = targetRoleIds.map((id) => roleNameById.get(id)).filter(Boolean) as string[];
        const userNames = targetUserIds.map((id) => userById.get(id)?.name).filter(Boolean) as string[];
        const recipientLabel =
          announcement.targetType === "all"
            ? "All Users"
            : announcement.targetType === "roles"
              ? roleNames.join(", ") || "Selected roles"
              : userNames.join(", ") || "Selected users";

        return {
          id: announcement.id,
          title: announcement.title,
          content: announcement.content,
          type: announcement.type,
          status: announcement.status,
          scheduledDate: formatDate(announcement.scheduledDate),
          scheduledDateRaw: dateValue(announcement.scheduledDate),
          expiryDate: formatDate(announcement.expiryDate),
          expiryDateRaw: dateValue(announcement.expiryDate),
          targetType: announcement.targetType,
          targetRoles: targetRoleIds,
          targetUsers: targetUserIds,
          recipientLabel,
          recipientCount: recipientCounts[index] ?? 0,
          emailSent: announcement.emailSent,
          createdByName: announcement.creator?.name ?? "System",
          publishedByName: announcement.publisher?.name ?? null,
          publishedAt: formatDate(announcement.publishedAt),
          createdAt: formatDate(announcement.createdAt),
        };
      })}
    />
  );
}
