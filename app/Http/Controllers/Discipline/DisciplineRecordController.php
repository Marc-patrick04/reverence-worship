<?php

namespace App\Http\Controllers\Discipline;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DisciplineRecordController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->get('type', 'all');
        $userId = $request->get('user_id');
        $status = $request->get('status', 'all');
        
        $query = "
            SELECT dr.*, u.name as user_name, u.email as user_email,
                   ru.name as recorded_by_name, resu.name as resolved_by_name,
                   ds.name as section_name,
                   TO_CHAR(dr.created_at, 'DD/MM/YYYY') as formatted_date
            FROM discipline_records dr
            JOIN users u ON u.id = dr.user_id
            JOIN users ru ON ru.id = dr.recorded_by
            LEFT JOIN users resu ON resu.id = dr.resolved_by
            LEFT JOIN discipline_sections ds ON ds.id = dr.section_id
            WHERE 1=1
        ";
        
        $params = [];
        
        if ($type !== 'all') {
            $query .= " AND dr.type = ?";
            $params[] = $type;
        }
        
        if ($userId) {
            $query .= " AND dr.user_id = ?";
            $params[] = $userId;
        }
        
        if ($status !== 'all') {
            $query .= " AND dr.status = ?";
            $params[] = $status;
        }
        
        $query .= " ORDER BY dr.created_at DESC";
        
        $records = DB::select($query, $params);
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'records' => $records
            ]);
        }
        
        $users = DB::select("SELECT id, name, email FROM users ORDER BY name");
        $sections = DB::select("SELECT id, name FROM discipline_sections ORDER BY sort_order, name");
        
        return view('modules.discipline.partials.discipline-records-tab', compact('records', 'users', 'sections', 'type'));
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'section_id' => 'nullable|exists:discipline_sections,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'points' => 'nullable|integer',
            'type' => 'required|in:positive,warning,penalty,suspension',
            'status' => 'required|in:active,resolved,appealed'
        ]);
        
        DB::beginTransaction();
        try {
            DB::insert("
                INSERT INTO discipline_records (
                    user_id, section_id, title, description, points, 
                    type, status, recorded_by, created_at, updated_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
            ", [
                $validated['user_id'],
                $validated['section_id'] ?? null,
                $validated['title'],
                $validated['description'] ?? null,
                $validated['points'] ?? 0,
                $validated['type'],
                $validated['status'],
                auth()->id()
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Discipline record created successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create discipline record: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function edit($id)
    {
        $record = DB::selectOne("
            SELECT * FROM discipline_records WHERE id = ?
        ", [$id]);
        
        if (!$record) {
            return response()->json([
                'success' => false,
                'message' => 'Discipline record not found'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'record' => $record
        ]);
    }
    
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'section_id' => 'nullable|exists:discipline_sections,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'points' => 'nullable|integer',
            'type' => 'required|in:positive,warning,penalty,suspension',
            'status' => 'required|in:active,resolved,appealed',
            'resolved_notes' => 'nullable|string'
        ]);
        
        DB::beginTransaction();
        try {
            $params = [
                $validated['user_id'],
                $validated['section_id'] ?? null,
                $validated['title'],
                $validated['description'] ?? null,
                $validated['points'] ?? 0,
                $validated['type'],
                $validated['status'],
                $id
            ];
            
            $query = "
                UPDATE discipline_records 
                SET user_id = ?, section_id = ?, title = ?, description = ?, 
                    points = ?, type = ?, status = ?, updated_at = NOW()
            ";
            
            if ($validated['status'] === 'resolved' && !DB::selectOne("SELECT resolved_at FROM discipline_records WHERE id = ?", [$id])->resolved_at ?? null) {
                $query .= ", resolved_by = ?, resolved_at = NOW()";
                $params[] = auth()->id();
                
                if (!empty($validated['resolved_notes'])) {
                    $query .= ", resolved_notes = ?";
                    $params[] = $validated['resolved_notes'];
                }
            }
            
            $query .= " WHERE id = ?";
            $params[] = $id;
            
            DB::update($query, $params);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Discipline record updated successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update discipline record: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            DB::delete("DELETE FROM discipline_records WHERE id = ?", [$id]);
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Discipline record deleted successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete discipline record: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function resolve(Request $request, $id)
    {
        $validated = $request->validate([
            'resolved_notes' => 'required|string'
        ]);
        
        DB::beginTransaction();
        try {
            DB::update("
                UPDATE discipline_records 
                SET status = 'resolved', resolved_by = ?, resolved_at = NOW(),
                    resolved_notes = ?, updated_at = NOW()
                WHERE id = ?
            ", [auth()->id(), $validated['resolved_notes'], $id]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Discipline record resolved successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to resolve discipline record: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function getStats()
    {
        $stats = DB::select("
            SELECT 
                COUNT(*) as total_records,
                SUM(CASE WHEN type = 'positive' THEN 1 ELSE 0 END) as positive_count,
                SUM(CASE WHEN type = 'warning' THEN 1 ELSE 0 END) as warning_count,
                SUM(CASE WHEN type = 'penalty' THEN 1 ELSE 0 END) as penalty_count,
                SUM(CASE WHEN type = 'suspension' THEN 1 ELSE 0 END) as suspension_count,
                SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_count,
                SUM(CASE WHEN status = 'resolved' THEN 1 ELSE 0 END) as resolved_count,
                COALESCE(AVG(points), 0) as avg_points
            FROM discipline_records
            WHERE created_at >= date_trunc('month', CURRENT_DATE)
        ");
        
        return response()->json([
            'success' => true,
            'stats' => $stats[0] ?? (object)[
                'total_records' => 0,
                'positive_count' => 0,
                'warning_count' => 0,
                'penalty_count' => 0,
                'suspension_count' => 0,
                'active_count' => 0,
                'resolved_count' => 0,
                'avg_points' => 0
            ]
        ]);
    }
    public function deleteSession(Request $request)
{
    $date = $request->get('date');
    $title = $request->get('title');
    
    DB::beginTransaction();
    try {
        DB::delete("
            DELETE FROM discipline_records 
            WHERE DATE(created_at) = ? AND title = ?
        ", [$date, $title]);
        
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
}