import { redirect } from "next/navigation";
import { LoginForm } from "@/components/login-form";
import { getCurrentUser } from "@/lib/auth";
import { isRegistrationEnabled } from "@/lib/system-settings";
import { oauthErrorMessage } from "@/lib/oauth-errors";

export default async function LoginPage({ searchParams }: { searchParams: Promise<{ oauth_error?: string }> }) {
  const { oauth_error: oauthErrorCode } = await searchParams;
  const [user, registrationEnabled] = await Promise.all([
    getCurrentUser(),
    isRegistrationEnabled(),
  ]);

  if (user) {
    redirect("/admin/dashboard");
  }

  return (
    <div className="auth-login-content mx-auto w-full max-w-sm">
      <LoginForm registrationEnabled={registrationEnabled} oauthError={oauthErrorMessage(oauthErrorCode)} />
    </div>
  );
}
