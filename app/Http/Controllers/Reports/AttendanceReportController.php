<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceReportController extends Controller
{
    public function index()
    {
        return view('reports.attendance.index');
    }
    
    public function getReport(Request $request)
    {
        try {
            $startDate = $request->get('start_date', date('Y-m-01'));
            $endDate = $request->get('end_date', date('Y-m-t'));
            $sessionType = $request->get('session_type');
            $status = $request->get('status');
            
            $query = "
                SELECT 
                    ar.*,
                    u.name as user_name,
                    u.email as user_email
                FROM attendance_records ar
                JOIN users u ON u.id = ar.user_id
                WHERE ar.session_date BETWEEN ? AND ?
            ";
            
            $params = [$startDate, $endDate];
            
            if ($sessionType && $sessionType !== 'all') {
                $query .= " AND ar.session_type = ?";
                $params[] = $sessionType;
            }
            
            if ($status && $status !== 'all') {
                $query .= " AND ar.status = ?";
                $params[] = $status;
            }
            
            $query .= " ORDER BY ar.session_date DESC";
            
            $attendances = DB::select($query, $params);
            
            // Group by session
            $sessions = [];
            $stats = [
                'total' => count($attendances),
                'present' => 0,
                'late' => 0,
                'absent' => 0,
                'excused' => 0,
                'unique_sessions' => 0
            ];
            
            foreach ($attendances as $att) {
                $key = $att->session_date . '|' . $att->session_type;
                if (!isset($sessions[$key])) {
                    $sessions[$key] = [
                        'date' => $att->session_date,
                        'session' => $att->session_type,
                        'present' => 0,
                        'late' => 0,
                        'absent' => 0,
                        'excused' => 0,
                        'total' => 0
                    ];
                }
                
                $sessions[$key]['total']++;
                if ($att->status === 'present') {
                    $sessions[$key]['present']++;
                    $stats['present']++;
                } elseif ($att->status === 'late') {
                    $sessions[$key]['late']++;
                    $stats['late']++;
                } elseif ($att->status === 'absent') {
                    $sessions[$key]['absent']++;
                    $stats['absent']++;
                } elseif ($att->status === 'excused') {
                    $sessions[$key]['excused']++;
                    $stats['excused']++;
                }
            }
            
            $stats['unique_sessions'] = count($sessions);
            $stats['attendance_rate'] = $stats['total'] > 0 
                ? round((($stats['present'] + $stats['late']) / $stats['total']) * 100, 1) 
                : 0;
            
            // Get session types for filter
            $sessionTypes = DB::select("SELECT DISTINCT session_type FROM attendance_records ORDER BY session_type");
            
            // Prepare session list with rates
            $sessionList = [];
            foreach ($sessions as $key => $session) {
                $session['rate'] = $session['total'] > 0 
                    ? round((($session['present'] + $session['late']) / $session['total']) * 100, 1) 
                    : 0;
                $sessionList[] = $session;
            }
            
            // Sort by date descending
            usort($sessionList, function($a, $b) {
                return strtotime($b['date']) - strtotime($a['date']);
            });
            
            return response()->json([
                'success' => true,
                'attendances' => $attendances,
                'sessions' => $sessionList,
                'stats' => $stats,
                'session_types' => $sessionTypes
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    public function exportReport(Request $request)
    {
        try {
            $startDate = $request->get('start_date', date('Y-m-01'));
            $endDate = $request->get('end_date', date('Y-m-t'));
            
            $attendances = DB::select("
                SELECT 
                    ar.*,
                    u.name as user_name,
                    u.email as user_email
                FROM attendance_records ar
                JOIN users u ON u.id = ar.user_id
                WHERE ar.session_date BETWEEN ? AND ?
                ORDER BY ar.session_date DESC
            ", [$startDate, $endDate]);
            
            $csv = "\uFEFF";
            $csv .= "Date,Session,Member,Status\n";
            
            foreach ($attendances as $att) {
                $csv .= "{$att->session_date},{$att->session_type},{$att->user_name},{$att->status}\n";
            }
            
            return response($csv, 200)
                ->header('Content-Type', 'text/csv; charset=UTF-8')
                ->header('Content-Disposition', 'attachment; filename="attendance_report_' . date('Y-m-d') . '.csv"');
                
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    public function getSummary(Request $request)
    {
        // Simple summary
        return response()->json(['success' => true]);
    }
    
    public function getSessions(Request $request)
    {
        try {
            $sessions = DB::select("
                SELECT 
                    session_date,
                    session_type,
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present,
                    SUM(CASE WHEN status = 'late' THEN 1 ELSE 0 END) as late,
                    SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) as absent,
                    SUM(CASE WHEN status = 'excused' THEN 1 ELSE 0 END) as excused
                FROM attendance_records
                GROUP BY session_date, session_type
                ORDER BY session_date DESC
            ");
            
            return response()->json(['success' => true, 'sessions' => $sessions]);
            
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    public function getStatusDistribution(Request $request)
    {
        try {
            $distribution = DB::select("
                SELECT 
                    status,
                    COUNT(*) as count
                FROM attendance_records
                GROUP BY status
            ");
            
            return response()->json(['success' => true, 'distribution' => $distribution]);
            
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    public function getTrend(Request $request)
    {
        try {
            $trend = DB::select("
                SELECT 
                    DATE_TRUNC('month', session_date) as month,
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present,
                    SUM(CASE WHEN status = 'late' THEN 1 ELSE 0 END) as late,
                    SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) as absent
                FROM attendance_records
                GROUP BY DATE_TRUNC('month', session_date)
                ORDER BY month DESC
                LIMIT 12
            ");
            
            return response()->json(['success' => true, 'trend' => $trend]);
            
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}