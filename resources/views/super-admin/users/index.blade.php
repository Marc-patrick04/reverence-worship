@extends('layouts.app')

@section('title', 'User Management')
@section('page-title', 'User Management')

@section('content')
<div class="max-w-7xl mx-auto">
    
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">User Management</h1>
        <p class="text-gray-500 text-sm mt-1">Manage users, roles, and permissions</p>
    </div>
    
    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-blue-500 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Total Users</p>
                    <p class="text-3xl font-bold text-blue-600 mt-1">{{ $stats['total'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-users text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-green-500 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Active Users</p>
                    <p class="text-3xl font-bold text-green-600 mt-1">{{ $stats['active'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-user-check text-green-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-purple-500 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Admins</p>
                    <p class="text-3xl font-bold text-purple-600 mt-1">{{ $stats['admins'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-user-shield text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-yellow-500 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Discipline Leaders</p>
                    <p class="text-3xl font-bold text-yellow-600 mt-1">{{ $stats['discipline_leaders'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-gavel text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Filters Bar -->
    <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
        <form method="GET" action="{{ route('users.index') }}" class="flex flex-wrap items-end gap-3">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-medium text-gray-700 mb-1">Search</label>
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input type="text" name="search" placeholder="Search by name or email..." 
                           value="{{ request('search') }}"
                           class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Filter by role...</label>
                <select name="role" class="w-48 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Roles</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ request('role') == $role->id ? 'selected' : '' }}>
                            {{ $role->display_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Filter by status...</label>
                <select name="status" class="w-40 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            
            <div class="flex gap-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm transition flex items-center gap-1">
                    <i class="fas fa-filter text-xs"></i> Filter
                </button>
                <a href="{{ route('users.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm transition flex items-center gap-1">
                    <i class="fas fa-sync-alt text-xs"></i> Reset
                </a>
            </div>
            
            <div class="flex gap-2 ml-auto">
                <button type="button" onclick="openCreateModal()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm transition flex items-center gap-1">
                    <i class="fas fa-user-plus"></i> Add User
                </button>
                <button type="button" onclick="exportToCSV()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm transition flex items-center gap-1">
                    <i class="fas fa-download"></i> Export CSV
                </button>
            </div>
        </form>
    </div>
    
    <!-- Users Table -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">USER</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">EMAIL</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ROLE</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">STATUS</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">REGISTERED</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ACTIONS</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($users as $user)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-blue-700 rounded-full flex items-center justify-center">
                                    <span class="text-white text-sm font-bold">{{ substr($user->name, 0, 2) }}</span>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $user->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $user->phone ?? 'No phone' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $user->email }}</td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-1">
                                @foreach($user->roles as $role)
                                    <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                        {{ $role->display_name }}
                                    </span>
                                @endforeach
                                @if($user->roles->isEmpty())
                                    <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-600">No role</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($user->is_active)
                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                    <i class="fas fa-circle text-xs mr-1"></i> Active
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">
                                    <i class="fas fa-circle text-xs mr-1"></i> Inactive
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $user->created_at ? date('M d, Y', strtotime($user->created_at)) : 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="relative">
                                <button onclick="toggleDropdown({{ $user->id }})" 
                                        class="text-gray-500 hover:text-gray-700 px-3 py-1.5 border border-gray-300 rounded-lg text-sm bg-white hover:bg-gray-50 transition flex items-center gap-1">
                                    Actions <i class="fas fa-chevron-down text-xs"></i>
                                </button>
                                
                                <div id="dropdown-{{ $user->id }}" class="dropdown-menu hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-50">
                                    <div class="py-1">
                                        <button onclick="openViewModal({{ $user->id }})" 
                                                class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 flex items-center gap-2">
                                            <i class="fas fa-eye text-blue-500 w-4"></i> View Details
                                        </button>
                                        <button onclick="openEditModal({{ $user->id }})" 
                                                class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 flex items-center gap-2">
                                            <i class="fas fa-edit text-green-500 w-4"></i> Edit User
                                        </button>
                                        <button onclick="openEditRolesModal({{ $user->id }})" 
                                                class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 flex items-center gap-2">
                                            <i class="fas fa-tags text-purple-500 w-4"></i> Edit Roles
                                        </button>
                                        @if(auth()->id() !== $user->id)
                                            <div class="border-t my-1"></div>
                                            <button onclick="openStatusModal({{ $user->id }}, '{{ addslashes($user->name) }}', {{ $user->is_active ? 'true' : 'false' }})" 
                                                    class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-orange-50 hover:text-orange-600 flex items-center gap-2">
                                                <i class="fas {{ $user->is_active ? 'fa-ban' : 'fa-check-circle' }} w-4 {{ $user->is_active ? 'text-orange-500' : 'text-green-500' }}"></i>
                                                {{ $user->is_active ? 'Deactivate User' : 'Activate User' }}
                                            </button>
                                            <button onclick="openDeleteModal({{ $user->id }}, '{{ addslashes($user->name) }}')" 
                                                    class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center gap-2">
                                                <i class="fas fa-trash-alt text-red-500 w-4"></i> Delete User
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-users fa-3x mb-3 text-gray-300"></i>
                            <p>No users found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($users->hasPages())
        <div class="px-6 py-4 bg-gray-50 border-t">
            {{ $users->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Modals -->
<div id="viewModal" class="modal hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center pb-3 border-b">
            <h3 class="text-xl font-bold text-gray-800">User Details</h3>
            <button onclick="closeModal('viewModal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div id="viewUserContent" class="mt-4"></div>
    </div>
</div>

<div id="editModal" class="modal hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center pb-3 border-b">
            <h3 class="text-xl font-bold text-gray-800">Edit User</h3>
            <button onclick="closeModal('editModal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div id="editUserContent" class="mt-4"></div>
    </div>
</div>

<div id="editRolesModal" class="modal hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-lg shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center pb-3 border-b">
            <h3 class="text-xl font-bold text-gray-800">Edit User Roles</h3>
            <button onclick="closeModal('editRolesModal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div id="editRolesContent" class="mt-4"></div>
    </div>
</div>

<div id="statusModal" class="modal hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center pb-3 border-b">
            <h3 id="statusModalTitle" class="text-xl font-bold text-gray-800">Confirm Action</h3>
            <button onclick="closeModal('statusModal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div id="statusModalContent" class="mt-4"></div>
    </div>
</div>

<div id="deleteModal" class="modal hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center pb-3 border-b">
            <h3 class="text-xl font-bold text-red-600">Delete User</h3>
            <button onclick="closeModal('deleteModal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div id="deleteModalContent" class="mt-4"></div>
    </div>
</div>

<div id="createModal" class="modal hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center pb-3 border-b">
            <h3 class="text-xl font-bold text-gray-800">Create New User</h3>
            <button onclick="closeModal('createModal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div id="createUserContent" class="mt-4"></div>
    </div>
</div>

<script>
function toggleDropdown(userId) {
    const dropdown = document.getElementById(`dropdown-${userId}`);
    document.querySelectorAll('.dropdown-menu').forEach(d => {
        if (d.id !== `dropdown-${userId}`) d.classList.add('hidden');
    });
    if (dropdown) dropdown.classList.toggle('hidden');
}

document.addEventListener('click', function(event) {
    if (!event.target.closest('.relative')) {
        document.querySelectorAll('.dropdown-menu').forEach(d => d.classList.add('hidden'));
    }
});

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

function openViewModal(userId) {
    fetch(`/users/${userId}/json`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('viewUserContent').innerHTML = `
                <div class="space-y-4">
                    <div class="flex items-center gap-4 border-b pb-4">
                        <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-blue-700 rounded-full flex items-center justify-center">
                            <span class="text-white text-xl font-bold">${data.name.substring(0,2)}</span>
                        </div>
                        <div>
                            <h4 class="text-xl font-bold">${data.name}</h4>
                            <p class="text-gray-600">${data.email}</p>
                            <span class="inline-block px-2 py-1 text-xs rounded-full ${data.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'} mt-1">
                                ${data.is_active ? 'Active' : 'Inactive'}
                            </span>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div><label class="text-xs text-gray-500">Phone</label><p class="text-sm">${data.phone || '-'}</p></div>
                        <div><label class="text-xs text-gray-500">Gender</label><p class="text-sm">${data.gender || '-'}</p></div>
                        <div><label class="text-xs text-gray-500">Role</label><p class="text-sm">${data.roles.map(r => r.display_name).join(', ') || '-'}</p></div>
                        <div><label class="text-xs text-gray-500">Registered</label><p class="text-sm">${data.created_at}</p></div>
                    </div>
                    <div class="flex justify-end pt-4 border-t">
                        <button onclick="closeModal('viewModal')" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Close</button>
                    </div>
                </div>
            `;
            document.getElementById('viewModal').classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Could not load user details');
        });
}

function openEditModal(userId) {
    fetch(`/users/${userId}/edit-form`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('editUserContent').innerHTML = html;
            document.getElementById('editModal').classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Could not load edit form');
        });
}

function openEditRolesModal(userId) {
    fetch(`/users/${userId}/roles/edit`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('editRolesContent').innerHTML = html;
            document.getElementById('editRolesModal').classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Could not load roles form');
        });
}

function openStatusModal(userId, userName, isActive) {
    const action = isActive ? 'Deactivate' : 'Activate';
    const btnClass = isActive ? 'bg-red-600' : 'bg-green-600';
    document.getElementById('statusModalTitle').innerHTML = `${action} User`;
    document.getElementById('statusModalContent').innerHTML = `
        <p class="mb-4">Are you sure you want to ${action.toLowerCase()} <strong>${userName}</strong>?</p>
        <div class="flex justify-end gap-3">
            <button onclick="closeModal('statusModal')" class="px-4 py-2 border rounded-lg">Cancel</button>
            <form action="/users/${userId}/toggle-status" method="POST" class="inline">
                @csrf
                <button type="submit" class="px-4 py-2 ${btnClass} text-white rounded-lg">${action}</button>
            </form>
        </div>
    `;
    document.getElementById('statusModal').classList.remove('hidden');
}

function openDeleteModal(userId, userName) {
    document.getElementById('deleteModalContent').innerHTML = `
        <p class="mb-4">Delete <strong>${userName}</strong>? This action cannot be undone.</p>
        <div class="flex justify-end gap-3">
            <button onclick="closeModal('deleteModal')" class="px-4 py-2 border rounded-lg">Cancel</button>
            <form action="/users/${userId}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg">Delete</button>
            </form>
        </div>
    `;
    document.getElementById('deleteModal').classList.remove('hidden');
}

function openCreateModal() {
    fetch('/users/create-form')
        .then(response => response.text())
        .then(html => {
            document.getElementById('createUserContent').innerHTML = html;
            document.getElementById('createModal').classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Could not load create form');
        });
}

function exportToCSV() {
    let url = '{{ route("users.export") }}';
    let params = new URLSearchParams(window.location.search);
    window.open(url + '?' + params.toString(), '_blank');
}

window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.classList.add('hidden');
    }
}
</script>

<style>
.modal { display: none; }
.modal:not(.hidden) { display: block !important; }
.dropdown-menu { display: none; }
.dropdown-menu:not(.hidden) { display: block !important; }
</style>
@endsection