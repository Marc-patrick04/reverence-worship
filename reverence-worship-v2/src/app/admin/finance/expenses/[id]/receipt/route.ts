import { readFile } from "node:fs/promises";
import path from "node:path";
import { NextResponse } from "next/server";
import { requirePageAccess } from "@/lib/auth";
import { prisma } from "@/lib/prisma";

export async function GET(_request: Request, context: { params: Promise<{ id: string }> }) {
  await requirePageAccess("finance");
  const { id: rawId } = await context.params;
  const id = Number(rawId);
  if (!Number.isInteger(id) || id <= 0) return NextResponse.json({ message: "Receipt not found." }, { status: 404 });

  const expense = await prisma.expense.findUnique({ where: { id }, select: { receiptPath: true } });
  const filename = expense?.receiptPath ? path.basename(expense.receiptPath) : "";
  if (!filename) return NextResponse.json({ message: "Receipt not found." }, { status: 404 });

  try {
    const file = await readFile(path.join(process.cwd(), "storage", "finance-receipts", filename));
    const extension = path.extname(filename).toLowerCase();
    const contentType = extension === ".pdf" ? "application/pdf" : extension === ".png" ? "image/png" : "image/jpeg";
    return new NextResponse(file, { headers: { "Content-Type": contentType, "Content-Disposition": `inline; filename="expense-${id}${extension}"`, "Cache-Control": "private, no-store" } });
  } catch {
    return NextResponse.json({ message: "Receipt file is unavailable." }, { status: 404 });
  }
}
