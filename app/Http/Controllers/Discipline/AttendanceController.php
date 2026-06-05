<?php

namespace App\Http\Controllers\Discipline;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->get('start_date', date('Y-m-01'));
        $endDate = $request->get('end_date', date('Y-m-t'));
        $sessionType = $request->get('session_type');
        $userId = $request->get('user_id');
        
        $query = "
            SELECT ar.*, u.name as user_name, u.email as user_email,
                   mu.name as marked_by_name
            FROM attendance_records ar
            JOIN users u ON u.id = ar.user_id
            LEFT JOIN users mu ON mu.id = ar.marked_by
            WHERE ar.session_date BETWEEN ? AND ?
        ";
        
        $params = [$startDate, $endDate];
        
        if ($sessionType) {
            $query .= " AND ar.session_type = ?";
            $params[] = $sessionType;
        }
        
        if ($userId) {
            $query .= " AND ar.user_id = ?";
            $params[] = $userId;
        }
        
        $query .= " ORDER BY ar.session_date DESC, ar.created_at DESC";
        
        $attendances = DB::select($query, $params);
        
        if ($request->ajax()) {
            // Get session types for filter
            $sessionTypes = DB::select("
                SELECT DISTINCT session_type FROM attendance_records ORDER BY session_type
            ");
            
            return response()->json([
                'success' => true,
                'attendances' => $attendances,
                'session_types' => $sessionTypes
            ]);
        }
        
        $users = DB::select("SELECT id, name, email FROM users ORDER BY name");
        $sessionTypes = DB::select("SELECT DISTINCT session_type FROM attendance_records ORDER BY session_type");
        
        return view('modules.discipline.partials.attendance-tab', compact('attendances', 'users', 'sessionTypes', 'startDate', 'endDate'));
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'session_date' => 'required|date',
            'session_type' => 'required|string|max:100',
            'status' => 'required|in:present,absent,late,excused',
            'check_in_time' => 'nullable',
            'check_out_time' => 'nullable',
            'late_minutes' => 'nullable|integer',
            'notes' => 'nullable|string'
        ]);
        
        DB::beginTransaction();
        try {
            // Check for duplicate
            $existing = DB::selectOne("
                SELECT id FROM attendance_records 
                WHERE user_id = ? AND session_date = ? AND session_type = ?
            ", [$validated['user_id'], $validated['session_date'], $validated['session_type']]);
            
            if ($existing) {
                return response()->json([
                    'success' => false,
                    'message' => 'Attendance record already exists for this user, date, and session type'
                ], 422);
            }
            
            DB::insert("
                INSERT INTO attendance_records (
                    user_id, session_date, session_type, status, 
                    check_in_time, check_out_time, late_minutes, notes, 
                    marked_by, created_at, updated_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
            ", [
                $validated['user_id'],
                $validated['session_date'],
                $validated['session_type'],
                $validated['status'],
                $validated['check_in_time'] ?? null,
                $validated['check_out_time'] ?? null,
                $validated['late_minutes'] ?? 0,
                $validated['notes'] ?? null,
                auth()->id()
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Attendance record created successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create attendance record: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function edit($id)
    {
        $attendance = DB::selectOne("
            SELECT * FROM attendance_records WHERE id = ?
        ", [$id]);
        
        if (!$attendance) {
            return response()->json([
                'success' => false,
                'message' => 'Attendance record not found'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'attendance' => $attendance
        ]);
    }
    
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'session_date' => 'required|date',
            'session_type' => 'required|string|max:100',
            'status' => 'required|in:present,absent,late,excused',
            'check_in_time' => 'nullable',
            'check_out_time' => 'nullable',
            'late_minutes' => 'nullable|integer',
            'notes' => 'nullable|string'
        ]);
        
        DB::beginTransaction();
        try {
            DB::update("
                UPDATE attendance_records 
                SET user_id = ?, session_date = ?, session_type = ?, status = ?,
                    check_in_time = ?, check_out_time = ?, late_minutes = ?, 
                    notes = ?, updated_at = NOW()
                WHERE id = ?
            ", [
                $validated['user_id'],
                $validated['session_date'],
                $validated['session_type'],
                $validated['status'],
                $validated['check_in_time'] ?? null,
                $validated['check_out_time'] ?? null,
                $validated['late_minutes'] ?? 0,
                $validated['notes'] ?? null,
                $id
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Attendance record updated successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update attendance record: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            DB::delete("DELETE FROM attendance_records WHERE id = ?", [$id]);
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Attendance record deleted successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete attendance record: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function deleteSession(Request $request)
    {
        $date = $request->get('date');
        $sessionType = $request->get('type');
        
        DB::beginTransaction();
        try {
            DB::delete("
                DELETE FROM attendance_records 
                WHERE session_date = ? AND session_type = ?
            ", [$date, $sessionType]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Session records deleted successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete session: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function getStats()
    {
        $stats = DB::select("
            SELECT 
                COUNT(*) as total_sessions,
                SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present_count,
                SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) as absent_count,
                SUM(CASE WHEN status = 'late' THEN 1 ELSE 0 END) as late_count,
                SUM(CASE WHEN status = 'excused' THEN 1 ELSE 0 END) as excused_count,
                AVG(late_minutes) as avg_late_minutes,
                COUNT(DISTINCT user_id) as unique_users
            FROM attendance_records
            WHERE session_date >= date_trunc('month', CURRENT_DATE)
        ");
        
        return response()->json([
            'success' => true,
            'stats' => $stats[0] ?? (object)[
                'total_sessions' => 0,
                'present_count' => 0,
                'absent_count' => 0,
                'late_count' => 0,
                'excused_count' => 0,
                'avg_late_minutes' => 0,
                'unique_users' => 0
            ]
        ]);
    }
}