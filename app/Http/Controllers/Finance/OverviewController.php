<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OverviewController extends Controller
{
    public function getOverviewStats(Request $request)
    {
        try {
            $year = $request->get('year', date('Y'));
            
            // Member Contributions - filter by year
            $totalExpected = (float) DB::table('contributions')
                ->where('year', $year)
                ->sum('annual_amount') ?? 0;
            
            // Count total members who have contributions
            $totalMembers = (int) DB::table('contributions')
                ->where('year', $year)
                ->distinct('user_id')
                ->count('user_id');
            
            $totalCollected = (float) DB::table('payments')
                ->where('year', $year)
                ->sum('amount') ?? 0;
            
            // Count members who have paid (>0 amount)
            $membersPaid = (int) DB::table('payments')
                ->where('year', $year)
                ->where('amount', '>', 0)
                ->distinct('user_id')
                ->count('user_id');
            
            $collectionRate = $totalExpected > 0 ? round(($totalCollected / $totalExpected) * 100, 1) : 0;
            
            // Gifts - all time (gifts don't have year filter)
            $giftCommitments = (float) DB::table('gifts')->sum('commitment_amount') ?? 0;
            $giftReceived = (float) DB::table('gifts')->sum('received_amount') ?? 0;
            $activeGifts = (int) DB::table('gifts')->where('status', 'active')->count();
            
            // Sponsors - filter by year
            $sponsorReceived = (float) DB::table('sponsor_payments')
                ->where('year', $year)
                ->sum('amount') ?? 0;
            
            $sponsorCommitments = (float) DB::table('sponsors')->sum('commitment_amount') ?? 0;
            $activeFunds = (int) DB::table('sponsors')->where('status', 'active')->count();
            
            // Expenses - filter by year
            $totalExpenses = (float) DB::table('expenses')
                ->whereYear('date', $year)
                ->sum('amount') ?? 0;
            
            $pendingApproval = (float) DB::table('expenses')
                ->where('status', 'pending')
                ->whereYear('date', $year)
                ->sum('amount') ?? 0;
            
            $transactionCount = (int) DB::table('expenses')
                ->whereYear('date', $year)
                ->count();
            
            $totalIncome = $totalCollected + $giftReceived + $sponsorReceived;
            
            $stats = [
                'total_income' => $totalIncome,
                'total_expenses' => $totalExpenses,
                'total_expected' => $totalExpected,
                'total_members' => $totalMembers,
                'total_collected' => $totalCollected,
                'members_paid' => $membersPaid,
                'collection_rate' => $collectionRate,
                'gift_commitments' => $giftCommitments,
                'gift_received' => $giftReceived,
                'active_gifts' => $activeGifts,
                'sponsor_commitments' => $sponsorCommitments,
                'sponsor_received' => $sponsorReceived,
                'active_funds' => $activeFunds,
                'pending_approval' => $pendingApproval,
                'transaction_count' => $transactionCount
            ];
            
            return response()->json(['success' => true, 'stats' => $stats]);
            
        } catch (\Exception $e) {
            Log::error('getOverviewStats error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ], 500);
        }
    }
}