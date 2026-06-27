<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    /**
     * Get all payments with year filtering by default
     */
    public function getPayments(Request $request)
    {
        try {
            $search = $request->input('search', '');
            $term = $request->input('term', '');
            $month = $request->input('month', '');
            $fromDate = $request->input('from_date');
            $toDate = $request->input('to_date');
            
            $query = DB::table('payments')
                ->join('users', 'payments.user_id', '=', 'users.id')
                ->select('payments.*', 'users.name as member_name', 'users.email as member_email');
            
            // Apply search filter
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('users.name', 'LIKE', "%{$search}%")
                      ->orWhere('users.email', 'LIKE', "%{$search}%");
                });
            }
            
            // Apply term filter
            if ($term && $term !== 'all') {
                $query->where('payments.term', $term);
            }
            
            // Apply month filter
            if ($month) {
                $query->whereYear('payments.payment_date', substr($month, 0, 4))
                      ->whereMonth('payments.payment_date', substr($month, 5, 2));
            }

            if ($fromDate && $toDate) {
                $query->whereDate('payments.payment_date', '>=', $fromDate)
                      ->whereDate('payments.payment_date', '<=', $toDate);
            }
            
            $payments = $query->orderBy('payments.created_at', 'desc')->get();
            
            return response()->json(['success' => true, 'payments' => $payments]);
        } catch (\Exception $e) {
            Log::error('getPayments error: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get payments list (alias for getPayments with proper filtering)
     */
    public function getPaymentsList(Request $request)
    {
        return $this->getPayments($request);
    }

    /**
     * Filter payments with search and filters - applies all filters by default
     */
    public function filterPayments(Request $request)
    {
        try {
            $search = $request->input('search', '');
            $term = $request->input('term', '');
            $month = $request->input('month', '');
            $fromDate = $request->input('from_date');
            $toDate = $request->input('to_date');
            $method = $request->input('method', '');
            
            $query = DB::table('payments')
                ->join('users', 'payments.user_id', '=', 'users.id')
                ->select('payments.*', 'users.name as member_name', 'users.email as member_email');
            
            // Search by member name or email
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('users.name', 'LIKE', "%{$search}%")
                      ->orWhere('users.email', 'LIKE', "%{$search}%");
                });
            }
            
            // Filter by term
            if ($term && $term !== 'all') {
                $query->where('payments.term', (int)$term);
            }
            
            // Filter by payment method
            if ($method && $method !== 'all') {
                $query->where('payments.payment_method', $method);
            }
            
            // Filter by month/year
            if ($month) {
                $query->whereYear('payments.payment_date', substr($month, 0, 4))
                      ->whereMonth('payments.payment_date', substr($month, 5, 2));
            }

            if ($fromDate && $toDate) {
                $query->whereDate('payments.payment_date', '>=', $fromDate)
                      ->whereDate('payments.payment_date', '<=', $toDate);
            }
            
            $payments = $query->orderBy('payments.created_at', 'desc')->get();
            
            return response()->json([
                'success' => true, 
                'payments' => $payments,
                'filters' => [
                    'search' => $search,
                    'term' => $term,
                    'month' => $month,
                    'from_date' => $fromDate,
                    'to_date' => $toDate,
                    'method' => $method
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('filterPayments error: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Filter payments list (alias for filterPayments)
     */
    public function filterPaymentsList(Request $request)
    {
        return $this->filterPayments($request);
    }

    /**
     * Export payments using the same filters as the payments table.
     */
    public function exportPayments(Request $request)
    {
        $request->validate([
            'from_date' => ['nullable', 'date'],
            'to_date' => ['nullable', 'date', 'after_or_equal:from_date'],
            'search' => ['nullable', 'string', 'max:255'],
        ]);

        $query = DB::table('payments')
            ->join('users', 'payments.user_id', '=', 'users.id')
            ->select(
                'payments.payment_date',
                'users.name as member_name',
                'users.email as member_email',
                'payments.term',
                'payments.amount',
                'payments.payment_method',
                'payments.reference_number',
                'payments.status',
                'payments.notes'
            );

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('users.name', 'LIKE', "%{$search}%")
                    ->orWhere('users.email', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('from_date')) {
            $query->whereDate('payments.payment_date', '>=', $request->input('from_date'));
        }

        if ($request->filled('to_date')) {
            $query->whereDate('payments.payment_date', '<=', $request->input('to_date'));
        }

        $payments = $query->orderBy('payments.payment_date')->get();
        $fromDate = $request->input('from_date', 'all');
        $toDate = $request->input('to_date', 'all');
        $filename = "payments_{$fromDate}_to_{$toDate}.csv";

        return response()->streamDownload(function () use ($payments) {
            $output = fopen('php://output', 'w');
            fwrite($output, "\xEF\xBB\xBF");
            fputcsv($output, [
                'Date', 'Member', 'Email', 'Term', 'Amount (RWF)',
                'Payment Method', 'Reference', 'Status', 'Notes'
            ]);

            foreach ($payments as $payment) {
                $row = [
                    $payment->payment_date,
                    $payment->member_name,
                    $payment->member_email,
                    $payment->term,
                    $payment->amount,
                    $payment->payment_method,
                    $payment->reference_number,
                    $payment->status,
                    $payment->notes,
                ];

                $row = array_map(function ($value) {
                    $value = (string) ($value ?? '');
                    return preg_match('/^[=+\-@]/', $value) ? "'{$value}" : $value;
                }, $row);

                fputcsv($output, $row);
            }

            fclose($output);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /**
     * Store a new payment
     */
    public function storePayment(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'required|exists:users,id',
                'term' => 'required|integer|min:1',
                'amount' => 'required|numeric|min:0',
                'payment_method' => 'nullable|string',
                'payment_date' => 'nullable|date',
                'year' => 'nullable|integer',
                'notes' => 'nullable|string'
            ]);
            
            $id = DB::table('payments')->insertGetId([
                'user_id' => $request->user_id,
                'term' => $request->term,
                'amount' => $request->amount,
                'payment_method' => $request->payment_method ?? 'cash',
                'payment_date' => $request->payment_date ?? now(),
                'year' => $request->year ?? date('Y'),
                'notes' => $request->notes,
                'created_by' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Payment recorded successfully',
                'id' => $id
            ]);
        } catch (\Exception $e) {
            Log::error('storePayment error: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get payment details for viewing
     */
    public function getPaymentDetails($id)
    {
        try {
            $payment = DB::table('payments')
                ->join('users', 'payments.user_id', '=', 'users.id')
                ->select(
                    'payments.*', 
                    'users.name as member_name', 
                    'users.email as member_email'
                )
                ->where('payments.id', $id)
                ->first();
            
            if (!$payment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment not found'
                ], 404);
            }
            
            // Get recorded by name if created_by exists
            if (isset($payment->created_by) && $payment->created_by) {
                $creator = DB::table('users')
                    ->where('id', $payment->created_by)
                    ->select('name')
                    ->first();
                $payment->recorded_by_name = $creator ? $creator->name : 'System';
            } else {
                $payment->recorded_by_name = 'System';
            }
            
            return response()->json(['success' => true, 'payment' => $payment]);
        } catch (\Exception $e) {
            Log::error('getPaymentDetails error: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show a single payment (alias for getPaymentDetails)
     */
    public function showPayment($id)
    {
        return $this->getPaymentDetails($id);
    }

    /**
     * Update a payment with history tracking
     */
    public function updatePayment(Request $request, $id)
    {
        try {
            $request->validate([
                'amount' => 'required|numeric|min:0',
                'payment_method' => 'required|string',
                'payment_date' => 'required|date',
                'term' => 'required|integer|min:1',
                'notes' => 'nullable|string'
            ]);
            
            $existing = DB::table('payments')->where('id', $id)->first();
            
            if (!$existing) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment not found'
                ], 404);
            }
            
            // Check if payment_histories table exists, if not create it
            try {
                // Insert into payment history
                DB::table('payment_histories')->insert([
                    'payment_id' => $existing->id,
                    'user_id' => $existing->user_id,
                    'old_term' => $existing->term,
                    'new_term' => $request->term,
                    'old_amount' => $existing->amount,
                    'new_amount' => $request->amount,
                    'old_payment_method' => $existing->payment_method,
                    'new_payment_method' => $request->payment_method,
                    'old_payment_date' => $existing->payment_date,
                    'new_payment_date' => $request->payment_date,
                    'notes' => $request->notes,
                    'edited_by' => auth()->id(),
                    'created_at' => now()
                ]);
            } catch (\Exception $e) {
                // If payment_histories table doesn't exist, just log the error but continue
                \Log::warning('payment_histories table not found, skipping history logging: ' . $e->getMessage());
            }
            
            // Update the payment
            DB::table('payments')
                ->where('id', $id)
                ->update([
                    'amount' => $request->amount,
                    'payment_method' => $request->payment_method,
                    'payment_date' => $request->payment_date,
                    'term' => $request->term,
                    'notes' => $request->notes,
                    'updated_at' => now()
                ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Payment updated successfully'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error: ' . $e->getMessage(),
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('updatePayment error: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a payment
     */
    public function deletePayment($id)
    {
        try {
            $payment = DB::table('payments')->where('id', $id)->first();
            
            if (!$payment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment not found'
                ], 404);
            }
            
            DB::table('payments')->where('id', $id)->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Payment deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('deletePayment error: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get payment history (for audit trail)
     */
    public function getPaymentHistory($paymentId)
    {
        try {
            $payment = DB::table('payments')->where('id', $paymentId)->first();
            
            if (!$payment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment not found'
                ], 404);
            }
            
            // Check if payment_histories table exists
            $history = [];
            try {
                $history = DB::table('payment_histories')
                    ->where('payment_id', $paymentId)
                    ->leftJoin('users', 'payment_histories.edited_by', '=', 'users.id')
                    ->select('payment_histories.*', 'users.name as edited_by_name', 'users.email as edited_by_email')
                    ->orderBy('payment_histories.created_at', 'desc')
                    ->get();
            } catch (\Exception $e) {
                // Table doesn't exist, return empty history
                \Log::warning('payment_histories table not found');
            }
            
            return response()->json([
                'success' => true,
                'payment' => $payment,
                'history' => $history
            ]);
        } catch (\Exception $e) {
            Log::error('getPaymentHistory error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
