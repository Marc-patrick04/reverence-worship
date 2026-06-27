<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SponsorController extends Controller
{
    /**
     * Filter sponsors based on year and search
     */
   /**
 * Filter sponsors based on year and search
 */
/**
 * Filter sponsors based on year and search
 * Shows sponsors that have payments OR commitments in the selected year
 */
public function filterSponsors(Request $request)
{
    try {
        $search = $request->input('search', '');
        $status = $request->input('status', 'all');
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        $fromYear = (int) date('Y', strtotime($fromDate));
        $toYear = (int) date('Y', strtotime($toDate));
        
        Log::info('Filter Sponsors Request', [
            'search' => $search,
            'status' => $status,
            'from_date' => $fromDate,
            'to_date' => $toDate
        ]);
        
        // Get sponsors that have payments in this year OR have a commitment for this year
        $query = DB::table('sponsors')
            ->where(function($q) use ($fromYear, $toYear, $fromDate, $toDate) {
                $q->whereBetween('year', [$fromYear, $toYear])
                  ->orWhereExists(function($sub) use ($fromDate, $toDate) {
                      $sub->select(DB::raw(1))
                          ->from('sponsor_payments')
                          ->whereColumn('sponsor_payments.sponsor_id', 'sponsors.id')
                          ->whereDate('sponsor_payments.payment_date', '>=', $fromDate)
                          ->whereDate('sponsor_payments.payment_date', '<=', $toDate);
                  });
            });
        
        // Apply search filter
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%");
            });
        }
        
        $sponsors = $query->orderBy('name')->get();
        
        // Calculate received amounts for each sponsor in the selected year
        foreach ($sponsors as $sponsor) {
            $paymentsQuery = DB::table('sponsor_payments')
                ->where('sponsor_id', $sponsor->id);

            if ($fromDate && $toDate) {
                $paymentsQuery->whereDate('payment_date', '>=', $fromDate)
                    ->whereDate('payment_date', '<=', $toDate);
            }

            $received = $paymentsQuery->sum('amount');
            
            $sponsor->received_amount = $received ?? 0;
            $sponsor->commitment_amount = $sponsor->commitment_amount ?? 0;
        }
        
        // Apply status filter if needed
        if ($status && $status !== 'all') {
            $sponsors = $sponsors->filter(function($sponsor) use ($status) {
                $commitment = $sponsor->commitment_amount ?? 0;
                $received = $sponsor->received_amount ?? 0;
                
                if ($status === 'completed') {
                    return $received >= $commitment && $commitment > 0;
                } elseif ($status === 'active') {
                    return $received > 0 && $received < $commitment && $commitment > 0;
                } elseif ($status === 'overpaid') {
                    return $received > $commitment && $commitment > 0;
                } elseif ($status === 'inactive') {
                    return $received == 0 && $commitment == 0;
                }
                return true;
            })->values();
        }
        
        return response()->json([
            'success' => true,
            'sponsors' => $sponsors,
            'from_date' => $fromDate,
            'to_date' => $toDate,
            'total' => $sponsors->count()
        ]);
    } catch (\Exception $e) {
        Log::error('filterSponsors error: ' . $e->getMessage());
        Log::error('filterSponsors trace: ' . $e->getTraceAsString());
        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
            'sponsors' => [],
            'from_date' => $fromDate,
            'to_date' => $toDate
        ], 200);
    }
}

    /**
     * Export sponsors using the active search and date range.
     */
    public function exportSponsors(Request $request)
    {
        $request->validate([
            'from_date' => ['required', 'date'],
            'to_date' => ['required', 'date', 'after_or_equal:from_date'],
            'search' => ['nullable', 'string', 'max:255'],
        ]);

        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        $fromYear = (int) date('Y', strtotime($fromDate));
        $toYear = (int) date('Y', strtotime($toDate));

        $query = DB::table('sponsors')
            ->where(function ($q) use ($fromYear, $toYear, $fromDate, $toDate) {
                $q->whereBetween('year', [$fromYear, $toYear])
                    ->orWhereExists(function ($sub) use ($fromDate, $toDate) {
                        $sub->select(DB::raw(1))
                            ->from('sponsor_payments')
                            ->whereColumn('sponsor_payments.sponsor_id', 'sponsors.id')
                            ->whereDate('sponsor_payments.payment_date', '>=', $fromDate)
                            ->whereDate('sponsor_payments.payment_date', '<=', $toDate);
                    });
            });

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhere('phone', 'LIKE', "%{$search}%");
            });
        }

        $sponsors = $query->orderBy('name')->get();

        foreach ($sponsors as $sponsor) {
            $sponsor->range_received = DB::table('sponsor_payments')
                ->where('sponsor_id', $sponsor->id)
                ->whereDate('payment_date', '>=', $fromDate)
                ->whereDate('payment_date', '<=', $toDate)
                ->sum('amount');
        }

        $filename = "sponsors_{$fromDate}_to_{$toDate}.csv";

        return response()->streamDownload(function () use ($sponsors) {
            $output = fopen('php://output', 'w');
            fwrite($output, "\xEF\xBB\xBF");
            fputcsv($output, [
                'Sponsor', 'Email', 'Phone', 'Commitment (RWF)',
                'Received (RWF)', 'Remaining (RWF)', 'Fund Type', 'Status', 'Notes'
            ]);

            foreach ($sponsors as $sponsor) {
                $commitment = (float) ($sponsor->commitment_amount ?? 0);
                $received = (float) ($sponsor->range_received ?? 0);
                $row = [
                    $sponsor->name,
                    $sponsor->email,
                    $sponsor->phone,
                    $commitment,
                    $received,
                    $commitment - $received,
                    $sponsor->fund_type ?? '',
                    $sponsor->status,
                    $sponsor->notes,
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
     * Store a new sponsor
     */
    public function storeSponsor(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:50',
                'commitment_amount' => 'nullable|numeric|min:0',
                'notes' => 'nullable|string',
                'year' => 'nullable|integer'
            ]);
            
            $id = DB::table('sponsors')->insertGetId([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'commitment_amount' => $request->commitment_amount ?? 0,
                'notes' => $request->notes,
                'year' => $request->year ?? date('Y'),
                'status' => 'active',
                'created_by' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Sponsor created successfully',
                'id' => $id
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('storeSponsor error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update a sponsor
     */
    public function updateSponsor(Request $request, $id)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:50',
                'commitment_amount' => 'nullable|numeric|min:0',
                'notes' => 'nullable|string',
                'year' => 'nullable|integer'
            ]);
            
            // Check if sponsor exists
            $exists = DB::table('sponsors')->where('id', $id)->exists();
            if (!$exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sponsor not found'
                ], 404);
            }
            
            DB::table('sponsors')
                ->where('id', $id)
                ->update([
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'commitment_amount' => $request->commitment_amount ?? 0,
                    'notes' => $request->notes,
                    'year' => $request->year ?? date('Y'),
                    'updated_at' => now()
                ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Sponsor updated successfully'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('updateSponsor error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get sponsor for editing
     */
    public function editSponsor($id)
    {
        try {
            $sponsor = DB::table('sponsors')->where('id', $id)->first();
            
            if (!$sponsor) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sponsor not found'
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'sponsor' => $sponsor
            ]);
        } catch (\Exception $e) {
            Log::error('editSponsor error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a sponsor and all associated payments
     */
    public function deleteSponsor($id)
    {
        try {
            $exists = DB::table('sponsors')->where('id', $id)->exists();
            if (!$exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sponsor not found'
                ], 404);
            }
            
            // Delete associated payments
            DB::table('sponsor_payments')->where('sponsor_id', $id)->delete();
            // Delete the sponsor
            DB::table('sponsors')->where('id', $id)->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Sponsor deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('deleteSponsor error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Record a sponsor payment
     */
    public function recordSponsorPayment(Request $request)
    {
        try {
            $request->validate([
                'sponsor_id' => 'required|exists:sponsors,id',
                'amount' => 'required|numeric|min:0.01',
                'year' => 'required|integer',
                'payment_method' => 'nullable|string|max:50',
                'notes' => 'nullable|string'
            ]);
            
            $paymentId = DB::table('sponsor_payments')->insertGetId([
                'sponsor_id' => $request->sponsor_id,
                'amount' => $request->amount,
                'payment_date' => now(),
                'year' => $request->year,
                'payment_method' => $request->payment_method ?? 'cash',
                'notes' => $request->notes,
                'created_by' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            // Update sponsor status if commitment is met
            $totalReceived = DB::table('sponsor_payments')
                ->where('sponsor_id', $request->sponsor_id)
                ->where('year', $request->year)
                ->sum('amount');
            
            $sponsor = DB::table('sponsors')->where('id', $request->sponsor_id)->first();
            
            if ($sponsor && $sponsor->commitment_amount > 0 && $totalReceived >= $sponsor->commitment_amount) {
                DB::table('sponsors')->where('id', $request->sponsor_id)->update([
                    'status' => 'completed',
                    'updated_at' => now()
                ]);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Payment recorded successfully',
                'payment_id' => $paymentId
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('recordSponsorPayment error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get sponsor payments
     */
    public function getSponsorPayments($id, Request $request)
    {
        try {
            $year = $request->input('year', date('Y'));
            
            $payments = DB::table('sponsor_payments')
                ->where('sponsor_id', $id)
                ->where('year', $year)
                ->leftJoin('users', 'sponsor_payments.created_by', '=', 'users.id')
                ->select('sponsor_payments.*', 'users.name as recorded_by')
                ->orderBy('sponsor_payments.payment_date', 'desc')
                ->get();
            
            return response()->json([
                'success' => true,
                'payments' => $payments,
                'year' => $year
            ]);
        } catch (\Exception $e) {
            Log::error('getSponsorPayments error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'payments' => []
            ], 500);
        }
    }

    /**
     * Get all sponsors
     */
    public function getSponsors(Request $request)
    {
        try {
            $year = $request->input('year', date('Y'));
            
            $sponsors = DB::table('sponsors')
                ->orderBy('name', 'asc')
                ->get();
            
            foreach ($sponsors as $sponsor) {
                $received = DB::table('sponsor_payments')
                    ->where('sponsor_id', $sponsor->id)
                    ->where('year', $year)
                    ->sum('amount');
                
                $sponsor->received_amount = $received ?? 0;
                $sponsor->commitment_amount = $sponsor->commitment_amount ?? 0;
            }
            
            return response()->json([
                'success' => true,
                'sponsors' => $sponsors,
                'year' => $year
            ]);
        } catch (\Exception $e) {
            Log::error('getSponsors error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show a single sponsor
     */
    public function showSponsor($id)
    {
        try {
            $sponsor = DB::table('sponsors')
                ->leftJoin('users', 'sponsors.created_by', '=', 'users.id')
                ->select('sponsors.*', 'users.name as created_by_name')
                ->where('sponsors.id', $id)
                ->first();
            
            if (!$sponsor) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sponsor not found'
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'sponsor' => $sponsor
            ]);
        } catch (\Exception $e) {
            Log::error('showSponsor error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
