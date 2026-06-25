<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExpenseController extends Controller
{
    public function filterExpenses(Request $request)
    {
        try {
            $category = $request->category;
            $status = $request->status;
            $startDate = $request->start_date;
            $endDate = $request->end_date;
            
            $query = DB::table('expenses');
            
            if ($category && $category !== 'all') {
                $query->where('category', $category);
            }
            if ($status && $status !== 'all') {
                $query->where('status', $status);
            }
            if ($startDate) {
                $query->whereDate('date', '>=', $startDate);
            }
            if ($endDate) {
                $query->whereDate('date', '<=', $endDate);
            }
            
            $expenses = $query->orderBy('date', 'desc')->get();
            
            return response()->json(['success' => true, 'expenses' => $expenses]);
        } catch (\Exception $e) {
            Log::error('filterExpenses error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getExpenses(Request $request)
    {
        try {
            $expenses = DB::table('expenses')
                ->orderBy('created_at', 'desc')
                ->get();
            
            return response()->json(['success' => true, 'expenses' => $expenses]);
        } catch (\Exception $e) {
            Log::error('getExpenses error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function storeExpense(Request $request)
    {
        try {
            $request->validate([
                'amount' => 'required|numeric|min:0',
                'description' => 'required|string',
                'date' => 'required|date',
                'category' => 'nullable|string',
                'request_approval' => 'nullable|boolean'
            ]);
            
            $status = $request->request_approval ? 'pending' : 'approved';
            
            $id = DB::table('expenses')->insertGetId([
                'amount' => $request->amount,
                'description' => $request->description,
                'date' => $request->date,
                'category' => $request->category ?? 'other',
                'status' => $status,
                'created_by' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            return response()->json(['success' => true, 'message' => 'Expense recorded successfully', 'id' => $id]);
        } catch (\Exception $e) {
            Log::error('storeExpense error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function updateExpense(Request $request, $id)
    {
        try {
            $request->validate([
                'amount' => 'required|numeric|min:0',
                'description' => 'required|string',
                'date' => 'required|date',
                'category' => 'nullable|string',
                'status' => 'nullable|string'
            ]);
            
            DB::table('expenses')->where('id', $id)->update([
                'amount' => $request->amount,
                'description' => $request->description,
                'date' => $request->date,
                'category' => $request->category ?? 'other',
                'status' => $request->status ?? 'pending',
                'updated_at' => now()
            ]);
            
            return response()->json(['success' => true, 'message' => 'Expense updated successfully']);
        } catch (\Exception $e) {
            Log::error('updateExpense error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function deleteExpense($id)
    {
        try {
            DB::table('expenses')->where('id', $id)->delete();
            return response()->json(['success' => true, 'message' => 'Expense deleted successfully']);
        } catch (\Exception $e) {
            Log::error('deleteExpense error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function showExpense($id)
    {
        try {
            $expense = DB::table('expenses')
                ->leftJoin('users', 'expenses.created_by', '=', 'users.id')
                ->select('expenses.*', 'users.name as created_by_name')
                ->where('expenses.id', $id)
                ->first();
            
            return response()->json(['success' => true, 'expense' => $expense]);
        } catch (\Exception $e) {
            Log::error('showExpense error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function approveExpense($id)
    {
        try {
            DB::table('expenses')->where('id', $id)->update([
                'status' => 'approved',
                'updated_at' => now()
            ]);
            
            return response()->json(['success' => true, 'message' => 'Expense approved successfully']);
        } catch (\Exception $e) {
            Log::error('approveExpense error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getExpenseDetails($id)
    {
        try {
            $expense = DB::table('expenses')
                ->leftJoin('users', 'expenses.created_by', '=', 'users.id')
                ->select('expenses.*', 'users.name as created_by_name')
                ->where('expenses.id', $id)
                ->first();
            
            return response()->json(['success' => true, 'expense' => $expense]);
        } catch (\Exception $e) {
            Log::error('getExpenseDetails error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}