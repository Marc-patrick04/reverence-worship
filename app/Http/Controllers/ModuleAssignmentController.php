<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User\User;
use App\Models\User\Role;
use App\Models\User\Permission;
use App\Models\System\ActivityLog;

class ModuleAssignmentController extends Controller
{
    // Show module assignment page
    public function index()
    {
        $users = User::with('roles')->where('id', '!=', auth()->id())->get();
        $modules = Permission::whereIn('module', [
            'music', 'intercession', 'fellowship', 'discipline', 
            'finance', 'announcements', 'reports', 'chats'
        ])->get();
        
        return view('super-admin.module-assignment', compact('users', 'modules'));
    }
    
    // Assign modules to user
    public function assignModules(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'modules' => 'array'
        ]);
        
        $user = User::findOrFail($request->user_id);
        
        // Get or create a role for module access
        $role = Role::firstOrCreate(
            ['name' => 'module-user'],
            ['display_name' => 'Module User', 'description' => 'User with module access']
        );
        
        // Get permission IDs for selected modules
        $permissionIds = [];
        if ($request->has('modules')) {
            $permissionIds = Permission::whereIn('name', $request->modules)->pluck('id')->toArray();
        }
        
        // Sync permissions to role
        $role->permissions()->sync($permissionIds);
        
        // Assign role to user if not already assigned
        if (!$user->roles->contains($role->id)) {
            $user->roles()->attach($role->id);
        }
        
        // Log activity
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'modules_assigned',
            'description' => 'Assigned modules to user: ' . $user->email . ' - Modules: ' . ($request->modules ? implode(', ', $request->modules) : 'None'),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);
        
        return redirect()->back()->with('success', 'Modules assigned successfully to ' . $user->name);
    }
    
    // Get user's assigned modules
    public function getUserModules($userId)
    {
        $user = User::with('roles.permissions')->findOrFail($userId);
        $assignedModules = [];
        
        foreach ($user->roles as $role) {
            foreach ($role->permissions as $permission) {
                if (!in_array($permission->name, $assignedModules)) {
                    $assignedModules[] = $permission->name;
                }
            }
        }
        
        return response()->json($assignedModules);
    }
}