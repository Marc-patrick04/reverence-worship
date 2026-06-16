@extends('layouts.app')

@section('title', 'Permission Manager')

@section('content')
<div class="max-w-full mx-auto px-4">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Role & Permission Manager</h1>
        <p class="text-gray-500 text-sm mt-1">Manage user roles and assign page permissions</p>
    </div>

    <!-- Two Column Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Column 1: Roles List -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-5 py-4 bg-gradient-to-r from-purple-50 to-white border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="font-bold text-gray-800">Roles</h2>
                        <p class="text-xs text-gray-500">Manage user roles</p>
                    </div>
                    <button onclick="openRoleModal()" class="bg-purple-600 hover:bg-purple-700 text-white px-3 py-1.5 rounded-lg text-xs flex items-center gap-1">
                        <i class="fas fa-plus"></i> New Role
                    </button>
                </div>
            </div>
            <div class="p-4">
                <div class="space-y-2 max-h-[600px] overflow-y-auto">
                    @forelse($roles as $role)
                    <div class="border rounded-lg p-3 hover:bg-gray-50 transition">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="font-semibold text-gray-800">{{ $role->display_name }}</h3>
                                <p class="text-xs text-gray-500">{{ $role->name }}</p>
                                @if($role->description)
                                <p class="text-xs text-gray-400 mt-1">{{ $role->description }}</p>
                                @endif
                            </div>
                            <div class="flex gap-2">
                                <button onclick="editRole({{ $role->id }})" class="text-blue-500 hover:text-blue-700">
                                    <i class="fas fa-edit"></i>
                                </button>
                                @if($role->name !== 'super-admin')
                                <button onclick="deleteRole({{ $role->id }}, '{{ $role->display_name }}')" class="text-red-500 hover:text-red-700">
                                    <i class="fas fa-trash"></i>
                                </button>
                                @endif
                            </div>
                        </div>
                        <div class="mt-2">
                            <button onclick="assignPermissionsToRole({{ $role->id }}, '{{ $role->display_name }}')" class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded hover:bg-blue-200 transition">
                                <i class="fas fa-key"></i> Assign Permissions
                            </button>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8 text-gray-400 text-sm">No roles created yet</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Column 2: Permissions Assignment -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-5 py-4 bg-gradient-to-r from-green-50 to-white border-b border-gray-200">
                <div>
                    <h2 class="font-bold text-gray-800">Assign Permissions</h2>
                    <p class="text-xs text-gray-500">Select a role to assign page permissions</p>
                </div>
            </div>
            <div class="p-4">
                <div id="permissionsAssignmentArea">
                    <div class="text-center py-8 text-gray-400 text-sm">
                        <i class="fas fa-hand-pointer text-3xl mb-2 block"></i>
                        Click "Assign Permissions" on any role to set permissions
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ==================== MODALS ==================== -->

<!-- Role Modal -->
<div id="roleModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center">
    <div class="bg-white rounded-xl w-full max-w-md p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 id="roleModalTitle" class="text-lg font-bold">Create New Role</h3>
            <button onclick="closeModal('roleModal')" class="text-gray-400 hover:text-gray-600">&times;</button>
        </div>
        <form id="roleForm">
            @csrf
            <input type="hidden" name="_method" id="roleMethod" value="POST">
            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium mb-1">Role Name *</label>
                    <input type="text" name="name" id="roleName" required class="w-full px-3 py-2 border rounded-lg">
                    <p class="text-xs text-gray-400">Example: admin, editor, viewer (use lowercase, no spaces)</p>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Display Name *</label>
                    <input type="text" name="display_name" id="roleDisplayName" required class="w-full px-3 py-2 border rounded-lg">
                    <p class="text-xs text-gray-400">Example: Administrator, Editor, Viewer</p>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Description</label>
                    <textarea name="description" id="roleDescription" rows="2" class="w-full px-3 py-2 border rounded-lg"></textarea>
                    <p class="text-xs text-gray-400">What this role can do (optional)</p>
                </div>
            </div>
            <div class="flex justify-end gap-2 mt-5">
                <button type="button" onclick="closeModal('roleModal')" class="px-4 py-2 border rounded-lg">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-lg">Save Role</button>
            </div>
        </form>
    </div>
</div>

<!-- Assign Permissions Modal -->
<div id="assignPermissionsModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center">
    <div class="bg-white rounded-xl w-full max-w-2xl p-6 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4 pb-3 border-b">
            <h3 id="assignModalTitle" class="text-lg font-bold">Assign Permissions</h3>
            <button onclick="closeModal('assignPermissionsModal')" class="text-gray-400 hover:text-gray-600">&times;</button>
        </div>
        <div id="assignPermissionsContent"></div>
        <div class="flex justify-end gap-2 mt-5 pt-3 border-t">
            <button onclick="closeModal('assignPermissionsModal')" class="px-4 py-2 border rounded-lg">Cancel</button>
            <button id="saveRolePermissionsBtn" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Save Permissions</button>
        </div>
    </div>
</div>

<script>
// Store current role being edited
let currentEditingRoleId = null;
let currentEditingRoleName = null;
let pagesData = @json($pages);
let featuresData = @json($allFeatures);
let roleAssignments = @json($allAssignments);

// ==================== ROLE CRUD ====================
function openRoleModal() {
    document.getElementById('roleModalTitle').innerText = 'Create New Role';
    document.getElementById('roleForm').action = '/permission-manager/role/store';
    document.getElementById('roleMethod').value = 'POST';
    document.getElementById('roleName').value = '';
    document.getElementById('roleDisplayName').value = '';
    document.getElementById('roleDescription').value = '';
    document.getElementById('roleModal').classList.remove('hidden');
}

function editRole(id) {
    fetch(`/permission-manager/role/${id}/edit`)
        .then(res => res.json())
        .then(data => {
            document.getElementById('roleModalTitle').innerText = 'Edit Role';
            document.getElementById('roleForm').action = `/permission-manager/role/${id}`;
            document.getElementById('roleMethod').value = 'PUT';
            document.getElementById('roleName').value = data.name;
            document.getElementById('roleDisplayName').value = data.display_name;
            document.getElementById('roleDescription').value = data.description || '';
            document.getElementById('roleModal').classList.remove('hidden');
        })
        .catch(error => console.error('Error:', error));
}

function deleteRole(id, name) {
    if (confirm(`Delete role "${name}"? This will remove all permissions for this role.`)) {
        fetch(`/permission-manager/role/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
        }).then(() => location.reload());
    }
}

// ==================== ASSIGN PERMISSIONS TO ROLE ====================
function assignPermissionsToRole(roleId, roleName) {
    currentEditingRoleId = roleId;
    currentEditingRoleName = roleName;
    
    document.getElementById('assignModalTitle').innerHTML = `Assign Permissions to "${roleName}"`;
    
    const assignedFeatures = roleAssignments[roleId] || [];
    const assignedFeatureIds = assignedFeatures.map(a => a.feature_id);
    
    let html = '<div class="space-y-4">';
    
    pagesData.forEach(page => {
        const pageFeatures = featuresData.filter(f => f.page_id === page.id);
        if (pageFeatures.length === 0) return;
        
        // Check if all features are assigned
        const allAssigned = pageFeatures.every(f => assignedFeatureIds.includes(f.id));
        
        html += `
            <div class="border rounded-lg p-3">
                <div class="flex items-center gap-2 mb-3 pb-2 border-b">
                    <i class="fas ${page.icon} text-blue-600"></i>
                    <span class="font-semibold text-gray-800">${page.display_name}</span>
                    <label class="ml-auto flex items-center gap-2 text-sm cursor-pointer">
                        <input type="checkbox" class="select-all-page" data-page-id="${page.id}" ${allAssigned ? 'checked' : ''}>
                        <span class="text-xs text-gray-500">Select All</span>
                    </label>
                </div>
                <div class="grid grid-cols-2 gap-2">
        `;
        
        // Sort features in order: view, create, edit, delete
        const sortedFeatures = [...pageFeatures].sort((a, b) => {
            const order = { view: 1, create: 2, edit: 3, delete: 4 };
            return (order[a.name] || 5) - (order[b.name] || 5);
        });
        
        sortedFeatures.forEach(feature => {
            const isChecked = assignedFeatureIds.includes(feature.id);
            let icon = '';
            let color = '';
            
            if (feature.name === 'view') {
                icon = 'fa-eye';
                color = 'text-green-600';
            } else if (feature.name === 'create') {
                icon = 'fa-plus-circle';
                color = 'text-blue-600';
            } else if (feature.name === 'edit') {
                icon = 'fa-edit';
                color = 'text-yellow-600';
            } else if (feature.name === 'delete') {
                icon = 'fa-trash-alt';
                color = 'text-red-600';
            } else {
                icon = 'fa-tag';
                color = 'text-gray-600';
            }
            
            html += `
                <label class="flex items-center gap-2 text-sm cursor-pointer p-2 rounded hover:bg-gray-50 transition">
                    <input type="checkbox" class="feature-checkbox" data-feature-id="${feature.id}" data-page-id="${page.id}" ${isChecked ? 'checked' : ''}>
                    <i class="fas ${icon} ${color} w-4"></i>
                    <span>${feature.display_name}</span>
                </label>
            `;
        });
        
        html += `
                </div>
            </div>
        `;
    });
    
    html += '</div>';
    
    document.getElementById('assignPermissionsContent').innerHTML = html;
    document.getElementById('assignPermissionsModal').classList.remove('hidden');
    
    // Add select all functionality
    document.querySelectorAll('.select-all-page').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const pageId = this.dataset.pageId;
            const pageCheckboxes = document.querySelectorAll(`.feature-checkbox[data-page-id="${pageId}"]`);
            pageCheckboxes.forEach(cb => cb.checked = this.checked);
        });
    });
}

// Save role permissions
document.getElementById('saveRolePermissionsBtn').addEventListener('click', function() {
    if (!currentEditingRoleId) return;
    
    const assignments = [];
    document.querySelectorAll('.feature-checkbox:checked').forEach(cb => {
        assignments.push({
            page_id: parseInt(cb.dataset.pageId),
            feature_id: parseInt(cb.dataset.featureId)
        });
    });
    
    fetch('/permission-manager/save-assignments', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ 
            role_id: currentEditingRoleId, 
            assignments: assignments 
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert(`Permissions saved for "${currentEditingRoleName}" successfully!`);
            closeModal('assignPermissionsModal');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Network error. Please try again.');
    });
});

// ==================== FORM SUBMISSIONS ====================
document.getElementById('roleForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    fetch(this.action, { 
        method: 'POST', 
        body: formData, 
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } 
    })
    .then(res => res.json())
    .then(data => { 
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => alert('Error: ' + error.message));
});

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        document.querySelectorAll('.modal:not(.hidden)').forEach(modal => {
            modal.classList.add('hidden');
        });
    }
});
</script>
@endsection