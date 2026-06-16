<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinancialReportController extends Controller
{
    public function index()
    {
        return view('reports.finance.index');
    }
    
    public function getReport(Request $request)
    {
        try {
            $year = $request->get('year');
            $period = $request->get('period', 'yearly');
            
            $yearCondition = "";
            $yearParams = [];
            
            if ($year) {
                $yearCondition = "AND EXTRACT(YEAR FROM created_at) = ?";
                $yearParams[] = $year;
            }
            
            // Get contributions
            $contributions = DB::select("
                SELECT COALESCE(SUM(amount), 0) as total
                FROM payments
                WHERE status = 'completed' 
            " . ($year ? "AND EXTRACT(YEAR FROM payment_date) = ?" : ""), $yearParams);
            
            // Get expenses
            $expenses = DB::select("
                SELECT COALESCE(SUM(amount), 0) as total
                FROM expenses
                WHERE status = 'approved'
                " . ($year ? "AND EXTRACT(YEAR FROM date) = ?" : ""), $yearParams);
            
            // Get sponsors
            $sponsors = DB::select("
                SELECT COALESCE(SUM(sp.amount), 0) as total
                FROM sponsor_payments sp
                JOIN sponsors s ON s.id = sp.sponsor_id
                WHERE s.status = 'active'
                " . ($year ? "AND EXTRACT(YEAR FROM sp.payment_date) = ?" : ""), $yearParams);
            
            // Get gifts
            $gifts = DB::select("
                SELECT COALESCE(SUM(amount), 0) as total
                FROM gifts
                WHERE status = 'completed'
                " . ($year ? "AND EXTRACT(YEAR FROM date) = ?" : ""), $yearParams);
            
            $totalContributions = $contributions[0]->total ?? 0;
            $totalExpenses = $expenses[0]->total ?? 0;
            $totalSponsors = $sponsors[0]->total ?? 0;
            $totalGifts = $gifts[0]->total ?? 0;
            $totalRevenue = $totalContributions + $totalSponsors + $totalGifts;
            
            // Monthly data
            $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            $monthlyRevenues = array_fill(0, 12, 0);
            $monthlyExpenses = array_fill(0, 12, 0);
            
            // Get monthly contributions
            $monthlyContributions = DB::select("
                SELECT 
                    COALESCE(SUM(amount), 0) as total,
                    EXTRACT(MONTH FROM payment_date) as month
                FROM payments
                WHERE status = 'completed'
                " . ($year ? "AND EXTRACT(YEAR FROM payment_date) = ?" : "") . "
                GROUP BY EXTRACT(MONTH FROM payment_date)
            ", $yearParams);
            
            foreach ($monthlyContributions as $mc) {
                $idx = ($mc->month ?? 1) - 1;
                if ($idx >= 0 && $idx < 12) {
                    $monthlyRevenues[$idx] += $mc->total;
                }
            }
            
            // Get monthly gifts
            $monthlyGifts = DB::select("
                SELECT 
                    COALESCE(SUM(amount), 0) as total,
                    EXTRACT(MONTH FROM date) as month
                FROM gifts
                WHERE status = 'completed'
                " . ($year ? "AND EXTRACT(YEAR FROM date) = ?" : "") . "
                GROUP BY EXTRACT(MONTH FROM date)
            ", $yearParams);
            
            foreach ($monthlyGifts as $mg) {
                $idx = ($mg->month ?? 1) - 1;
                if ($idx >= 0 && $idx < 12) {
                    $monthlyRevenues[$idx] += $mg->total;
                }
            }
            
            // Get monthly expenses
            $monthlyExpensesData = DB::select("
                SELECT 
                    COALESCE(SUM(amount), 0) as total,
                    EXTRACT(MONTH FROM date) as month
                FROM expenses
                WHERE status = 'approved'
                " . ($year ? "AND EXTRACT(YEAR FROM date) = ?" : "") . "
                GROUP BY EXTRACT(MONTH FROM date)
            ", $yearParams);
            
            foreach ($monthlyExpensesData as $me) {
                $idx = ($me->month ?? 1) - 1;
                if ($idx >= 0 && $idx < 12) {
                    $monthlyExpenses[$idx] += $me->total;
                }
            }
            
            // Expense breakdown by category
            $expenseBreakdown = DB::select("
                SELECT 
                    category,
                    COALESCE(SUM(amount), 0) as total
                FROM expenses
                WHERE status = 'approved'
                " . ($year ? "AND EXTRACT(YEAR FROM date) = ?" : "") . "
                GROUP BY category
                ORDER BY total DESC
            ", $yearParams);
            
            $expenseBreakdownArray = [];
            foreach ($expenseBreakdown as $eb) {
                $expenseBreakdownArray[$eb->category] = $eb->total;
            }
            
            // Table data by month
            $tableData = [];
            for ($i = 0; $i < 12; $i++) {
                $tableData[] = [
                    'category' => $months[$i],
                    'contributions' => $monthlyRevenues[$i],
                    'sponsors' => 0,
                    'gifts' => 0,
                    'expenses' => $monthlyExpenses[$i],
                    'net' => $monthlyRevenues[$i] - $monthlyExpenses[$i]
                ];
            }
            
            // Add total row
            $tableData[] = [
                'category' => 'TOTAL',
                'contributions' => $totalContributions,
                'sponsors' => $totalSponsors,
                'gifts' => $totalGifts,
                'expenses' => $totalExpenses,
                'net' => $totalRevenue - $totalExpenses
            ];
            
            return response()->json([
                'success' => true,
                'summary' => [
                    'total_revenue' => $totalRevenue,
                    'total_expenses' => $totalExpenses,
                    'total_contributions' => $totalContributions,
                    'total_sponsors' => $totalSponsors,
                    'total_gifts' => $totalGifts,
                    'collection_rate' => $totalRevenue > 0 ? round(($totalRevenue / ($totalRevenue + $totalExpenses)) * 100, 1) : 0,
                    'net_balance' => $totalRevenue - $totalExpenses
                ],
                'monthly_data' => [
                    'months' => $months,
                    'revenues' => $monthlyRevenues,
                    'expenses' => $monthlyExpenses
                ],
                'income_breakdown' => [
                    'contributions' => $totalContributions,
                    'sponsors' => $totalSponsors,
                    'gifts' => $totalGifts,
                    'other' => 0
                ],
                'expense_breakdown' => $expenseBreakdownArray,
                'table_data' => $tableData
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Financial report error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    public function getSummary(Request $request)
    {
        try {
            $year = $request->get('year', date('Y'));
            
            $revenue = DB::select("
                SELECT 
                    (SELECT COALESCE(SUM(amount), 0) FROM payments WHERE status = 'completed' AND EXTRACT(YEAR FROM payment_date) = ?) +
                    (SELECT COALESCE(SUM(amount), 0) FROM sponsor_payments sp JOIN sponsors s ON s.id = sp.sponsor_id WHERE s.status = 'active' AND EXTRACT(YEAR FROM sp.payment_date) = ?) +
                    (SELECT COALESCE(SUM(amount), 0) FROM gifts WHERE status = 'completed' AND EXTRACT(YEAR FROM date) = ?) as total_revenue
            ", [$year, $year, $year]);
            
            $expenses = DB::select("
                SELECT COALESCE(SUM(amount), 0) as total
                FROM expenses
                WHERE status = 'approved' AND EXTRACT(YEAR FROM date) = ?
            ", [$year]);
            
            $totalRevenue = $revenue[0]->total_revenue ?? 0;
            $totalExpenses = $expenses[0]->total ?? 0;
            
            return response()->json([
                'success' => true,
                'total_revenue' => $totalRevenue,
                'total_expenses' => $totalExpenses,
                'net_balance' => $totalRevenue - $totalExpenses,
                'collection_rate' => $totalRevenue > 0 ? round(($totalRevenue / ($totalRevenue + $totalExpenses)) * 100, 1) : 0
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    public function getIncomeBreakdown(Request $request)
    {
        try {
            $year = $request->get('year', date('Y'));
            
            $contributions = DB::select("
                SELECT COALESCE(SUM(amount), 0) as total
                FROM payments
                WHERE status = 'completed' AND EXTRACT(YEAR FROM payment_date) = ?
            ", [$year]);
            
            $sponsors = DB::select("
                SELECT COALESCE(SUM(sp.amount), 0) as total
                FROM sponsor_payments sp
                JOIN sponsors s ON s.id = sp.sponsor_id
                WHERE s.status = 'active' AND EXTRACT(YEAR FROM sp.payment_date) = ?
            ", [$year]);
            
            $gifts = DB::select("
                SELECT COALESCE(SUM(amount), 0) as total
                FROM gifts
                WHERE status = 'completed' AND EXTRACT(YEAR FROM date) = ?
            ", [$year]);
            
            return response()->json([
                'success' => true,
                'contributions' => $contributions[0]->total ?? 0,
                'sponsors' => $sponsors[0]->total ?? 0,
                'gifts' => $gifts[0]->total ?? 0
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    public function getExpenseBreakdown(Request $request)
    {
        try {
            $year = $request->get('year', date('Y'));
            
            $expenses = DB::select("
                SELECT 
                    category,
                    COALESCE(SUM(amount), 0) as total
                FROM expenses
                WHERE status = 'approved' AND EXTRACT(YEAR FROM date) = ?
                GROUP BY category
                ORDER BY total DESC
            ", [$year]);
            
            return response()->json([
                'success' => true,
                'expenses' => $expenses
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    public function getMonthlyTrend(Request $request)
    {
        try {
            $year = $request->get('year', date('Y'));
            
            $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            $revenues = array_fill(0, 12, 0);
            $expenses = array_fill(0, 12, 0);
            
            // Monthly contributions
            $contributions = DB::select("
                SELECT 
                    COALESCE(SUM(amount), 0) as total,
                    EXTRACT(MONTH FROM payment_date) as month
                FROM payments
                WHERE status = 'completed' AND EXTRACT(YEAR FROM payment_date) = ?
                GROUP BY EXTRACT(MONTH FROM payment_date)
            ", [$year]);
            
            foreach ($contributions as $c) {
                $idx = ($c->month ?? 1) - 1;
                if ($idx >= 0 && $idx < 12) {
                    $revenues[$idx] += $c->total;
                }
            }
            
            // Monthly gifts
            $gifts = DB::select("
                SELECT 
                    COALESCE(SUM(amount), 0) as total,
                    EXTRACT(MONTH FROM date) as month
                FROM gifts
                WHERE status = 'completed' AND EXTRACT(YEAR FROM date) = ?
                GROUP BY EXTRACT(MONTH FROM date)
            ", [$year]);
            
            foreach ($gifts as $g) {
                $idx = ($g->month ?? 1) - 1;
                if ($idx >= 0 && $idx < 12) {
                    $revenues[$idx] += $g->total;
                }
            }
            
            // Monthly expenses
            $expensesData = DB::select("
                SELECT 
                    COALESCE(SUM(amount), 0) as total,
                    EXTRACT(MONTH FROM date) as month
                FROM expenses
                WHERE status = 'approved' AND EXTRACT(YEAR FROM date) = ?
                GROUP BY EXTRACT(MONTH FROM date)
            ", [$year]);
            
            foreach ($expensesData as $e) {
                $idx = ($e->month ?? 1) - 1;
                if ($idx >= 0 && $idx < 12) {
                    $expenses[$idx] += $e->total;
                }
            }
            
            return response()->json([
                'success' => true,
                'months' => $months,
                'revenues' => $revenues,
                'expenses' => $expenses
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    public function getYears(Request $request)
    {
        try {
            $years = DB::select("
                SELECT DISTINCT EXTRACT(YEAR FROM created_at) as year
                FROM (
                    SELECT created_at FROM payments
                    UNION
                    SELECT created_at FROM expenses
                    UNION
                    SELECT created_at FROM gifts
                    UNION
                    SELECT created_at FROM sponsor_payments
                ) as all_dates
                ORDER BY year DESC
            ");
            
            return response()->json(['success' => true, 'years' => $years]);
            
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    public function exportReport(Request $request)
    {
        try {
            $year = $request->get('year');
            
            // Get data
            $yearCondition = $year ? "AND EXTRACT(YEAR FROM created_at) = {$year}" : "";
            
            $contributions = DB::select("
                SELECT COALESCE(SUM(amount), 0) as total
                FROM payments
                WHERE status = 'completed' {$yearCondition}
            ");
            
            $expenses = DB::select("
                SELECT COALESCE(SUM(amount), 0) as total
                FROM expenses
                WHERE status = 'approved' {$yearCondition}
            ");
            
            $csv = "\uFEFF";
            $csv .= "Category,Contributions,Sponsors,Gifts,Expenses,Net\n";
            $csv .= "TOTAL,{$contributions[0]->total},0,0,{$expenses[0]->total}," . ($contributions[0]->total - $expenses[0]->total) . "\n";
            
            return response($csv, 200)
                ->header('Content-Type', 'text/csv; charset=UTF-8')
                ->header('Content-Disposition', 'attachment; filename="financial_report_' . date('Y-m-d') . '.csv"');
                
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    // Additional methods for contributions, expenses, sponsors reports
    public function getContributionsReport() { return response()->json(['success' => true]); }
    public function getContributionsData() { return response()->json(['success' => true]); }
    public function exportContributions() { return response()->json(['success' => true]); }
    public function getContributionsSummary() { return response()->json(['success' => true]); }
    public function getExpensesReport() { return response()->json(['success' => true]); }
    public function getExpensesData() { return response()->json(['success' => true]); }
    public function exportExpenses() { return response()->json(['success' => true]); }
    public function getExpensesSummary() { return response()->json(['success' => true]); }
    public function getExpensesByCategory() { return response()->json(['success' => true]); }
    public function getSponsorsReport() { return response()->json(['success' => true]); }
    public function getSponsorsData() { return response()->json(['success' => true]); }
    public function exportSponsors() { return response()->json(['success' => true]); }
    public function getSponsorsSummary() { return response()->json(['success' => true]); }
}