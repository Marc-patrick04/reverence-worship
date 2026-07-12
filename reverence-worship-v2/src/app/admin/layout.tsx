import { AdminShell } from "@/components/admin-shell";
import { getUserPermissionSet, requireUser } from "@/lib/auth";

export default async function AdminLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  const user = await requireUser();
  const roles = user.roles.map((userRole) => userRole.role.name);
  const permissions = Array.from(await getUserPermissionSet(user));

  return (
    <AdminShell user={{ name: user.name, email: user.email, roles, permissions }}>
      {children}
    </AdminShell>
  );
}
