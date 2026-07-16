export function oauthErrorMessage(code: string | undefined) {
  const messages: Record<string, string> = {
    not_configured: "Google sign-in is not configured yet.",
    invalid_state: "Your Google sign-in session expired. Please try again.",
    cancelled: "Google sign-in was cancelled.",
    google_failed: "Google sign-in could not be completed. Please try again.",
    account_conflict: "This Google account conflicts with an existing linked account. Contact an administrator.",
    account_not_found: "No account uses that Google email. Create an account with Google first.",
    registration_disabled: "New account registration is currently disabled.",
    approval_pending: "Registration received. An administrator must activate your account before login.",
    account_inactive: "Your account is inactive. Contact an administrator.",
  };
  return code ? messages[code] ?? messages.google_failed : undefined;
}
