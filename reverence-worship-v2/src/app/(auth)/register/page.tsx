import { redirect } from "next/navigation";
import { RegisterForm } from "@/components/register-form";
import { getCurrentUser } from "@/lib/auth";
import { isRegistrationEnabled } from "@/lib/system-settings";
import { prisma } from "@/lib/prisma";
import { oauthErrorMessage } from "@/lib/oauth-errors";

export default async function RegisterPage({ searchParams }: { searchParams: Promise<{ oauth_error?: string }> }) {
  const { oauth_error: oauthErrorCode } = await searchParams;
  const [user, registrationEnabled, userCount] = await Promise.all([
    getCurrentUser(),
    isRegistrationEnabled(),
    prisma.user.count(),
  ]);

  if (user) {
    redirect("/admin/dashboard");
  }

  if (!registrationEnabled && userCount > 0) {
    redirect("/login");
  }

  return (
    <div className="auth-register-content w-full">
      <RegisterForm oauthError={oauthErrorMessage(oauthErrorCode)} />
    </div>
  );
}
