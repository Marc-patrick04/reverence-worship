"use client";

import { Loader2 } from "lucide-react";
import { useFormStatus } from "react-dom";

type AuthFormButtonProps = {
  children: React.ReactNode;
};

export function AuthFormButton({ children }: AuthFormButtonProps) {
  const { pending } = useFormStatus();

  return (
    <button
      type="submit"
      disabled={pending}
      className="primary-action inline-flex h-11 w-full items-center justify-center gap-2 rounded-lg px-4 text-sm font-bold transition disabled:cursor-not-allowed disabled:opacity-70"
    >
      {pending ? <Loader2 className="size-4 animate-spin" aria-hidden="true" /> : null}
      {children}
    </button>
  );
}
