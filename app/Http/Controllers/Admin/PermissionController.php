<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User\Permission;
use App\Models\user\Role;
use App\Models\System\ActivityLog;

class PermissionController extends Controller
{
    // Display list of permissions
    public function index()
    {
        $permissions = Permission::orderBy('module')->orderBy('name')->paginate(15);
        $modules = Permission::select('module')->distinct()->get();
        return view('super-admin.permissions.index', compact('permissions', 'modules'));
    }
    
    // Show create permission form
    public function create()
    {
        return view('super-admin.permissions.create');
    }
    
    // Store new permission
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions',
            'display_name' => 'required|string|max:255',
            'module' => 'required|string|max:100',
            'description' => 'nullable|string'
        ]);
        
        $permission = Permission::create([
            'name' => strtolower(str_replace(' ', '-', $request->name)),
            'display_name' => $request->display_name,
            'module' => strtolower($request->module),
            'description' => $request->description
        ]);
        
        // Log activity
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'permission_created',
            'description' => 'Created permission: ' . $permission->display_name,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);
        
        return redirect()->route('permissions.index')->with('success', 'Permission created successfully!');
    }
    
    // Show edit permission form
    public function edit($id)
    {
        $permission = Permission::findOrFail($id);
        return view('super-admin.permissions.edit', compact('permission'));
    }
    
    // Update permission
    public function update(Request $request, $id)
    {
        $permission = Permission::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name,' . $id,
            'display_name' => 'required|string|max:255',
            'module' => 'required|string|max:100',
            'description' => 'nullable|string'
        ]);
        
        $permission->name = strtolower(str_replace(' ', '-', $request->name));
        $permission->display_name = $request->display_name;
        $permission->module = strtolower($request->module);
        $permission->description = $request->description;
        $permission->save();
        
        // Log activity
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'permission_updated',
            'description' => 'Updated permission: ' . $permission->display_name,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);
        
        return redirect()->route('permissions.index')->with('success', 'Permission updated successfully!');
    }
    
    // Delete permission
    public function destroy(Request $request, $id)
    {
        $permission = Permission::findOrFail($id);
        
        // Check if permission is being used by any role
        if ($permission->roles()->count() > 0) {
            return back()->with('error', 'Cannot delete permission. It is assigned to ' . $permission->roles()->count() . ' role(s).');
        }
        
        $permissionName = $permission->display_name;
        $permission->delete();
        
        // Log activity
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'permission_deleted',
            'description' => 'Deleted permission: ' . $permissionName,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);
        
        return redirect()->route('permissions.index')->with('success', 'Permission deleted successfully!');
    }
    
    // Assign permissions to role
    public function showAssignForm()
    {
        $roles = Role::where('name', '!=', 'super-admin')->get();
        $permissions = Permission::all();
        return view('super-admin.permissions.assign', compact('roles', 'permissions'));
    }
    
    // Assign permissions to role
    public function assignPermissions(Request $request)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'permissions' => 'array'
        ]);
        
        $role = Role::findOrFail($request->role_id);
        
        // Prevent modifying super-admin
        if ($role->name === 'super-admin') {
            return back()->with('error', 'Super Admin role automatically has all permissions.');
        }
        
        $role->permissions()->sync($request->permissions);
        
        // Log activity
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'permissions_assigned',
            'description' => 'Assigned permissions to role: ' . $role->display_name,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);
        
        return redirect()->route('permissions.assign')->with('success', 'Permissions assigned to role successfully!');
    }
    
    // View permissions by module
    public function byModule($module)
    {
        $permissions = Permission::where('module', $module)->paginate(20);
        $modules = Permission::select('module')->distinct()->get();
        return view('super-admin.permissions.index', compact('permissions', 'modules'));
    }
}