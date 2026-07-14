import assert from "node:assert/strict";
import test from "node:test";
import { calculateAvailableBalance, canApproveExpense, validateExpenseRequest } from "../src/lib/finance-rules";

test("pending expenses reserve funds while rejected and voided expenses do not", () => {
  assert.equal(calculateAvailableBalance({
    memberIncome: 700,
    giftIncome: 200,
    sponsorIncome: 100,
    expenses: [
      { amount: 250, status: "pending" },
      { amount: 100, status: "approved" },
      { amount: 500, status: "rejected" },
      { amount: 500, status: "voided" },
    ],
  }), 650);
});

test("balance never becomes negative", () => {
  assert.equal(calculateAvailableBalance({ memberIncome: 100, giftIncome: 0, sponsorIncome: 0, expenses: [{ amount: 150, status: "approved" }] }), 0);
});

test("only the selected approver can approve a pending expense", () => {
  assert.equal(canApproveExpense({ status: "pending", approverId1: 9 }, 9), true);
  assert.equal(canApproveExpense({ status: "pending", approverId1: 9 }, 8), false);
  assert.equal(canApproveExpense({ status: "approved", approverId1: 9 }, 9), false);
  assert.equal(canApproveExpense({ status: "void_pending", approverId1: 9 }, 9), true);
});

test("expense validation requires a different approver and enough funds", () => {
  assert.equal(validateExpenseRequest({ amount: 10, availableBalance: 100, recorderId: 1, approverId: null }), "An approver is required.");
  assert.equal(validateExpenseRequest({ amount: 10, availableBalance: 100, recorderId: 1, approverId: 1 }), "You cannot select yourself as the expense approver.");
  assert.match(validateExpenseRequest({ amount: 101, availableBalance: 100, recorderId: 1, approverId: 2 }) ?? "", /cannot exceed/);
  assert.equal(validateExpenseRequest({ amount: 100, availableBalance: 100, recorderId: 1, approverId: 2 }), null);
});
