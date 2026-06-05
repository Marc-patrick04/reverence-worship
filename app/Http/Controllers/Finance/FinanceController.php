<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FinanceController extends Controller
{
    public function index()
{
    // Get term settings from database
    $termSettings = DB::table('finance_term_settings')->first();
    
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
        // Adjust first term to make total 100
        $termPercentages[1] = 100 - (($numberOfTerms - 1) * round($equalPercent, 2));
    }
    
    $users = DB::table('users')->select('id', 'name', 'email')->orderBy('name')->get();
    
    // Get all contributions with user data
    $contributions = DB::table('contributions')
        ->join('users', 'contributions.user_id', '=', 'users.id')
        ->select('contributions.*', 'users.name as user_name', 'users.email')
        ->orderBy('users.name')
        ->get();
    
    return view('modules.finance.index', compact('numberOfTerms', 'termPercentages', 'users', 'contributions'));
}
    
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
   public function filterContributions(Request $request)
{
    try {
        $search = $request->search;
        
        // Get term settings
        $termSettings = DB::table('finance_term_settings')->first();
        if (!$termSettings) {
            // Create default settings if not exists
            $termSettingsId = DB::table('finance_term_settings')->insertGetId([
                'current_year' => date('Y'),
                'number_of_terms' => 3,
                'term_percentages' => json_encode([1 => 40, 2 => 30, 3 => 30]),
                'term_numbers' => json_encode([1, 2, 3]),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $termSettings = DB::table('finance_term_settings')->first();
        }
        
        $numberOfTerms = $termSettings->number_of_terms ?? 3;
        $termPercentages = json_decode($termSettings->term_percentages ?? '[]', true);
        $currentYear = $termSettings->current_year ?? date('Y');
        
        // Get all users (including those without contributions)
        $query = DB::table('users')
            ->leftJoin('contributions', function($join) use ($currentYear) {
                $join->on('users.id', '=', 'contributions.user_id')
                     ->where('contributions.year', '=', $currentYear);
            })
            ->select(
                'users.id as user_id',
                'users.name as user_name',
                'users.email',
                'contributions.annual_amount',
                'contributions.notes as contribution_notes'
            );
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('users.name', 'ilike', "%{$search}%")
                  ->orWhere('users.email', 'ilike', "%{$search}%");
            });
        }
        
        $contributions = $query->orderBy('users.name')->get();
        
        foreach ($contributions as $cont) {
            $annualAmount = $cont->annual_amount ?? 0;
            $cont->term_targets = [];
            $cont->total_paid = 0;
            
            for ($i = 1; $i <= $numberOfTerms; $i++) {
                $percentage = isset($termPercentages[$i]) ? $termPercentages[$i] : (100 / $numberOfTerms);
                $termTarget = ($annualAmount * $percentage) / 100;
                $cont->term_targets[$i] = round($termTarget, 2);
                
                $termPaid = DB::table('payments')
                    ->where('user_id', $cont->user_id)
                    ->where('term', $i)
                    ->where('year', $currentYear)
                    ->sum('amount');
                
                $cont->{"term{$i}_paid"} = $termPaid ?? 0;
                $cont->total_paid += $termPaid ?? 0;
            }
        }
        
        return response()->json(['success' => true, 'contributions' => $contributions]);
    } catch (\Exception $e) {
        \Log::error('filterContributions error: ' . $e->getMessage() . ' Line: ' . $e->getLine());
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}

public function filterSponsors(Request $request)
{
    try {
        $search = $request->search;
        $status = $request->status;
        
        $query = DB::table('sponsors');
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                  ->orWhere('email', 'ilike', "%{$search}%");
            });
        }
        
        if ($status && $status !== 'all') {
            if ($status === 'completed') {
                $query->whereRaw('received_amount >= commitment_amount');
            } else {
                $query->where('status', $status);
            }
        }
        
        $sponsors = $query->orderBy('name')->get();
        
        // Calculate received amounts from payments
        foreach ($sponsors as $sponsor) {
            $received = DB::table('sponsor_payments')
                ->where('sponsor_id', $sponsor->id)
                ->sum('amount');
            $sponsor->received_amount = $received ?? 0;
        }
        
        return response()->json(['success' => true, 'sponsors' => $sponsors]);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
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
            'fund_type' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);
        
        $id = DB::table('sponsors')->insertGetId([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'commitment_amount' => $request->commitment_amount,
            'fund_type' => $request->fund_type ?? 'one_time',
            'notes' => $request->notes,
            'status' => 'active',
            'created_by' => auth()->id(),
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        return response()->json(['success' => true, 'message' => 'Sponsor created successfully', 'id' => $id]);
    } catch (\Exception $e) {
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
            'fund_type' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);
        
        DB::table('sponsors')->where('id', $id)->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'commitment_amount' => $request->commitment_amount,
            'fund_type' => $request->fund_type ?? 'one_time',
            'notes' => $request->notes,
            'updated_at' => now()
        ]);
        
        return response()->json(['success' => true, 'message' => 'Sponsor updated successfully']);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}

public function editSponsor($id)
{
    try {
        $sponsor = DB::table('sponsors')->where('id', $id)->first();
        return response()->json(['success' => true, 'sponsor' => $sponsor]);
    } catch (\Exception $e) {
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
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}

public function recordSponsorPayment(Request $request)
{
    try {
        $request->validate([
            'sponsor_id' => 'required|exists:sponsors,id',
            'amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'payment_method' => 'nullable|string'
        ]);
        
        DB::table('sponsor_payments')->insert([
            'sponsor_id' => $request->sponsor_id,
            'amount' => $request->amount,
            'payment_date' => $request->payment_date,
            'payment_method' => $request->payment_method ?? 'cash',
            'notes' => $request->notes,
            'created_by' => auth()->id(),
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        // Update sponsor status if fully paid
        $received = DB::table('sponsor_payments')
            ->where('sponsor_id', $request->sponsor_id)
            ->sum('amount');
        
        $sponsor = DB::table('sponsors')->where('id', $request->sponsor_id)->first();
        
        if ($received >= $sponsor->commitment_amount) {
            DB::table('sponsors')->where('id', $request->sponsor_id)->update([
                'status' => 'completed',
                'updated_at' => now()
            ]);
        }
        
        return response()->json(['success' => true, 'message' => 'Payment recorded successfully']);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}
public function deleteContribution($userId)
{
    try {
        // Delete payments first
        DB::table('payments')->where('user_id', $userId)->delete();
        // Delete contribution
        DB::table('contributions')->where('user_id', $userId)->delete();
        
        return response()->json(['success' => true, 'message' => 'Contributions deleted successfully']);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}

public function setAnnualContribution(Request $request)
{
    try {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'annual_amount' => 'required|numeric|min:0'
        ]);
        
        $termSettings = DB::table('finance_term_settings')->first();
        $year = $termSettings->current_year ?? date('Y');
        
        // Check if contribution already exists
        $existing = DB::table('contributions')
            ->where('user_id', $request->user_id)
            ->where('year', $year)
            ->first();
        
        if ($existing) {
            DB::table('contributions')
                ->where('user_id', $request->user_id)
                ->where('year', $year)
                ->update([
                    'annual_amount' => $request->annual_amount,
                    'notes' => $request->notes,
                    'updated_at' => now()
                ]);
        } else {
            DB::table('contributions')->insert([
                'user_id' => $request->user_id,
                'annual_amount' => $request->annual_amount,
                'year' => $year,
                'notes' => $request->notes,
                'created_by' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
        
        return response()->json(['success' => true, 'message' => 'Annual contribution set successfully']);
    } catch (\Exception $e) {
        \Log::error('setAnnualContribution error: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}

public function payContribution(Request $request)
{
    try {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'term' => 'required|integer|min:1',
            'amount' => 'required|numeric|min:0'
        ]);
        
        $termSettings = DB::table('finance_term_settings')->first();
        $year = $termSettings->current_year ?? date('Y');
        
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
        
        return response()->json(['success' => true, 'message' => 'Payment recorded successfully']);
    } catch (\Exception $e) {
        \Log::error('payContribution error: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}

public function getContributionDetails($userId)
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
        
        return response()->json([
            'success' => true,
            'user_name' => $user->name,
            'annual_amount' => $annualAmount,
            'total_paid' => $totalPaid,
            'progress' => $progress,
            'payments' => $payments
        ]);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
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
    
    public function getSettings()
    {
        try {
            $settings = DB::table('finance_term_settings')->first();
            
            if (!$settings) {
                $settings = (object) [
                    'current_year' => date('Y'),
                    'number_of_terms' => 3,
                    'term_percentages' => json_encode([40, 30, 30])
                ];
            }
            
            // Decode term percentages
            $settings->term_percentages = json_decode($settings->term_percentages ?? '[]', true);
            
            return response()->json(['success' => true, 'settings' => $settings]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage(),
                'settings' => (object) [
                    'current_year' => date('Y'),
                    'number_of_terms' => 3,
                    'term_percentages' => [40, 30, 30]
                ]
            ]);
        }
    }
    
    // KEEP ONLY ONE OF THESE - Remove the duplicate
    public function updateSettings(Request $request)
{
    try {
        $termPercentages = json_decode($request->term_percentages, true);
        $termNumbers = json_decode($request->term_numbers, true);
        $numberOfTerms = $request->number_of_terms;
        
        $total = array_sum($termPercentages);
        
        if (abs($total - 100) > 0.01) {
            return response()->json([
                'success' => false,
                'message' => 'Total percentage must equal 100%'
            ], 400);
        }
        
        // Create term percentages array indexed by term number
        $termPercentagesAssoc = [];
        foreach ($termNumbers as $index => $termNum) {
            $termPercentagesAssoc[$termNum] = $termPercentages[$index];
        }
        
        DB::table('finance_term_settings')->updateOrInsert(
            ['id' => 1],
            [
                'current_year' => $request->current_year,
                'number_of_terms' => $numberOfTerms,
                'term_percentages' => json_encode($termPercentagesAssoc),
                'term_numbers' => json_encode($termNumbers),
                'updated_at' => now()
            ]
        );
        
        return response()->json(['success' => true, 'message' => 'Settings saved successfully']);
    } catch (\Exception $e) {
        \Log::error('Error saving settings: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}
    
    public function getContributions(Request $request)
    {
        try {
            $contributions = DB::table('contributions')
                ->join('users', 'contributions.user_id', '=', 'users.id')
                ->select('contributions.*', 'users.name as user_name')
                ->orderBy('contributions.created_at', 'desc')
                ->get();
            
            return response()->json(['success' => true, 'contributions' => $contributions]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
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
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    public function getGifts(Request $request)
    {
        try {
            $gifts = DB::table('gifts')
                ->orderBy('created_at', 'desc')
                ->get();
            
            return response()->json(['success' => true, 'gifts' => $gifts]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
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
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
   public function filterActionPlans(Request $request)
{
    try {
        $status = $request->status;
        $priority = $request->priority;
        
        // Check if department column exists
        $columns = DB::select("
            SELECT column_name 
            FROM information_schema.columns 
            WHERE table_name = 'action_plans' AND column_name = 'department'
        ");
        
        $query = DB::table('action_plans');
        
        // Only filter by department if column exists
        if (!empty($columns)) {
            $query->where('department', 'finance');
        }
        
        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }
        if ($priority && $priority !== 'all') {
            // Check if priority column exists
            $priorityColumns = DB::select("
                SELECT column_name 
                FROM information_schema.columns 
                WHERE table_name = 'action_plans' AND column_name = 'priority'
            ");
            if (!empty($priorityColumns)) {
                $query->where('priority', $priority);
            }
        }
        
        $plans = $query->orderBy('created_at', 'desc')->get();
        
        return response()->json(['success' => true, 'plans' => $plans]);
    } catch (\Exception $e) {
        \Log::error('filterActionPlans error: ' . $e->getMessage());
        return response()->json(['success' => true, 'plans' => []]);
    }
}
    public function storeActionPlan(Request $request)
{
    try {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'priority' => 'nullable|string',
            'budget' => 'nullable|numeric'
        ]);
        
        $id = DB::table('action_plans')->insertGetId([
            'title' => $request->title,
            'description' => $request->description,
            'due_date' => $request->due_date,
            'priority' => $request->priority ?? 'medium',
            'status' => 'pending',
            'progress' => 0,
            'budget' => $request->budget ?? 0,
            'department' => 'finance',  // Add this line
            'created_by' => auth()->id(),
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        return response()->json(['success' => true, 'message' => 'Action plan created successfully', 'id' => $id]);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}


public function updateActionPlan(Request $request, $id)
{
    try {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'priority' => 'nullable|string',
            'status' => 'nullable|string',
            'progress' => 'nullable|integer|min:0|max:100',
            'budget' => 'nullable|numeric'
        ]);
        
        DB::table('action_plans')
            ->where('id', $id)
            ->where('department', 'finance')  // Only update finance department plans
            ->update([
                'title' => $request->title,
                'description' => $request->description,
                'due_date' => $request->due_date,
                'priority' => $request->priority ?? 'medium',
                'status' => $request->status ?? 'pending',
                'progress' => $request->progress ?? 0,
                'budget' => $request->budget ?? 0,
                'updated_at' => now()
            ]);
        
        return response()->json(['success' => true, 'message' => 'Action plan updated successfully']);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}
public function deleteActionPlan($id)
{
    try {
        DB::table('action_plans')
            ->where('id', $id)
            ->where('department', 'finance')  // Only delete finance department plans
            ->delete();
        
        return response()->json(['success' => true, 'message' => 'Action plan deleted successfully']);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}


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
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}

public function deleteExpense($id)
{
    try {
        DB::table('expenses')->where('id', $id)->delete();
        return response()->json(['success' => true, 'message' => 'Expense deleted successfully']);
    } catch (\Exception $e) {
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
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}
  public function getOverviewStats()
{
    try {
        // Member Contributions
        $totalExpected = DB::table('contributions')->sum('annual_amount') ?? 0;
        $totalCollected = DB::table('payments')->sum('amount') ?? 0;
        $collectionRate = $totalExpected > 0 ? round(($totalCollected / $totalExpected) * 100, 1) : 0;
        
        // Gifts (if table has data)
        $giftCommitments = DB::table('gifts')->sum('commitment_amount') ?? 0;
        $giftReceived = DB::table('gifts')->sum('received_amount') ?? 0;
        $activeGifts = DB::table('gifts')->where('status', 'active')->count();
        
        // Sponsors - Sum directly from sponsor_payments for received amount
        $sponsorCommitments = DB::table('sponsors')->sum('commitment_amount') ?? 0;
        $sponsorReceived = DB::table('sponsor_payments')->sum('amount') ?? 0;
        $activeFunds = DB::table('sponsors')->where('status', 'active')->count();
        
        // Expenses
        $totalExpenses = DB::table('expenses')->sum('amount') ?? 0;
        $pendingApproval = DB::table('expenses')->where('status', 'pending')->sum('amount') ?? 0;
        $transactionCount = DB::table('expenses')->count();
        
        // Total Income from all sources
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
        
        // Log for debugging
        \Log::info('Overview Stats:', $stats);
        
        return response()->json(['success' => true, 'stats' => $stats]);
    } catch (\Exception $e) {
        \Log::error('getOverviewStats error: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}

    public function generateContributionsReport()
{
    try {
        $termSettings = DB::table('finance_term_settings')->first();
        $numberOfTerms = $termSettings->number_of_terms ?? 3;
        $year = $termSettings->current_year ?? date('Y');
        
        // Get all users who have either contributions OR payments
        $contributions = DB::table('users')
            ->leftJoin('contributions', function($join) use ($year) {
                $join->on('users.id', '=', 'contributions.user_id')
                     ->where('contributions.year', '=', $year);
            })
            ->leftJoin('payments', function($join) use ($year) {
                $join->on('users.id', '=', 'payments.user_id')
                     ->where('payments.year', '=', $year);
            })
            ->select(
                'users.id as user_id',
                'users.name as member_name',
                'users.email',
                DB::raw('COALESCE(contributions.annual_amount, 0) as annual_amount')
            )
            ->where(function($query) use ($year) {
                $query->whereNotNull('contributions.id')
                      ->orWhereNotNull('payments.id');
            })
            ->groupBy('users.id', 'users.name', 'users.email', 'contributions.annual_amount')
            ->orderBy('users.name')
            ->get();
        
        foreach ($contributions as $cont) {
            $totalPaid = DB::table('payments')
                ->where('user_id', $cont->user_id)
                ->where('year', $year)
                ->sum('amount');
            $cont->total_paid = $totalPaid ?? 0;
            $cont->progress = $cont->annual_amount > 0 ? round(($totalPaid / $cont->annual_amount) * 100, 1) : 100;
            
            // Get term breakdown
            for ($i = 1; $i <= $numberOfTerms; $i++) {
                $termPaid = DB::table('payments')
                    ->where('user_id', $cont->user_id)
                    ->where('term', $i)
                    ->where('year', $year)
                    ->sum('amount');
                $cont->{"term{$i}_paid"} = $termPaid ?? 0;
            }
        }
        
        $totalExpected = $contributions->sum('annual_amount');
        $totalCollected = $contributions->sum('total_paid');
        $collectionRate = $totalExpected > 0 ? round(($totalCollected / $totalExpected) * 100, 1) : 100;
        
        $html = $this->renderContributionsReportHTML($contributions, $numberOfTerms, $totalExpected, $totalCollected, $collectionRate, $year);
        $csv = $this->renderContributionsReportCSV($contributions, $numberOfTerms);
        
        return response()->json([
            'success' => true,
            'title' => "Member Contributions Report - {$year}",
            'html' => $html,
            'csv' => $csv
        ]);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}

private function renderContributionsReportHTML($contributions, $numberOfTerms, $totalExpected, $totalCollected, $collectionRate, $year)
{
    $html = '<div class="space-y-6">';
    $html .= '<div class="bg-blue-50 rounded-lg p-4">';
    $html .= '<h2 class="text-xl font-bold text-gray-800 mb-2">Member Contributions Report</h2>';
    $html .= "<p class=\"text-sm text-gray-600\">Year: {$year}</p>";
    $html .= '</div>';
    
    $html .= '<div class="grid grid-cols-3 gap-4 mb-6">';
    $html .= "<div class=\"bg-gray-50 rounded-lg p-3 text-center\"><p class=\"text-xs text-gray-500\">Total Expected</p><p class=\"text-xl font-bold text-blue-600\">RWF " . number_format($totalExpected, 2) . "</p></div>";
    $html .= "<div class=\"bg-gray-50 rounded-lg p-3 text-center\"><p class=\"text-xs text-gray-500\">Total Collected</p><p class=\"text-xl font-bold text-green-600\">RWF " . number_format($totalCollected, 2) . "</p></div>";
    $html .= "<div class=\"bg-gray-50 rounded-lg p-3 text-center\"><p class=\"text-xs text-gray-500\">Collection Rate</p><p class=\"text-xl font-bold text-purple-600\">{$collectionRate}%</p></div>";
    $html .= '</div>';
    
    $html .= '<table class="min-w-full divide-y divide-gray-200 border">';
    $html .= '<thead class="bg-gray-50"><tr>';
    $html .= '<th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Member</th>';
    $html .= '<th class="px-4 py-2 text-right text-xs font-medium text-gray-500">Annual Amount</th>';
    for ($i = 1; $i <= $numberOfTerms; $i++) {
        $html .= "<th class=\"px-4 py-2 text-right text-xs font-medium text-gray-500\">Term {$i}</th>";
    }
    $html .= '<th class="px-4 py-2 text-right text-xs font-medium text-gray-500">Total Paid</th>';
    $html .= '<th class="px-4 py-2 text-center text-xs font-medium text-gray-500">Progress</th>';
    $html .= '</tr></thead><tbody class="bg-white divide-y divide-gray-200">';
    
    foreach ($contributions as $cont) {
        $html .= '<tr>';
        $html .= "<td class=\"px-4 py-2 text-sm\">" . htmlspecialchars($cont->member_name) . "</td>";
        $html .= "<td class=\"px-4 py-2 text-sm text-right\">RWF " . number_format($cont->annual_amount, 2) . "</td>";
        for ($i = 1; $i <= $numberOfTerms; $i++) {
            $termPaid = $cont->{"term{$i}_paid"} ?? 0;
            $html .= "<td class=\"px-4 py-2 text-sm text-right\">RWF " . number_format($termPaid, 2) . "</td>";
        }
        $html .= "<td class=\"px-4 py-2 text-sm text-right font-semibold text-green-600\">RWF " . number_format($cont->total_paid, 2) . "</td>";
        $html .= "<td class=\"px-4 py-2 text-sm text-center\"><div class=\"flex items-center gap-2\"><div class=\"w-16 bg-gray-200 rounded-full h-2\"><div class=\"bg-blue-600 h-2 rounded-full\" style=\"width: {$cont->progress}%\"></div></div><span>{$cont->progress}%</span></div></td>";
        $html .= '</tr>';
    }
    
    $html .= '</tbody></table></div>';
    return $html;
}

private function renderContributionsReportCSV($contributions, $numberOfTerms)
{
    $csv = "Member,Email,Annual Amount";
    for ($i = 1; $i <= $numberOfTerms; $i++) {
        $csv .= ",Term {$i} Paid";
    }
    $csv .= ",Total Paid,Progress (%)\n";
    
    foreach ($contributions as $cont) {
        $csv .= "\"" . str_replace('"', '""', $cont->member_name) . "\",";
        $csv .= "\"" . str_replace('"', '""', $cont->email ?? '') . "\",";
        $csv .= number_format($cont->annual_amount, 2);
        for ($i = 1; $i <= $numberOfTerms; $i++) {
            $termPaid = $cont->{"term{$i}_paid"} ?? 0;
            $csv .= "," . number_format($termPaid, 2);
        }
        $csv .= "," . number_format($cont->total_paid, 2);
        $csv .= "," . $cont->progress . "\n";
    }
    
    return $csv;
}

public function generatePaymentsReport()
{
    try {
        $payments = DB::table('payments')
            ->join('users', 'payments.user_id', '=', 'users.id')
            ->select('payments.*', 'users.name as member_name', 'users.email')
            ->orderBy('payments.payment_date', 'desc')
            ->get();
        
        $totalAmount = $payments->sum('amount');
        
        $html = $this->renderPaymentsReportHTML($payments, $totalAmount);
        $csv = $this->renderPaymentsReportCSV($payments);
        
        return response()->json([
            'success' => true,
            'title' => 'Payment Records Report',
            'html' => $html,
            'csv' => $csv
        ]);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}

private function renderPaymentsReportHTML($payments, $totalAmount)
{
    $html = '<div class="space-y-6">';
    $html .= '<div class="bg-green-50 rounded-lg p-4">';
    $html .= '<h2 class="text-xl font-bold text-gray-800 mb-2">Payment Records Report</h2>';
    $html .= "<p class=\"text-sm text-gray-600\">Total Payments: <strong>RWF " . number_format($totalAmount, 2) . "</strong></p>";
    $html .= '</div>';
    
    $html .= '<table class="min-w-full divide-y divide-gray-200 border">';
    $html .= '<thead class="bg-gray-50"><tr>';
    $html .= '<th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Date</th>';
    $html .= '<th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Member</th>';
    $html .= '<th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Term</th>';
    $html .= '<th class="px-4 py-2 text-right text-xs font-medium text-gray-500">Amount</th>';
    $html .= '<th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Method</th>';
    $html .= '</tr></thead><tbody class="bg-white divide-y divide-gray-200">';
    
    foreach ($payments as $payment) {
        $html .= '<tr>';
        $html .= "<td class=\"px-4 py-2 text-sm\">" . date('d/m/Y', strtotime($payment->payment_date)) . "</td>";
        $html .= "<td class=\"px-4 py-2 text-sm\">" . htmlspecialchars($payment->member_name) . "</td>";
        $html .= "<td class=\"px-4 py-2 text-sm\">Term {$payment->term}</td>";
        $html .= "<td class=\"px-4 py-2 text-sm text-right font-semibold text-green-600\">RWF " . number_format($payment->amount, 2) . "</td>";
        $html .= "<td class=\"px-4 py-2 text-sm capitalize\">" . ($payment->payment_method ?? 'Cash') . "</td>";
        $html .= '</tr>';
    }
    
    $html .= '</tbody></table></div>';
    return $html;
}

private function renderPaymentsReportCSV($payments)
{
    $csv = "Date,Member,Term,Amount,Payment Method\n";
    
    foreach ($payments as $payment) {
        $csv .= date('d/m/Y', strtotime($payment->payment_date)) . ",";
        $csv .= "\"" . str_replace('"', '""', $payment->member_name) . "\",";
        $csv .= "Term {$payment->term},";
        $csv .= number_format($payment->amount, 2) . ",";
        $csv .= ($payment->payment_method ?? 'Cash') . "\n";
    }
    
    return $csv;
}

public function generateGiftsReport()
{
    try {
        $gifts = DB::table('gifts')->orderBy('date', 'desc')->get();
        $totalCommitments = $gifts->sum('commitment_amount');
        $totalReceived = $gifts->sum('received_amount');
        
        $html = $this->renderGiftsReportHTML($gifts, $totalCommitments, $totalReceived);
        $csv = $this->renderGiftsReportCSV($gifts);
        
        return response()->json([
            'success' => true,
            'title' => 'Gift Reports',
            'html' => $html,
            'csv' => $csv
        ]);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}

private function renderGiftsReportHTML($gifts, $totalCommitments, $totalReceived)
{
    $html = '<div class="space-y-6">';
    $html .= '<div class="bg-purple-50 rounded-lg p-4">';
    $html .= '<h2 class="text-xl font-bold text-gray-800 mb-2">Gift Reports</h2>';
    $html .= "<p class=\"text-sm text-gray-600\">Total Commitments: <strong>RWF " . number_format($totalCommitments, 2) . "</strong> | Total Received: <strong>RWF " . number_format($totalReceived, 2) . "</strong></p>";
    $html .= '</div>';
    
    $html .= '<table class="min-w-full divide-y divide-gray-200 border">';
    $html .= '<thead class="bg-gray-50"><tr>';
    $html .= '<th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Donor</th>';
    $html .= '<th class="px-4 py-2 text-right text-xs font-medium text-gray-500">Commitment</th>';
    $html .= '<th class="px-4 py-2 text-right text-xs font-medium text-gray-500">Received</th>';
    $html .= '<th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Date</th>';
    $html .= '<th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Type</th>';
    $html .= '</tr></thead><tbody class="bg-white divide-y divide-gray-200">';
    
    foreach ($gifts as $gift) {
        $html .= '<tr>';
        $html .= "<td class=\"px-4 py-2 text-sm\">" . htmlspecialchars($gift->donor_name) . "</td>";
        $html .= "<td class=\"px-4 py-2 text-sm text-right\">RWF " . number_format($gift->commitment_amount, 2) . "</td>";
        $html .= "<td class=\"px-4 py-2 text-sm text-right\">RWF " . number_format($gift->received_amount, 2) . "</td>";
        $html .= "<td class=\"px-4 py-2 text-sm\">" . ($gift->date ? date('d/m/Y', strtotime($gift->date)) : '-') . "</td>";
        $html .= "<td class=\"px-4 py-2 text-sm capitalize\">" . ($gift->gift_type ?? '-') . "</td>";
        $html .= '</tr>';
    }
    
    $html .= '</tbody></table></div>';
    return $html;
}

private function renderGiftsReportCSV($gifts)
{
    $csv = "Donor,Commitment Amount,Received Amount,Date,Gift Type\n";
    
    foreach ($gifts as $gift) {
        $csv .= "\"" . str_replace('"', '""', $gift->donor_name) . "\",";
        $csv .= number_format($gift->commitment_amount, 2) . ",";
        $csv .= number_format($gift->received_amount, 2) . ",";
        $csv .= ($gift->date ?? '') . ",";
        $csv .= ($gift->gift_type ?? '') . "\n";
    }
    
    return $csv;
}

public function generateSponsorsReport()
{
    try {
        $sponsors = DB::table('sponsors')->orderBy('name')->get();
        
        foreach ($sponsors as $sponsor) {
            $received = DB::table('sponsor_payments')
                ->where('sponsor_id', $sponsor->id)
                ->sum('amount');
            $sponsor->received_amount = $received ?? 0;
            $sponsor->progress = $sponsor->commitment_amount > 0 ? round(($received / $sponsor->commitment_amount) * 100, 1) : 0;
        }
        
        $totalCommitments = $sponsors->sum('commitment_amount');
        $totalReceived = $sponsors->sum('received_amount');
        
        $html = $this->renderSponsorsReportHTML($sponsors, $totalCommitments, $totalReceived);
        $csv = $this->renderSponsorsReportCSV($sponsors);
        
        return response()->json([
            'success' => true,
            'title' => 'Sponsor Report',
            'html' => $html,
            'csv' => $csv
        ]);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}

public function generateSponsorsGiftsReport()
{
    try {
        $sponsors = DB::table('sponsors')->get();
        foreach ($sponsors as $sponsor) {
            $received = DB::table('sponsor_payments')->where('sponsor_id', $sponsor->id)->sum('amount');
            $sponsor->received_amount = $received ?? 0;
        }
        
        $gifts = DB::table('gifts')->get();
        
        $totalSponsorCommitments = $sponsors->sum('commitment_amount');
        $totalSponsorReceived = $sponsors->sum('received_amount');
        $totalGiftCommitments = $gifts->sum('commitment_amount');
        $totalGiftReceived = $gifts->sum('received_amount');
        
        $html = $this->renderSponsorsGiftsReportHTML($sponsors, $gifts, $totalSponsorCommitments, $totalSponsorReceived, $totalGiftCommitments, $totalGiftReceived);
        $csv = $this->renderSponsorsGiftsReportCSV($sponsors, $gifts);
        
        return response()->json([
            'success' => true,
            'title' => 'Sponsor & Gift Report',
            'html' => $html,
            'csv' => $csv
        ]);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}

public function generateExpensesReport()
{
    try {
        $expenses = DB::table('expenses')->orderBy('date', 'desc')->get();
        $totalAmount = $expenses->sum('amount');
        $pendingAmount = $expenses->where('status', 'pending')->sum('amount');
        $approvedAmount = $expenses->where('status', 'approved')->sum('amount');
        
        $html = $this->renderExpensesReportHTML($expenses, $totalAmount, $pendingAmount, $approvedAmount);
        $csv = $this->renderExpensesReportCSV($expenses);
        
        return response()->json([
            'success' => true,
            'title' => 'Expenses Report',
            'html' => $html,
            'csv' => $csv
        ]);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}

public function generateSummaryReport()
{
    try {
        // Get all financial data
        $contributions = DB::table('contributions')->sum('annual_amount');
        $collected = DB::table('payments')->sum('amount');
        $sponsorCommitments = DB::table('sponsors')->sum('commitment_amount');
        $sponsorReceived = DB::table('sponsor_payments')->sum('amount');
        $giftCommitments = DB::table('gifts')->sum('commitment_amount');
        $giftReceived = DB::table('gifts')->sum('received_amount');
        $expenses = DB::table('expenses')->sum('amount');
        
        $totalIncome = $collected + $sponsorReceived + $giftReceived;
        $totalCommitments = $contributions + $sponsorCommitments + $giftCommitments;
        $netBalance = $totalIncome - $expenses;
        
        $html = $this->renderSummaryReportHTML($totalIncome, $totalCommitments, $expenses, $netBalance, $collected, $sponsorReceived, $giftReceived);
        
        return response()->json([
            'success' => true,
            'title' => 'Financial Summary Report',
            'html' => $html,
            'csv' => ''
        ]);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}
private function renderSponsorsReportHTML($sponsors, $totalCommitments, $totalReceived)
{
    $html = '<div class="space-y-6">';
    $html .= '<div class="bg-yellow-50 rounded-lg p-4">';
    $html .= '<h2 class="text-xl font-bold text-gray-800 mb-2">Sponsor Report</h2>';
    $html .= "<p class=\"text-sm text-gray-600\">Total Commitments: <strong>RWF " . number_format($totalCommitments, 2) . "</strong> | Total Received: <strong>RWF " . number_format($totalReceived, 2) . "</strong></p>";
    $html .= '</div>';
    
    $html .= '<table class="min-w-full divide-y divide-gray-200 border">';
    $html .= '<thead class="bg-gray-50"><tr>';
    $html .= '<th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Sponsor</th>';
    $html .= '<th class="px-4 py-2 text-right text-xs font-medium text-gray-500">Commitment</th>';
    $html .= '<th class="px-4 py-2 text-right text-xs font-medium text-gray-500">Received</th>';
    $html .= '<th class="px-4 py-2 text-right text-xs font-medium text-gray-500">Pending</th>';
    $html .= '<th class="px-4 py-2 text-center text-xs font-medium text-gray-500">Progress</th>';
    $html .= '<th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Status</th>';
    $html .= '</tr></thead><tbody class="bg-white divide-y divide-gray-200">';
    
    foreach ($sponsors as $sponsor) {
        $pending = $sponsor->commitment_amount - $sponsor->received_amount;
        $status = $sponsor->received_amount >= $sponsor->commitment_amount ? 'Completed' : ($sponsor->status ?? 'Active');
        $statusClass = $status === 'Completed' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700';
        
        $html .= '<tr>';
        $html .= "<td class=\"px-4 py-2 text-sm\">" . htmlspecialchars($sponsor->name) . "</td>";
        $html .= "<td class=\"px-4 py-2 text-sm text-right\">RWF " . number_format($sponsor->commitment_amount, 2) . "</td>";
        $html .= "<td class=\"px-4 py-2 text-sm text-right text-green-600\">RWF " . number_format($sponsor->received_amount, 2) . "</td>";
        $html .= "<td class=\"px-4 py-2 text-sm text-right text-yellow-600\">RWF " . number_format($pending, 2) . "</td>";
        $html .= "<td class=\"px-4 py-2 text-sm text-center\"><div class=\"flex items-center gap-2\"><div class=\"w-16 bg-gray-200 rounded-full h-2\"><div class=\"bg-blue-600 h-2 rounded-full\" style=\"width: {$sponsor->progress}%\"></div></div><span>{$sponsor->progress}%</span></div></td>";
        $html .= "<td class=\"px-4 py-2 text-sm\"><span class=\"px-2 py-1 rounded-full text-xs {$statusClass}\">{$status}</span></td>";
        $html .= '</tr>';
    }
    
    $html .= '</tbody></table></div>';
    return $html;
}

private function renderSponsorsReportCSV($sponsors)
{
    $csv = "Sponsor,Commitment Amount,Received Amount,Pending Amount,Progress (%),Status\n";
    
    foreach ($sponsors as $sponsor) {
        $pending = $sponsor->commitment_amount - $sponsor->received_amount;
        $status = $sponsor->received_amount >= $sponsor->commitment_amount ? 'Completed' : ($sponsor->status ?? 'Active');
        $csv .= "\"" . str_replace('"', '""', $sponsor->name) . "\",";
        $csv .= number_format($sponsor->commitment_amount, 2) . ",";
        $csv .= number_format($sponsor->received_amount, 2) . ",";
        $csv .= number_format($pending, 2) . ",";
        $csv .= $sponsor->progress . ",";
        $csv .= $status . "\n";
    }
    
    return $csv;
}

private function renderSponsorsGiftsReportHTML($sponsors, $gifts, $totalSponsorCommitments, $totalSponsorReceived, $totalGiftCommitments, $totalGiftReceived)
{
    $html = '<div class="space-y-6">';
    $html .= '<div class="bg-indigo-50 rounded-lg p-4">';
    $html .= '<h2 class="text-xl font-bold text-gray-800 mb-2">Sponsor & Gift Report</h2>';
    $html .= '</div>';
    
    // Sponsors Section
    $html .= '<div>';
    $html .= '<h3 class="text-lg font-semibold text-gray-800 mb-3">Sponsors</h3>';
    $html .= '<div class="grid grid-cols-2 gap-4 mb-4">';
    $html .= "<div class=\"bg-gray-50 rounded-lg p-3 text-center\"><p class=\"text-xs text-gray-500\">Total Commitments</p><p class=\"text-lg font-bold text-blue-600\">RWF " . number_format($totalSponsorCommitments, 2) . "</p></div>";
    $html .= "<div class=\"bg-gray-50 rounded-lg p-3 text-center\"><p class=\"text-xs text-gray-500\">Total Received</p><p class=\"text-lg font-bold text-green-600\">RWF " . number_format($totalSponsorReceived, 2) . "</p></div>";
    $html .= '</div>';
    $html .= '<table class="min-w-full divide-y divide-gray-200 border mb-6">';
    $html .= '<thead class="bg-gray-50"></td><th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Sponsor</th><th class="px-4 py-2 text-right text-xs font-medium text-gray-500">Commitment</th><th class="px-4 py-2 text-right text-xs font-medium text-gray-500">Received</th></tr></thead><tbody>';
    foreach ($sponsors as $sponsor) {
        $html .= "<tr><td class=\"px-4 py-2 text-sm\">" . htmlspecialchars($sponsor->name) . "</td><td class=\"px-4 py-2 text-sm text-right\">RWF " . number_format($sponsor->commitment_amount, 2) . "</td><td class=\"px-4 py-2 text-sm text-right text-green-600\">RWF " . number_format($sponsor->received_amount, 2) . "</td></tr>";
    }
    $html .= '</tbody></table></div>';
    
    // Gifts Section
    $html .= '<div>';
    $html .= '<h3 class="text-lg font-semibold text-gray-800 mb-3">Gifts</h3>';
    $html .= '<div class="grid grid-cols-2 gap-4 mb-4">';
    $html .= "<div class=\"bg-gray-50 rounded-lg p-3 text-center\"><p class=\"text-xs text-gray-500\">Total Commitments</p><p class=\"text-lg font-bold text-blue-600\">RWF " . number_format($totalGiftCommitments, 2) . "</p></div>";
    $html .= "<div class=\"bg-gray-50 rounded-lg p-3 text-center\"><p class=\"text-xs text-gray-500\">Total Received</p><p class=\"text-lg font-bold text-green-600\">RWF " . number_format($totalGiftReceived, 2) . "</p></div>";
    $html .= '</div>';
    $html .= '<table class="min-w-full divide-y divide-gray-200 border">';
    $html .= '<thead class="bg-gray-50"><tr><th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Donor</th><th class="px-4 py-2 text-right text-xs font-medium text-gray-500">Commitment</th><th class="px-4 py-2 text-right text-xs font-medium text-gray-500">Received</th></tr></thead><tbody>';
    foreach ($gifts as $gift) {
        $html .= "<tr><td class=\"px-4 py-2 text-sm\">" . htmlspecialchars($gift->donor_name) . "</td><td class=\"px-4 py-2 text-sm text-right\">RWF " . number_format($gift->commitment_amount, 2) . "</td><td class=\"px-4 py-2 text-sm text-right text-green-600\">RWF " . number_format($gift->received_amount, 2) . "</td></tr>";
    }
    $html .= '</tbody></table></div></div>';
    return $html;
}

private function renderSponsorsGiftsReportCSV($sponsors, $gifts)
{
    $csv = "=== SPONSORS ===\n";
    $csv .= "Sponsor,Commitment Amount,Received Amount\n";
    foreach ($sponsors as $sponsor) {
        $csv .= "\"" . str_replace('"', '""', $sponsor->name) . "\",";
        $csv .= number_format($sponsor->commitment_amount, 2) . ",";
        $csv .= number_format($sponsor->received_amount, 2) . "\n";
    }
    $csv .= "\n=== GIFTS ===\n";
    $csv .= "Donor,Commitment Amount,Received Amount\n";
    foreach ($gifts as $gift) {
        $csv .= "\"" . str_replace('"', '""', $gift->donor_name) . "\",";
        $csv .= number_format($gift->commitment_amount, 2) . ",";
        $csv .= number_format($gift->received_amount, 2) . "\n";
    }
    return $csv;
}

private function renderExpensesReportHTML($expenses, $totalAmount, $pendingAmount, $approvedAmount)
{
    $html = '<div class="space-y-6">';
    $html .= '<div class="bg-red-50 rounded-lg p-4">';
    $html .= '<h2 class="text-xl font-bold text-gray-800 mb-2">Expenses Report</h2>';
    $html .= '</div>';
    
    $html .= '<div class="grid grid-cols-3 gap-4 mb-6">';
    $html .= "<div class=\"bg-gray-50 rounded-lg p-3 text-center\"><p class=\"text-xs text-gray-500\">Total Expenses</p><p class=\"text-xl font-bold text-red-600\">RWF " . number_format($totalAmount, 2) . "</p></div>";
    $html .= "<div class=\"bg-gray-50 rounded-lg p-3 text-center\"><p class=\"text-xs text-gray-500\">Pending Approval</p><p class=\"text-xl font-bold text-yellow-600\">RWF " . number_format($pendingAmount, 2) . "</p></div>";
    $html .= "<div class=\"bg-gray-50 rounded-lg p-3 text-center\"><p class=\"text-xs text-gray-500\">Approved</p><p class=\"text-xl font-bold text-green-600\">RWF " . number_format($approvedAmount, 2) . "</p></div>";
    $html .= '</div>';
    
    $html .= '<table class="min-w-full divide-y divide-gray-200 border">';
    $html .= '<thead class="bg-gray-50"><tr>';
    $html .= '<th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Date</th>';
    $html .= '<th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Description</th>';
    $html .= '<th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Category</th>';
    $html .= '<th class="px-4 py-2 text-right text-xs font-medium text-gray-500">Amount</th>';
    $html .= '<th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Status</th>';
    $html .= '</tr></thead><tbody class="bg-white divide-y divide-gray-200">';
    
    foreach ($expenses as $expense) {
        $statusClass = $expense->status === 'approved' ? 'bg-green-100 text-green-700' : ($expense->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-700');
        $html .= '<tr>';
        $html .= "<td class=\"px-4 py-2 text-sm\">" . date('d/m/Y', strtotime($expense->date)) . "</td>";
        $html .= "<td class=\"px-4 py-2 text-sm\">" . htmlspecialchars($expense->description ?? '-') . "</td>";
        $html .= "<td class=\"px-4 py-2 text-sm capitalize\">" . ($expense->category ?? '-') . "</td>";
        $html .= "<td class=\"px-4 py-2 text-sm text-right font-semibold text-red-600\">RWF " . number_format($expense->amount, 2) . "</td>";
        $html .= "<td class=\"px-4 py-2 text-sm\"><span class=\"px-2 py-1 rounded-full text-xs {$statusClass}\">" . ucfirst($expense->status ?? 'Pending') . "</span></td>";
        $html .= '</tr>';
    }
    
    $html .= '</tbody><table></div>';
    return $html;
}

private function renderExpensesReportCSV($expenses)
{
    $csv = "Date,Description,Category,Amount,Status\n";
    
    foreach ($expenses as $expense) {
        $csv .= date('d/m/Y', strtotime($expense->date)) . ",";
        $csv .= "\"" . str_replace('"', '""', $expense->description ?? '') . "\",";
        $csv .= ($expense->category ?? '') . ",";
        $csv .= number_format($expense->amount, 2) . ",";
        $csv .= ucfirst($expense->status ?? 'Pending') . "\n";
    }
    
    return $csv;
}

private function renderSummaryReportHTML($totalIncome, $totalCommitments, $totalExpenses, $netBalance, $collected, $sponsorReceived, $giftReceived)
{
    $html = '<div class="space-y-6">';
    $html .= '<div class="bg-gray-800 rounded-lg p-4 text-white">';
    $html .= '<h2 class="text-xl font-bold mb-2">Financial Summary Report</h2>';
    $html .= '</div>';
    
    $html .= '<div class="grid grid-cols-1 md:grid-cols-2 gap-6">';
    
    // Income Section
    $html .= '<div class="border rounded-lg p-4">';
    $html .= '<h3 class="text-lg font-semibold text-gray-800 mb-3 border-b pb-2">Income</h3>';
    $html .= "<div class=\"space-y-2\"><div class=\"flex justify-between\"><span class=\"text-gray-600\">Member Contributions Collected:</span><span class=\"font-semibold text-green-600\">RWF " . number_format($collected, 2) . "</span></div>";
    $html .= "<div class=\"flex justify-between\"><span class=\"text-gray-600\">Sponsor Payments Received:</span><span class=\"font-semibold text-green-600\">RWF " . number_format($sponsorReceived, 2) . "</span></div>";
    $html .= "<div class=\"flex justify-between\"><span class=\"text-gray-600\">Gifts Received:</span><span class=\"font-semibold text-green-600\">RWF " . number_format($giftReceived, 2) . "</span></div>";
    $html .= "<div class=\"flex justify-between pt-2 border-t\"><span class=\"font-bold text-gray-800\">Total Income:</span><span class=\"font-bold text-green-600\">RWF " . number_format($totalIncome, 2) . "</span></div></div>";
    $html .= '</div>';
    
    // Commitments Section
    $html .= '<div class="border rounded-lg p-4">';
    $html .= '<h3 class="text-lg font-semibold text-gray-800 mb-3 border-b pb-2">Commitments</h3>';
    $html .= "<div class=\"space-y-2\"><div class=\"flex justify-between\"><span class=\"text-gray-600\">Member Contributions Expected:</span><span class=\"font-semibold text-blue-600\">RWF " . number_format($totalCommitments, 2) . "</span></div>";
    $html .= "<div class=\"flex justify-between pt-2 border-t\"><span class=\"font-bold text-gray-800\">Total Expected Income:</span><span class=\"font-bold text-blue-600\">RWF " . number_format($totalCommitments, 2) . "</span></div></div>";
    $html .= '</div>';
    
    // Expenses Section
    $html .= '<div class="border rounded-lg p-4">';
    $html .= '<h3 class="text-lg font-semibold text-gray-800 mb-3 border-b pb-2">Expenses</h3>';
    $html .= "<div class=\"space-y-2\"><div class=\"flex justify-between\"><span class=\"text-gray-600\">Total Expenses:</span><span class=\"font-semibold text-red-600\">RWF " . number_format($totalExpenses, 2) . "</span></div>";
    $html .= "<div class=\"flex justify-between pt-2 border-t\"><span class=\"font-bold text-gray-800\">Net Balance:</span><span class=\"font-bold " . ($netBalance >= 0 ? 'text-green-600' : 'text-red-600') . "\">RWF " . number_format($netBalance, 2) . "</span></div></div>";
    $html .= '</div>';
    
    // Summary Section
    $html .= '<div class="border rounded-lg p-4 bg-gray-50">';
    $html .= '<h3 class="text-lg font-semibold text-gray-800 mb-3 border-b pb-2">Summary</h3>';
    $html .= "<div class=\"space-y-2\"><div class=\"flex justify-between\"><span class=\"text-gray-600\">Total Income (Received):</span><span class=\"font-semibold text-green-600\">RWF " . number_format($totalIncome, 2) . "</span></div>";
    $html .= "<div class=\"flex justify-between\"><span class=\"text-gray-600\">Total Expenses:</span><span class=\"font-semibold text-red-600\">RWF " . number_format($totalExpenses, 2) . "</span></div>";
    $html .= "<div class=\"flex justify-between pt-2 border-t\"><span class=\"font-bold text-gray-800\">Net Surplus:</span><span class=\"font-bold " . ($netBalance >= 0 ? 'text-green-600' : 'text-red-600') . "\">RWF " . number_format($netBalance, 2) . "</span></div>";
    $html .= "<div class=\"flex justify-between\"><span class=\"text-gray-600\">Collection Rate:</span><span class=\"font-semibold text-purple-600\">" . ($totalCommitments > 0 ? round(($totalIncome / $totalCommitments) * 100, 1) : 0) . "%</span></div></div>";
    $html .= '</div>';
    
    $html .= '</div></div>';
    return $html;
}

}