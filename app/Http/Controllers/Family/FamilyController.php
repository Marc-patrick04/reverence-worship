<?php

namespace App\Http\Controllers\Family;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class FamilyController extends Controller
{
    public function index()
    {
        // Get ONLY the family of the logged-in user
        $userFamily = DB::table('family_members')
            ->join('families', 'family_members.family_id', '=', 'families.id')
            ->where('family_members.user_id', auth()->id())
            ->select('families.*')
            ->first();
        
        $familyMembers = collect();
        $familyTasks = collect();
        $taskStats = ['completed' => 0, 'pending' => 0, 'in_progress' => 0];
        $recentActivities = collect();
        
        if ($userFamily) {
            // Get members of ONLY this family - limit to 20 for performance
            $familyMembers = DB::table('family_members')
                ->join('users', 'family_members.user_id', '=', 'users.id')
                ->where('family_members.family_id', $userFamily->id)
                ->select('family_members.*', 'users.name', 'users.email', 'users.phone')
                ->orderByRaw("CASE WHEN LOWER(family_members.role) = 'parent' THEN 0 ELSE 1 END")
                ->orderBy('users.name')
                ->limit(50)
                ->get();
            
            // Use the same family task source and ordering as the Parent Dashboard.
            $familyTasks = DB::table('family_tasks')
                ->where('family_id', $userFamily->id)
                ->orderBy('created_at', 'desc')
                ->get();

            $subtasksByTask = collect();
            if ($familyTasks->isNotEmpty() && DB::getSchemaBuilder()->hasTable('task_subtasks')) {
                $subtasksByTask = DB::table('task_subtasks')
                    ->whereIn('task_id', $familyTasks->pluck('id'))
                    ->orderBy('created_at', 'asc')
                    ->get()
                    ->groupBy('task_id');
            }

            foreach ($familyTasks as $task) {
                $task->subtasks = $subtasksByTask->get($task->id, collect())->values();
                $task->subtasks_total = $task->subtasks->count();
                $task->subtasks_completed = $task->subtasks
                    ->where('is_completed', true)
                    ->count();
                $task->progress = $task->subtasks_total > 0
                    ? (int) round(($task->subtasks_completed / $task->subtasks_total) * 100)
                    : (int) ($task->progress ?? ($task->status === 'completed' ? 100 : 0));

                // Parent Dashboard derives the visible status from subtask progress.
                if ($task->subtasks_total > 0) {
                    $task->status = $task->progress === 100
                        ? 'completed'
                        : ($task->progress > 0 ? 'in-progress' : 'pending');
                }
            }

            $taskStats = [
                'completed' => $familyTasks->where('status', 'completed')->count(),
                'pending' => $familyTasks->where('status', 'pending')->count(),
                'in_progress' => $familyTasks->where('status', 'in-progress')->count(),
            ];
            
            $recentActivities = DB::table('family_tasks')
                ->where('family_id', $userFamily->id)
                ->orderBy('updated_at', 'desc')
                ->limit(5)
                ->get();
        }
        
        return view('modules.family.index', compact('userFamily', 'familyMembers', 'familyTasks', 'taskStats', 'recentActivities'));
    }
    
    public function updateTaskStatus(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'status' => 'required|in:pending,in-progress,completed',
            ]);

            $userFamilyId = DB::table('family_members')
                ->where('user_id', auth()->id())
                ->value('family_id');
            
            $taskFamilyId = DB::table('family_tasks')
                ->where('id', $id)
                ->value('family_id');
            
            if ($taskFamilyId != $userFamilyId) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
            
            DB::table('family_tasks')->where('id', $id)->update([
                'status' => $validated['status'],
                'updated_at' => now()
            ]);
            
            return response()->json([
                'success' => true,
                'status' => $validated['status'],
                'message' => $validated['status'] === 'completed'
                    ? 'Task marked as done.'
                    : 'Task status updated.',
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    public function getMemberDetails($id)
    {
        try {
            $userFamilyId = DB::table('family_members')
                ->where('user_id', auth()->id())
                ->value('family_id');
            
            $memberFamilyId = DB::table('family_members')
                ->where('user_id', $id)
                ->value('family_id');
            
            if ($memberFamilyId != $userFamilyId) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
            
            $member = DB::table('users')
                ->where('id', $id)
                ->select('id', 'name', 'email', 'phone', 'province', 'district', 'sector', 'village')
                ->first();
            
            $role = DB::table('family_members')
                ->where('user_id', $id)
                ->where('family_id', $userFamilyId)
                ->value('role');
            
            return response()->json([
                'success' => true,
                'member' => $member,
                'role' => $role
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
