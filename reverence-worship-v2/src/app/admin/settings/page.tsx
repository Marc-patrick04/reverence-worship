import { SettingsClient, type SettingsValues } from "@/components/settings-client";
import { requirePageAccess } from "@/lib/auth";
import { prisma } from "@/lib/prisma";

function textValue(value: unknown, fallback: string) {
  if (typeof value === "string") return value;
  if (typeof value === "number" || typeof value === "boolean") return String(value);
  return fallback;
}

function boolValue(value: unknown, fallback = false) {
  if (typeof value === "boolean") return value;
  if (typeof value === "string") return value === "1" || value === "true";
  if (typeof value === "number") return value === 1;
  return fallback;
}

function numberValue(value: unknown, fallback: number) {
  const numeric = Number(value);
  return Number.isFinite(numeric) ? numeric : fallback;
}

export default async function SettingsPage() {
  await requirePageAccess("settings");

  const rows = await prisma.systemSetting.findMany({
    orderBy: [{ group: "asc" }, { key: "asc" }],
  });

  const settings = new Map(rows.map((row) => [row.key, row.value]));
  const values: SettingsValues = {
    appName: textValue(settings.get("app_name"), "Reverence Worship"),
    appUrl: textValue(settings.get("app_url"), "http://localhost:3000"),
    appDebug: boolValue(settings.get("app_debug")),
    registrationEnabled: boolValue(settings.get("registration_enabled"), true),
    mailMailer: textValue(settings.get("mail_mailer"), "smtp"),
    mailHost: textValue(settings.get("mail_host"), "smtp.mailtrap.io"),
    mailPort: numberValue(settings.get("mail_port"), 2525),
    mailUsername: textValue(settings.get("mail_username"), ""),
    mailPassword: textValue(settings.get("mail_password"), ""),
    mailEncryption: textValue(settings.get("mail_encryption"), "tls"),
    mailFromAddress: textValue(settings.get("mail_from_address"), "hello@example.com"),
    mailFromName: textValue(settings.get("mail_from_name"), "Reverence Worship"),
    sessionLifetime: numberValue(settings.get("session_lifetime"), 120),
    passwordMinLength: numberValue(settings.get("password_min_length"), 6),
    requirePasswordConfirm: boolValue(settings.get("require_password_confirm")),
    maxLoginAttempts: numberValue(settings.get("max_login_attempts"), 5),
    lockoutDuration: numberValue(settings.get("lockout_duration"), 15),
  };

  return <SettingsClient values={values} />;
}
