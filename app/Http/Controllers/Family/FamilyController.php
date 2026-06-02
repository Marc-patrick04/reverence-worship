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
                ->limit(50)
                ->get();
            
            // Get tasks for ONLY this family - limit to 20 recent tasks
            $familyTasks = DB::table('family_tasks')
                ->where('family_id', $userFamily->id)
                ->orderByRaw("CASE WHEN status = 'pending' THEN 1 WHEN status = 'in-progress' THEN 2 ELSE 3 END")
                ->orderBy('due_date', 'asc')
                ->limit(20)
                ->get();
            
            $taskStats = [
                'completed' => DB::table('family_tasks')->where('family_id', $userFamily->id)->where('status', 'completed')->count(),
                'pending' => DB::table('family_tasks')->where('family_id', $userFamily->id)->where('status', 'pending')->count(),
                'in_progress' => DB::table('family_tasks')->where('family_id', $userFamily->id)->where('status', 'in-progress')->count(),
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
                'status' => $request->status,
                'updated_at' => now()
            ]);
            
            return response()->json(['success' => true]);
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