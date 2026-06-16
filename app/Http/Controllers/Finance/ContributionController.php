<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Finance\Contribution;
use App\Models\Finance\ContributionSetting;
use App\Models\System\ActivityLog;
use Illuminate\Support\Facades\DB;  

class ContributionController extends Controller
{
    // Show my contributions page
  public function myContributions(Request $request)
{
    $userId = auth()->id();
    $year = $request->get('year', date('Y'));
    $currentYear = $year;
    
    // Get available years for dropdown
    $availableYears = DB::table('contributions')
        ->where('user_id', $userId)
        ->select('year')
        ->distinct()
        ->orderBy('year', 'desc')
        ->pluck('year')
        ->toArray();
    
    if (empty($availableYears)) {
        $availableYears = [date('Y')];
    }
    
    // Get contribution
    $contribution = DB::table('contributions')
        ->where('user_id', $userId)
        ->where('year', $year)
        ->first();
    
    // Get payments
    $payments = DB::table('payments')
        ->where('user_id', $userId)
        ->where('year', $year)
        ->orderBy('payment_date', 'desc')
        ->get();
    
    // Get term settings
    $termSettings = DB::table('finance_term_settings')->first();
    $numberOfTerms = $termSettings->number_of_terms ?? 3;
    
    // Get term percentages - ensure it's associative array with term numbers as keys
    $termPercentages = [];
    if ($termSettings && $termSettings->term_percentages) {
        $termPercentages = json_decode($termSettings->term_percentages, true);
        // If it's indexed array (0,1,2), convert to associative (1,2,3)
        if (is_array($termPercentages) && !isset($termPercentages[1]) && isset($termPercentages[0])) {
            $assoc = [];
            foreach ($termPercentages as $index => $percentage) {
                $assoc[$index + 1] = $percentage;
            }
            $termPercentages = $assoc;
        }
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
    
    // Calculate term targets and paid amounts based on annual amount
    $annualAmount = $contribution->annual_amount ?? 0;
    $termTargets = [];
    $termPaidAmounts = [];
    $totalRequired = 0;
    $totalPaid = 0;
    
    for ($i = 1; $i <= $numberOfTerms; $i++) {
        $percentage = isset($termPercentages[$i]) ? $termPercentages[$i] : (100 / $numberOfTerms);
        $termTargets[$i] = ($annualAmount * $percentage) / 100;
        $totalRequired += $termTargets[$i];
        
        // Get paid amount for this term
        $termPaid = DB::table('payments')
            ->where('user_id', $userId)
            ->where('term', $i)
            ->where('year', $year)
            ->sum('amount');
        
        $termPaidAmounts[$i] = $termPaid ?? 0;
        $totalPaid += $termPaidAmounts[$i];
    }
    
    $remainingAmount = $totalRequired - $totalPaid;
    $progressPercent = $totalRequired > 0 ? round(($totalPaid / $totalRequired) * 100, 1) : 0;
    $progressPercentage = $progressPercent;
    
    // Get term statuses based on paid amounts vs targets
    $termStatuses = [];
    foreach ($termTargets as $termNum => $target) {
        $paid = $termPaidAmounts[$termNum];
        if ($paid >= $target && $target > 0) {
            $termStatuses[$termNum] = 'completed';
        } elseif ($paid > 0) {
            $termStatuses[$termNum] = 'partial';
        } else {
            $termStatuses[$termNum] = 'pending';
        }
    }
    
    return view('modules.financial.my-contributions', compact(
        'contribution', 
        'payments', 
        'termTargets', 
        'termPaidAmounts',
        'termStatuses',
        'termPercentages',
        'numberOfTerms', 
        'annualAmount', 
        'currentYear',
        'availableYears',
        'totalRequired',
        'totalPaid',
        'remainingAmount',
        'progressPercent',
        'progressPercentage'
    ));
}
    
    // Submit contribution payment
    public function submitPayment(Request $request)
{
    try {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'term' => 'required|integer|min:1'
        ]);
        
        $userId = auth()->id();
        $year = date('Y');
        
        // Check if contribution exists (no term condition)
        $contribution = DB::table('contributions')
            ->where('user_id', $userId)
            ->where('year', $year)
            ->first();
        
        if (!$contribution) {
            return redirect()->back()->with('error', 'Please set your annual contribution amount first.');
        }
        
        // Record payment
        DB::table('payments')->insert([
            'user_id' => $userId,
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
        
        return redirect()->back()->with('success', 'Payment submitted successfully!');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
    }
}
    
    // Edit annual amount (Super Admin only)
    public function updateAnnualAmount(Request $request)
{
    try {
        $request->validate([
            'annual_amount' => 'required|numeric|min:0'
        ]);
        
        $userId = auth()->id();
        $year = date('Y');
        
        // Check if contribution exists (no term condition)
        $existing = DB::table('contributions')
            ->where('user_id', $userId)
            ->where('year', $year)
            ->first();
        
        if ($existing) {
            DB::table('contributions')
                ->where('user_id', $userId)
                ->where('year', $year)
                ->update([
                    'annual_amount' => $request->annual_amount,
                    'updated_at' => now()
                ]);
        } else {
            DB::table('contributions')->insert([
                'user_id' => $userId,
                'annual_amount' => $request->annual_amount,
                'year' => $year,
                'created_by' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
        
        return redirect()->back()->with('success', 'Annual amount updated successfully!');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
    }
}
    
    // Admin view all contributions
    public function adminIndex(Request $request)
{
    $query = DB::table('contributions')
        ->join('users', 'contributions.user_id', '=', 'users.id')
        ->select('contributions.*', 'users.name as user_name', 'users.email');
    
    // Remove any 'term' conditions
    if ($request->has('year') && $request->year) {
        $query->where('contributions.year', $request->year);
    }
    
    $contributions = $query->orderBy('users.name')->paginate(20);
    $years = DB::table('contributions')->select('year')->distinct()->orderBy('year', 'desc')->get();
    
    return view('modules.financial.admin-contributions', compact('contributions', 'years'));
}
    
    // Approve contribution
    public function approveContribution($id)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Only Super Admin can approve contributions.');
        }
        
        $contribution = Contribution::findOrFail($id);
        $contribution->status = 'approved';
        $contribution->approved_by = auth()->id();
        $contribution->approved_at = now();
        $contribution->save();
        
        return redirect()->back()->with('success', 'Contribution approved successfully!');
    }
}