<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExpenseController extends Controller
{
    /**
     * Filter expenses with year, status, date range, and approver filters
     */
    public function filterExpenses(Request $request)
    {
        try {
            $category = $request->category;
            $status = $request->status;
            $startDate = $request->start_date;
            $endDate = $request->end_date;
            $approverId = $request->approver_id;
            
            $query = DB::table('expenses')
                ->leftJoin('users as approver1', 'expenses.approver_id_1', '=', 'approver1.id')
                ->leftJoin('users as approver2', 'expenses.approver_id_2', '=', 'approver2.id')
                ->leftJoin('users as creator', 'expenses.created_by', '=', 'creator.id')
                ->select(
                    'expenses.*',
                    'approver1.name as approver_1_name',
                    'approver2.name as approver_2_name',
                    'creator.name as created_by_name'
                );
            
            // Filter by category (if still using category)
            if ($category && $category !== 'all') {
                $query->where('expenses.category', $category);
            }
            
            // Filter by status
            if ($status && $status !== 'all') {
                $query->where('expenses.status', $status);
            }
            
            // Filter by date range
            if ($startDate) {
                $query->whereDate('expenses.date', '>=', $startDate);
            }
            if ($endDate) {
                $query->whereDate('expenses.date', '<=', $endDate);
            }
            
            if ($approverId && $approverId !== 'all') {
                $query->where(function ($approverQuery) use ($approverId) {
                    $approverQuery->where('expenses.approver_id_1', $approverId)
                        ->orWhere('expenses.approver_id_2', $approverId);
                });
            }
            
            $expenses = $query->orderBy('expenses.date', 'desc')->get();
            
            return response()->json([
                'success' => true, 
                'expenses' => $expenses
            ]);
        } catch (\Exception $e) {
            Log::error('filterExpenses error: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all expenses with approver names
     */
    public function getExpenses(Request $request)
    {
        try {
            $expenses = DB::table('expenses')
                ->leftJoin('users as approver1', 'expenses.approver_id_1', '=', 'approver1.id')
                ->leftJoin('users as approver2', 'expenses.approver_id_2', '=', 'approver2.id')
                ->leftJoin('users as creator', 'expenses.created_by', '=', 'creator.id')
                ->select(
                    'expenses.*',
                    'approver1.name as approver_1_name',
                    'approver2.name as approver_2_name',
                    'creator.name as created_by_name'
                )
                ->orderBy('expenses.created_at', 'desc')
                ->get();
            
            return response()->json([
                'success' => true, 
                'expenses' => $expenses
            ]);
        } catch (\Exception $e) {
            Log::error('getExpenses error: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a new expense - date is auto-assigned
     */
    public function storeExpense(Request $request)
    {
        try {
            $request->validate([
                'amount' => 'required|numeric|min:0',
                'description' => 'required|string|max:1000',
                'year' => 'required|integer',
                'approver_id_1' => 'nullable|exists:users,id',
                'approver_id_2' => 'nullable|exists:users,id'
            ]);
            
            // Auto-assign current date
            $currentDate = now();
            
            // Determine status based on approvers
            // If at least one approver is selected, status is 'pending', otherwise 'approved'
            $status = ($request->approver_id_1 || $request->approver_id_2) ? 'pending' : 'approved';
            
            $id = DB::table('expenses')->insertGetId([
                'amount' => $request->amount,
                'description' => $request->description,
                'date' => $currentDate,
                'year' => $request->year,
                'approver_id_1' => $request->approver_id_1,
                'approver_id_2' => $request->approver_id_2,
                'status' => $status,
                'category' => 'other', // Default category
                'created_by' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            // Get the created expense with approver names
            $expense = DB::table('expenses')
                ->leftJoin('users as approver1', 'expenses.approver_id_1', '=', 'approver1.id')
                ->leftJoin('users as approver2', 'expenses.approver_id_2', '=', 'approver2.id')
                ->leftJoin('users as creator', 'expenses.created_by', '=', 'creator.id')
                ->select(
                    'expenses.*',
                    'approver1.name as approver_1_name',
                    'approver2.name as approver_2_name',
                    'creator.name as created_by_name'
                )
                ->where('expenses.id', $id)
                ->first();
            
            return response()->json([
                'success' => true,
                'message' => 'Expense recorded successfully',
                'id' => $id,
                'expense' => $expense
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('storeExpense error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update an expense
     */
    public function updateExpense(Request $request, $id)
    {
        try {
            $request->validate([
                'amount' => 'required|numeric|min:0',
                'description' => 'required|string|max:1000',
                'date' => 'required|date',
                'category' => 'nullable|string',
                'status' => 'nullable|string',
                'approver_id_1' => 'nullable|exists:users,id',
                'approver_id_2' => 'nullable|exists:users,id'
            ]);
            
            $data = [
                'amount' => $request->amount,
                'description' => $request->description,
                'date' => $request->date,
                'category' => $request->category ?? 'other',
                'status' => $request->status ?? 'pending',
                'approver_id_1' => $request->approver_id_1,
                'approver_id_2' => $request->approver_id_2,
                'updated_at' => now()
            ];
            
            DB::table('expenses')->where('id', $id)->update($data);
            
            return response()->json([
                'success' => true,
                'message' => 'Expense updated successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('updateExpense error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete an expense
     */
    public function deleteExpense($id)
    {
        try {
            $exists = DB::table('expenses')->where('id', $id)->exists();
            if (!$exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Expense not found'
                ], 404);
            }
            
            DB::table('expenses')->where('id', $id)->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Expense deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('deleteExpense error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show a single expense
     */
    public function showExpense($id)
    {
        try {
            $expense = DB::table('expenses')
                ->leftJoin('users as creator', 'expenses.created_by', '=', 'creator.id')
                ->leftJoin('users as approved_by', 'expenses.approved_by', '=', 'approved_by.id')
                ->leftJoin('users as approver1', 'expenses.approver_id_1', '=', 'approver1.id')
                ->leftJoin('users as approver2', 'expenses.approver_id_2', '=', 'approver2.id')
                ->select(
                    'expenses.*',
                    'creator.name as created_by_name',
                    'approved_by.name as approved_by_name',
                    'approver1.name as approver_1_name',
                    'approver2.name as approver_2_name'
                )
                ->where('expenses.id', $id)
                ->first();
            
            if (!$expense) {
                return response()->json([
                    'success' => false,
                    'message' => 'Expense not found'
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'expense' => $expense
            ]);
        } catch (\Exception $e) {
            Log::error('showExpense error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Approve an expense
     */
    public function approveExpense($id)
    {
        try {
            $exists = DB::table('expenses')->where('id', $id)->exists();
            if (!$exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Expense not found'
                ], 404);
            }
            
            DB::table('expenses')->where('id', $id)->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'updated_at' => now()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Expense approved successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('approveExpense error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get expense details
     */
    public function getExpenseDetails($id)
    {
        return $this->showExpense($id);
    }
}
