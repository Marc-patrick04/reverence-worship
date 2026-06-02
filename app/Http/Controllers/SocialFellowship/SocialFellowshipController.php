<?php

namespace App\Http\Controllers\SocialFellowship;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\User\User;

class SocialFellowshipController extends Controller
{
    public function index()
{
    // Debug: Log start
    \Log::info('SocialFellowship index method called');
    
    // Get all families
    $families = DB::table('families')
        ->leftJoin('family_members', 'families.id', '=', 'family_members.family_id')
        ->select('families.*', DB::raw('COUNT(DISTINCT family_members.id) as members_count'))
        ->groupBy('families.id')
        ->orderBy('families.created_at', 'desc')
        ->get();
    
    \Log::info('Families count: ' . $families->count());
    
    // Get all tasks with family names
    $tasks = DB::table('family_tasks')
        ->join('families', 'family_tasks.family_id', '=', 'families.id')
        ->leftJoin('users', 'family_tasks.assigned_to', '=', 'users.id')
        ->select('family_tasks.*', 'families.name as family_name', 'users.name as assigned_name')
        ->orderByRaw("CASE WHEN family_tasks.status = 'pending' THEN 1 WHEN family_tasks.status = 'in-progress' THEN 2 ELSE 3 END")
        ->orderBy('family_tasks.due_date', 'asc')
        ->get();
    
    \Log::info('Tasks count: ' . $tasks->count());
    if ($tasks->count() > 0) {
        \Log::info('First task: ' . json_encode($tasks->first()));
    } else {
        \Log::warning('No tasks found in family_tasks table');
    }
    
    // Get action plans with stats
    $actionPlans = DB::table('family_action_plans')
        ->leftJoin('families', 'family_action_plans.family_id', '=', 'families.id')
        ->select('family_action_plans.*', 'families.name as family_name')
        ->orderBy('created_at', 'desc')
        ->get();
    
    \Log::info('Action Plans count: ' . $actionPlans->count());
    
    $totalActionPlans = $actionPlans->count();
    $completedPlans = $actionPlans->where('status', 'completed')->count();
    $inProgressPlans = $actionPlans->where('status', 'in-progress')->count();
    $pendingPlans = $actionPlans->where('status', 'pending')->count();
    $overallProgress = $totalActionPlans > 0 ? round(($completedPlans / $totalActionPlans) * 100) : 0;
    
    // Archive sections
    $archiveSections = DB::table('archive_sections')
        ->leftJoin('archive_pages', 'archive_sections.id', '=', 'archive_pages.section_id')
        ->select('archive_sections.*', DB::raw('COUNT(archive_pages.id) as pages_count'))
        ->groupBy('archive_sections.id')
        ->orderBy('archive_sections.created_at', 'desc')
        ->get();
    
    \Log::info('Archive Sections count: ' . $archiveSections->count());
    
    // Available users (not in any family)
    $availableUsers = DB::table('users')
        ->whereNotIn('id', function($query) {
            $query->select('user_id')->from('family_members');
        })
        ->get();
    
    \Log::info('Available Users count: ' . $availableUsers->count());
    
    // All users for users tab (including family info)
    $allUsers = DB::table('users')
        ->leftJoin('family_members', 'users.id', '=', 'family_members.user_id')
        ->leftJoin('families', 'family_members.family_id', '=', 'families.id')
        ->select(
            'users.id',
            'users.name',
            'users.email',
            'users.phone',
            'users.province',
            'users.district',
            'users.sector',
            'users.village',
            'family_members.family_id',
            'family_members.role',
            'families.name as family_name',
            DB::raw("CONCAT(COALESCE(users.province, ''), ', ', COALESCE(users.district, ''), ', ', COALESCE(users.sector, '')) as residence")
        )
        ->orderBy('users.name')
        ->paginate(15);
    
    \Log::info('All Users count (paginated): ' . $allUsers->total());
    
    // Regular users list for dropdowns
    $users = DB::table('users')->select('id', 'name', 'email')->get();
    
    $totalFamilies = $families->count();
    $totalMembers = DB::table('family_members')->count();
    $activeTasks = DB::table('family_tasks')->where('status', 'pending')->count();
    
    // Debug: Check if tables exist
    $tables = DB::select("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'");
    $tableNames = array_column($tables, 'table_name');
    \Log::info('Available tables: ' . implode(', ', $tableNames));
    
    return view('modules.social-fellowship.index', compact(
        'families', 
        'tasks',
        'actionPlans',
        'totalActionPlans',
        'completedPlans',
        'inProgressPlans',
        'pendingPlans',
        'overallProgress',
        'archiveSections',
        'availableUsers',
        'allUsers',
        'users',
        'totalFamilies', 
        'totalMembers', 
        'activeTasks'
    ));
}

// Add a debug route to check data
public function debugData()
{
    $data = [];
    
    // Check families table
    $data['families_count'] = DB::table('families')->count();
    $data['families'] = DB::table('families')->get();
    
    // Check family_tasks table
    $data['family_tasks_count'] = DB::table('family_tasks')->count();
    $data['family_tasks'] = DB::table('family_tasks')->get();
    
    // Check family_action_plans table
    $data['family_action_plans_count'] = DB::table('family_action_plans')->count();
    $data['family_action_plans'] = DB::table('family_action_plans')->get();
    
    // Check archive_sections table
    $data['archive_sections_count'] = DB::table('archive_sections')->count();
    $data['archive_sections'] = DB::table('archive_sections')->get();
    
    // Check archive_pages table
    $data['archive_pages_count'] = DB::table('archive_pages')->count();
    
    // Check family_members table
    $data['family_members_count'] = DB::table('family_members')->count();
    
    return response()->json($data);
}
    
    // ==================== FAMILY METHODS ====================
    
    public function getFamily($id)
    {
        try {
            $family = DB::table('families')->where('id', $id)->first();
            return response()->json(['success' => true, 'family' => $family]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    public function deleteFamily($id)
    {
        try {
            DB::table('family_members')->where('family_id', $id)->delete();
            DB::table('family_tasks')->where('family_id', $id)->delete();
            DB::table('family_action_plans')->where('family_id', $id)->delete();
            DB::table('families')->where('id', $id)->delete();
            
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    public function getFamilyDetails($id)
    {
        try {
            $family = DB::table('families')->where('id', $id)->first();
            
            $members = DB::table('family_members')
                ->join('users', 'family_members.user_id', '=', 'users.id')
                ->where('family_members.family_id', $id)
                ->select(
                    'family_members.id',
                    'family_members.user_id',
                    'family_members.family_id',
                    'family_members.role',
                    'users.name',
                    'users.email',
                    'users.phone',
                    'users.province',
                    'users.district',
                    'users.sector',
                    'users.village'
                )
                ->get();
            
            foreach ($members as $member) {
                $locationParts = array_filter([
                    $member->province ?? '',
                    $member->district ?? '',
                    $member->sector ?? '',
                    $member->village ?? ''
                ]);
                $member->location = !empty($locationParts) ? implode(', ', $locationParts) : 'Not specified';
            }
            
            return response()->json([
                'success' => true,
                'family' => $family,
                'members' => $members
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    public function storeFamily(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'parent_id' => 'nullable|exists:users,id',
                'parent_name' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'motto' => 'nullable|string'
            ]);
            
            // Check if the selected parent is already a parent of another family
            if ($request->parent_id) {
                $existingParent = DB::table('families')
                    ->where('parent_id', $request->parent_id)
                    ->first();
                
                if ($existingParent) {
                    return response()->json([
                        'success' => false, 
                        'message' => 'This user is already a parent of another family. A parent cannot be in multiple families.'
                    ], 400);
                }
                
                // Check if the selected parent is already a member of another family
                $existingMember = DB::table('family_members')
                    ->where('user_id', $request->parent_id)
                    ->first();
                
                if ($existingMember) {
                    return response()->json([
                        'success' => false, 
                        'message' => 'This user is already a member of another family. A person cannot be in multiple families.'
                    ], 400);
                }
            }
            
            $parentName = $request->parent_name;
            if ($request->parent_id) {
                $parentUser = DB::table('users')->where('id', $request->parent_id)->first();
                if ($parentUser) {
                    $parentName = $parentUser->name;
                }
            }
            
            $id = DB::table('families')->insertGetId([
                'name' => $request->name,
                'parent_name' => $parentName,
                'parent_id' => $request->parent_id,
                'description' => $request->description,
                'motto' => $request->motto,
                'created_by' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            // Add the parent as a member of the family
            if ($request->parent_id) {
                DB::table('family_members')->insert([
                    'family_id' => $id,
                    'user_id' => $request->parent_id,
                    'role' => 'parent',
                    'joined_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
            
            return response()->json(['success' => true, 'message' => 'Family created successfully', 'family_id' => $id]);
        } catch (\Illuminate\Database\QueryException $e) {
            if (str_contains($e->getMessage(), 'unique_user_per_family')) {
                return response()->json([
                    'success' => false, 
                    'message' => 'This user is already a member of another family. A person cannot be in multiple families.'
                ], 400);
            }
            if (str_contains($e->getMessage(), 'unique_parent_per_family')) {
                return response()->json([
                    'success' => false, 
                    'message' => 'This user is already a parent of another family. A parent cannot be in multiple families.'
                ], 400);
            }
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    public function addMember(Request $request, $familyId)
    {
        try {
            $request->validate([
                'user_id' => 'required|exists:users,id',
                'role' => 'required|string'
            ]);
            
            // Check if user is already a member of ANY family
            $existingMember = DB::table('family_members')
                ->where('user_id', $request->user_id)
                ->first();
            
            if ($existingMember) {
                return response()->json([
                    'success' => false, 
                    'message' => 'This user is already a member of another family. A person cannot be in multiple families.'
                ], 400);
            }
            
            // Check if user is already a parent of another family
            $existingParent = DB::table('families')
                ->where('parent_id', $request->user_id)
                ->first();
            
            if ($existingParent) {
                return response()->json([
                    'success' => false, 
                    'message' => 'This user is already a parent of another family. A parent cannot be in multiple families.'
                ], 400);
            }
            
            DB::table('family_members')->insert([
                'family_id' => $familyId,
                'user_id' => $request->user_id,
                'role' => $request->role,
                'joined_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            return response()->json(['success' => true, 'message' => 'Member added successfully']);
        } catch (\Illuminate\Database\QueryException $e) {
            if (str_contains($e->getMessage(), 'unique_user_per_family')) {
                return response()->json([
                    'success' => false, 
                    'message' => 'This user is already a member of another family. A person cannot be in multiple families.'
                ], 400);
            }
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    public function removeMember($familyId, $userId)
    {
        try {
            DB::table('family_members')
                ->where('family_id', $familyId)
                ->where('user_id', $userId)
                ->delete();
            
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    // ==================== TASK METHODS ====================
    
    public function storeTask(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'family_id' => 'required|exists:families,id',
                'due_date' => 'nullable|date',
                'priority' => 'nullable|string',
                'status' => 'nullable|string'
            ]);
            
            $taskId = DB::table('family_tasks')->insertGetId([
                'title' => $request->title,
                'description' => $request->description,
                'family_id' => $request->family_id,
                'due_date' => $request->due_date,
                'priority' => $request->priority ?? 'medium',
                'status' => $request->status ?? 'pending',
                'assigned_to' => $request->assigned_to ?? null,
                'created_by' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            if ($request->ajax()) {
                return response()->json(['success' => true, 'task_id' => $taskId]);
            }
            
            return redirect()->back()->with('success', 'Task created successfully');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Error creating task: ' . $e->getMessage());
        }
    }
    
    public function getTask($id)
    {
        try {
            $task = DB::table('family_tasks')
                ->join('families', 'family_tasks.family_id', '=', 'families.id')
                ->leftJoin('users', 'family_tasks.assigned_to', '=', 'users.id')
                ->where('family_tasks.id', $id)
                ->select('family_tasks.*', 'families.name as family_name', 'users.name as assigned_name')
                ->first();
            
            return response()->json(['success' => true, 'task' => $task]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    public function editTask($id)
    {
        try {
            $task = DB::table('family_tasks')->where('id', $id)->first();
            return response()->json(['success' => true, 'task' => $task]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    public function updateTask(Request $request, $id)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'family_id' => 'required|exists:families,id',
                'due_date' => 'nullable|date',
                'priority' => 'nullable|string',
                'status' => 'nullable|string'
            ]);
            
            DB::table('family_tasks')->where('id', $id)->update([
                'title' => $request->title,
                'description' => $request->description,
                'family_id' => $request->family_id,
                'due_date' => $request->due_date,
                'priority' => $request->priority ?? 'medium',
                'status' => $request->status ?? 'pending',
                'assigned_to' => $request->assigned_to ?? null,
                'updated_at' => now()
            ]);
            
            if ($request->ajax()) {
                return response()->json(['success' => true]);
            }
            
            return redirect()->back()->with('success', 'Task updated successfully');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Error updating task: ' . $e->getMessage());
        }
    }
    
    public function deleteTask($id)
    {
        try {
            DB::table('family_tasks')->where('id', $id)->delete();
            
            if (request()->ajax()) {
                return response()->json(['success' => true]);
            }
            
            return redirect()->back()->with('success', 'Task deleted successfully');
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Error deleting task: ' . $e->getMessage());
        }
    }
    
    // ==================== ACTION PLAN METHODS ====================
    
    public function storeActionPlan(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'family_id' => 'required|exists:families,id',
                'due_date' => 'nullable|date',
                'progress' => 'nullable|integer|min:0|max:100',
                'status' => 'nullable|string'
            ]);
            
            $planId = DB::table('family_action_plans')->insertGetId([
                'title' => $request->title,
                'description' => $request->description,
                'family_id' => $request->family_id,
                'due_date' => $request->due_date,
                'progress' => $request->progress ?? 0,
                'status' => $request->status ?? 'pending',
                'created_by' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            if ($request->ajax()) {
                return response()->json(['success' => true, 'plan_id' => $planId]);
            }
            
            return redirect()->back()->with('success', 'Action plan created successfully');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Error creating action plan: ' . $e->getMessage());
        }
    }
    
    public function editActionPlan($id)
    {
        try {
            $plan = DB::table('family_action_plans')->where('id', $id)->first();
            return response()->json(['success' => true, 'plan' => $plan]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    public function updateActionPlan(Request $request, $id)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'family_id' => 'required|exists:families,id',
                'due_date' => 'nullable|date',
                'progress' => 'nullable|integer|min:0|max:100',
                'status' => 'nullable|string'
            ]);
            
            DB::table('family_action_plans')->where('id', $id)->update([
                'title' => $request->title,
                'description' => $request->description,
                'family_id' => $request->family_id,
                'due_date' => $request->due_date,
                'progress' => $request->progress ?? 0,
                'status' => $request->status ?? 'pending',
                'updated_at' => now()
            ]);
            
            if ($request->ajax()) {
                return response()->json(['success' => true]);
            }
            
            return redirect()->back()->with('success', 'Action plan updated successfully');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Error updating action plan: ' . $e->getMessage());
        }
    }
    
    public function deleteActionPlan($id)
    {
        try {
            DB::table('family_action_plans')->where('id', $id)->delete();
            
            if (request()->ajax()) {
                return response()->json(['success' => true]);
            }
            
            return redirect()->back()->with('success', 'Action plan deleted successfully');
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Error deleting action plan: ' . $e->getMessage());
        }
    }
    
    // ==================== ARCHIVES METHODS ====================
    
    public function storeArchiveSection(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255'
            ]);
            
            $id = DB::table('archive_sections')->insertGetId([
                'name' => $request->name,
                'created_by' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Section created successfully',
                    'section_id' => $id
                ]);
            }
            
            return redirect()->back()->with('success', 'Section created successfully');
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function updateArchiveSection(Request $request, $id)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255'
            ]);
            
            DB::table('archive_sections')->where('id', $id)->update([
                'name' => $request->name,
                'updated_at' => now()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Section updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function deleteArchiveSection($id)
    {
        try {
            DB::table('archive_pages')->where('section_id', $id)->delete();
            DB::table('archive_sections')->where('id', $id)->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Section deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function getSectionPages($id)
    {
        try {
            $section = DB::table('archive_sections')->where('id', $id)->first();
            $pages = DB::table('archive_pages')
                ->where('section_id', $id)
                ->orderBy('created_at', 'desc')
                ->get();
            
            foreach ($pages as $page) {
                $page->excerpt = Str::limit(strip_tags($page->content), 100);
                $page->formatted_date = date('F j, Y', strtotime($page->created_at));
            }
            
            return response()->json([
                'success' => true,
                'section_name' => $section->name ?? 'Pages',
                'pages' => $pages
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function storeArchivePage(Request $request)
    {
        try {
            $request->validate([
                'section_id' => 'required|integer',
                'title' => 'required|string|max:255',
                'content' => 'required|string'
            ]);
            
            $id = DB::table('archive_pages')->insertGetId([
                'section_id' => $request->section_id,
                'title' => $request->title,
                'content' => $request->content,
                'is_published' => $request->has('is_published'),
                'created_by' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Page created successfully',
                'page_id' => $id
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function updateArchivePage(Request $request, $id)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string'
            ]);
            
            DB::table('archive_pages')->where('id', $id)->update([
                'section_id' => $request->section_id,
                'title' => $request->title,
                'content' => $request->content,
                'is_published' => $request->has('is_published'),
                'updated_at' => now()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Page updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function deleteArchivePage($id)
    {
        try {
            DB::table('archive_pages')->where('id', $id)->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Page deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function editArchivePage($id)
    {
        try {
            $page = DB::table('archive_pages')->where('id', $id)->first();
            
            return response()->json([
                'success' => true,
                'page' => $page
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function showArchivePage($id)
    {
        $page = DB::table('archive_pages')->where('id', $id)->first();
        
        if (!$page) {
            abort(404);
        }
        
        return view('modules.social-fellowship.partials.archive-page-show', compact('page'));
    }
}