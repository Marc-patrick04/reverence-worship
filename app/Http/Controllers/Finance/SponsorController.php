<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SponsorController extends Controller
{
    public function filterSponsors(Request $request)
    {
        try {
            $search = $request->input('search', '');
            $status = $request->input('status', 'all');
            $year = $request->input('year', date('Y'));
            
            Log::info('Filter Sponsors Request', [
                'search' => $search,
                'status' => $status,
                'year' => $year
            ]);
            
            $query = DB::table('sponsors');
            
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('email', 'LIKE', "%{$search}%")
                      ->orWhere('phone', 'LIKE', "%{$search}%");
                });
            }
            
            $sponsors = $query->orderBy('name')->get();
            
            foreach ($sponsors as $sponsor) {
                $paymentsQuery = DB::table('sponsor_payments')
                    ->where('sponsor_id', $sponsor->id);
                
                if ($year && $year !== 'all') {
                    $paymentsQuery->where('year', $year);
                }
                
                $received = $paymentsQuery->sum('amount');
                $sponsor->received_amount = $received ?? 0;
                $sponsor->filter_year = $year;
            }
            
            if ($status && $status !== 'all') {
                $sponsors = $sponsors->filter(function($sponsor) use ($status) {
                    $commitment = $sponsor->commitment_amount ?? 0;
                    $received = $sponsor->received_amount ?? 0;
                    
                    if ($status === 'completed') {
                        return $received >= $commitment && $commitment > 0;
                    } elseif ($status === 'active') {
                        return $received > 0 && $received < $commitment;
                    } elseif ($status === 'overpaid') {
                        return $received > $commitment && $commitment > 0;
                    } elseif ($status === 'inactive') {
                        return $received == 0;
                    }
                    return true;
                })->values();
            }
            
            return response()->json([
                'success' => true,
                'sponsors' => $sponsors,
                'filter_year' => $year
            ]);
        } catch (\Exception $e) {
            Log::error('filterSponsors error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function storeSponsor(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'commitment_amount' => 'required|numeric|min:0',
                'email' => 'nullable|email',
                'phone' => 'nullable|string|max:50',
                'notes' => 'nullable|string'
            ]);
            
            $id = DB::table('sponsors')->insertGetId([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'commitment_amount' => $request->commitment_amount,
                'notes' => $request->notes,
                'status' => 'active',
                'created_by' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            return response()->json(['success' => true, 'message' => 'Sponsor created successfully', 'id' => $id]);
        } catch (\Exception $e) {
            Log::error('storeSponsor error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function updateSponsor(Request $request, $id)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'commitment_amount' => 'required|numeric|min:0',
                'email' => 'nullable|email',
                'phone' => 'nullable|string|max:50',
                'notes' => 'nullable|string'
            ]);
            
            DB::table('sponsors')->where('id', $id)->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'commitment_amount' => $request->commitment_amount,
                'notes' => $request->notes,
                'updated_at' => now()
            ]);
            
            return response()->json(['success' => true, 'message' => 'Sponsor updated successfully']);
        } catch (\Exception $e) {
            Log::error('updateSponsor error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function editSponsor($id)
    {
        try {
            $sponsor = DB::table('sponsors')->where('id', $id)->first();
            return response()->json(['success' => true, 'sponsor' => $sponsor]);
        } catch (\Exception $e) {
            Log::error('editSponsor error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function deleteSponsor($id)
    {
        try {
            DB::table('sponsor_payments')->where('sponsor_id', $id)->delete();
            DB::table('sponsors')->where('id', $id)->delete();
            return response()->json(['success' => true, 'message' => 'Sponsor deleted successfully']);
        } catch (\Exception $e) {
            Log::error('deleteSponsor error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function recordSponsorPayment(Request $request)
    {
        try {
            $request->validate([
                'sponsor_id' => 'required|exists:sponsors,id',
                'amount' => 'required|numeric|min:0',
                'payment_year' => 'required|integer',
                'payment_method' => 'nullable|string'
            ]);
            
            $paymentId = DB::table('sponsor_payments')->insertGetId([
                'sponsor_id' => $request->sponsor_id,
                'amount' => $request->amount,
                'payment_date' => now(),
                'year' => $request->payment_year,
                'payment_method' => $request->payment_method ?? 'cash',
                'notes' => $request->notes,
                'created_by' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            $totalReceived = DB::table('sponsor_payments')
                ->where('sponsor_id', $request->sponsor_id)
                ->where('year', $request->payment_year)
                ->sum('amount');
            
            $sponsor = DB::table('sponsors')->where('id', $request->sponsor_id)->first();
            
            if ($totalReceived >= $sponsor->commitment_amount) {
                DB::table('sponsors')->where('id', $request->sponsor_id)->update([
                    'status' => 'completed',
                    'updated_at' => now()
                ]);
            }
            
            return response()->json(['success' => true, 'message' => 'Payment recorded successfully', 'payment_id' => $paymentId]);
        } catch (\Exception $e) {
            Log::error('recordSponsorPayment error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getSponsorPayments($id)
    {
        try {
            $payments = DB::table('sponsor_payments')
                ->where('sponsor_id', $id)
                ->leftJoin('users', 'sponsor_payments.created_by', '=', 'users.id')
                ->select('sponsor_payments.*', 'users.name as recorded_by')
                ->orderBy('sponsor_payments.payment_date', 'desc')
                ->get();
            
            return response()->json([
                'success' => true,
                'payments' => $payments
            ]);
        } catch (\Exception $e) {
            Log::error('getSponsorPayments error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'payments' => []
            ]);
        }
    }

    public function getSponsors(Request $request)
    {
        try {
            $sponsors = DB::table('sponsors')
                ->orderBy('created_at', 'desc')
                ->get();
            
            return response()->json(['success' => true, 'sponsors' => $sponsors]);
        } catch (\Exception $e) {
            Log::error('getSponsors error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}