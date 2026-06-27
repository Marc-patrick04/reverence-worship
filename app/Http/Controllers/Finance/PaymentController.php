<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function getPayments(Request $request)
    {
        try {
            $payments = DB::table('payments')
                ->join('users', 'payments.user_id', '=', 'users.id')
                ->select('payments.*', 'users.name as member_name')
                ->orderBy('payments.created_at', 'desc')
                ->get();
            
            return response()->json(['success' => true, 'payments' => $payments]);
        } catch (\Exception $e) {
            Log::error('getPayments error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function filterPayments(Request $request)
    {
        try {
            $query = DB::table('payments')
                ->join('users', 'payments.user_id', '=', 'users.id')
                ->select('payments.*', 'users.name as member_name');
            
            if ($request->member_id) {
                $query->where('payments.user_id', $request->member_id);
            }
            if ($request->status && $request->status != 'all') {
                $query->where('payments.status', $request->status);
            }
            if ($request->month) {
                $query->whereYear('payments.payment_date', substr($request->month, 0, 4))
                      ->whereMonth('payments.payment_date', substr($request->month, 5, 2));
            }
            
            $payments = $query->orderBy('payments.created_at', 'desc')->get();
            
            return response()->json(['success' => true, 'payments' => $payments]);
        } catch (\Exception $e) {
            Log::error('filterPayments error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}