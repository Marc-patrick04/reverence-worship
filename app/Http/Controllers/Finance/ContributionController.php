<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ContributionController extends Controller
{
    public function index(Request $request)
{
    // Get selected year from request or default to current
    $selectedYear = $request->input('year', date('Y'));
    
    // Get term settings from database
    $termSettings = DB::table('finance_term_settings')
        ->where('current_year', $selectedYear)
        ->first();
    
    // Get number of terms from settings, default to 3 if not set
    $numberOfTerms = $termSettings->number_of_terms ?? 3;
    
    // Get term percentages from settings
    $termPercentages = [];
    if ($termSettings && $termSettings->term_percentages) {
        $termPercentages = json_decode($termSettings->term_percentages, true);
    }
    
    // If no percentages, create default distribution
    if (empty($termPercentages)) {
        $equalPercent = 100 / $numberOfTerms;
        for ($i = 1; $i <= $numberOfTerms; $i++) {
            $termPercentages[$i] = round($equalPercent, 2);
        }
        $termPercentages[1] = 100 - (($numberOfTerms - 1) * round($equalPercent, 2));
    }
    
    // Get all active users
    $users = DB::table('users')
        ->where('is_active', true)
        ->select('id', 'name', 'email')
        ->orderBy('name')
        ->get();
    
    // Get families for the selected year only (for the filter dropdown)
    $families = DB::table('families')
        ->where('year', $selectedYear)
        ->orderBy('name')
        ->get();
    
    return view('modules.finance.index', compact(
        'numberOfTerms', 
        'termPercentages', 
        'users', 
        'families',
        'selectedYear'
    ));
}
    
    // ==================== HELPER METHODS ====================
    
    private function safeSum($table, $column, $status = null)
    {
        try {
            $query = DB::table($table);
            if ($status) {
                $query->where('status', $status);
            }
            return $query->sum($column) ?? 0;
        } catch (\Exception $e) {
            return 0;
        }
    }
    
    private function safeCount($table, $column = null, $value = null)
    {
        try {
            $query = DB::table($table);
            if ($column && $value) {
                $query->where($column, $value);
            }
            return $query->count();
        } catch (\Exception $e) {
            return 0;
        }
    }
    
    // ==================== CONTRIBUTION METHODS ====================
    
   public function filterMemberContributions(Request $request)
{
    try {
        $search = $request->input('search', '');
        $year = $request->input('year', date('Y'));
        $familyId = $request->input('family_id', 'all');
        
        // Get or create term settings for the year
        $termSettings = DB::table('finance_term_settings')
            ->where('current_year', $year)
            ->first();
            
        if (!$termSettings) {
            $termSettingsId = DB::table('finance_term_settings')->insertGetId([
                'current_year' => $year,
                'number_of_terms' => 3,
                'term_percentages' => json_encode([1 => 40, 2 => 30, 3 => 30]),
                'term_numbers' => json_encode([1, 2, 3]),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $termSettings = DB::table('finance_term_settings')
                ->where('current_year', $year)
                ->first();
        }
        
        $numberOfTerms = $termSettings->number_of_terms ?? 3;
        $termPercentages = json_decode($termSettings->term_percentages ?? '[]', true);
        
        if (empty($termPercentages) || !is_array($termPercentages)) {
            $equalPercent = 100 / $numberOfTerms;
            for ($i = 1; $i <= $numberOfTerms; $i++) {
                $termPercentages[$i] = round($equalPercent, 2);
            }
            $termPercentages[1] = 100 - (($numberOfTerms - 1) * round($equalPercent, 2));
        }
        
        // Build the query - get ALL active users
        $query = DB::table('users')
            ->leftJoin('contributions', function($join) use ($year) {
                $join->on('users.id', '=', 'contributions.user_id')
                     ->where('contributions.year', '=', $year);
            })
            // First get the family member record
            ->leftJoin('family_members', 'users.id', '=', 'family_members.user_id')
            // Then join families with the year condition
            ->leftJoin('families', function($join) use ($year) {
                $join->on('family_members.family_id', '=', 'families.id')
                     ->where('families.year', '=', $year);
            })
            ->select(
                'users.id as user_id',
                'users.name as user_name',
                'users.email',
                'users.is_active',
                'contributions.id',
                'contributions.annual_amount',
                'contributions.notes as contribution_notes',
                'families.id as family_id',
                'families.name as family_name',
                'families.year as family_year'
            )
            ->where('users.is_active', true)  // Only show active users
            ->orderBy('users.name');
        
        // Apply family filter - only show users who are in the selected family
        if ($familyId && $familyId !== 'all') {
            $query->where('families.id', $familyId);
        }
        
        // Apply search filter
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('users.name', 'LIKE', "%{$search}%")
                  ->orWhere('users.email', 'LIKE', "%{$search}%");
            });
        }
        
        $contributions = $query->get();
        
        // Process contributions with term calculations
        $result = [];
        foreach ($contributions as $cont) {
            $annualAmount = $cont->annual_amount ?? 0;
            $cont->total_paid = 0;
            
            // Get all payments for this user in this year
            $payments = DB::table('payments')
                ->where('user_id', $cont->user_id)
                ->where('year', $year)
                ->get();
            
            // Group payments by term
            $termPayments = [];
            foreach ($payments as $payment) {
                $term = $payment->term;
                if (!isset($termPayments[$term])) {
                    $termPayments[$term] = 0;
                }
                $termPayments[$term] += $payment->amount;
            }
            
            // Calculate term targets and paid amounts
            for ($i = 1; $i <= $numberOfTerms; $i++) {
                $percentage = isset($termPercentages[$i]) ? $termPercentages[$i] : (100 / $numberOfTerms);
                $termTarget = ($annualAmount * $percentage) / 100;
                $termPaid = isset($termPayments[$i]) ? $termPayments[$i] : 0;
                
                $cont->{"term{$i}_target"} = round($termTarget, 2);
                $cont->{"term{$i}_paid"} = $termPaid;
                $cont->total_paid += $termPaid;
            }
            
            // Check if user has a family in this year
            // The family_year will be the year from the families table
            if ($cont->family_name && $cont->family_year == $year) {
                // User has a family in this year - keep it
            } else {
                // User has no family in this year
                $cont->family_name = null;
                $cont->family_id = null;
                $cont->family_year = null;
            }
            
            $result[] = $cont;
        }
        
        return response()->json([
            'success' => true, 
            'contributions' => $result,
            'filters' => [
                'search' => $search,
                'year' => $year,
                'family_id' => $familyId
            ]
        ]);
    } catch (\Exception $e) {
        Log::error('filterMemberContributions error: ' . $e->getMessage());
        return response()->json([
            'success' => false, 
            'message' => $e->getMessage()
        ], 500);
    }
}

public function exportContributions(Request $request)
{
    $request->validate([
        'year' => ['required', 'integer', 'min:2000', 'max:2100'],
        'family_id' => ['nullable'],
        'search' => ['nullable', 'string', 'max:255'],
    ]);

    $year = (int) $request->input('year');
    $familyId = $request->input('family_id', 'all');
    $termSettings = DB::table('finance_term_settings')->where('current_year', $year)->first();
    $numberOfTerms = (int) ($termSettings->number_of_terms ?? 3);
    $termPercentages = json_decode($termSettings->term_percentages ?? '[]', true);

    if (empty($termPercentages)) {
        $termPercentages = array_fill(1, $numberOfTerms, 100 / $numberOfTerms);
    }

    $query = DB::table('users')
        ->leftJoin('contributions', function ($join) use ($year) {
            $join->on('users.id', '=', 'contributions.user_id')
                ->where('contributions.year', $year);
        })
        ->leftJoin('family_members', 'users.id', '=', 'family_members.user_id')
        ->leftJoin('families', function ($join) use ($year) {
            $join->on('family_members.family_id', '=', 'families.id')
                ->where('families.year', $year);
        })
        ->select(
            'users.id as user_id',
            'users.name',
            'users.email',
            'contributions.annual_amount',
            'families.name as family_name'
        )
        ->where('users.is_active', true);

    if ($familyId && $familyId !== 'all') {
        $query->where('families.id', $familyId);
    }

    if ($request->filled('search')) {
        $search = $request->input('search');
        $query->where(function ($q) use ($search) {
            $q->where('users.name', 'LIKE', "%{$search}%")
                ->orWhere('users.email', 'LIKE', "%{$search}%");
        });
    }

    $members = $query->orderBy('users.name')->get();
    $payments = DB::table('payments')
        ->where('year', $year)
        ->select('user_id', 'term', DB::raw('SUM(amount) as total'))
        ->groupBy('user_id', 'term')
        ->get()
        ->groupBy('user_id');

    return response()->streamDownload(function () use ($members, $payments, $numberOfTerms, $termPercentages) {
        $output = fopen('php://output', 'w');
        fwrite($output, "\xEF\xBB\xBF");

        $headers = ['Member', 'Email', 'Family', 'Annual Target (RWF)'];
        for ($term = 1; $term <= $numberOfTerms; $term++) {
            $headers[] = "Term {$term} Target (RWF)";
            $headers[] = "Term {$term} Paid (RWF)";
        }
        $headers = array_merge($headers, ['Total Paid (RWF)', 'Outstanding (RWF)', 'Progress']);
        fputcsv($output, $headers);

        foreach ($members as $member) {
            $annualAmount = (float) ($member->annual_amount ?? 0);
            $memberPayments = $payments->get($member->user_id, collect())->keyBy('term');
            $row = [$member->name, $member->email, $member->family_name ?? '', $annualAmount];
            $totalPaid = 0;

            for ($term = 1; $term <= $numberOfTerms; $term++) {
                $percentage = (float) ($termPercentages[$term] ?? (100 / $numberOfTerms));
                $termTarget = round($annualAmount * $percentage / 100, 2);
                $termPayment = $memberPayments->get($term);
                $termPaid = (float) ($termPayment->total ?? 0);
                $row[] = $termTarget;
                $row[] = $termPaid;
                $totalPaid += $termPaid;
            }

            $row[] = $totalPaid;
            $row[] = max($annualAmount - $totalPaid, 0);
            $row[] = $annualAmount > 0 ? round($totalPaid / $annualAmount * 100, 1) . '%' : '0%';
            $row = array_map(function ($value) {
                $value = (string) ($value ?? '');
                return preg_match('/^[=+\-@]/', $value) ? "'{$value}" : $value;
            }, $row);
            fputcsv($output, $row);
        }

        fclose($output);
    }, "contributions_{$year}.csv", ['Content-Type' => 'text/csv; charset=UTF-8']);
}

/**
 * Get families that have members with contributions in the selected year
 */
public function getFamiliesWithContributions(Request $request)
{
    try {
        $year = $request->input('year', date('Y'));
        
        // Get families that have members with contributions in the selected year
        $families = DB::table('families')
            ->join('family_members', 'families.id', '=', 'family_members.family_id')
            ->join('users', 'family_members.user_id', '=', 'users.id')
            ->join('contributions', 'users.id', '=', 'contributions.user_id')
            ->where('contributions.year', $year)
            ->select('families.id', 'families.name', DB::raw('COUNT(DISTINCT users.id) as member_count'))
            ->groupBy('families.id', 'families.name')
            ->orderBy('families.name')
            ->get();
        
        return response()->json([
            'success' => true,
            'families' => $families
        ]);
    } catch (\Exception $e) {
        Log::error('getFamiliesWithContributions error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}
/**
 * Get families that exist in the selected year
 */
public function getFamilyFilterOptions(Request $request)
{
    try {
        $year = $request->input('year', date('Y'));
        
        // Get families that exist in the selected year
        $families = DB::table('families')
            ->where('year', $year)
            ->select('families.id', 'families.name')
            ->orderBy('families.name')
            ->get();
        
        return response()->json([
            'success' => true,
            'families' => $families
        ]);
    } catch (\Exception $e) {
        \Log::error('getFamilyFilterOptions error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}
    
    /**
     * Get contributions for initial page load with all filters applied
     */
    public function getInitialContributions(Request $request)
    {
        return $this->filterMemberContributions($request);
    }
    
    public function setMemberAnnualContribution(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'required|exists:users,id',
                'annual_amount' => 'required|numeric|min:0'
            ]);
            
            $year = $request->year ?? date('Y');
            $userId = $request->user_id;
            $annualAmount = $request->annual_amount;
            $notes = $request->notes;
            
            // Check if contribution exists
            $existing = DB::table('contributions')
                ->where('user_id', $userId)
                ->where('year', $year)
                ->first();
            
            if ($existing) {
                // Log the change if amount changed
                if ($existing->annual_amount != $annualAmount) {
                    // Insert into contribution history
                    try {
                        DB::table('contribution_histories')->insert([
                            'contribution_id' => $existing->id,
                            'user_id' => $userId,
                            'old_amount' => $existing->annual_amount,
                            'new_amount' => $annualAmount,
                            'year' => $year,
                            'notes' => $notes,
                            'edited_by' => auth()->id(),
                            'created_at' => now()
                        ]);
                    } catch (\Exception $e) {
                        \Log::warning('contribution_histories table not found');
                    }
                }
                
                DB::table('contributions')
                    ->where('user_id', $userId)
                    ->where('year', $year)
                    ->update([
                        'annual_amount' => $annualAmount,
                        'notes' => $notes,
                        'updated_at' => now()
                    ]);
            } else {
                DB::table('contributions')->insert([
                    'user_id' => $userId,
                    'annual_amount' => $annualAmount,
                    'year' => $year,
                    'notes' => $notes,
                    'created_by' => auth()->id(),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
            
            return response()->json([
                'success' => true, 
                'message' => 'Annual contribution set successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('setMemberAnnualContribution error: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function payMemberContribution(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'required|exists:users,id',
                'term' => 'required|integer|min:1',
                'amount' => 'required|numeric|min:0'
            ]);
            
            $year = $request->year ?? date('Y');
            
            DB::table('payments')->insert([
                'user_id' => $request->user_id,
                'term' => $request->term,
                'amount' => $request->amount,
                'payment_method' => $request->payment_method ?? 'cash',
                'payment_date' => now(),
                'year' => $year,
                'notes' => $request->notes,
                'created_by' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            return response()->json([
                'success' => true, 
                'message' => 'Payment recorded successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('payMemberContribution error: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function deleteMemberContribution($userId)
    {
        try {
            DB::table('payments')->where('user_id', $userId)->delete();
            DB::table('contributions')->where('user_id', $userId)->delete();
            
            return response()->json([
                'success' => true, 
                'message' => 'Contributions deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('deleteMemberContribution error: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function getMemberContributionDetails($userId)
    {
        try {
            $user = DB::table('users')->where('id', $userId)->first();
            $contribution = DB::table('contributions')->where('user_id', $userId)->first();
            $payments = DB::table('payments')
                ->where('user_id', $userId)
                ->orderBy('payment_date', 'desc')
                ->get();
            
            $totalPaid = $payments->sum('amount');
            $annualAmount = $contribution->annual_amount ?? 0;
            $progress = $annualAmount > 0 ? round(($totalPaid / $annualAmount) * 100, 1) : 0;
            
            // Get contribution history
            $contributionHistory = [];
            try {
                $contributionHistory = DB::table('contribution_histories')
                    ->where('user_id', $userId)
                    ->leftJoin('users', 'contribution_histories.edited_by', '=', 'users.id')
                    ->select('contribution_histories.*', 'users.name as edited_by_name', 'users.email as edited_by_email')
                    ->orderBy('contribution_histories.created_at', 'desc')
                    ->get();
            } catch (\Exception $e) {
                \Log::warning('contribution_histories table not found');
            }
            
            return response()->json([
                'success' => true,
                'user_name' => $user->name,
                'annual_amount' => $annualAmount,
                'total_paid' => $totalPaid,
                'progress' => $progress,
                'payments' => $payments,
                'contribution_history' => $contributionHistory
            ]);
        } catch (\Exception $e) {
            Log::error('getMemberContributionDetails error: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function getMemberContributions(Request $request)
    {
        try {
            $year = $request->input('year', date('Y'));
            $contributions = DB::table('contributions')
                ->join('users', 'contributions.user_id', '=', 'users.id')
                ->select('contributions.*', 'users.name as user_name')
                ->where('contributions.year', $year)
                ->orderBy('contributions.created_at', 'desc')
                ->get();
            
            return response()->json(['success' => true, 'contributions' => $contributions]);
        } catch (\Exception $e) {
            Log::error('getMemberContributions error: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    // ==================== PAYMENT METHODS ====================
    
    public function getPaymentsList(Request $request)
    {
        try {
            $year = $request->input('year', date('Y'));
            $payments = DB::table('payments')
                ->join('users', 'payments.user_id', '=', 'users.id')
                ->select('payments.*', 'users.name as member_name')
                ->where('payments.year', $year)
                ->orderBy('payments.created_at', 'desc')
                ->get();
            
            return response()->json(['success' => true, 'payments' => $payments]);
        } catch (\Exception $e) {
            Log::error('getPaymentsList error: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function filterPaymentsList(Request $request)
    {
        try {
            $query = DB::table('payments')
                ->join('users', 'payments.user_id', '=', 'users.id')
                ->select('payments.*', 'users.name as member_name');
            
            // Apply filters by default
            if ($request->has('year') && $request->year) {
                $query->where('payments.year', $request->year);
            } else {
                // Default to current year if no year specified
                $query->where('payments.year', date('Y'));
            }
            
            if ($request->member_id && $request->member_id != 'all') {
                $query->where('payments.user_id', $request->member_id);
            }
            
            if ($request->term && $request->term != 'all') {
                $query->where('payments.term', $request->term);
            }
            
            if ($request->month) {
                $query->whereYear('payments.payment_date', substr($request->month, 0, 4))
                      ->whereMonth('payments.payment_date', substr($request->month, 5, 2));
            }
            
            if ($request->search) {
                $query->where(function($q) use ($request) {
                    $q->where('users.name', 'LIKE', "%{$request->search}%")
                      ->orWhere('users.email', 'LIKE', "%{$request->search}%");
                });
            }
            
            $payments = $query->orderBy('payments.created_at', 'desc')->get();
            
            return response()->json(['success' => true, 'payments' => $payments]);
        } catch (\Exception $e) {
            Log::error('filterPaymentsList error: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function storePayment(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'required|exists:users,id',
                'term' => 'required|integer|min:1',
                'amount' => 'required|numeric|min:0',
                'payment_method' => 'nullable|string'
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
    
    public function updatePayment(Request $request, $id)
    {
        try {
            $request->validate([
                'user_id' => 'required|exists:users,id',
                'term' => 'required|integer|min:1',
                'amount' => 'required|numeric|min:0',
                'payment_method' => 'nullable|string'
            ]);
            
            DB::table('payments')
                ->where('id', $id)
                ->update([
                    'user_id' => $request->user_id,
                    'term' => $request->term,
                    'amount' => $request->amount,
                    'payment_method' => $request->payment_method ?? 'cash',
                    'payment_date' => $request->payment_date ?? now(),
                    'year' => $request->year ?? date('Y'),
                    'notes' => $request->notes,
                    'updated_at' => now()
                ]);
            
            return response()->json([
                'success' => true, 
                'message' => 'Payment updated successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('updatePayment error: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function deletePayment($id)
    {
        try {
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
    
    public function showPayment($id)
    {
        try {
            $payment = DB::table('payments')
                ->join('users', 'payments.user_id', '=', 'users.id')
                ->leftJoin('users as creators', 'payments.created_by', '=', 'creators.id')
                ->select('payments.*', 'users.name as member_name', 'creators.name as created_by_name')
                ->where('payments.id', $id)
                ->first();
            
            return response()->json(['success' => true, 'payment' => $payment]);
        } catch (\Exception $e) {
            Log::error('showPayment error: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    // ==================== OVERVIEW STATS ====================
    
    public function getOverviewStats(Request $request)
    {
        try {
            $validated = $request->validate([
                'from_date' => ['nullable', 'date'],
                'to_date' => ['nullable', 'date', 'after_or_equal:from_date'],
            ]);

            $fromDate = $validated['from_date'] ?? date('Y-01-01');
            $toDate = $validated['to_date'] ?? date('Y-12-31');
            $fromYear = (int) date('Y', strtotime($fromDate));
            $toYear = (int) date('Y', strtotime($toDate));
            
            // Contribution commitments are annual, so include each year touched by the range.
            $totalExpected = DB::table('contributions')
                ->whereBetween('year', [$fromYear, $toYear])
                ->sum('annual_amount') ?? 0;
                
            $totalCollected = DB::table('payments')
                ->whereDate('payment_date', '>=', $fromDate)
                ->whereDate('payment_date', '<=', $toDate)
                ->sum('amount') ?? 0;
                
            $collectionRate = $totalExpected > 0 ? round(($totalCollected / $totalExpected) * 100, 1) : 0;
            
            $giftCommitments = DB::table('gifts')
                ->whereDate('date', '>=', $fromDate)
                ->whereDate('date', '<=', $toDate)
                ->sum('commitment_amount') ?? 0;
            $giftReceived = DB::table('gifts')
                ->whereDate('date', '>=', $fromDate)
                ->whereDate('date', '<=', $toDate)
                ->sum('received_amount') ?? 0;
            $activeGifts = DB::table('gifts')
                ->where('status', 'active')
                ->whereDate('date', '>=', $fromDate)
                ->whereDate('date', '<=', $toDate)
                ->count();
            
            $sponsorReceived = DB::table('sponsor_payments')
                ->whereDate('payment_date', '>=', $fromDate)
                ->whereDate('payment_date', '<=', $toDate)
                ->sum('amount') ?? 0;
                
            $sponsorCommitments = DB::table('sponsors')
                ->whereDate('created_at', '>=', $fromDate)
                ->whereDate('created_at', '<=', $toDate)
                ->sum('commitment_amount') ?? 0;
            $activeFunds = DB::table('sponsors')
                ->where('status', 'active')
                ->whereDate('created_at', '>=', $fromDate)
                ->whereDate('created_at', '<=', $toDate)
                ->count();
            
            $totalExpenses = DB::table('expenses')
                ->whereDate('date', '>=', $fromDate)
                ->whereDate('date', '<=', $toDate)
                ->sum('amount') ?? 0;
                
            $pendingApproval = DB::table('expenses')
                ->where('status', 'pending')
                ->whereDate('date', '>=', $fromDate)
                ->whereDate('date', '<=', $toDate)
                ->sum('amount') ?? 0;
                
            $transactionCount = DB::table('expenses')
                ->whereDate('date', '>=', $fromDate)
                ->whereDate('date', '<=', $toDate)
                ->count();
            
            $totalIncome = $totalCollected + $giftReceived + $sponsorReceived;
            
            $stats = [
                'total_income' => $totalIncome,
                'total_expenses' => $totalExpenses,
                'total_expected' => $totalExpected,
                'total_collected' => $totalCollected,
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
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    // ==================== ADDITIONAL METHODS FOR FINANCE CONTROLLER ====================

    /**
     * Get monthly trend data for charts
     */
    public function getMonthlyTrend(Request $request)
    {
        try {
            $year = $request->input('year', date('Y'));
            
            // Get monthly income data
            $incomeData = DB::table('payments')
                ->where('year', $year)
                ->select(
                    DB::raw('EXTRACT(MONTH FROM payment_date) as month'),
                    DB::raw('SUM(amount) as total')
                )
                ->whereNotNull('payment_date')
                ->groupBy(DB::raw('EXTRACT(MONTH FROM payment_date)'))
                ->pluck('total', 'month')
                ->toArray();
            
            // Get monthly expense data
            $expenseData = DB::table('expenses')
                ->whereYear('date', $year)
                ->select(
                    DB::raw('EXTRACT(MONTH FROM date) as month'),
                    DB::raw('SUM(amount) as total')
                )
                ->groupBy(DB::raw('EXTRACT(MONTH FROM date)'))
                ->pluck('total', 'month')
                ->toArray();
            
            // Fill missing months with 0
            $income = array_fill(1, 12, 0);
            $expenses = array_fill(1, 12, 0);
            
            foreach ($incomeData as $month => $total) {
                $income[(int)$month] = (float)$total;
            }
            
            foreach ($expenseData as $month => $total) {
                $expenses[(int)$month] = (float)$total;
            }
            
            return response()->json([
                'success' => true,
                'income' => array_values($income),
                'expenses' => array_values($expenses)
            ]);
        } catch (\Exception $e) {
            Log::error('getMonthlyTrend error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get income breakdown for charts
     */
    public function getIncomeBreakdown(Request $request)
    {
        try {
            $year = $request->input('year', date('Y'));
            
            $memberContributions = DB::table('payments')
                ->where('year', $year)
                ->sum('amount') ?? 0;
            
            $gifts = DB::table('gifts')
                ->sum('received_amount') ?? 0;
            
            $sponsors = DB::table('sponsor_payments')
                ->where('year', $year)
                ->sum('amount') ?? 0;
            
            return response()->json([
                'success' => true,
                'breakdown' => [
                    'member_contributions' => (float)$memberContributions,
                    'gifts' => (float)$gifts,
                    'sponsors' => (float)$sponsors
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('getIncomeBreakdown error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get expense breakdown for charts
     */
    public function getExpenseBreakdown(Request $request)
    {
        try {
            $year = $request->input('year', date('Y'));
            
            $expenses = DB::table('expenses')
                ->whereYear('date', $year)
                ->select('category', DB::raw('SUM(amount) as total'))
                ->groupBy('category')
                ->get();
            
            $breakdown = [];
            foreach ($expenses as $expense) {
                $breakdown[$expense->category] = (float)$expense->total;
            }
            
            return response()->json([
                'success' => true,
                'breakdown' => $breakdown
            ]);
        } catch (\Exception $e) {
            Log::error('getExpenseBreakdown error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get settings for a specific year
     */
    public function getSettings(Request $request)
    {
        try {
            $year = $request->input('year', date('Y'));
            
            $settings = DB::table('finance_term_settings')
                ->where('current_year', $year)
                ->first();
            
            if (!$settings) {
                return response()->json([
                    'success' => true,
                    'settings' => null,
                    'message' => 'No settings found for year ' . $year
                ]);
            }
            
            $termPercentages = json_decode($settings->term_percentages, true);
            $settings->term_percentages = $termPercentages;
            $settings->term_numbers = json_decode($settings->term_numbers, true) ?: array_keys($termPercentages);
            
            return response()->json([
                'success' => true,
                'settings' => $settings
            ]);
        } catch (\Exception $e) {
            Log::error('getSettings error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update settings
     */
    public function updateSettings(Request $request)
    {
        try {
            $termPercentages = json_decode($request->term_percentages, true);
            $termNumbers = json_decode($request->term_numbers, true);
            $numberOfTerms = $request->number_of_terms;
            $currentYear = $request->current_year;
            
            if (empty($currentYear)) {
                $currentYear = date('Y');
            }
            
            $total = array_sum($termPercentages);
            
            if (abs($total - 100) > 0.01) {
                return response()->json([
                    'success' => false,
                    'message' => 'Total percentage must equal 100%'
                ], 400);
            }
            
            $termPercentagesAssoc = [];
            foreach ($termNumbers as $index => $termNum) {
                $termPercentagesAssoc[$termNum] = (float) $termPercentages[$index];
            }
            
            $existingSettings = DB::table('finance_term_settings')
                ->where('current_year', $currentYear)
                ->first();
            
            if ($existingSettings) {
                DB::table('finance_term_settings')
                    ->where('id', $existingSettings->id)
                    ->update([
                        'number_of_terms' => (int) $numberOfTerms,
                        'term_percentages' => json_encode($termPercentagesAssoc),
                        'term_numbers' => json_encode($termNumbers),
                        'updated_at' => now()
                    ]);
            } else {
                DB::table('finance_term_settings')->insert([
                    'current_year' => $currentYear,
                    'number_of_terms' => (int) $numberOfTerms,
                    'term_percentages' => json_encode($termPercentagesAssoc),
                    'term_numbers' => json_encode($termNumbers),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
            
            $savedSettings = DB::table('finance_term_settings')
                ->where('current_year', $currentYear)
                ->first();
            
            return response()->json([
                'success' => true,
                'message' => 'Settings saved successfully',
                'settings' => [
                    'current_year' => $savedSettings->current_year,
                    'number_of_terms' => $savedSettings->number_of_terms,
                    'term_percentages' => json_decode($savedSettings->term_percentages, true),
                    'term_numbers' => json_decode($savedSettings->term_numbers, true)
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('updateSettings error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update a member contribution
     */
    public function updateMemberContribution(Request $request)
    {
        try {
            $request->validate([
                'contribution_id' => 'required|integer',
                'user_id' => 'required|exists:users,id',
                'annual_amount' => 'required|numeric|min:0',
                'year' => 'required|integer'
            ]);
            
            $existing = DB::table('contributions')
                ->where('id', $request->contribution_id)
                ->first();
            
            if ($existing) {
                // Log the change
                DB::table('contribution_histories')->insert([
                    'contribution_id' => $existing->id,
                    'user_id' => $existing->user_id,
                    'old_amount' => $existing->annual_amount,
                    'new_amount' => $request->annual_amount,
                    'year' => $request->year,
                    'notes' => $request->notes,
                    'edited_by' => auth()->id(),
                    'created_at' => now()
                ]);
                
                DB::table('contributions')
                    ->where('id', $request->contribution_id)
                    ->update([
                        'annual_amount' => $request->annual_amount,
                        'notes' => $request->notes,
                        'updated_at' => now()
                    ]);
            } else {
                DB::table('contributions')->insert([
                    'user_id' => $request->user_id,
                    'annual_amount' => $request->annual_amount,
                    'year' => $request->year,
                    'notes' => $request->notes,
                    'created_by' => auth()->id(),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Contribution updated successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('updateMemberContribution error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get payment history
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
            
            $history = DB::table('payment_histories')
                ->where('payment_id', $paymentId)
                ->leftJoin('users', 'payment_histories.edited_by', '=', 'users.id')
                ->select('payment_histories.*', 'users.name as edited_by_name', 'users.email as edited_by_email')
                ->orderBy('payment_histories.created_at', 'desc')
                ->get();
            
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

    // ==================== SPONSOR METHODS ====================

    public function getSponsors(Request $request)
    {
        try {
            $sponsors = DB::table('sponsors')
                ->orderBy('created_at', 'desc')
                ->get();
            
            return response()->json(['success' => true, 'sponsors' => $sponsors]);
        } catch (\Exception $e) {
            Log::error('getSponsors error: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function filterSponsors(Request $request)
    {
        try {
            $search = $request->input('search', '');
            $status = $request->input('status', 'all');
            $year = $request->input('year', date('Y'));
            
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
            
            return response()->json(['success' => true, 'sponsors' => $sponsors]);
        } catch (\Exception $e) {
            Log::error('filterSponsors error: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // ==================== GIFTS METHODS ====================

    public function getGifts(Request $request)
    {
        try {
            $gifts = DB::table('gifts')
                ->orderBy('created_at', 'desc')
                ->get();
            
            return response()->json(['success' => true, 'gifts' => $gifts]);
        } catch (\Exception $e) {
            Log::error('getGifts error: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // ==================== EXPENSES METHODS ====================

    public function getExpenses(Request $request)
    {
        try {
            $year = $request->input('year', date('Y'));
            $expenses = DB::table('expenses')
                ->whereYear('date', $year)
                ->orderBy('created_at', 'desc')
                ->get();
            
            return response()->json(['success' => true, 'expenses' => $expenses]);
        } catch (\Exception $e) {
            Log::error('getExpenses error: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function filterExpenses(Request $request)
    {
        try {
            $category = $request->category;
            $status = $request->status;
            $startDate = $request->start_date;
            $endDate = $request->end_date;
            $year = $request->input('year', date('Y'));
            
            $query = DB::table('expenses')
                ->whereYear('date', $year);
            
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
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ], 500);
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
            
            return response()->json([
                'success' => true, 
                'message' => 'Expense recorded successfully', 
                'id' => $id
            ]);
        } catch (\Exception $e) {
            Log::error('storeExpense error: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ], 500);
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

    public function deleteExpense($id)
    {
        try {
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
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getExpenseDetails($id)
    {
        return $this->showExpense($id);
    }

    public function approveExpense($id)
    {
        try {
            DB::table('expenses')->where('id', $id)->update([
                'status' => 'approved',
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
}
