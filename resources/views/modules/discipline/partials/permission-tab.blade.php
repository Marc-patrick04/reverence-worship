<div class="space-y-6">
    @php
        $canCreate = auth()->check() && auth()->user()->canAccess('discipline', 'create');
        $canApprove = auth()->check() && auth()->user()->canAccess('discipline', 'approve-permission');
        $canReject = auth()->check() && auth()->user()->canAccess('discipline', 'reject-permission');
        $canDelete = auth()->check() && auth()->user()->canAccess('discipline', 'delete');
        $canView = auth()->check() && auth()->user()->canAccess('discipline', 'view-permissions');
    @endphp

    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h3 class="text-2xl font-bold text-gray-800">Permission Management</h3>
            <p class="text-sm text-gray-500 mt-1">Review and manage user permission requests</p>
        </div>
        @if($canCreate)
        <button onclick="openPermissionModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm flex items-center gap-2 shadow-sm transition">
            <i class="fas fa-plus-circle"></i> New Request
        </button>
        @endif
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-2xl font-bold text-gray-800" id="total_requests">0</p>
                    <p class="text-xs text-gray-500">Total Requests</p>
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-envelope text-blue-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-2xl font-bold text-yellow-600" id="pending_requests">0</p>
                    <p class="text-xs text-gray-500">Pending</p>
                </div>
                <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-2xl font-bold text-green-600" id="approved_requests">0</p>
                    <p class="text-xs text-gray-500">Approved</p>
                </div>
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-2xl font-bold text-red-600" id="rejected_requests">0</p>
                    <p class="text-xs text-gray-500">Rejected</p>
                </div>
                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-times-circle text-red-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters - Only show if user can view -->
    @if($canView)
    <div class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs text-gray-600 mb-1">Search</label>
            <div class="relative">
                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" id="permission_search" placeholder="Search by name or reason..." 
                       class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
        </div>
        <div class="w-40">
            <label class="block text-xs text-gray-600 mb-1">Status</label>
            <select id="permission_status_filter" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white focus:ring-2 focus:ring-blue-500">
                <option value="all">All Status</option>
                <option value="pending">Pending</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
            </select>
        </div>
        <button onclick="filterPermissions()" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm transition">
            <i class="fas fa-search mr-1"></i> Filter
        </button>
    </div>
    @endif

    <!-- Permissions List -->
    <div id="permissions-list" class="space-y-3">
        <div class="text-center py-12">
            <i class="fas fa-spinner fa-spin text-2xl text-gray-400 mb-2"></i>
            <p class="text-gray-500">Loading requests...</p>
        </div>
    </div>
</div>

<script>
// Make functions available globally
window.openPermissionModal = openPermissionModal;
window.filterPermissions = filterPermissions;
window.approvePermission = approvePermission;
window.rejectPermission = rejectPermission;
window.deletePermission = deletePermission;

let currentPermissions = [];

@php
    $canCreate = auth()->check() && auth()->user()->canAccess('discipline', 'create');
    $canApprove = auth()->check() && auth()->user()->canAccess('discipline', 'approve-permission');
    $canReject = auth()->check() && auth()->user()->canAccess('discipline', 'reject-permission');
    $canDelete = auth()->check() && auth()->user()->canAccess('discipline', 'delete');
    $canView = auth()->check() && auth()->user()->canAccess('discipline', 'view-permissions');
@endphp

function openPermissionModal(permissionId = null) {
    @if(!$canCreate)
        alert('You do not have permission to create permission requests.');
        return;
    @endif
    
    const modal = document.getElementById('permissionModal');
    if (!modal) {
        alert('Form not ready. Please refresh.');
        return;
    }
    
    if (permissionId) {
        fetch(`/discipline/permission/${permissionId}/edit`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('permission_modal_title').textContent = 'Edit Permission Request';
                document.getElementById('permission_id').value = data.permission.id;
                document.getElementById('permission_user_id').value = data.permission.user_id;
                document.getElementById('permission_type').value = data.permission.type;
                document.getElementById('permission_start_date').value = data.permission.start_date;
                document.getElementById('permission_end_date').value = data.permission.end_date;
                document.getElementById('permission_reason').value = data.permission.reason;
                modal.classList.remove('hidden');
            }
        });
    } else {
        document.getElementById('permission_modal_title').textContent = 'New Permission Request';
        document.getElementById('permission_id').value = '';
        document.getElementById('permission_user_id').value = '';
        document.getElementById('permission_type').value = '';
        document.getElementById('permission_start_date').value = new Date().toISOString().split('T')[0];
        document.getElementById('permission_end_date').value = new Date().toISOString().split('T')[0];
        document.getElementById('permission_reason').value = '';
        modal.classList.remove('hidden');
    }
}

function filterPermissions() {
    @if(!$canView)
        alert('You do not have permission to view permission requests.');
        return;
    @endif
    
    const status = document.getElementById('permission_status_filter')?.value || 'all';
    const search = document.getElementById('permission_search')?.value || '';
    
    let url = `/discipline/permission?status=${status}`;
    if (search) {
        url += `&search=${encodeURIComponent(search)}`;
    }
    
    fetch(url, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            currentPermissions = data.permissions;
            renderPermissionsList(currentPermissions);
            updateStats(currentPermissions);
        }
    })
    .catch(error => console.error('Error loading permissions:', error));
}

function updateStats(permissions) {
    const total = permissions.length;
    const pending = permissions.filter(p => p.status === 'pending').length;
    const approved = permissions.filter(p => p.status === 'approved').length;
    const rejected = permissions.filter(p => p.status === 'rejected').length;
    
    document.getElementById('total_requests').textContent = total;
    document.getElementById('pending_requests').textContent = pending;
    document.getElementById('approved_requests').textContent = approved;
    document.getElementById('rejected_requests').textContent = rejected;
}

function renderPermissionsList(permissions) {
    const container = document.getElementById('permissions-list');
    
    if (!permissions || permissions.length === 0) {
        container.innerHTML = `
            <div class="text-center py-12 bg-gray-50 rounded-xl">
                <i class="fas fa-inbox text-4xl text-gray-300 mb-3"></i>
                <p class="text-gray-500">No permission requests found</p>
                @if($canCreate)
                <button onclick="openPermissionModal()" class="mt-3 text-blue-600 hover:text-blue-700 text-sm">
                    <i class="fas fa-plus"></i> Create request
                </button>
                @endif
            </div>
        `;
        return;
    }
    
    container.innerHTML = permissions.map(perm => {
        const startDate = new Date(perm.start_date);
        const endDate = new Date(perm.end_date);
        const totalDays = Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24)) + 1;
        
        let statusConfig = {
            class: '',
            icon: '',
            text: ''
        };
        
        switch(perm.status) {
            case 'approved':
                statusConfig = { class: 'bg-green-100 text-green-700', icon: 'fa-check-circle', text: 'Approved' };
                break;
            case 'rejected':
                statusConfig = { class: 'bg-red-100 text-red-700', icon: 'fa-times-circle', text: 'Rejected' };
                break;
            default:
                statusConfig = { class: 'bg-yellow-100 text-yellow-700', icon: 'fa-clock', text: 'Pending' };
        }
        
        return `
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition">
                <div class="p-4">
                    <div class="flex flex-wrap justify-between items-start gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-gray-500"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-800">${escapeHtml(perm.user_name)}</h4>
                                <div class="flex items-center gap-3 text-xs text-gray-400 mt-0.5">
                                    <span><i class="far fa-calendar-alt mr-1"></i> ${formatDate(perm.created_at)}</span>
                                    <span><i class="far fa-hourglass mr-1"></i> ${totalDays} day${totalDays !== 1 ? 's' : ''}</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-1 rounded-full text-xs font-medium ${statusConfig.class}">
                                <i class="fas ${statusConfig.icon} mr-1"></i> ${statusConfig.text}
                            </span>
                            ${perm.status === 'pending' ? `
                                <div class="flex gap-1">
                                    @if($canApprove)
                                    <button onclick="approvePermission(${perm.id})" class="p-1.5 text-green-600 hover:bg-green-50 rounded-lg transition" title="Approve">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    @endif
                                    @if($canReject)
                                    <button onclick="rejectPermission(${perm.id})" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition" title="Reject">
                                        <i class="fas fa-times"></i>
                                    </button>
                                    @endif
                                </div>
                            ` : ''}
                            @if($canDelete)
                            <button onclick="deletePermission(${perm.id})" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Delete">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                            @endif
                        </div>
                    </div>
                    
                    <div class="mt-3 pt-3 border-t border-gray-100">
                        <div class="flex flex-wrap gap-4 text-sm">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-calendar-plus text-gray-400 text-xs"></i>
                                <span class="text-gray-500">From:</span>
                                <span class="font-medium text-gray-700">${formatDate(perm.start_date)}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="fas fa-calendar-times text-gray-400 text-xs"></i>
                                <span class="text-gray-500">To:</span>
                                <span class="font-medium text-gray-700">${formatDate(perm.end_date)}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="fas fa-tag text-gray-400 text-xs"></i>
                                <span class="text-gray-500">Type:</span>
                                <span class="font-medium text-gray-700">${escapeHtml(perm.type || 'General')}</span>
                            </div>
                        </div>
                        <div class="mt-3 bg-gray-50 rounded-lg p-3">
                            <p class="text-sm text-gray-600">
                                <i class="fas fa-comment mr-2 text-gray-400"></i>
                                ${escapeHtml(perm.reason)}
                            </p>
                        </div>
                        ${perm.status === 'approved' && perm.approved_by_name ? `
                            <div class="mt-2 text-xs text-gray-500 flex items-center gap-2">
                                <i class="fas fa-user-check text-green-500"></i>
                                <span>Approved by ${escapeHtml(perm.approved_by_name)} on ${formatDate(perm.approved_at)}</span>
                            </div>
                        ` : ''}
                        ${perm.status === 'rejected' && perm.rejection_reason ? `
                            <div class="mt-2 text-xs text-red-600 flex items-center gap-2">
                                <i class="fas fa-exclamation-triangle"></i>
                                <span>Rejection reason: ${escapeHtml(perm.rejection_reason)}</span>
                            </div>
                        ` : ''}
                    </div>
                </div>
            </div>
        `;
    }).join('');
}

function formatDate(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric' 
    });
}

function approvePermission(id) {
    @if(!$canApprove)
        alert('You do not have permission to approve requests.');
        return;
    @endif
    
    if (confirm('Approve this permission request?')) {
        fetch(`/discipline/permission/${id}`, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ status: 'approved' })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                filterPermissions();
            } else {
                alert('Error: ' + (data.message || 'Failed to approve'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error approving request');
        });
    }
}

function rejectPermission(id) {
    @if(!$canReject)
        alert('You do not have permission to reject requests.');
        return;
    @endif
    
    const reason = prompt('Enter rejection reason:');
    if (reason !== null) {
        if (reason.trim() === '') {
            alert('Please provide a reason for rejection');
            return;
        }
        fetch(`/discipline/permission/${id}`, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ status: 'rejected', rejection_reason: reason })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                filterPermissions();
            } else {
                alert('Error: ' + (data.message || 'Failed to reject'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error rejecting request');
        });
    }
}

function deletePermission(id) {
    @if(!$canDelete)
        alert('You do not have permission to delete requests.');
        return;
    @endif
    
    if (confirm('Are you sure you want to delete this permission request?')) {
        fetch(`/discipline/permission/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                filterPermissions();
            } else {
                alert('Error: ' + (data.message || 'Failed to delete'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting request');
        });
    }
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Load initial data - only if user has view permission
@if($canView)
document.addEventListener('DOMContentLoaded', function() {
    filterPermissions();
});
@endif

// Event listeners - only if user has view permission
@if($canView)
document.getElementById('permission_search')?.addEventListener('keyup', function(e) {
    if (e.key === 'Enter') filterPermissions();
});
document.getElementById('permission_status_filter')?.addEventListener('change', filterPermissions);
@endif
</script>