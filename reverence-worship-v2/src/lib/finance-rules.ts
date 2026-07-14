export const ACTIVE_EXPENSE_STATUSES = new Set(["pending", "approved", "void_pending"]);

export function calculateAvailableBalance(input: {
  memberIncome: number;
  giftIncome: number;
  sponsorIncome: number;
  expenses: Array<{ amount: number; status?: string | null }>;
}) {
  const income = input.memberIncome + input.giftIncome + input.sponsorIncome;
  const reservedAndSpent = input.expenses
    .filter((expense) => ACTIVE_EXPENSE_STATUSES.has(expense.status ?? "pending"))
    .reduce((sum, expense) => sum + expense.amount, 0);
  return Math.max(income - reservedAndSpent, 0);
}

export function canApproveExpense(expense: { status?: string | null; approverId1: number | null }, userId: number) {
  return (expense.status === "pending" || expense.status === "void_pending") && expense.approverId1 === userId;
}

export function validateExpenseRequest(input: {
  amount: number;
  availableBalance: number;
  recorderId: number;
  approverId: number | null;
}) {
  if (!Number.isFinite(input.amount) || input.amount <= 0) return "Expense amount must be greater than zero.";
  if (!input.approverId) return "An approver is required.";
  if (input.approverId === input.recorderId) return "You cannot select yourself as the expense approver.";
  if (input.amount > input.availableBalance) {
    return `Expense cannot exceed the available account balance of RWF ${input.availableBalance.toLocaleString()}.`;
  }
  return null;
}
