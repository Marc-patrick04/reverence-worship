import { prisma } from "@/lib/prisma";

type UserExportFilters = {
  search?: string | null;
  role?: string | null;
  status?: string | null;
};

export type UserExportRow = {
  index: number;
  name: string;
  email: string;
  phone: string;
  role: string;
  status: string;
  dob: string;
  gender: string;
  maritalStatus: string;
  residence: string;
  family: string;
  occupation: string;
};

function formatDate(date: Date | null | undefined) {
  if (!date) return "-";

  return new Intl.DateTimeFormat("en-GB", {
    day: "2-digit",
    month: "2-digit",
    year: "2-digit",
  }).format(date);
}

function titleCase(value: string | null | undefined) {
  if (!value) return "-";
  return value.slice(0, 1).toUpperCase() + value.slice(1);
}

export async function getUserExportRows(filters: UserExportFilters) {
  const search = filters.search?.trim();
  const roleId = filters.role ? Number(filters.role) : undefined;
  const status = filters.status;

  const users = await prisma.user.findMany({
    where: {
      ...(search
        ? {
            OR: [
              { name: { contains: search, mode: "insensitive" } },
              { email: { contains: search, mode: "insensitive" } },
            ],
          }
        : {}),
      ...(status === "active" || status === "pending" || status === "inactive"
        ? { status }
        : {}),
      ...(Number.isFinite(roleId)
        ? {
            roles: {
              some: { roleId },
            },
          }
        : {}),
    },
    orderBy: { createdAt: "desc" },
    include: {
      roles: {
        include: {
          role: true,
        },
      },
    },
  });

  return users.map<UserExportRow>((user, index) => {
    const residenceParts = [user.province, user.district, user.sector, user.village].filter(
      Boolean,
    );

    return {
      index: index + 1,
      name: user.name,
      email: user.email,
      phone: user.phone || "-",
      role: user.roles.map(({ role }) => role.displayName).join(", ") || "-",
      status: titleCase(user.status),
      dob: formatDate(user.dateOfBirth),
      gender: titleCase(user.gender),
      maritalStatus: user.maritalStatus || "-",
      residence: residenceParts.length > 0 ? residenceParts.join(", ") : "-",
      family: "-",
      occupation: user.occupation || "-",
    };
  });
}
