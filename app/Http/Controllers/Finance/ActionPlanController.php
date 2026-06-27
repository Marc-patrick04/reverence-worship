<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ActionPlanController extends Controller
{
    public function filterActionPlans(Request $request)
    {
        try {
            $status = $request->status;
            $priority = $request->priority;
            
            $query = DB::table('action_plans');
            
            $columns = DB::select("
                SELECT column_name 
                FROM information_schema.columns 
                WHERE table_name = 'action_plans' AND column_name = 'department'
            ");
            
            if (!empty($columns)) {
                $query->where('department', 'finance');
            }
            
            if ($status && $status !== 'all') {
                $query->where('status', $status);
            }
            if ($priority && $priority !== 'all') {
                $priorityColumns = DB::select("
                    SELECT column_name 
                    FROM information_schema.columns 
                    WHERE table_name = 'action_plans' AND column_name = 'priority'
                ");
                if (!empty($priorityColumns)) {
                    $query->where('priority', $priority);
                }
            }
            
            $plans = $query->orderBy('created_at', 'desc')->get();
            
            return response()->json(['success' => true, 'plans' => $plans]);
        } catch (\Exception $e) {
            Log::error('filterActionPlans error: ' . $e->getMessage());
            return response()->json(['success' => true, 'plans' => []]);
        }
    }

    public function storeActionPlan(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'due_date' => 'nullable|date',
                'priority' => 'nullable|string',
                'budget' => 'nullable|numeric',
                'assigned_to' => 'nullable|exists:users,id'
            ]);
            
            $id = DB::table('action_plans')->insertGetId([
                'title' => $request->title,
                'description' => $request->description,
                'due_date' => $request->due_date,
                'priority' => $request->priority ?? 'medium',
                'status' => 'pending',
                'progress' => 0,
                'budget' => $request->budget ?? 0,
                'assigned_to' => $request->assigned_to,
                'department' => 'finance',
                'created_by' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            return response()->json(['success' => true, 'message' => 'Action plan created successfully', 'id' => $id]);
        } catch (\Exception $e) {
            Log::error('storeActionPlan error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function updateActionPlan(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'title' => 'sometimes|required|string|max:255',
                'description' => 'nullable|string',
                'due_date' => 'nullable|date',
                'priority' => 'nullable|string',
                'status' => 'nullable|string',
                'progress' => 'nullable|integer|min:0|max:100',
                'budget' => 'nullable|numeric',
                'assigned_to' => 'nullable|exists:users,id'
            ]);

            $allowed = [
                'title', 'description', 'due_date', 'priority',
                'status', 'progress', 'budget', 'assigned_to'
            ];
            $updates = array_intersect_key($validated, array_flip($allowed));
            $updates['updated_at'] = now();

            $updated = DB::table('action_plans')
                ->where('id', $id)
                ->where('department', 'finance')
                ->update($updates);

            if (!$updated && !DB::table('action_plans')->where('id', $id)->where('department', 'finance')->exists()) {
                return response()->json(['success' => false, 'message' => 'Action plan not found'], 404);
            }
            
            return response()->json(['success' => true, 'message' => 'Action plan updated successfully']);
        } catch (\Exception $e) {
            Log::error('updateActionPlan error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function deleteActionPlan($id)
    {
        try {
            DB::table('action_plans')
                ->where('id', $id)
                ->where('department', 'finance')
                ->delete();
            
            return response()->json(['success' => true, 'message' => 'Action plan deleted successfully']);
        } catch (\Exception $e) {
            Log::error('deleteActionPlan error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function editActionPlan($id)
    {
        $plan = DB::table('action_plans')
            ->where('id', $id)
            ->where('department', 'finance')
            ->first();

        if (!$plan) {
            return response()->json(['success' => false, 'message' => 'Action plan not found'], 404);
        }

        return response()->json(['success' => true, 'plan' => $plan]);
    }

    public function showActionPlan($id)
    {
        return $this->editActionPlan($id);
    }
}
