<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\User\User;

class ParentController extends Controller
{
   
    /**
     * Get user location from users table
     */
    private function getUserLocation($userId)
    {
        $user = DB::table('users')
            ->select('province', 'district', 'sector', 'village')
            ->where('id', $userId)
            ->first();
        
        if ($user) {
            $locationParts = array_filter([
                $user->province ?? '',
                $user->district ?? '',
                $user->sector ?? '',
                $user->village ?? ''
            ]);
            
            if (!empty($locationParts)) {
                return implode(', ', $locationParts);
            }
        }
        
        return 'N/A';
    }

    /**
     * Check if the authenticated user is a parent
     */
    private function isParent($user = null)
    {
        $user = $user ?? auth()->user();
        
        $parentRole = DB::table('family_members')
            ->where('user_id', $user->id)
            ->where('role', 'parent')
            ->exists();
        
        if ($parentRole) {
            return true;
        }
        
        $isFamilyParent = DB::table('families')
            ->where('parent_id', $user->id)
            ->exists();
        
        return $isFamilyParent;
    }

    /**
     * Display parent dashboard with children list
     */
    public function index()
    {
        $parent = auth()->user();
        
        if (!$this->isParent($parent)) {
            abort(403, 'You do not have permission to access this page. Only parents can view this dashboard.');
        }
        
        $parentFamily = DB::table('family_members')
            ->where('user_id', $parent->id)
            ->where('role', 'parent')
            ->first();
        
        if (!$parentFamily) {
            return view('modules.parent.index', [
                'children' => collect(),
                'parent' => $parent,
                'familyName' => null,
                'error' => 'You are not associated with any family as a parent. Please contact an administrator.'
            ]);
        }
        
        $familyId = $parentFamily->family_id;
        
        $family = DB::table('families')->where('id', $familyId)->first();
        $familyName = $family ? $family->name : 'Unknown Family';
        
        // Get children (family members excluding parent)
        $children = DB::table('users')
            ->join('family_members', 'users.id', '=', 'family_members.user_id')
            ->where('family_members.family_id', $familyId)
            ->where('users.id', '!=', $parent->id)
            ->select(
                'users.id', 
                'users.name', 
                'users.email', 
                'users.phone', 
                'users.created_at',
                'users.province',
                'users.district',
                'users.sector',
                'users.village',
                'family_members.role as family_role'
            )
            ->get();
        
        // Check columns in contributions table
        $contribColumns = $this->getTableColumns('contributions');
        $hasAnnualAmount = in_array('annual_amount', $contribColumns);
        $hasAmount = in_array('amount', $contribColumns);
        
        foreach ($children as $child) {
            // Get total contributions (using annual_amount from contributions table)
            if ($hasAnnualAmount) {
                // Get the annual amount for this child
                $annualContribution = DB::table('contributions')
                    ->where('user_id', $child->id)
                    ->where('year', date('Y'))
                    ->first();
                
                $child->total_required = $annualContribution->annual_amount ?? 0;
                
                // Get total paid from payments table (not contributions)
                $child->total_contributions = DB::table('payments')
                    ->where('user_id', $child->id)
                    ->where('year', date('Y'))
                    ->sum('amount') ?? 0;
                
                $child->payment_count = DB::table('payments')
                    ->where('user_id', $child->id)
                    ->where('year', date('Y'))
                    ->count();
            } else {
                // Fallback if no annual_amount
                $child->total_contributions = 0;
                $child->total_required = 0;
                $child->payment_count = 0;
            }
            
            $child->progress = $child->total_required > 0 
                ? round(($child->total_contributions / $child->total_required) * 100, 1) 
                : 0;
            
            $child->location = $this->getUserLocation($child->id);
        }
        
        return view('modules.parent.index', compact('children', 'parent', 'familyName'));
    }
    
    /**
     * Get child details for modal
     */
    public function getChildDetails($id)
    {
        try {
            $parent = auth()->user();
            
            if (!$this->isParent($parent)) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to access this information.'
                ], 403);
            }
            
            $parentFamily = DB::table('family_members')
                ->where('user_id', $parent->id)
                ->where('role', 'parent')
                ->first();
            
            if (!$parentFamily) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not associated with any family as a parent.'
                ], 403);
            }
            
            $familyId = $parentFamily->family_id;
            
            $childMember = DB::table('family_members')
                ->where('family_id', $familyId)
                ->where('user_id', $id)
                ->first();
            
            if ($id == $parent->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot view your own details as a child.'
                ], 403);
            }
            
            if (!$childMember) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have access to this child\'s information.'
                ], 403);
            }
            
            $child = DB::table('users')
                ->select('id', 'name', 'email', 'phone', 'created_at', 'province', 'district', 'sector', 'village')
                ->where('id', $id)
                ->first();
            
            if (!$child) {
                return response()->json([
                    'success' => false,
                    'message' => 'Child not found.'
                ], 404);
            }
            
            // Get contributions columns
            $contribColumns = $this->getTableColumns('contributions');
            $hasAnnualAmount = in_array('annual_amount', $contribColumns);
            
            if ($hasAnnualAmount) {
                $annualContribution = DB::table('contributions')
                    ->where('user_id', $id)
                    ->where('year', date('Y'))
                    ->first();
                
                $child->total_required = $annualContribution->annual_amount ?? 0;
                $child->total_contributions = DB::table('payments')
                    ->where('user_id', $id)
                    ->where('year', date('Y'))
                    ->sum('amount') ?? 0;
                $child->payment_count = DB::table('payments')
                    ->where('user_id', $id)
                    ->where('year', date('Y'))
                    ->count();
            } else {
                $child->total_required = 0;
                $child->total_contributions = 0;
                $child->payment_count = 0;
            }
            
            $child->progress = $child->total_required > 0 
                ? round(($child->total_contributions / $child->total_required) * 100, 1) 
                : 0;
            
            // Get recent payments
            $child->recent_payments = DB::table('payments')
                ->where('user_id', $id)
                ->orderBy('payment_date', 'desc')
                ->limit(5)
                ->get();
            
            $child->location = $this->getUserLocation($id);
            
            return response()->json([
                'success' => true,
                'child' => $child
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting child details: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error loading child details: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get child financial report for modal
     */
    public function getChildFinancialReport($id)
    {
        try {
            $parent = auth()->user();
            
            if (!$this->isParent($parent)) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to access this information.'
                ], 403);
            }
            
            $parentFamily = DB::table('family_members')
                ->where('user_id', $parent->id)
                ->where('role', 'parent')
                ->first();
            
            if (!$parentFamily) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not associated with any family as a parent.'
                ], 403);
            }
            
            $familyId = $parentFamily->family_id;
            
            $hasAccess = DB::table('family_members')
                ->where('family_id', $familyId)
                ->where('user_id', $id)
                ->exists();
            
            if (!$hasAccess || $id == $parent->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied'
                ], 403);
            }
            
            $child = DB::table('users')
                ->select('id', 'name', 'email')
                ->where('id', $id)
                ->first();
            
            if (!$child) {
                return response()->json([
                    'success' => false,
                    'message' => 'Child not found.'
                ], 404);
            }
            
            $contribColumns = $this->getTableColumns('contributions');
            $hasAnnualAmount = in_array('annual_amount', $contribColumns);
            
            // Get annual target
            if ($hasAnnualAmount) {
                $annualContribution = DB::table('contributions')
                    ->where('user_id', $id)
                    ->where('year', date('Y'))
                    ->first();
                
                $annualTarget = $annualContribution->annual_amount ?? 0;
            } else {
                $annualTarget = 0;
            }
            
            // Get all payments for this child
            $payments = DB::table('payments')
                ->where('user_id', $id)
                ->orderBy('payment_date', 'desc')
                ->get();
            
            $totalPaid = $payments->sum('amount') ?? 0;
            $progress = $annualTarget > 0 ? round(($totalPaid / $annualTarget) * 100, 1) : 0;
            
            // Get term totals
            $termTotals = [];
            for ($i = 1; $i <= 3; $i++) {
                $termTotals[$i] = DB::table('payments')
                    ->where('user_id', $id)
                    ->where('term', $i)
                    ->where('year', date('Y'))
                    ->sum('amount') ?? 0;
            }
            
            // Get monthly data
            $monthlyData = collect();
            try {
                $monthlyData = DB::table('payments')
                    ->where('user_id', $id)
                    ->select(
                        DB::raw('EXTRACT(YEAR FROM payment_date) as year'),
                        DB::raw('EXTRACT(MONTH FROM payment_date) as month'),
                        DB::raw('SUM(amount) as total')
                    )
                    ->whereNotNull('payment_date')
                    ->groupBy(DB::raw('EXTRACT(YEAR FROM payment_date), EXTRACT(MONTH FROM payment_date)'))
                    ->orderBy(DB::raw('EXTRACT(YEAR FROM payment_date)'), 'desc')
                    ->orderBy(DB::raw('EXTRACT(MONTH FROM payment_date)'), 'desc')
                    ->limit(12)
                    ->get();
            } catch (\Exception $e) {
                Log::warning('Could not get monthly data: ' . $e->getMessage());
            }
            
            return response()->json([
                'success' => true,
                'child' => $child,
                'payments' => $payments,
                'term_totals' => $termTotals,
                'annual_target' => $annualTarget,
                'total_paid' => $totalPaid,
                'progress' => $progress,
                'monthly_data' => $monthlyData,
                'payment_count' => $payments->count()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting financial report: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error loading financial report: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get children contributions for the parent
     */
    public function getChildrenContributions(Request $request)
    {
        try {
            $parent = auth()->user();
            
            if (!$this->isParent($parent)) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to access this information.'
                ], 403);
            }
            
            $parentFamily = DB::table('family_members')
                ->where('user_id', $parent->id)
                ->where('role', 'parent')
                ->first();
            
            if (!$parentFamily) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not associated with any family as a parent.'
                ], 403);
            }
            
            $familyId = $parentFamily->family_id;
            $year = $request->input('year', date('Y'));
            $search = $request->input('search', '');
            
            // Get children
            $childrenQuery = DB::table('users')
                ->join('family_members', 'users.id', '=', 'family_members.user_id')
                ->where('family_members.family_id', $familyId)
                ->where('users.id', '!=', $parent->id);
            
            if ($search) {
                $childrenQuery->where(function($q) use ($search) {
                    $q->where('users.name', 'ilike', "%{$search}%")
                      ->orWhere('users.email', 'ilike', "%{$search}%");
                });
            }
            
            $children = $childrenQuery
                ->select('users.id', 'users.name', 'users.email')
                ->get();
            
            $contributions = [];
            $termTotals = [];
            $numberOfTerms = 3;
            
            foreach ($children as $child) {
                // Get annual amount from contributions table
                $annualContribution = DB::table('contributions')
                    ->where('user_id', $child->id)
                    ->where('year', $year)
                    ->first();
                
                $annualAmount = $annualContribution->annual_amount ?? 0;
                
                // Get total paid from payments table
                $totalPaid = DB::table('payments')
                    ->where('user_id', $child->id)
                    ->where('year', $year)
                    ->sum('amount') ?? 0;
                
                // Get term totals from payments table
                $termTotalsForChild = [];
                for ($i = 1; $i <= $numberOfTerms; $i++) {
                    $termTotal = DB::table('payments')
                        ->where('user_id', $child->id)
                        ->where('term', $i)
                        ->where('year', $year)
                        ->sum('amount') ?? 0;
                    $termTotalsForChild[$i] = $termTotal;
                }
                $termTotals[$child->id] = $termTotalsForChild;
                
                $contributionData = [
                    'user_id' => $child->id,
                    'user_name' => $child->name,
                    'email' => $child->email,
                    'annual_amount' => $annualAmount,
                    'total_paid' => $totalPaid,
                ];
                
                // Add term paid amounts
                for ($i = 1; $i <= $numberOfTerms; $i++) {
                    $termPaid = DB::table('payments')
                        ->where('user_id', $child->id)
                        ->where('term', $i)
                        ->where('year', $year)
                        ->sum('amount') ?? 0;
                    $contributionData["term{$i}_paid"] = $termPaid;
                }
                
                $contributions[] = $contributionData;
            }
            
            return response()->json([
                'success' => true,
                'contributions' => $contributions,
                'term_totals' => $termTotals
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting children contributions: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error loading contributions: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get child contribution details
     */
    public function getChildContributionDetails(Request $request, $userId)
    {
        try {
            $parent = auth()->user();
            
            if (!$this->isParent($parent)) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to access this information.'
                ], 403);
            }
            
            $parentFamily = DB::table('family_members')
                ->where('user_id', $parent->id)
                ->where('role', 'parent')
                ->first();
            
            if (!$parentFamily) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not associated with any family as a parent.'
                ], 403);
            }
            
            $familyId = $parentFamily->family_id;
            $year = $request->input('year', date('Y'));
            
            // Verify child belongs to parent's family
            $child = DB::table('family_members')
                ->where('family_id', $familyId)
                ->where('user_id', $userId)
                ->first();
            
            if (!$child || $userId == $parent->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied.'
                ], 403);
            }
            
            $user = DB::table('users')
                ->select('id', 'name', 'email')
                ->where('id', $userId)
                ->first();
            
            // Get annual contribution
            $annualContribution = DB::table('contributions')
                ->where('user_id', $userId)
                ->where('year', $year)
                ->first();
            
            $annualAmount = $annualContribution->annual_amount ?? 0;
            
            // Get all payments for this child in the selected year
            $payments = DB::table('payments')
                ->where('user_id', $userId)
                ->where('year', $year)
                ->orderBy('payment_date', 'desc')
                ->get();
            
            $totalPaid = $payments->sum('amount') ?? 0;
            $progress = $annualAmount > 0 ? round(($totalPaid / $annualAmount) * 100, 1) : 0;
            
            // Get term details
            $termDetails = [];
            $numberOfTerms = 3;
            for ($i = 1; $i <= $numberOfTerms; $i++) {
                $termPaid = DB::table('payments')
                    ->where('user_id', $userId)
                    ->where('year', $year)
                    ->where('term', $i)
                    ->sum('amount') ?? 0;
                
                $termTarget = $annualAmount > 0 ? round($annualAmount / $numberOfTerms, 2) : 0;
                $termProgress = $termTarget > 0 ? round(($termPaid / $termTarget) * 100, 1) : 0;
                
                $termDetails[$i] = [
                    'target' => $termTarget,
                    'paid' => $termPaid,
                    'progress' => $termProgress
                ];
            }
            
            return response()->json([
                'success' => true,
                'user_name' => $user->name,
                'email' => $user->email,
                'annual_amount' => $annualAmount,
                'total_paid' => $totalPaid,
                'progress' => $progress,
                'payments' => $payments,
                'term_details' => $termDetails
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting child contribution details: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error loading contribution details: ' . $e->getMessage()
            ], 500);
        }
    }

    // ============================================
    // TASK METHODS
    // ============================================

    /**
     * Get tasks for parent's children
     */
    /**
 * Get tasks with subtasks
 */
public function getTasks(Request $request)
{
    try {
        $parent = auth()->user();
        
        if (!$this->isParent($parent)) {
            return response()->json(['success' => false, 'message' => 'Access denied'], 403);
        }
        
        $parentFamily = DB::table('family_members')
            ->where('user_id', $parent->id)
            ->where('role', 'parent')
            ->first();
        
        $familyId = $parentFamily ? $parentFamily->family_id : null;
        
        // Get tasks - either by family_id or created_by
        $query = DB::table('family_tasks');
        
        // Check if family_id column exists
        $columns = $this->getTableColumns('family_tasks');
        $hasFamilyId = in_array('family_id', $columns);
        
        if ($hasFamilyId && $familyId) {
            $query->where('family_id', $familyId);
        } else {
            // If no family_id, get tasks created by the parent
            $query->where('created_by', $parent->id);
        }
        
        // Apply status filter
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        
        // Apply search filter
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'ilike', "%{$search}%")
                  ->orWhere('description', 'ilike', "%{$search}%");
            });
        }
        
        $tasks = $query->orderBy('created_at', 'desc')->get();
        
        // Get subtasks for each task
        foreach ($tasks as $task) {
            // Check if subtasks table exists
            $subtasksExist = $this->tableExists('task_subtasks');
            if ($subtasksExist) {
                $task->subtasks = DB::table('task_subtasks')
                    ->where('task_id', $task->id)
                    ->orderBy('created_at', 'asc')
                    ->get();
            } else {
                $task->subtasks = [];
            }
        }
        
        return response()->json(['success' => true, 'tasks' => $tasks]);
        
    } catch (\Exception $e) {
        Log::error('Error getting tasks: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}
/**
 * Check if a table exists
 */
private function tableExists($tableName)
{
    try {
        $result = DB::select("
            SELECT EXISTS (
                SELECT FROM information_schema.tables 
                WHERE table_schema = 'public' 
                AND table_name = ?
            ) as exists
        ", [$tableName]);
        
        return $result[0]->exists ?? false;
    } catch (\Exception $e) {
        return false;
    }
}

/**
 * Get column names from a table
 */
private function getTableColumns($tableName)
{
    try {
        $columns = DB::select("
            SELECT column_name 
            FROM information_schema.columns 
            WHERE table_name = ? 
            AND table_schema = 'public'
        ", [$tableName]);
        
        return array_column($columns, 'column_name');
    } catch (\Exception $e) {
        return [];
    }
}

    
    /**
     * Get task for editing
     */
    public function editTask($id)
{
    try {
        $task = DB::table('family_tasks')->where('id', $id)->first();
        
        if ($task) {
            $task->subtasks = DB::table('task_subtasks')
                ->where('task_id', $task->id)
                ->orderBy('created_at', 'asc')
                ->get();
        }
        
        return response()->json(['success' => true, 'task' => $task]);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}

public function getTask($id)
{
    try {
        $task = DB::table('family_tasks')
            ->join('families', 'family_tasks.family_id', '=', 'families.id')
            ->where('family_tasks.id', $id)
            ->select('family_tasks.*', 'families.name as family_name')
            ->first();
        
        if ($task) {
            $task->subtasks = DB::table('task_subtasks')
                ->where('task_id', $task->id)
                ->orderBy('created_at', 'asc')
                ->get();
        }
        
        return response()->json(['success' => true, 'task' => $task]);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}

   
    /**
     * Delete task
     */
    public function deleteTask($id)
    {
        try {
            DB::table('family_tasks')->where('id', $id)->delete();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Mark task as complete
     */
    public function completeTask($id)
    {
        try {
            DB::table('family_tasks')
                ->where('id', $id)
                ->update([
                    'status' => 'completed',
                    'updated_at' => now()
                ]);
            
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    /**
 * Toggle subtask completion status
 */
public function toggleSubtask($id)
{
    try {
        $subtask = DB::table('task_subtasks')->where('id', $id)->first();
        
        if (!$subtask) {
            return response()->json(['success' => false, 'message' => 'Subtask not found'], 404);
        }
        
        $newStatus = !$subtask->is_completed;
        
        DB::table('task_subtasks')
            ->where('id', $id)
            ->update([
                'is_completed' => $newStatus,
                'completed_at' => $newStatus ? now() : null,
                'updated_at' => now()
            ]);
        
        // Update task progress
        $taskId = $subtask->task_id;
        $this->updateTaskProgress($taskId);
        
        return response()->json(['success' => true]);
        
    } catch (\Exception $e) {
        Log::error('Error toggling subtask: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}

/**
 * Update task progress based on subtasks
 */
private function updateTaskProgress($taskId)
{
    $subtasks = DB::table('task_subtasks')
        ->where('task_id', $taskId)
        ->get();
    
    $total = $subtasks->count();
    $completed = $subtasks->where('is_completed', true)->count();
    $progress = $total > 0 ? round(($completed / $total) * 100) : 0;
    
    $status = 'pending';
    if ($progress === 100) {
        $status = 'completed';
    } elseif ($progress > 0) {
        $status = 'in-progress';
    }
    
    DB::table('family_tasks')
        ->where('id', $taskId)
        ->update([
            'progress' => $progress,
            'status' => $status,
            'updated_at' => now()
        ]);
}

/**
 * Get tasks with subtasks
 */


/**
 * Store a new task with subtasks
 */
public function storeTask(Request $request)
{
    try {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'subtasks' => 'required|array|min:1',
            'subtasks.*' => 'required|string|max:255'
        ]);
        
        $parent = auth()->user();
        
        $parentFamily = DB::table('family_members')
            ->where('user_id', $parent->id)
            ->where('role', 'parent')
            ->first();
        
        $familyId = $parentFamily ? $parentFamily->family_id : null;
        
        // Create the task
        $taskId = DB::table('family_tasks')->insertGetId([
            'title' => $request->title,
            'description' => $request->description,
            'family_id' => $familyId,
            'due_date' => $request->due_date,
            'status' => 'pending',
            'progress' => 0,
            'created_by' => $parent->id,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        // Create subtasks
        foreach ($request->subtasks as $subtaskTitle) {
            if (!empty(trim($subtaskTitle))) {
                DB::table('task_subtasks')->insert([
                    'task_id' => $taskId,
                    'title' => trim($subtaskTitle),
                    'is_completed' => false,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
        
        return response()->json(['success' => true, 'task_id' => $taskId]);
        
    } catch (\Exception $e) {
        Log::error('Error creating task: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}

/**
 * Update task with subtasks
 */
public function updateTask(Request $request, $id)
{
    try {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'subtasks' => 'required|array|min:1',
            'subtasks.*' => 'required|string|max:255'
        ]);
        
        // Update the task
        DB::table('family_tasks')
            ->where('id', $id)
            ->update([
                'title' => $request->title,
                'description' => $request->description,
                'due_date' => $request->due_date,
                'updated_at' => now()
            ]);
        
        // Get existing subtask IDs
        $existingSubtaskIds = DB::table('task_subtasks')
            ->where('task_id', $id)
            ->pluck('id')
            ->toArray();
        
        $subtaskIds = $request->input('subtask_ids', []);
        $subtaskTitles = $request->input('subtasks', []);
        
        // Update or create subtasks
        foreach ($subtaskTitles as $index => $title) {
            if (empty(trim($title))) continue;
            
            $subtaskId = isset($subtaskIds[$index]) && $subtaskIds[$index] !== 'new' 
                ? $subtaskIds[$index] 
                : null;
            
            if ($subtaskId && in_array($subtaskId, $existingSubtaskIds)) {
                // Update existing subtask
                DB::table('task_subtasks')
                    ->where('id', $subtaskId)
                    ->update([
                        'title' => trim($title),
                        'updated_at' => now()
                    ]);
                // Remove from list to keep
                $existingSubtaskIds = array_diff($existingSubtaskIds, [$subtaskId]);
            } else {
                // Create new subtask
                DB::table('task_subtasks')->insert([
                    'task_id' => $id,
                    'title' => trim($title),
                    'is_completed' => false,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
        
        // Delete removed subtasks
        if (!empty($existingSubtaskIds)) {
            DB::table('task_subtasks')
                ->whereIn('id', $existingSubtaskIds)
                ->delete();
        }
        
        // Update task progress
        $this->updateTaskProgress($id);
        
        return response()->json(['success' => true]);
        
    } catch (\Exception $e) {
        Log::error('Error updating task: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}
}