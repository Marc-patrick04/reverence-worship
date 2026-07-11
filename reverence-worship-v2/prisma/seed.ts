import "dotenv/config";
import { PrismaPg } from "@prisma/adapter-pg";
import { PrismaClient } from "../src/generated/prisma/client";

const adapter = new PrismaPg({
  connectionString: process.env.DATABASE_URL,
});

const prisma = new PrismaClient({ adapter });

const roles = [
  {
    name: "super-admin",
    displayName: "Super Admin",
    description: "Full access to all modules.",
    isSystem: true,
  },
  {
    name: "admin",
    displayName: "Admin",
    description: "Administrative access assigned by a super admin.",
    isSystem: true,
  },
  {
    name: "member",
    displayName: "Member",
    description: "Default church member access.",
    isSystem: true,
  },
];

const modules = [
  ["dashboard", "Dashboard", "/", "LayoutDashboard"],
  ["users", "Users", "/users", "Users"],
  ["music-ministry", "Music Ministry", "/music", "Music"],
  ["intercession", "Intercession", "/intercession", "BookOpen"],
  ["social-fellowship", "Social Fellowship", "/social-fellowship", "Handshake"],
  ["discipline", "Discipline", "/discipline", "ClipboardCheck"],
  ["finance", "Finance", "/finance", "Wallet"],
  ["family", "Family", "/family", "Home"],
  ["announcements", "Announcements", "/announcements", "Megaphone"],
  ["reports", "Reports", "/reports", "BarChart3"],
  ["settings", "Settings", "/settings", "Settings"],
] as const;

const featureNames = [
  ["view", "View"],
  ["create", "Create"],
  ["edit", "Edit"],
  ["delete", "Delete"],
  ["approve", "Approve"],
  ["export", "Export"],
] as const;

async function main() {
  await prisma.role.createMany({
    data: roles,
    skipDuplicates: true,
  });

  await prisma.page.createMany({
    data: modules.map(([name, label, href, icon], index) => ({
      name,
      label,
      href,
      icon,
      sortOrder: index + 1,
      isActive: true,
    })),
    skipDuplicates: true,
  });

  const [superAdminRole, pages] = await Promise.all([
    prisma.role.findUniqueOrThrow({ where: { name: "super-admin" } }),
    prisma.page.findMany({ where: { name: { in: modules.map(([name]) => name) } } }),
  ]);

  await prisma.feature.createMany({
    data: pages.flatMap((page) =>
      featureNames.map(([name, label]) => ({
        pageId: page.id,
        name,
        label,
      })),
    ),
    skipDuplicates: true,
  });

  const features = await prisma.feature.findMany({
    where: { pageId: { in: pages.map((page) => page.id) } },
  });

  await prisma.rolePageFeature.createMany({
    data: features.map((feature) => ({
      roleId: superAdminRole.id,
      pageId: feature.pageId,
      featureId: feature.id,
    })),
    skipDuplicates: true,
  });

  console.log(
    `Seeded ${roles.length} roles, ${pages.length} pages, ${features.length} features, and super-admin permissions.`,
  );
}

main()
  .then(async () => {
    await prisma.$disconnect();
  })
  .catch(async (error) => {
    console.error(error);
    await prisma.$disconnect();
    process.exit(1);
  });
