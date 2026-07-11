import { redirect } from "next/navigation";
import { RegisterForm } from "@/components/register-form";
import { getCurrentUser } from "@/lib/auth";

export default async function RegisterPage() {
  const user = await getCurrentUser();

  if (user) {
    redirect("/admin/dashboard");
  }

  return (
    <div className="w-full">
      <RegisterForm />
    </div>
  );
}
