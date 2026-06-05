<div>
    <div class="mb-6">
        <h3 class="text-xl font-bold text-gray-800 mb-1">Permission requests Management</h3>
        <p class="text-gray-500 text-sm">Review and manage user Permission requests</p>
    </div>
    
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-5 text-white">
            <div class="flex items-center justify-between mb-2">
                <i class="fas fa-envelope-open-text text-2xl opacity-80"></i>
                <span class="text-xs opacity-80">Total</span>
            </div>
            <p class="text-3xl font-bold" id="total_requests">0</p>
            <p class="text-sm mt-1 opacity-90">Total Requests</p>
        </div>
        
        <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl shadow-lg p-5 text-white">
            <div class="flex items-center justify-between mb-2">
                <i class="fas fa-clock text-2xl opacity-80"></i>
                <span class="text-xs opacity-80">Pending</span>
            </div>
            <p class="text-3xl font-bold" id="pending_requests">0</p>
            <p class="text-sm mt-1 opacity-90">Pending</p>
        </div>
        
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-5 text-white">
            <div class="flex items-center justify-between mb-2">
                <i class="fas fa-check-circle text-2xl opacity-80"></i>
                <span class="text-xs opacity-80">Approved</span>
            </div>
            <p class="text-3xl font-bold" id="approved_requests">0</p>
            <p class="text-sm mt-1 opacity-90">Approved</p>
        </div>
        
        <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl shadow-lg p-5 text-white">
            <div class="flex items-center justify-between mb-2">
                <i class="fas fa-times-circle text-2xl opacity-80"></i>
                <span class="text-xs opacity-80">Rejected</span>
            </div>
            <p class="text-3xl font-bold" id="rejected_requests">0</p>
            <p class="text-sm mt-1 opacity-90">Rejected</p>
        </div>
    </div>
    
    <!-- Search and Filter Bar -->
    <div class="bg-white rounded-xl shadow-md p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    <input type="text" id="permission_search" placeholder="Search by name, ID, or reason..." 
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select id="permission_status_filter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="all">All Statuses</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>
        </div>
    </div>
    
    <!-- Permission Cards List -->
    <div id="permissions-list" class="space-y-4">
        <div class="text-center py-12">
            <i class="fas fa-spinner fa-spin text-3xl text-gray-400 mb-3"></i>
            <p class="text-gray-500">Loading permission requests...</p>
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

function openPermissionModal(permissionId = null) {
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
                document.getElementById('permissionModal').classList.remove('hidden');
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
        document.getElementById('permissionModal').classList.remove('hidden');
    }
}

function filterPermissions() {
    const status = document.getElementById('permission_status_filter').value;
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
            updatePermissionsList(data.permissions);
            updateStats(data.permissions);
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

function updatePermissionsList(permissions) {
    const container = document.getElementById('permissions-list');
    
    if (!permissions || permissions.length === 0) {
        container.innerHTML = `
            <div class="text-center py-12 bg-gray-50 rounded-xl">
                <i class="fas fa-inbox text-5xl text-gray-300 mb-3"></i>
                <p class="text-gray-500">No permission requests found</p>
                <button onclick="openPermissionModal()" class="mt-3 text-blue-600 hover:text-blue-700 text-sm">
                    <i class="fas fa-plus"></i> Create your first request
                </button>
            </div>
        `;
        return;
    }
    
    container.innerHTML = permissions.map(perm => {
        const startDate = new Date(perm.start_date);
        const endDate = new Date(perm.end_date);
        const totalDays = Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24)) + 1;
        
        let statusColor = '';
        let statusIcon = '';
        let statusBg = '';
        
        switch(perm.status) {
            case 'approved':
                statusColor = 'text-green-600';
                statusIcon = 'fa-check-circle';
                statusBg = 'bg-green-50 border-green-200';
                break;
            case 'rejected':
                statusColor = 'text-red-600';
                statusIcon = 'fa-times-circle';
                statusBg = 'bg-red-50 border-red-200';
                break;
            case 'pending':
                statusColor = 'text-yellow-600';
                statusIcon = 'fa-clock';
                statusBg = 'bg-yellow-50 border-yellow-200';
                break;
            default:
                statusColor = 'text-gray-600';
                statusIcon = 'fa-question-circle';
                statusBg = 'bg-gray-50 border-gray-200';
        }
        
        return `
            <div class="bg-white rounded-xl shadow-md border ${statusBg} overflow-hidden hover:shadow-lg transition">
                <div class="p-5">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-gray-500"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-800">${escapeHtml(perm.user_name)}</h4>
                                    <p class="text-xs text-gray-400 font-mono">ID: ${perm.user_id || 'N/A'}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4 text-sm text-gray-500 mt-2">
                                <span><i class="fas fa-calendar-alt mr-1"></i> Submission: ${formatDate(perm.created_at)}</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="px-3 py-1 rounded-full text-sm font-medium ${statusColor} bg-white shadow-sm">
                                <i class="fas ${statusIcon} mr-1"></i> ${perm.status.charAt(0).toUpperCase() + perm.status.slice(1)}
                            </span>
                            ${perm.status === 'pending' ? `
                                <div class="flex gap-1">
                                    <button onclick="approvePermission(${perm.id})" class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition" title="Approve">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button onclick="rejectPermission(${perm.id})" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition" title="Reject">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            ` : ''}
                            <button onclick="deletePermission(${perm.id})" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div class="flex items-center gap-2 text-sm">
                            <i class="fas fa-calendar-plus text-gray-400"></i>
                            <span class="text-gray-600">Start Date:</span>
                            <span class="font-medium">${formatDate(perm.start_date)}</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm">
                            <i class="fas fa-calendar-times text-gray-400"></i>
                            <span class="text-gray-600">End Date:</span>
                            <span class="font-medium">${formatDate(perm.end_date)}</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm">
                            <i class="fas fa-hourglass-half text-gray-400"></i>
                            <span class="text-gray-600">Total Days:</span>
                            <span class="font-medium">${totalDays} day${totalDays !== 1 ? 's' : ''}</span>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 rounded-lg p-3 mb-4">
                        <p class="text-sm text-gray-600">
                            <i class="fas fa-comment mr-2 text-gray-400"></i>
                            <strong>Reason:</strong> ${escapeHtml(perm.reason)}
                        </p>
                    </div>
                    
                    ${perm.status === 'approved' && perm.approved_by_name ? `
                        <div class="text-sm text-gray-500 border-t pt-3 mt-2">
                            <i class="fas fa-user-check mr-1"></i>
                            Approved by: ${escapeHtml(perm.approved_by_name)} on ${formatDate(perm.approved_at)}
                        </div>
                    ` : ''}
                    
                    ${perm.status === 'rejected' && perm.rejection_reason ? `
                        <div class="text-sm text-red-600 border-t pt-3 mt-2">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            Rejection reason: ${escapeHtml(perm.rejection_reason)}
                        </div>
                    ` : ''}
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
                alert('Error approving request: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error approving request');
        });
    }
}

function rejectPermission(id) {
    const reason = prompt('Enter rejection reason:');
    if (reason) {
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
                alert('Error rejecting request: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error rejecting request');
        });
    }
}

function deletePermission(id) {
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
                alert('Error deleting permission request: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting permission request');
        });
    }
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Load initial data
setTimeout(() => {
    filterPermissions();
}, 100);

// Add event listeners for search and filter
document.getElementById('permission_search')?.addEventListener('keyup', function(e) {
    filterPermissions();
});
document.getElementById('permission_status_filter')?.addEventListener('change', function() {
    filterPermissions();
});
</script>