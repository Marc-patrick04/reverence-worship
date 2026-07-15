"use client";

import Link from "next/link";
import { useActionState } from "react";
import { completePasswordResetAction, requestPasswordResetAction } from "@/app/auth-actions";
import { AuthFormButton } from "@/components/auth-form-button";
import { PasswordField } from "@/components/password-field";

type State = { error?: string; success?: string };

function Message({ state }: { state: State }) {
  if (state.error) return <p className="rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700">{state.error}</p>;
  if (state.success) return <p className="rounded-lg border border-green-200 bg-green-50 p-3 text-sm text-green-700">{state.success}</p>;
  return null;
}

export function ForgotPasswordForm() {
  const [state, action] = useActionState(requestPasswordResetAction, {});
  return <form action={action} className="space-y-4"><h1 className="text-2xl font-bold">Reset your password</h1><p className="text-sm text-gray-600">Enter your account email and we will send a secure reset link.</p><input name="email" type="email" required placeholder="name@example.com" className="auth-field w-full" /><Message state={state} /><AuthFormButton>Send reset link</AuthFormButton><Link href="/login" className="auth-link block text-center text-sm font-semibold">Back to sign in</Link></form>;
}

export function ResetPasswordForm({ token }: { token: string }) {
  const [state, action] = useActionState(completePasswordResetAction, {});
  return <form action={action} className="space-y-4"><h1 className="text-2xl font-bold">Choose a new password</h1><input type="hidden" name="token" value={token} /><PasswordField id="password" name="password" label="New password" placeholder="At least 6 characters" autoComplete="new-password" /><PasswordField id="passwordConfirmation" name="passwordConfirmation" label="Confirm password" placeholder="Repeat password" autoComplete="new-password" /><Message state={state} /><AuthFormButton>Reset password</AuthFormButton>{state.success ? <Link href="/login" className="auth-link block text-center text-sm font-semibold">Sign in</Link> : null}</form>;
}
