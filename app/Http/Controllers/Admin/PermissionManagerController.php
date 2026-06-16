<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User\Role;
use App\Models\System\Page;
use App\Models\System\Feature;
use App\Models\User\RolePageFeature;
use App\Models\System\ActivityLog;
use App\Models\User\User;  // Fixed: Correct User model path
use Illuminate\Support\Facades\DB;

class PermissionManagerController extends Controller
{
    // ==================== MAIN VIEW ====================
    
    public function index()
    {
        // Pages & Features data
        $pages = Page::with('features')->orderBy('sort_order')->get();
        $allFeatures = Feature::with('page')->get();
        
        // Roles data
        $roles = Role::where('name', '!=', 'super-admin')->get();
        foreach ($roles as $role) {
            $role->users_count = DB::table('role_user')->where('role_id', $role->id)->count();
        }
        
        // Users for module assignment
        $users = User::orderBy('name')->get();
        
        // Role-Page-Feature assignments
        $allAssignments = RolePageFeature::all()->groupBy('role_id');
        
        return view('super-admin.permission-manager', compact(
            'pages', 'allFeatures', 'roles', 'users', 'allAssignments'
        ));
    }
    
    // ==================== PAGE CRUD ====================
    
    public function storePage(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|unique:pages,name',
                'display_name' => 'required|string',
                'icon' => 'required|string',
                'route' => 'nullable|string',
                'sort_order' => 'nullable|integer',
                'is_active' => 'nullable|boolean'
            ]);
            
            $page = Page::create([
                'name' => $request->name,
                'display_name' => $request->display_name,
                'icon' => $request->icon,
                'route' => $request->route,
                'sort_order' => $request->sort_order ?? 999,
                'is_active' => $request->has('is_active')
            ]);
            
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'page_created',
                'description' => 'Created page: ' . $page->display_name,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
            
            return response()->json(['success' => true, 'message' => 'Page created successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    public function editPage($id)
    {
        $page = Page::findOrFail($id);
        return response()->json($page);
    }
    
    public function updatePage(Request $request, $id)
    {
        try {
            $page = Page::findOrFail($id);
            
            $request->validate([
                'name' => 'required|string|unique:pages,name,' . $id,
                'display_name' => 'required|string',
                'icon' => 'required|string',
                'route' => 'nullable|string',
                'sort_order' => 'nullable|integer',
                'is_active' => 'nullable|boolean'
            ]);
            
            $page->update([
                'name' => $request->name,
                'display_name' => $request->display_name,
                'icon' => $request->icon,
                'route' => $request->route,
                'sort_order' => $request->sort_order ?? 999,
                'is_active' => $request->has('is_active')
            ]);
            
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'page_updated',
                'description' => 'Updated page: ' . $page->display_name,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
            
            return response()->json(['success' => true, 'message' => 'Page updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    public function deletePage($id)
    {
        try {
            $page = Page::findOrFail($id);
            $pageName = $page->display_name;
            
            // Delete associated features
            $page->features()->delete();
            // Delete role assignments
            RolePageFeature::where('page_id', $id)->delete();
            // Delete the page
            $page->delete();
            
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'page_deleted',
                'description' => 'Deleted page: ' . $pageName,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);
            
            return redirect()->back()->with('success', 'Page deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
    
    // ==================== FEATURE CRUD ====================
    
    public function storeFeature(Request $request)
    {
        try {
            $request->validate([
                'page_id' => 'required|exists:pages,id',
                'name' => 'required|string|unique:features,name',
                'display_name' => 'required|string',
                'description' => 'nullable|string'
            ]);
            
            $feature = Feature::create([
                'page_id' => $request->page_id,
                'name' => $request->name,
                'display_name' => $request->display_name,
                'description' => $request->description
            ]);
            
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'feature_created',
                'description' => 'Created feature: ' . $feature->display_name,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
            
            return response()->json(['success' => true, 'message' => 'Feature created successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    public function editFeature($id)
    {
        $feature = Feature::findOrFail($id);
        return response()->json($feature);
    }
    
    public function updateFeature(Request $request, $id)
    {
        try {
            $feature = Feature::findOrFail($id);
            
            $request->validate([
                'name' => 'required|string|unique:features,name,' . $id,
                'display_name' => 'required|string',
                'description' => 'nullable|string'
            ]);
            
            $feature->update([
                'name' => $request->name,
                'display_name' => $request->display_name,
                'description' => $request->description
            ]);
            
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'feature_updated',
                'description' => 'Updated feature: ' . $feature->display_name,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
            
            return response()->json(['success' => true, 'message' => 'Feature updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    public function deleteFeature($id)
    {
        try {
            $feature = Feature::findOrFail($id);
            $featureName = $feature->display_name;
            
            // Delete role assignments for this feature
            RolePageFeature::where('feature_id', $id)->delete();
            $feature->delete();
            
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'feature_deleted',
                'description' => 'Deleted feature: ' . $featureName,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);
            
            return redirect()->back()->with('success', 'Feature deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
    
    // ==================== ROLE CRUD ====================
    
    public function storeRole(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255|unique:roles',
                'display_name' => 'required|string|max:255',
                'description' => 'nullable|string',
            ]);
            
            $role = Role::create([
                'name' => strtolower(str_replace(' ', '-', $request->name)),
                'display_name' => $request->display_name,
                'description' => $request->description
            ]);
            
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'role_created',
                'description' => 'Created role: ' . $role->display_name,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
            
            return response()->json(['success' => true, 'message' => 'Role created successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    public function editRole($id)
    {
        $role = Role::findOrFail($id);
        return response()->json($role);
    }
    
    public function updateRole(Request $request, $id)
    {
        try {
            $role = Role::findOrFail($id);
            
            $request->validate([
                'name' => 'required|string|max:255|unique:roles,name,' . $id,
                'display_name' => 'required|string|max:255',
                'description' => 'nullable|string',
            ]);
            
            // Prevent modifying super-admin name
            if ($role->name !== 'super-admin') {
                $role->name = strtolower(str_replace(' ', '-', $request->name));
            }
            $role->display_name = $request->display_name;
            $role->description = $request->description;
            $role->save();
            
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'role_updated',
                'description' => 'Updated role: ' . $role->display_name,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
            
            return response()->json(['success' => true, 'message' => 'Role updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    public function deleteRole($id)
    {
        try {
            $role = Role::findOrFail($id);
            
            if ($role->name === 'super-admin') {
                return redirect()->back()->with('error', 'Cannot delete Super Admin role!');
            }
            
            $roleName = $role->display_name;
            
            // Delete associated page features
            RolePageFeature::where('role_id', $id)->delete();
            $role->delete();
            
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'role_deleted',
                'description' => 'Deleted role: ' . $roleName,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);
            
            return redirect()->back()->with('success', 'Role deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
    
    // ==================== MODULE ASSIGNMENT (USER MODULES) ====================
    
    public function getUserModules($userId)
    {
        try {
            // Check if table exists first
            $tableExists = DB::select("SELECT to_regclass('user_module_assignments')");
            
            if (!$tableExists[0]->to_regclass) {
                return response()->json([]);
            }
            
            $modules = DB::table('user_module_assignments')
                ->where('user_id', $userId)
                ->pluck('module_name');
            
            return response()->json($modules);
        } catch (\Exception $e) {
            return response()->json([]);
        }
    }
    
    public function assignModules(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'required|exists:users,id',
                'modules' => 'nullable|array'
            ]);
            
            $userId = $request->user_id;
            
            // Clear existing assignments
            DB::table('user_module_assignments')->where('user_id', $userId)->delete();
            
            // Assign new modules
            if ($request->has('modules')) {
                foreach ($request->modules as $module) {
                    DB::table('user_module_assignments')->insert([
                        'user_id' => $userId,
                        'module_name' => $module,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }
            
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'user_modules_assigned',
                'description' => 'Assigned modules to user ID: ' . $userId,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
            
            return response()->json(['success' => true, 'message' => 'Modules assigned successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    // ==================== PAGE ASSIGNMENT (ROLE-FEATURE) ====================
    
    public function savePageAssignments(Request $request)
    {
        try {
            $request->validate([
                'role_id' => 'required|exists:roles,id',
                'assignments' => 'nullable|array'
            ]);
            
            $roleId = $request->role_id;
            $role = Role::find($roleId);
            
            // Delete existing assignments for this role
            RolePageFeature::where('role_id', $roleId)->delete();
            
            // Insert new assignments
            foreach ($request->assignments as $assignment) {
                RolePageFeature::create([
                    'role_id' => $roleId,
                    'page_id' => $assignment['page_id'],
                    'feature_id' => $assignment['feature_id']
                ]);
            }
            
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'role_permissions_assigned',
                'description' => 'Assigned permissions to role: ' . ($role ? $role->display_name : 'Unknown'),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
            
            return response()->json(['success' => true, 'message' => 'Assignments saved successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}