<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{
    public function generateContributionsReport()
    {
        try {
            $termSettings = DB::table('finance_term_settings')->first();
            $numberOfTerms = $termSettings->number_of_terms ?? 3;
            $year = $termSettings->current_year ?? date('Y');
            
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
            Log::error('generateContributionsReport error: ' . $e->getMessage());
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
        $html .= "<div class=\"bg-gray-50 rounded-lg p-3 text-center\"><p class=\"text-xs text-gray-500\">Total Expected</p><p class=\"text-xl font-bold text-blue-600\">RWF " . number_format($totalExpected, 0) . "</p></div>";
        $html .= "<div class=\"bg-gray-50 rounded-lg p-3 text-center\"><p class=\"text-xs text-gray-500\">Total Collected</p><p class=\"text-xl font-bold text-green-600\">RWF " . number_format($totalCollected, 0) . "</p></div>";
        $html .= "<div class=\"bg-gray-50 rounded-lg p-3 text-center\"><p class=\"text-xs text-gray-500\">Collection Rate</p><p class=\"text-xl font-bold text-purple-600\">{$collectionRate}%</p></div>";
        $html .= '</div>';
        
        $html .= '<table class="min-w-full divide-y divide-gray-200 border">';
        $html .= '<thead class="bg-gray-50">';
        $html .= '<th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Member</th>';
        $html .= '<th class="px-4 py-2 text-right text-xs font-medium text-gray-500">Annual Amount</th>';
        for ($i = 1; $i <= $numberOfTerms; $i++) {
            $html .= "<th class=\"px-4 py-2 text-right text-xs font-medium text-gray-500\">Term {$i}</th>";
        }
        $html .= '<th class="px-4 py-2 text-right text-xs font-medium text-gray-500">Total Paid</th>';
        $html .= '<th class="px-4 py-2 text-center text-xs font-medium text-gray-500">Progress</th>';
        $html .= '</thead><tbody class="bg-white divide-y divide-gray-200">';
        
        foreach ($contributions as $cont) {
            $html .= '<tr>';
            $html .= "<td class=\"px-4 py-2 text-sm\">" . htmlspecialchars($cont->member_name) . "</td>";
            $html .= "<td class=\"px-4 py-2 text-sm text-right\">RWF " . number_format($cont->annual_amount, 0) . "</td>";
            for ($i = 1; $i <= $numberOfTerms; $i++) {
                $termPaid = $cont->{"term{$i}_paid"} ?? 0;
                $html .= "<td class=\"px-4 py-2 text-sm text-right\">RWF " . number_format($termPaid, 0) . "</td>";
            }
            $html .= "<td class=\"px-4 py-2 text-sm text-right font-semibold text-green-600\">RWF " . number_format($cont->total_paid, 0) . "</td>";
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
            $csv .= number_format($cont->annual_amount, 0);
            for ($i = 1; $i <= $numberOfTerms; $i++) {
                $termPaid = $cont->{"term{$i}_paid"} ?? 0;
                $csv .= "," . number_format($termPaid, 0);
            }
            $csv .= "," . number_format($cont->total_paid, 0);
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
            Log::error('generatePaymentsReport error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    private function renderPaymentsReportHTML($payments, $totalAmount)
    {
        $html = '<div class="space-y-6">';
        $html .= '<div class="bg-green-50 rounded-lg p-4">';
        $html .= '<h2 class="text-xl font-bold text-gray-800 mb-2">Payment Records Report</h2>';
        $html .= "<p class=\"text-sm text-gray-600\">Total Payments: <strong>RWF " . number_format($totalAmount, 0) . "</strong></p>";
        $html .= '</div>';
        
        $html .= '<table class="min-w-full divide-y divide-gray-200 border">';
        $html .= '<thead class="bg-gray-50">';
        $html .= '<th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Date</th>';
        $html .= '<th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Member</th>';
        $html .= '<th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Term</th>';
        $html .= '<th class="px-4 py-2 text-right text-xs font-medium text-gray-500">Amount</th>';
        $html .= '<th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Method</th>';
        $html .= '</thead><tbody class="bg-white divide-y divide-gray-200">';
        
        foreach ($payments as $payment) {
            $html .= '<tr>';
            $html .= "<td class=\"px-4 py-2 text-sm\">" . date('d/m/Y', strtotime($payment->payment_date)) . "</td>";
            $html .= "<td class=\"px-4 py-2 text-sm\">" . htmlspecialchars($payment->member_name) . "</td>";
            $html .= "<td class=\"px-4 py-2 text-sm\">Term {$payment->term}</td>";
            $html .= "<td class=\"px-4 py-2 text-sm text-right font-semibold text-green-600\">RWF " . number_format($payment->amount, 0) . "</td>";
            $html .= "<td class=\"px-4 py-2 text-sm capitalize\">" . ($payment->payment_method ?? 'Cash') . "</td>";
            $html .= '</tr>';
        }
        
        $html .= '</tbody></tr></div>';
        return $html;
    }

    private function renderPaymentsReportCSV($payments)
    {
        $csv = "Date,Member,Term,Amount,Payment Method\n";
        
        foreach ($payments as $payment) {
            $csv .= date('d/m/Y', strtotime($payment->payment_date)) . ",";
            $csv .= "\"" . str_replace('"', '""', $payment->member_name) . "\",";
            $csv .= "Term {$payment->term},";
            $csv .= number_format($payment->amount, 0) . ",";
            $csv .= ($payment->payment_method ?? 'Cash') . "\n";
        }
        
        return $csv;
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
            Log::error('generateExpensesReport error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    private function renderExpensesReportHTML($expenses, $totalAmount, $pendingAmount, $approvedAmount)
    {
        $html = '<div class="space-y-6">';
        $html .= '<div class="bg-red-50 rounded-lg p-4">';
        $html .= '<h2 class="text-xl font-bold text-gray-800 mb-2">Expenses Report</h2>';
        $html .= '</div>';
        
        $html .= '<div class="grid grid-cols-3 gap-4 mb-6">';
        $html .= "<div class=\"bg-gray-50 rounded-lg p-3 text-center\"><p class=\"text-xs text-gray-500\">Total Expenses</p><p class=\"text-xl font-bold text-red-600\">RWF " . number_format($totalAmount, 0) . "</p></div>";
        $html .= "<div class=\"bg-gray-50 rounded-lg p-3 text-center\"><p class=\"text-xs text-gray-500\">Pending Approval</p><p class=\"text-xl font-bold text-yellow-600\">RWF " . number_format($pendingAmount, 0) . "</p></div>";
        $html .= "<div class=\"bg-gray-50 rounded-lg p-3 text-center\"><p class=\"text-xs text-gray-500\">Approved</p><p class=\"text-xl font-bold text-green-600\">RWF " . number_format($approvedAmount, 0) . "</p></div>";
        $html .= '</div>';
        
        $html .= '<table class="min-w-full divide-y divide-gray-200 border">';
        $html .= '<thead class="bg-gray-50">';
        $html .= '<th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Date</th>';
        $html .= '<th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Description</th>';
        $html .= '<th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Category</th>';
        $html .= '<th class="px-4 py-2 text-right text-xs font-medium text-gray-500">Amount</th>';
        $html .= '<th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Status</th>';
        $html .= '</thead><tbody class="bg-white divide-y divide-gray-200">';
        
        foreach ($expenses as $expense) {
            $statusClass = $expense->status === 'approved' ? 'bg-green-100 text-green-700' : ($expense->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-700');
            $html .= '<tr>';
            $html .= "<td class=\"px-4 py-2 text-sm\">" . date('d/m/Y', strtotime($expense->date)) . "</td>";
            $html .= "<td class=\"px-4 py-2 text-sm\">" . htmlspecialchars($expense->description ?? '-') . "</td>";
            $html .= "<td class=\"px-4 py-2 text-sm capitalize\">" . ($expense->category ?? '-') . "</td>";
            $html .= "<td class=\"px-4 py-2 text-sm text-right font-semibold text-red-600\">RWF " . number_format($expense->amount, 0) . "</td>";
            $html .= "<td class=\"px-4 py-2 text-sm\"><span class=\"px-2 py-1 rounded-full text-xs {$statusClass}\">" . ucfirst($expense->status ?? 'Pending') . "</span></td>";
            $html .= '</tr>';
        }
        
        $html .= '</tbody></table></div>';
        return $html;
    }

    private function renderExpensesReportCSV($expenses)
    {
        $csv = "Date,Description,Category,Amount,Status\n";
        
        foreach ($expenses as $expense) {
            $csv .= date('d/m/Y', strtotime($expense->date)) . ",";
            $csv .= "\"" . str_replace('"', '""', $expense->description ?? '') . "\",";
            $csv .= ($expense->category ?? '') . ",";
            $csv .= number_format($expense->amount, 0) . ",";
            $csv .= ucfirst($expense->status ?? 'Pending') . "\n";
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
            Log::error('generateSponsorsReport error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    private function renderSponsorsReportHTML($sponsors, $totalCommitments, $totalReceived)
    {
        $html = '<div class="space-y-6">';
        $html .= '<div class="bg-yellow-50 rounded-lg p-4">';
        $html .= '<h2 class="text-xl font-bold text-gray-800 mb-2">Sponsor Report</h2>';
        $html .= "<p class=\"text-sm text-gray-600\">Total Commitments: <strong>RWF " . number_format($totalCommitments, 0) . "</strong> | Total Received: <strong>RWF " . number_format($totalReceived, 0) . "</strong></p>";
        $html .= '</div>';
        
        $html .= '<table class="min-w-full divide-y divide-gray-200 border">';
        $html .= '<thead class="bg-gray-50">';
        $html .= '<th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Sponsor</th>';
        $html .= '<th class="px-4 py-2 text-right text-xs font-medium text-gray-500">Commitment</th>';
        $html .= '<th class="px-4 py-2 text-right text-xs font-medium text-gray-500">Received</th>';
        $html .= '<th class="px-4 py-2 text-right text-xs font-medium text-gray-500">Pending</th>';
        $html .= '<th class="px-4 py-2 text-center text-xs font-medium text-gray-500">Progress</th>';
        $html .= '<th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Status</th>';
        $html .= '</thead><tbody class="bg-white divide-y divide-gray-200">';
        
        foreach ($sponsors as $sponsor) {
            $pending = $sponsor->commitment_amount - $sponsor->received_amount;
            $status = $sponsor->received_amount >= $sponsor->commitment_amount ? 'Completed' : ($sponsor->status ?? 'Active');
            $statusClass = $status === 'Completed' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700';
            
            $html .= '<tr>';
            $html .= "<td class=\"px-4 py-2 text-sm\">" . htmlspecialchars($sponsor->name) . "</td>";
            $html .= "<td class=\"px-4 py-2 text-sm text-right\">RWF " . number_format($sponsor->commitment_amount, 0) . "</td>";
            $html .= "<td class=\"px-4 py-2 text-sm text-right text-green-600\">RWF " . number_format($sponsor->received_amount, 0) . "</td>";
            $html .= "<td class=\"px-4 py-2 text-sm text-right text-yellow-600\">RWF " . number_format($pending, 0) . "</td>";
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
            $csv .= number_format($sponsor->commitment_amount, 0) . ",";
            $csv .= number_format($sponsor->received_amount, 0) . ",";
            $csv .= number_format($pending, 0) . ",";
            $csv .= $sponsor->progress . ",";
            $csv .= $status . "\n";
        }
        
        return $csv;
    }

    public function generateSummaryReport()
    {
        try {
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
            Log::error('generateSummaryReport error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    private function renderSummaryReportHTML($totalIncome, $totalCommitments, $totalExpenses, $netBalance, $collected, $sponsorReceived, $giftReceived)
    {
        $html = '<div class="space-y-6">';
        $html .= '<div class="bg-gray-800 rounded-lg p-4 text-white">';
        $html .= '<h2 class="text-xl font-bold mb-2">Financial Summary Report</h2>';
        $html .= '</div>';
        
        $html .= '<div class="grid grid-cols-1 md:grid-cols-2 gap-6">';
        
        $html .= '<div class="border rounded-lg p-4">';
        $html .= '<h3 class="text-lg font-semibold text-gray-800 mb-3 border-b pb-2">Income</h3>';
        $html .= "<div class=\"space-y-2\"><div class=\"flex justify-between\"><span class=\"text-gray-600\">Member Contributions Collected:</span><span class=\"font-semibold text-green-600\">RWF " . number_format($collected, 0) . "</span></div>";
        $html .= "<div class=\"flex justify-between\"><span class=\"text-gray-600\">Sponsor Payments Received:</span><span class=\"font-semibold text-green-600\">RWF " . number_format($sponsorReceived, 0) . "</span></div>";
        $html .= "<div class=\"flex justify-between\"><span class=\"text-gray-600\">Gifts Received:</span><span class=\"font-semibold text-green-600\">RWF " . number_format($giftReceived, 0) . "</span></div>";
        $html .= "<div class=\"flex justify-between pt-2 border-t\"><span class=\"font-bold text-gray-800\">Total Income:</span><span class=\"font-bold text-green-600\">RWF " . number_format($totalIncome, 0) . "</span></div></div>";
        $html .= '</div>';
        
        $html .= '<div class="border rounded-lg p-4">';
        $html .= '<h3 class="text-lg font-semibold text-gray-800 mb-3 border-b pb-2">Commitments</h3>';
        $html .= "<div class=\"space-y-2\"><div class=\"flex justify-between\"><span class=\"text-gray-600\">Member Contributions Expected:</span><span class=\"font-semibold text-blue-600\">RWF " . number_format($totalCommitments, 0) . "</span></div>";
        $html .= "<div class=\"flex justify-between pt-2 border-t\"><span class=\"font-bold text-gray-800\">Total Expected Income:</span><span class=\"font-bold text-blue-600\">RWF " . number_format($totalCommitments, 0) . "</span></div></div>";
        $html .= '</div>';
        
        $html .= '<div class="border rounded-lg p-4">';
        $html .= '<h3 class="text-lg font-semibold text-gray-800 mb-3 border-b pb-2">Expenses</h3>';
        $html .= "<div class=\"space-y-2\"><div class=\"flex justify-between\"><span class=\"text-gray-600\">Total Expenses:</span><span class=\"font-semibold text-red-600\">RWF " . number_format($totalExpenses, 0) . "</span></div>";
        $html .= "<div class=\"flex justify-between pt-2 border-t\"><span class=\"font-bold text-gray-800\">Net Balance:</span><span class=\"font-bold " . ($netBalance >= 0 ? 'text-green-600' : 'text-red-600') . "\">RWF " . number_format($netBalance, 0) . "</span></div></div>";
        $html .= '</div>';
        
        $html .= '<div class="border rounded-lg p-4 bg-gray-50">';
        $html .= '<h3 class="text-lg font-semibold text-gray-800 mb-3 border-b pb-2">Summary</h3>';
        $html .= "<div class=\"space-y-2\"><div class=\"flex justify-between\"><span class=\"text-gray-600\">Total Income (Received):</span><span class=\"font-semibold text-green-600\">RWF " . number_format($totalIncome, 0) . "</span></div>";
        $html .= "<div class=\"flex justify-between\"><span class=\"text-gray-600\">Total Expenses:</span><span class=\"font-semibold text-red-600\">RWF " . number_format($totalExpenses, 0) . "</span></div>";
        $html .= "<div class=\"flex justify-between pt-2 border-t\"><span class=\"font-bold text-gray-800\">Net Surplus:</span><span class=\"font-bold " . ($netBalance >= 0 ? 'text-green-600' : 'text-red-600') . "\">RWF " . number_format($netBalance, 0) . "</span></div>";
        $html .= "<div class=\"flex justify-between\"><span class=\"text-gray-600\">Collection Rate:</span><span class=\"font-semibold text-purple-600\">" . ($totalCommitments > 0 ? round(($totalIncome / $totalCommitments) * 100, 1) : 0) . "%</span></div></div>";
        $html .= '</div>';
        
        $html .= '</div></div>';
        return $html;
    }
}