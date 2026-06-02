@extends('layouts.app')

@section('title', 'Assign Permissions')
@section('page-title', 'Permission Management')
@section('content')
<div class="max-w-6xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Assign Permissions to Role</h1>
            <p class="text-gray-600 mt-1">Manage which permissions each role has</p>
        </div>
        <a href="{{ route('permissions.index') }}" class="text-gray-600 hover:text-gray-900">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Permissions
        </a>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Role Selection -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-lg p-6 sticky top-20">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Select Role</h3>
                
                <div class="space-y-2">
                    @foreach($roles as $role)
                        <button type="button" 
                                onclick="loadRolePermissions({{ $role->id }}, '{{ $role->display_name }}')"
                                class="role-btn w-full text-left px-4 py-3 rounded-lg transition"
                                data-role-id="{{ $role->id }}">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-medium text-gray-800">{{ $role->display_name }}</p>
                                    <p class="text-xs text-gray-500">{{ $role->name }}</p>
                                </div>
                                <i class="fas fa-chevron-right text-gray-400"></i>
                            </div>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
        
        <!-- Permissions Assignment -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div id="permission-form">
                    <div class="text-center py-12 text-gray-500">
                        <i class="fas fa-hand-pointer fa-3x mb-3 text-gray-300"></i>
                        <p>Select a role from the left to assign permissions</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function loadRolePermissions(roleId, roleName) {
    // Highlight selected role
    document.querySelectorAll('.role-btn').forEach(btn => {
        btn.classList.remove('bg-blue-600', 'text-white');
        btn.classList.add('hover:bg-gray-50');
    });
    const selectedBtn = document.querySelector(`.role-btn[data-role-id="${roleId}"]`);
    selectedBtn.classList.add('bg-blue-600', 'text-white');
    selectedBtn.classList.remove('hover:bg-gray-50');
    
    // Fetch role permissions via AJAX
    fetch(`/permissions/role/${roleId}/permissions`)
        .then(response => response.json())
        .then(data => {
            const permissionsByModule = {};
            data.permissions.forEach(perm => {
                if (!permissionsByModule[perm.module]) {
                    permissionsByModule[perm.module] = [];
                }
                permissionsByModule[perm.module].push(perm);
            });
            
            let html = `
                <h3 class="text-xl font-bold text-gray-800 mb-4">
                    Assign Permissions for: ${roleName}
                </h3>
                <form method="POST" action="{{ route('permissions.assign.submit') }}">
                    @csrf
                    <input type="hidden" name="role_id" value="${roleId}">
                    <div class="space-y-6 max-h-96 overflow-y-auto">
            `;
            
            for (const [module, perms] of Object.entries(permissionsByModule)) {
                html += `
                    <div class="border rounded-lg p-4">
                        <h4 class="font-bold text-gray-700 mb-3 capitalize">${module}</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                `;
                
                perms.forEach(perm => {
                    const isChecked = data.role_permissions.includes(perm.id);
                    html += `
                        <label class="flex items-center space-x-2 p-2 rounded hover:bg-gray-50 cursor-pointer">
                            <input type="checkbox" name="permissions[]" value="${perm.id}" 
                                   ${isChecked ? 'checked' : ''}
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <div>
                                <span class="text-sm text-gray-700">${perm.display_name}</span>
                                <p class="text-xs text-gray-500">${perm.name}</p>
                            </div>
                        </label>
                    `;
                });
                
                html += `
                        </div>
                    </div>
                `;
            }
            
            html += `
                    </div>
                    <div class="flex justify-end space-x-3 mt-6 pt-6 border-t">
                        <button type="reset" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                            Reset
                        </button>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg shadow-md">
                            <i class="fas fa-save mr-2"></i>
                            Save Permissions
                        </button>
                    </div>
                </form>
            `;
            
            document.getElementById('permission-form').innerHTML = html;
        });
}
</script>
@endsection