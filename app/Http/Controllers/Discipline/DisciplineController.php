<?php

namespace App\Http\Controllers\Discipline;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DisciplineController extends Controller
{
    public function index()
    {
        // Get statistics
        $stats = DB::select("
            SELECT 
                COUNT(DISTINCT dr.id) as total_discipline_sessions,
                COALESCE(AVG(CASE WHEN dr.type = 'positive' THEN 100 ELSE 0 END), 0) as avg_good_behavior,
                COUNT(DISTINCT ar.id) as attendance_sessions,
                COUNT(DISTINCT pr.id) as permission_requests
            FROM discipline_records dr
            CROSS JOIN attendance_records ar
            CROSS JOIN permission_requests pr
        ");
        
        $stats = $stats[0] ?? (object)[
            'total_discipline_sessions' => 0,
            'avg_good_behavior' => 0,
            'attendance_sessions' => 0,
            'permission_requests' => 0
        ];
        
        // Get recent discipline sessions
        $recentSessions = DB::select("
            SELECT dr.*, u.name as user_name, u.email as user_email,
                   TO_CHAR(dr.created_at, 'DD/MM/YYYY') as formatted_date
            FROM discipline_records dr
            JOIN users u ON u.id = dr.user_id
            ORDER BY dr.created_at DESC
            LIMIT 5
        ");
        
        // Get recent permission requests
        $recentPermissions = DB::select("
            SELECT pr.*, u.name as user_name, u.email as user_email,
                   TO_CHAR(pr.created_at, 'DD/MM/YYYY') as formatted_date
            FROM permission_requests pr
            JOIN users u ON u.id = pr.user_id
            ORDER BY pr.created_at DESC
            LIMIT 5
        ");
        
        return view('modules.discipline.index', compact('stats', 'recentSessions', 'recentPermissions'));
    }
    
    public function getOverview()
    {
        $stats = DB::select("
            SELECT 
                COUNT(DISTINCT dr.id) as total_sessions,
                COALESCE(AVG(CASE WHEN dr.type = 'positive' THEN 100 ELSE 0 END), 0) as good_behavior_percentage,
                COUNT(DISTINCT ar.id) as total_attendance,
                COUNT(DISTINCT pr.id) as total_permissions
            FROM discipline_records dr
            CROSS JOIN attendance_records ar
            CROSS JOIN permission_requests pr
            WHERE dr.created_at >= date_trunc('month', CURRENT_DATE)
        ");
        
        $recentActivities = DB::select("
            (SELECT 'discipline' as type, dr.title, dr.created_at, u.name as user_name,
                    TO_CHAR(dr.created_at, 'DD/MM/YYYY') as formatted_date
             FROM discipline_records dr
             JOIN users u ON u.id = dr.user_id
             ORDER BY dr.created_at DESC
             LIMIT 5)
            UNION ALL
            (SELECT 'attendance' as type, ar.session_type as title, ar.created_at, u.name as user_name,
                    TO_CHAR(ar.created_at, 'DD/MM/YYYY') as formatted_date
             FROM attendance_records ar
             JOIN users u ON u.id = ar.user_id
             ORDER BY ar.created_at DESC
             LIMIT 5)
            UNION ALL
            (SELECT 'permission' as type, pr.type as title, pr.created_at, u.name as user_name,
                    TO_CHAR(pr.created_at, 'DD/MM/YYYY') as formatted_date
             FROM permission_requests pr
             JOIN users u ON u.id = pr.user_id
             ORDER BY pr.created_at DESC
             LIMIT 5)
            ORDER BY created_at DESC
            LIMIT 10
        ");
        
        return response()->json([
            'success' => true,
            'stats' => $stats[0] ?? [],
            'recent_activities' => $recentActivities
        ]);
    }
}