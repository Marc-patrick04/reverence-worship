import { approveExpense, rejectExpense } from "@/app/admin/finance/actions";
import { requireUser } from "@/lib/auth";
import { prisma } from "@/lib/prisma";

function formatMoney(value: unknown) {
  return `RWF ${Number(value ?? 0).toLocaleString()}`;
}

async function approveAssigned(formData: FormData) {
  "use server";
  await approveExpense(Number(formData.get("expenseId")));
}

async function rejectAssigned(formData: FormData) {
  "use server";
  await rejectExpense(Number(formData.get("expenseId")), String(formData.get("reason") ?? ""));
}

export default async function ExpenseApprovalsPage() {
  const user = await requireUser();
  const expenses = await prisma.expense.findMany({
    where: { approverId1: user.id, status: { in: ["pending", "void_pending"] } },
    include: { creator: { select: { name: true, email: true } } },
    orderBy: { createdAt: "desc" },
  });

  return <div className="mx-auto max-w-4xl space-y-4 px-3 py-6 sm:px-6">
    <div><h1 className="text-2xl font-bold text-gray-900">Expense Approvals</h1><p className="text-sm text-gray-500">Only expenses specifically assigned to you are shown here.</p></div>
    {expenses.length ? expenses.map((expense) => <article key={expense.id} className="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
      <div className="flex flex-col justify-between gap-3 sm:flex-row">
        <div>
          <span className={`mb-2 inline-flex rounded-full px-2 py-1 text-xs font-medium ${expense.status === "void_pending" ? "bg-orange-100 text-orange-700" : "bg-amber-100 text-amber-700"}`}>{expense.status === "void_pending" ? "Void approval" : "Expense approval"}</span>
          <p className="text-lg font-bold text-blue-700">{formatMoney(expense.amount)}</p>
          <p className="font-medium text-gray-900">{expense.description || "Expense request"}</p>
          <p className="mt-1 text-xs text-gray-500">Requested by {expense.creator?.name ?? "Unknown"} · {expense.date?.toLocaleDateString() ?? "No date"}</p>
          {expense.status === "void_pending" && expense.voidReason ? <p className="mt-2 rounded-lg bg-red-50 p-2 text-sm text-red-700">Void reason: {expense.voidReason}</p> : null}
          {expense.referenceNumber ? <p className="mt-1 text-xs text-gray-500">Reference: {expense.referenceNumber}</p> : null}
        </div>
        <form action={approveAssigned}><input type="hidden" name="expenseId" value={expense.id} /><button className="h-9 rounded-lg bg-emerald-600 px-4 text-sm font-medium text-white hover:bg-emerald-700">{expense.status === "void_pending" ? "Approve Void" : "Approve"}</button></form>
      </div>
      <form action={rejectAssigned} className="mt-4 flex flex-col gap-2 border-t border-gray-100 pt-4 sm:flex-row">
        <input type="hidden" name="expenseId" value={expense.id} />
        <input name="reason" required minLength={3} placeholder={expense.status === "void_pending" ? "Reason for rejecting the void" : "Reason for rejection"} className="h-9 flex-1 rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-red-400" />
        <button className="h-9 rounded-lg border border-red-300 px-4 text-sm font-medium text-red-700 hover:bg-red-50">{expense.status === "void_pending" ? "Reject Void" : "Reject"}</button>
      </form>
    </article>) : <div className="rounded-xl border border-gray-200 bg-white px-4 py-12 text-center text-gray-500">You have no pending expense approvals.</div>}
  </div>;
}
