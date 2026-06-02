<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Finance\Contribution;
use App\Models\Finance\ContributionSetting;
use App\Models\System\ActivityLog;

class ContributionController extends Controller
{
    // Show my contributions page
    public function myContributions()
    {
        $user = auth()->user();
        $currentYear = date('Y');
        
        // Get settings for current year
        $settings = ContributionSetting::where('year', $currentYear)->first();
        if (!$settings) {
            $settings = ContributionSetting::create([
                'year' => $currentYear,
                'term1_amount' => 48000,
                'term2_amount' => 36000,
                'term3_amount' => 36000,
                'term4_amount' => 0,
                'is_active' => true
            ]);
        }
        
        // Get user's contributions
        $contributions = [];
        $totalPaid = 0;
        $totalRequired = 0;
        
        for ($term = 1; $term <= 4; $term++) {
            $termAmount = $settings->getTermAmount($term);
            if ($termAmount > 0) {
                $totalRequired += $termAmount;
                
                $contribution = Contribution::where('user_id', $user->id)
                    ->where('term', $term)
                    ->where('year', $currentYear)
                    ->first();
                
                if (!$contribution) {
                    $contribution = new Contribution();
                    $contribution->user_id = $user->id;
                    $contribution->term = $term;
                    $contribution->year = $currentYear;
                    $contribution->amount = 0;
                    $contribution->status = 'pending';
                    $contribution->save();
                }
                
                $totalPaid += $contribution->amount;
                $contributions[$term] = $contribution;
            }
        }
        
        $progressPercent = $totalRequired > 0 ? ($totalPaid / $totalRequired) * 100 : 0;
        
        return view('modules.financial.my-contributions', compact(
            'user', 'settings', 'contributions', 'totalPaid', 'totalRequired', 'progressPercent', 'currentYear'
        ));
    }
    
    // Submit contribution payment
    public function submitPayment(Request $request)
    {
        $request->validate([
            'term' => 'required|integer|min:1|max:4',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'transaction_id' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);
        
        $user = auth()->user();
        $currentYear = date('Y');
        
        $contribution = Contribution::where('user_id', $user->id)
            ->where('term', $request->term)
            ->where('year', $currentYear)
            ->first();
        
        if (!$contribution) {
            $contribution = new Contribution();
            $contribution->user_id = $user->id;
            $contribution->term = $request->term;
            $contribution->year = $currentYear;
        }
        
        // Get term required amount
        $settings = ContributionSetting::where('year', $currentYear)->first();
        $requiredAmount = $settings ? $settings->getTermAmount($request->term) : 0;
        
        $contribution->amount = $request->amount;
        $contribution->payment_method = $request->payment_method;
        $contribution->transaction_id = $request->transaction_id;
        $contribution->notes = $request->notes;
        $contribution->payment_date = now();
        $contribution->submitted_by = $user->id;
        
        // Determine status
        if ($contribution->amount >= $requiredAmount) {
            $contribution->status = 'completed';
        } elseif ($contribution->amount > 0) {
            $contribution->status = 'partial';
        } else {
            $contribution->status = 'pending';
        }
        
        $contribution->save();
        
        // Log activity
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'contribution_submitted',
            'description' => 'Submitted contribution for Term ' . $request->term . ': ' . number_format($request->amount, 0) . ' RWF',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);
        
        return redirect()->back()->with('success', 'Payment submitted successfully!');
    }
    
    // Edit annual amount (Super Admin only)
    public function updateAnnualAmount(Request $request)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Only Super Admin can update annual amount.');
        }
        
        $request->validate([
            'annual_amount' => 'required|numeric|min:0',
            'year' => 'required|integer'
        ]);
        
        // Calculate term amounts (e.g., Term1 = 40%, Term2 = 30%, Term3 = 30%)
        $annualAmount = $request->annual_amount;
        $term1Amount = round($annualAmount * 0.4);
        $term2Amount = round($annualAmount * 0.3);
        $term3Amount = round($annualAmount * 0.3);
        
        $settings = ContributionSetting::updateOrCreate(
            ['year' => $request->year],
            [
                'term1_amount' => $term1Amount,
                'term2_amount' => $term2Amount,
                'term3_amount' => $term3Amount,
                'term4_amount' => 0,
                'is_active' => true,
                'updated_by' => auth()->id()
            ]
        );
        
        return redirect()->back()->with('success', 'Annual amount updated successfully!');
    }
    
    // Admin view all contributions
    public function adminIndex(Request $request)
    {
        if (!auth()->user()->canAccess('financial', 'view')) {
            abort(403, 'You do not have permission to access this page.');
        }
        
        $query = Contribution::with('user')->orderBy('created_at', 'desc');
        
        if ($request->has('term') && $request->term) {
            $query->where('term', $request->term);
        }
        
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        $contributions = $query->paginate(20);
        $stats = [
            'total_contributions' => Contribution::sum('amount'),
            'total_completed' => Contribution::where('status', 'completed')->count(),
            'total_pending' => Contribution::where('status', 'pending')->count(),
            'total_members' => Contribution::distinct('user_id')->count('user_id')
        ];
        
        return view('super-admin.financial.index', compact('contributions', 'stats'));
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