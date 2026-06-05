<?php

namespace App\Http\Controllers\Discipline;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        return view('modules.discipline.partials.reports-tab');
    }
    
    public function generate(Request $request)
    {
        // Discipline Summary
        $disciplineSummary = DB::select("
            SELECT 
                COUNT(*) as total_records,
                SUM(CASE WHEN type = 'positive' THEN 1 ELSE 0 END) as positive_count,
                SUM(CASE WHEN type = 'warning' THEN 1 ELSE 0 END) as warning_count,
                SUM(CASE WHEN type = 'penalty' THEN 1 ELSE 0 END) as penalty_count,
                SUM(CASE WHEN type = 'suspension' THEN 1 ELSE 0 END) as suspension_count,
                SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_count,
                SUM(CASE WHEN status = 'resolved' THEN 1 ELSE 0 END) as resolved_count
            FROM discipline_records
            WHERE created_at >= date_trunc('month', CURRENT_DATE)
        ");
        
        // Attendance Summary
        $attendanceSummary = DB::select("
            SELECT 
                COUNT(*) as total_sessions,
                SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present_count,
                SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) as absent_count,
                SUM(CASE WHEN status = 'late' THEN 1 ELSE 0 END) as late_count,
                SUM(CASE WHEN status = 'excused' THEN 1 ELSE 0 END) as excused_count,
                COALESCE(AVG(late_minutes), 0) as avg_late_minutes
            FROM attendance_records
            WHERE session_date >= date_trunc('month', CURRENT_DATE)
        ");
        
        // Top Performers
        $topPerformers = DB::select("
            SELECT 
                u.id, u.name,
                COUNT(CASE WHEN dr.type = 'positive' THEN 1 END) as positive_points,
                COUNT(CASE WHEN dr.type = 'warning' THEN 1 END) as warnings
            FROM users u
            LEFT JOIN discipline_records dr ON dr.user_id = u.id
            WHERE dr.created_at >= date_trunc('month', CURRENT_DATE) OR dr.created_at IS NULL
            GROUP BY u.id, u.name
            ORDER BY positive_points DESC, warnings ASC
            LIMIT 5
        ");
        
        // Recent Trends
        $recentTrends = DB::select("
            SELECT 
                TO_CHAR(date_trunc('month', created_at), 'Mon YYYY') as month,
                'positive' as type,
                COUNT(*) as count
            FROM discipline_records
            WHERE type = 'positive'
            GROUP BY date_trunc('month', created_at)
            ORDER BY date_trunc('month', created_at) DESC
            LIMIT 6
            UNION ALL
            SELECT 
                TO_CHAR(date_trunc('month', created_at), 'Mon YYYY') as month,
                'penalty' as type,
                COUNT(*) as count
            FROM discipline_records
            WHERE type IN ('warning', 'penalty', 'suspension')
            GROUP BY date_trunc('month', created_at)
            ORDER BY date_trunc('month', created_at) DESC
            LIMIT 6
        ");
        
        return response()->json([
            'success' => true,
            'discipline_summary' => $disciplineSummary[0] ?? [],
            'attendance_summary' => $attendanceSummary[0] ?? [],
            'top_performers' => $topPerformers,
            'recent_trends' => $recentTrends
        ]);
    }
    
    public function export(Request $request)
    {
        $type = $request->get('type', 'attendance');
        $startDate = $request->get('start_date', date('Y-m-01'));
        $endDate = $request->get('end_date', date('Y-m-t'));
        
        if ($type === 'attendance') {
            $data = DB::select("
                SELECT 
                    u.name as user_name,
                    ar.session_date,
                    ar.session_type,
                    ar.status,
                    ar.check_in_time,
                    ar.late_minutes,
                    ar.notes
                FROM attendance_records ar
                JOIN users u ON u.id = ar.user_id
                WHERE ar.session_date BETWEEN ? AND ?
                ORDER BY ar.session_date DESC, u.name
            ", [$startDate, $endDate]);
            
            $filename = "attendance_report_{$startDate}_to_{$endDate}.csv";
            
            return $this->exportToCsv($data, $filename);
        }
        
        if ($type === 'discipline') {
            $data = DB::select("
                SELECT 
                    u.name as user_name,
                    dr.title,
                    dr.type,
                    dr.points,
                    dr.status,
                    dr.created_at,
                    dr.resolved_at,
                    dr.resolved_notes
                FROM discipline_records dr
                JOIN users u ON u.id = dr.user_id
                WHERE dr.created_at BETWEEN ? AND ?
                ORDER BY dr.created_at DESC
            ", [$startDate, $endDate]);
            
            $filename = "discipline_report_{$startDate}_to_{$endDate}.csv";
            
            return $this->exportToCsv($data, $filename);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Invalid export type'
        ], 400);
    }
    
    private function exportToCsv($data, $filename)
    {
        if (empty($data)) {
            return response()->json([
                'success' => false,
                'message' => 'No data to export'
            ], 404);
        }
        
        $headers = array_keys((array)$data[0]);
        
        $callback = function() use ($data, $headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            
            foreach ($data as $row) {
                fputcsv($file, (array)$row);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}"
        ]);
    }

    public function generate(Request $request)
{
    $type = $request->get('type', 'attendance');
    
    // Discipline Summary
    $disciplineSummary = DB::select("
        SELECT 
            COUNT(*) as total_records,
            SUM(CASE WHEN type = 'positive' THEN 1 ELSE 0 END) as positive_count,
            SUM(CASE WHEN type = 'warning' THEN 1 ELSE 0 END) as warning_count,
            SUM(CASE WHEN type = 'penalty' THEN 1 ELSE 0 END) as penalty_count,
            SUM(CASE WHEN type = 'suspension' THEN 1 ELSE 0 END) as suspension_count,
            SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_count,
            SUM(CASE WHEN status = 'resolved' THEN 1 ELSE 0 END) as resolved_count
        FROM discipline_records
        WHERE created_at >= date_trunc('month', CURRENT_DATE)
    ");
    
    // Attendance Summary
    $attendanceSummary = DB::select("
        SELECT 
            COUNT(*) as total_sessions,
            SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present_count,
            SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) as absent_count,
            SUM(CASE WHEN status = 'late' THEN 1 ELSE 0 END) as late_count,
            SUM(CASE WHEN status = 'excused' THEN 1 ELSE 0 END) as excused_count,
            COALESCE(AVG(late_minutes), 0) as avg_late_minutes
        FROM attendance_records
        WHERE session_date >= date_trunc('month', CURRENT_DATE)
    ");
    
    // Permission Summary
    $permissionSummary = DB::select("
        SELECT 
            COUNT(*) as total_requests,
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_count,
            SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved_count,
            SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected_count,
            ROUND(COALESCE(SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END)::numeric / NULLIF(COUNT(*), 0) * 100, 0), 1) as approval_rate
        FROM permission_requests
        WHERE created_at >= date_trunc('month', CURRENT_DATE)
    ");
    
    // Top Performers
    $topPerformers = DB::select("
        SELECT 
            u.id, u.name as user_name,
            COUNT(CASE WHEN dr.type = 'positive' THEN 1 END) as positive_points,
            COUNT(CASE WHEN dr.type IN ('warning', 'penalty', 'suspension') THEN 1 END) as negative_points
        FROM users u
        LEFT JOIN discipline_records dr ON dr.user_id = u.id
        WHERE dr.created_at >= date_trunc('month', CURRENT_DATE) OR dr.created_at IS NULL
        GROUP BY u.id, u.name
        ORDER BY positive_points DESC, negative_points ASC
        LIMIT 5
    ");
    
    return response()->json([
        'success' => true,
        'discipline_summary' => $disciplineSummary[0] ?? [],
        'attendance_summary' => $attendanceSummary[0] ?? [],
        'permission_summary' => $permissionSummary[0] ?? [],
        'top_performers' => $topPerformers
    ]);
}
}