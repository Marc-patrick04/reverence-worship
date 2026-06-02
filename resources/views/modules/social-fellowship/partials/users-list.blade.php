<div class="bg-white rounded-xl shadow-md p-6">
    
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Users</h2>
            <p class="text-gray-500 text-sm mt-1">Manage family members and their roles</p>
        </div>
        <button onclick="openAddUserModal()" class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded-lg text-sm flex items-center gap-2 transition">
            <i class="fas fa-user-plus"></i> Add User to Family
        </button>
    </div>
    
    <!-- Search Bar -->
    <div class="mb-6">
        <div class="relative">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input type="text" id="searchUsers" placeholder="Search by name, email, or family..." 
                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-gray-500 focus:border-gray-500">
        </div>
    </div>
    
    <!-- Users Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">USER</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">FAMILY</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">RESIDENCE</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">STATUS</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ACTIONS</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200" id="usersTableBody">
                @forelse($allUsers ?? [] as $user)
                <tr class="hover:bg-gray-50 transition user-row" 
                    data-name="{{ strtolower($user->name) }}"
                    data-email="{{ strtolower($user->email) }}"
                    data-family="{{ strtolower($user->family_name ?? 'unassigned') }}">
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gray-600 rounded-full flex items-center justify-center">
                                <span class="text-white text-sm font-bold">{{ substr($user->name, 0, 2) }}</span>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">{{ $user->name }}</p>
                                <p class="text-xs text-gray-500">{{ $user->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        @if($user->family_name)
                            <div>
                                <p class="text-sm font-medium text-gray-800">{{ $user->family_name }}</p>
                                <p class="text-xs text-gray-500">Role: <span class="font-medium">{{ ucfirst($user->role ?? 'member') }}</span></p>
                            </div>
                        @else
                            <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-500">
                                <i class="fas fa-user-clock mr-1"></i> Unassigned
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-600">
                        {{ $user->residence ?? 'Not specified' }}
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">
                            <i class="fas fa-circle text-xs mr-1"></i> Active
                        </span>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        <div class="flex gap-2">
                            <button onclick="viewUserDetails({{ $user->id }})" class="text-gray-600 hover:text-gray-900" title="View Details">
                                <i class="fas fa-eye"></i>
                            </button>
                            @if($user->family_name)
                                <button onclick="removeFromFamily({{ $user->id }}, {{ $user->family_id }})" class="text-red-500 hover:text-red-700" title="Remove from Family">
                                    <i class="fas fa-user-minus"></i>
                                </button>
                            @else
                                <button onclick="assignToFamily({{ $user->id }})" class="text-green-600 hover:text-green-800" title="Assign to Family">
                                    <i class="fas fa-plus-circle"></i>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                        <i class="fas fa-users fa-3x mb-3 text-gray-300"></i>
                        <p>No users found</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination - Only show if using paginate -->
    @if(method_exists($allUsers, 'hasPages') && $allUsers->hasPages())
    <div class="mt-4">
        {{ $allUsers->links() }}
    </div>
    @endif
</div>

<!-- Rest of the modals and scripts remain the same -->
<script>
// Search functionality for main table
document.getElementById('searchUsers')?.addEventListener('keyup', function() {
    const searchTerm = this.value.toLowerCase();
    const rows = document.querySelectorAll('.user-row');
    
    rows.forEach(row => {
        const name = row.dataset.name || '';
        const email = row.dataset.email || '';
        const family = row.dataset.family || '';
        
        if (name.includes(searchTerm) || email.includes(searchTerm) || family.includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});

// Search users in the list
document.getElementById('searchUserInput')?.addEventListener('keyup', function() {
    const searchTerm = this.value.toLowerCase();
    const userItems = document.querySelectorAll('.user-select-item');
    
    userItems.forEach(item => {
        const name = item.dataset.userName?.toLowerCase() || '';
        const email = item.dataset.userEmail?.toLowerCase() || '';
        
        if (name.includes(searchTerm) || email.includes(searchTerm)) {
            item.style.display = '';
        } else {
            item.style.display = 'none';
        }
    });
});

// Select user from list
function selectUser(userId, userName, userEmail) {
    // Remove selected class from all items
    document.querySelectorAll('.user-select-item').forEach(item => {
        item.classList.remove('bg-gray-200', 'border-gray-400');
    });
    
    // Add selected class to clicked item
    const selectedItem = document.querySelector(`.user-select-item[data-user-id="${userId}"]`);
    if (selectedItem) {
        selectedItem.classList.add('bg-gray-200', 'border-gray-400');
    }
    
    // Set hidden input value
    document.getElementById('selectedUserId').value = userId;
    
    // Show selected user display
    document.getElementById('selectedUserDisplay').classList.remove('hidden');
    document.getElementById('selectedUserName').innerHTML = `${userName} (${userEmail})`;
    
    // Enable submit button
    const submitBtn = document.getElementById('submitAddUserBtn');
    submitBtn.disabled = false;
    submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
    submitBtn.classList.add('opacity-100', 'cursor-pointer');
}

// Clear selected user
function clearSelectedUser() {
    document.getElementById('selectedUserId').value = '';
    document.getElementById('selectedUserDisplay').classList.add('hidden');
    
    // Remove selected class from all items
    document.querySelectorAll('.user-select-item').forEach(item => {
        item.classList.remove('bg-gray-200', 'border-gray-400');
    });
    
    // Disable submit button
    const submitBtn = document.getElementById('submitAddUserBtn');
    submitBtn.disabled = true;
    submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
    submitBtn.classList.remove('opacity-100', 'cursor-pointer');
}

// Reset modal when opened
function openAddUserModal() {
    document.getElementById('addUserModal').classList.remove('hidden');
    // Reset form
    document.getElementById('addUserForm').reset();
    document.getElementById('searchUserInput').value = '';
    document.getElementById('selectedUserId').value = '';
    document.getElementById('selectedUserDisplay').classList.add('hidden');
    
    // Disable submit button
    const submitBtn = document.getElementById('submitAddUserBtn');
    submitBtn.disabled = true;
    submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
    submitBtn.classList.remove('opacity-100', 'cursor-pointer');
    
    // Show all user items
    document.querySelectorAll('.user-select-item').forEach(item => {
        item.style.display = '';
        item.classList.remove('bg-gray-200', 'border-gray-400');
    });
}

// Assign to Family (for unassigned users)
function assignToFamily(userId) {
    document.getElementById('assignUserId').value = userId;
    document.getElementById('assignModal').classList.remove('hidden');
}

// Attach click events to user list items
document.querySelectorAll('.user-select-item').forEach(item => {
    item.addEventListener('click', function() {
        const userId = this.dataset.userId;
        const userName = this.dataset.userName;
        const userEmail = this.dataset.userEmail;
        selectUser(userId, userName, userEmail);
    });
});

// Add User Form Submission
document.getElementById('addUserForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const userId = document.getElementById('selectedUserId').value;
    const familyId = document.getElementById('selectFamilyId').value;
    
    if (!userId) {
        alert('Please select a user');
        return;
    }
    
    if (!familyId) {
        alert('Please select a family');
        return;
    }
    
    const formData = new FormData();
    formData.append('user_id', userId);
    formData.append('family_id', familyId);
    formData.append('role', document.getElementById('selectRole').value);
    
    fetch(`/social-fellowship/family/${familyId}/member`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeModal('addUserModal');
            showNotification('User added to family successfully!', 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error adding user to family');
    });
});

// Assign Form Submission
document.getElementById('assignForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const userId = document.getElementById('assignUserId').value;
    const familyId = document.getElementById('assignFamilyId').value;
    
    if (!familyId) {
        alert('Please select a family');
        return;
    }
    
    fetch(`/social-fellowship/family/${familyId}/member`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeModal('assignModal');
            showNotification('User assigned to family successfully!', 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error assigning user to family');
    });
});

// View User Details
function viewUserDetails(userId) {
    window.location.href = `/users/${userId}`;
}

// Remove from Family
function removeFromFamily(userId, familyId) {
    document.getElementById('removeModalContent').innerHTML = `
        <p class="mb-4">Are you sure you want to remove this member from the family?</p>
        <div class="flex justify-end gap-3">
            <button onclick="closeModal('removeModal')" class="px-4 py-2 border rounded-lg text-sm">Cancel</button>
            <button onclick="confirmRemove(${userId}, ${familyId})" class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm">Remove</button>
        </div>
    `;
    document.getElementById('removeModal').classList.remove('hidden');
}

function confirmRemove(userId, familyId) {
    fetch(`/social-fellowship/family/${familyId}/member/${userId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeModal('removeModal');
            showNotification('Member removed successfully!', 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error removing member');
    });
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-4 py-2 rounded-lg shadow-lg text-white z-50 ${
        type === 'success' ? 'bg-green-500' : 'bg-red-500'
    }`;
    notification.innerHTML = message;
    document.body.appendChild(notification);
    setTimeout(() => notification.remove(), 3000);
}
</script>