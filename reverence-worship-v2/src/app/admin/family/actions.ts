"use server";

import { revalidatePath } from "next/cache";
import { requireUser } from "@/lib/auth";
import { prisma } from "@/lib/prisma";

export async function updateFamilyTaskStatus(taskId: number, status: string) {
  const user = await requireUser();

  if (!["pending", "in-progress", "completed"].includes(status)) {
    return { ok: false, message: "Invalid task status." };
  }

  const membership = await prisma.familyMember.findUnique({
    where: { userId: user.id },
  });

  if (!membership) {
    return { ok: false, message: "No family assigned." };
  }

  const task = await prisma.familyTask.findUnique({
    where: { id: taskId },
    select: { familyId: true },
  });

  if (!task || task.familyId !== membership.familyId) {
    return { ok: false, message: "Unauthorized task." };
  }

  await prisma.familyTask.update({
    where: { id: taskId },
    data: {
      status,
      progress: status === "completed" ? 100 : status === "in-progress" ? 50 : 0,
    },
  });

  revalidatePath("/admin/family");

  return {
    ok: true,
    message: status === "completed" ? "Task marked as done." : "Task status updated.",
  };
}
