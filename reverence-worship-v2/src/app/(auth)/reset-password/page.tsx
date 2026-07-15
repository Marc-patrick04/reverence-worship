import { ResetPasswordForm } from "@/components/password-reset-forms";

export default async function ResetPasswordPage({ searchParams }: { searchParams: Promise<{ token?: string }> }) {
  const { token = "" } = await searchParams;
  return <main className="flex min-h-screen items-center justify-center bg-slate-50 p-6"><section className="w-full max-w-md rounded-2xl border border-gray-200 bg-white p-8 shadow-xl"><ResetPasswordForm token={token} /></section></main>;
}
