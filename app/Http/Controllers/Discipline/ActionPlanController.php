<?php

namespace App\Http\Controllers\Discipline;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ActionPlanController extends Controller
{
    public function index(Request $request)
    {
        try {
            $userId = $request->get('user_id');
            $status = $request->get('status', 'all');
            
            // Simplified query without assigned_by join
            $query = "
                SELECT ap.*, u.name as user_name, u.email as user_email,
                       TO_CHAR(ap.created_at, 'DD/MM/YYYY') as formatted_date
                FROM action_plans ap
                JOIN users u ON u.id = ap.user_id
                WHERE 1=1
            ";
            
            $params = [];
            
            if ($userId) {
                $query .= " AND ap.user_id = ?";
                $params[] = $userId;
            }
            
            if ($status !== 'all') {
                $query .= " AND ap.status = ?";
                $params[] = $status;
            }
            
            $query .= " ORDER BY ap.due_date ASC NULLS LAST, ap.created_at DESC";
            
            $actionPlans = DB::select($query, $params);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'action_plans' => $actionPlans
                ]);
            }
            
            $users = DB::select("SELECT id, name, email FROM users ORDER BY name");
            
            return view('modules.discipline.partials.action-plans-tab', compact('actionPlans', 'users'));
            
        } catch (\Exception $e) {
            Log::error('ActionPlanController index error: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error loading action plans: ' . $e->getMessage()
                ], 500);
            }
            
            $users = DB::select("SELECT id, name, email FROM users ORDER BY name");
            $actionPlans = [];
            
            return view('modules.discipline.partials.action-plans-tab', compact('actionPlans', 'users'));
        }
    }
    
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'due_date' => 'nullable|date'
            ]);
            
            DB::beginTransaction();
            
            // Check if assigned_by column exists, if not, use NULL or current user id
            $columns = DB::select("
                SELECT column_name 
                FROM information_schema.columns 
                WHERE table_name = 'action_plans' AND column_name = 'assigned_by'
            ");
            
            $hasAssignedBy = !empty($columns);
            
            if ($hasAssignedBy) {
                $result = DB::insert("
                    INSERT INTO action_plans (
                        user_id, title, description, due_date, status, 
                        progress, assigned_by, created_at, updated_at
                    ) VALUES (?, ?, ?, ?, 'pending', 0, ?, NOW(), NOW())
                ", [
                    $validated['user_id'],
                    $validated['title'],
                    $validated['description'] ?? null,
                    $validated['due_date'] ?? null,
                    auth()->id()
                ]);
            } else {
                $result = DB::insert("
                    INSERT INTO action_plans (
                        user_id, title, description, due_date, status, 
                        progress, created_at, updated_at
                    ) VALUES (?, ?, ?, ?, 'pending', 0, NOW(), NOW())
                ", [
                    $validated['user_id'],
                    $validated['title'],
                    $validated['description'] ?? null,
                    $validated['due_date'] ?? null
                ]);
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Action plan created successfully'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('ActionPlanController store error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create action plan: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function edit($id)
    {
        try {
            $plan = DB::selectOne("
                SELECT * FROM action_plans WHERE id = ?
            ", [$id]);
            
            if (!$plan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Action plan not found'
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'plan' => $plan
            ]);
            
        } catch (\Exception $e) {
            Log::error('ActionPlanController edit error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error loading action plan: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'due_date' => 'nullable|date',
                'status' => 'sometimes|in:pending,in_progress,completed,cancelled',
                'progress' => 'sometimes|integer|min:0|max:100'
            ]);
            
            DB::beginTransaction();
            
            $setClauses = [];
            $params = [];
            
            if (isset($validated['title'])) {
                $setClauses[] = "title = ?";
                $params[] = $validated['title'];
            }
            if (isset($validated['description'])) {
                $setClauses[] = "description = ?";
                $params[] = $validated['description'];
            }
            if (isset($validated['due_date'])) {
                $setClauses[] = "due_date = ?";
                $params[] = $validated['due_date'];
            }
            if (isset($validated['status'])) {
                $setClauses[] = "status = ?";
                $params[] = $validated['status'];
                
                if ($validated['status'] === 'completed') {
                    $setClauses[] = "completed_at = NOW()";
                    $setClauses[] = "progress = 100";
                }
            }
            if (isset($validated['progress'])) {
                $setClauses[] = "progress = ?";
                $params[] = $validated['progress'];
            }
            
            $setClauses[] = "updated_at = NOW()";
            $params[] = $id;
            
            $query = "UPDATE action_plans SET " . implode(", ", $setClauses) . " WHERE id = ?";
            
            DB::update($query, $params);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Action plan updated successfully'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('ActionPlanController update error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update action plan: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            
            // Delete related tasks first
            $taskExists = DB::select("
                SELECT EXISTS (SELECT 1 FROM information_schema.tables WHERE table_name = 'action_plan_tasks')
            ");
            
            if ($taskExists[0]->exists) {
                DB::delete("DELETE FROM action_plan_tasks WHERE action_plan_id = ?", [$id]);
            }
            
            DB::delete("DELETE FROM action_plans WHERE id = ?", [$id]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Action plan deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('ActionPlanController destroy error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete action plan: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function addTask(Request $request, $planId)
    {
        try {
            $validated = $request->validate([
                'task_name' => 'required|string|max:255'
            ]);
            
            DB::beginTransaction();
            
            // Check if action_plan_tasks table exists
            $tableExists = DB::select("
                SELECT EXISTS (SELECT 1 FROM information_schema.tables WHERE table_name = 'action_plan_tasks')
            ");
            
            if (!$tableExists[0]->exists) {
                // Create the tasks table if it doesn't exist
                DB::statement("
                    CREATE TABLE IF NOT EXISTS action_plan_tasks (
                        id SERIAL PRIMARY KEY,
                        action_plan_id INTEGER NOT NULL,
                        task_name VARCHAR(255) NOT NULL,
                        is_completed BOOLEAN DEFAULT FALSE,
                        completed_at TIMESTAMP,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                    )
                ");
            }
            
            DB::insert("
                INSERT INTO action_plan_tasks (action_plan_id, task_name, created_at, updated_at)
                VALUES (?, ?, NOW(), NOW())
            ", [$planId, $validated['task_name']]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Task added successfully'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('ActionPlanController addTask error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to add task: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function updateTask(Request $request, $taskId)
    {
        try {
            $validated = $request->validate([
                'is_completed' => 'required|boolean'
            ]);
            
            DB::beginTransaction();
            
            DB::update("
                UPDATE action_plan_tasks 
                SET is_completed = ?, completed_at = CASE WHEN ? THEN NOW() ELSE NULL END,
                    updated_at = NOW()
                WHERE id = ?
            ", [$validated['is_completed'], $validated['is_completed'], $taskId]);
            
            // Get the action_plan_id
            $planInfo = DB::selectOne("SELECT action_plan_id FROM action_plan_tasks WHERE id = ?", [$taskId]);
            
            if ($planInfo) {
                // Update parent action plan progress
                DB::update("
                    UPDATE action_plans 
                    SET progress = (
                        SELECT COALESCE(ROUND(AVG(CASE WHEN is_completed THEN 100 ELSE 0 END)), 0)
                        FROM action_plan_tasks
                        WHERE action_plan_id = ?
                    ),
                    status = CASE 
                        WHEN (
                            SELECT COUNT(*) FROM action_plan_tasks 
                            WHERE action_plan_id = ?
                            AND is_completed = FALSE
                        ) = 0 AND (
                            SELECT COUNT(*) FROM action_plan_tasks 
                            WHERE action_plan_id = ?
                        ) > 0 THEN 'completed'
                        WHEN status = 'pending' AND (
                            SELECT COUNT(*) FROM action_plan_tasks 
                            WHERE action_plan_id = ?
                            AND is_completed = FALSE
                        ) > 0 THEN 'in_progress'
                        ELSE status
                    END,
                    completed_at = CASE 
                        WHEN (
                            SELECT COUNT(*) FROM action_plan_tasks 
                            WHERE action_plan_id = ?
                            AND is_completed = FALSE
                        ) = 0 AND (
                            SELECT COUNT(*) FROM action_plan_tasks 
                            WHERE action_plan_id = ?
                        ) > 0 THEN NOW()
                        ELSE completed_at
                    END,
                    updated_at = NOW()
                    WHERE id = ?
                ", [
                    $planInfo->action_plan_id,
                    $planInfo->action_plan_id,
                    $planInfo->action_plan_id,
                    $planInfo->action_plan_id,
                    $planInfo->action_plan_id,
                    $planInfo->action_plan_id,
                    $planInfo->action_plan_id
                ]);
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Task updated successfully'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('ActionPlanController updateTask error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update task: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function deleteTask($taskId)
    {
        try {
            DB::beginTransaction();
            
            $planInfo = DB::selectOne("SELECT action_plan_id FROM action_plan_tasks WHERE id = ?", [$taskId]);
            DB::delete("DELETE FROM action_plan_tasks WHERE id = ?", [$taskId]);
            
            if ($planInfo) {
                // Update parent action plan progress
                $taskCount = DB::selectOne("SELECT COUNT(*) as count FROM action_plan_tasks WHERE action_plan_id = ?", [$planInfo->action_plan_id]);
                
                if ($taskCount->count > 0) {
                    DB::update("
                        UPDATE action_plans 
                        SET progress = (
                            SELECT COALESCE(ROUND(AVG(CASE WHEN is_completed THEN 100 ELSE 0 END)), 0)
                            FROM action_plan_tasks
                            WHERE action_plan_id = ?
                        ),
                        updated_at = NOW()
                        WHERE id = ?
                    ", [$planInfo->action_plan_id, $planInfo->action_plan_id]);
                } else {
                    DB::update("
                        UPDATE action_plans 
                        SET progress = 0, status = 'pending', updated_at = NOW()
                        WHERE id = ?
                    ", [$planInfo->action_plan_id]);
                }
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Task deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('ActionPlanController deleteTask error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete task: ' . $e->getMessage()
            ], 500);
        }
    }
}