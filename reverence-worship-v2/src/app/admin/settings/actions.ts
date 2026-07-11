"use server";

import { revalidatePath } from "next/cache";
import { requireUser } from "@/lib/auth";
import { prisma } from "@/lib/prisma";

type ActionResult = {
  ok: boolean;
  message: string;
};

type SettingValue = string | number | boolean;

function readString(formData: FormData, key: string) {
  const value = formData.get(key);
  return typeof value === "string" ? value.trim() : "";
}

function readBoolean(formData: FormData, key: string) {
  return formData.get(key) === "1" || formData.get(key) === "on";
}

function readNumber(formData: FormData, key: string, fallback: number) {
  const value = Number(readString(formData, key));
  return Number.isFinite(value) ? value : fallback;
}

function assertRange(value: number, min: number, max: number, label: string) {
  if (value < min || value > max) {
    throw new Error(`${label} must be between ${min} and ${max}.`);
  }
}

async function saveSettings(
  group: string,
  values: Record<string, SettingValue>,
  message: string,
) {
  const user = await requireUser();

  await prisma.$transaction(async (tx) => {
    for (const [key, value] of Object.entries(values)) {
      await tx.systemSetting.upsert({
        where: { key },
        update: { value, group },
        create: { key, value, group },
      });
    }

    await tx.activityLog.create({
      data: {
        userId: user.id,
        action: "settings_updated",
        module: "settings",
        metadata: {
          group,
          keys: Object.keys(values),
        },
      },
    });
  });

  revalidatePath("/admin/settings");
  return { ok: true, message } satisfies ActionResult;
}

export async function updateGeneralSettings(formData: FormData) {
  try {
    const appName = readString(formData, "app_name");
    const appUrl = readString(formData, "app_url");

    if (!appName) return { ok: false, message: "Application name is required." };
    if (!appUrl) return { ok: false, message: "Application URL is required." };

    new URL(appUrl);

    return saveSettings(
      "general",
      {
        app_name: appName,
        app_url: appUrl,
        app_debug: readBoolean(formData, "app_debug"),
        registration_enabled: readBoolean(formData, "registration_enabled"),
      },
      "General settings updated successfully.",
    );
  } catch (error) {
    return {
      ok: false,
      message: error instanceof Error ? error.message : "Failed to update general settings.",
    };
  }
}

export async function updateEmailSettings(formData: FormData) {
  try {
    const mailMailer = readString(formData, "mail_mailer") || "smtp";
    const mailHost = readString(formData, "mail_host");
    const mailPort = readNumber(formData, "mail_port", 2525);
    const fromAddress = readString(formData, "mail_from_address");
    const fromName = readString(formData, "mail_from_name");

    if (!mailHost) return { ok: false, message: "SMTP host is required." };
    if (!fromAddress || !fromAddress.includes("@")) {
      return { ok: false, message: "A valid from address is required." };
    }
    if (!fromName) return { ok: false, message: "From name is required." };

    return saveSettings(
      "email",
      {
        mail_mailer: mailMailer,
        mail_host: mailHost,
        mail_port: mailPort,
        mail_username: readString(formData, "mail_username"),
        mail_password: readString(formData, "mail_password"),
        mail_encryption: readString(formData, "mail_encryption"),
        mail_from_address: fromAddress,
        mail_from_name: fromName,
      },
      "Email settings updated successfully.",
    );
  } catch (error) {
    return {
      ok: false,
      message: error instanceof Error ? error.message : "Failed to update email settings.",
    };
  }
}

export async function updateSecuritySettings(formData: FormData) {
  try {
    const sessionLifetime = readNumber(formData, "session_lifetime", 120);
    const passwordMinLength = readNumber(formData, "password_min_length", 6);
    const maxLoginAttempts = readNumber(formData, "max_login_attempts", 5);
    const lockoutDuration = readNumber(formData, "lockout_duration", 15);

    assertRange(sessionLifetime, 1, 1440, "Session lifetime");
    assertRange(passwordMinLength, 6, 255, "Minimum password length");
    assertRange(maxLoginAttempts, 3, 10, "Max login attempts");
    assertRange(lockoutDuration, 5, 1440, "Lockout duration");

    return saveSettings(
      "security",
      {
        session_lifetime: sessionLifetime,
        password_min_length: passwordMinLength,
        require_password_confirm: readBoolean(formData, "require_password_confirm"),
        max_login_attempts: maxLoginAttempts,
        lockout_duration: lockoutDuration,
      },
      "Security settings updated successfully.",
    );
  } catch (error) {
    return {
      ok: false,
      message: error instanceof Error ? error.message : "Failed to update security settings.",
    };
  }
}

export async function clearSystemCache() {
  const user = await requireUser();

  await prisma.activityLog.create({
    data: {
      userId: user.id,
      action: "cache_cleared",
      module: "settings",
      metadata: { route: "/admin/settings" },
    },
  });

  revalidatePath("/", "layout");
  return { ok: true, message: "System cache cleared successfully." } satisfies ActionResult;
}

export async function requestDatabaseBackup() {
  const user = await requireUser();

  await prisma.activityLog.create({
    data: {
      userId: user.id,
      action: "database_backup_requested",
      module: "settings",
      metadata: { provider: "neon" },
    },
  });

  return {
    ok: true,
    message: "Database backup request recorded. Use Neon backups for the actual database snapshot.",
  } satisfies ActionResult;
}
