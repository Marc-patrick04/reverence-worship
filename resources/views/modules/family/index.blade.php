@extends('layouts.app')

@section('title', $userFamily->name ?? 'My Family')
@section('page-title', $userFamily->name ?? 'My Family')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6">

    @if($userFamily)
    <!-- Compact Family Header -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-4 sm:mb-6">
        <div class="px-4 sm:px-5 py-3 sm:py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-home text-white text-lg"></i>
                </div>
                <div>
                    <h1 class="text-lg sm:text-xl font-bold text-gray-800">{{ $userFamily->name }}</h1>
                    <div class="flex items-center gap-2 text-xs text-gray-500">
                        <span><i class="fas fa-users mr-1"></i> {{ $familyMembers->count() }} members</span>
                        @if(isset($userFamily->parent_name) && $userFamily->parent_name)
                        <span>•</span>
                        <span><i class="fas fa-user-check mr-1"></i> {{ $userFamily->parent_name }}</span>
                        @endif
                    </div>
                </div>
            </div>
            <!-- Export Button -->
            @if(auth()->check() && auth()->user()->canAccess('family', 'export'))
            <button onclick="exportMembersToCSV()" class="bg-gray-50 hover:bg-gray-100 text-gray-600 px-3 py-1.5 rounded-lg text-sm transition flex items-center gap-1.5 border border-gray-200 self-start sm:self-center">
                <i class="fas fa-download text-xs"></i> Export
            </button>
            @endif
        </div>
    </div>

    <!-- Mobile: Toggle between Members and Tasks -->
    <div class="sm:hidden mb-4">
        <div class="bg-gray-100 rounded-lg p-1 flex gap-1">
            <button id="showMembersBtn" class="flex-1 py-2 px-4 rounded-lg text-sm font-medium transition-all bg-white text-blue-600 shadow-sm">
                <i class="fas fa-users mr-1"></i> Members ({{ $familyMembers->count() }})
            </button>
            <button id="showTasksBtn" class="flex-1 py-2 px-4 rounded-lg text-sm font-medium transition-all text-gray-600">
                <i class="fas fa-tasks mr-1"></i> Tasks ({{ ($taskStats['completed'] ?? 0) + ($taskStats['in_progress'] ?? 0) + ($taskStats['pending'] ?? 0) }})
            </button>
        </div>
    </div>

    <!-- Two Column Layout - Desktop -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
        
        <!-- LEFT: Members List -->
        <div id="membersPanel" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-4 py-3 bg-gray-50/50 border-b border-gray-100">
                <div class="flex justify-between items-center">
                    <h2 class="text-base font-semibold text-gray-800">
                        <i class="fas fa-users text-blue-500 mr-2"></i> Family Members
                        <span class="text-sm font-normal text-gray-500 ml-1">({{ $familyMembers->count() }})</span>
                    </h2>
                    @if(auth()->check() && auth()->user()->canAccess('family', 'create'))
                    <button onclick="openAddMemberModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-2 py-1 rounded-lg text-xs transition flex items-center gap-1">
                        <i class="fas fa-plus-circle"></i> Add
                    </button>
                    @endif
                </div>
            </div>
            
            <div class="divide-y divide-gray-100 max-h-[500px] overflow-y-auto">
                @forelse($familyMembers as $member)
                @php
                $isParent = strtolower($member->role ?? '') === 'parent';
                $isChild = strtolower($member->role ?? '') === 'child';
                @endphp
                
                <div class="p-3 hover:bg-gray-50 transition" id="member-{{ $member->id }}">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-semibold text-sm
                                {{ $isParent ? 'bg-purple-500' : ($isChild ? 'bg-green-500' : 'bg-blue-500') }}">
                                {{ strtoupper(substr($member->name, 0, 2)) }}
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="font-medium text-gray-900 text-sm">{{ $member->name }}</span>
                                @if($isParent)
                                <span class="text-xs px-1.5 py-0.5 bg-purple-100 text-purple-600 rounded">Parent</span>
                                @elseif($isChild)
                                <span class="text-xs px-1.5 py-0.5 bg-green-100 text-green-600 rounded">Child</span>
                                @endif
                            </div>
                            <div class="mt-1 space-y-0.5">
                                @if($member->phone)
                                <div class="flex items-center gap-1.5 text-xs text-gray-500">
                                    <i class="fas fa-phone text-gray-400 text-xs w-3"></i>
                                    <span>{{ $member->phone }}</span>
                                </div>
                                @endif
                                @if($member->email)
                                <div class="flex items-center gap-1.5 text-xs text-gray-500">
                                    <i class="fas fa-envelope text-gray-400 text-xs w-3"></i>
                                    <span class="truncate">{{ $member->email }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center gap-1">
                            @if(auth()->check() && auth()->user()->canAccess('family', 'edit'))
                            
                            @endif
                            
                        </div>
                    </div>
                </div>
                @empty
                <div class="p-8 text-center text-gray-400 text-sm">
                    <i class="fas fa-users text-3xl mb-2 block"></i>
                    No members found
                </div>
                @endforelse
            </div>
        </div>

        <!-- RIGHT: Tasks List -->
        <div id="tasksPanel" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-4 py-3 bg-gray-50/50 border-b border-gray-100">
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <h2 class="text-base font-semibold text-gray-800">
                            <i class="fas fa-tasks text-green-500 mr-2"></i> Tasks
                        </h2>
                        <div class="flex gap-1.5">
                            <span class="px-2 py-0.5 text-xs bg-green-100 text-green-600 rounded-full">
                                ✓ {{ $taskStats['completed'] ?? 0 }}
                            </span>
                            <span class="px-2 py-0.5 text-xs bg-yellow-100 text-yellow-600 rounded-full">
                                ⏳ {{ ($taskStats['in_progress'] ?? 0) + ($taskStats['pending'] ?? 0) }}
                            </span>
                        </div>
                    </div>
                    @if(auth()->check() && auth()->user()->canAccess('family', 'create'))
                    <button onclick="openAddTaskModal()" class="bg-green-600 hover:bg-green-700 text-white px-2 py-1 rounded-lg text-xs transition flex items-center gap-1">
                        <i class="fas fa-plus-circle"></i> Add Task
                    </button>
                    @endif
                </div>
            </div>

            <!-- Task Filters -->
            <div class="px-4 pt-2 pb-1 border-b border-gray-100 bg-white">
                <div class="flex gap-1.5 overflow-x-auto">
                    <button class="task-filter active px-2.5 py-1 text-xs rounded-md bg-blue-600 text-white whitespace-nowrap" data-filter="all">
                        All
                    </button>
                    <button class="task-filter px-2.5 py-1 text-xs rounded-md bg-gray-100 text-gray-600 whitespace-nowrap" data-filter="pending">
                        Pending
                    </button>
                    <button class="task-filter px-2.5 py-1 text-xs rounded-md bg-gray-100 text-gray-600 whitespace-nowrap" data-filter="in-progress">
                        In Progress
                    </button>
                    <button class="task-filter px-2.5 py-1 text-xs rounded-md bg-gray-100 text-gray-600 whitespace-nowrap" data-filter="completed">
                        Completed
                    </button>
                </div>
            </div>

            <!-- Tasks List -->
            <div class="divide-y divide-gray-100 max-h-[500px] overflow-y-auto" id="tasksList">
                @forelse($familyTasks as $task)
                <div class="task-item p-3 hover:bg-gray-50 transition" data-status="{{ $task->status }}" id="task-{{ $task->id }}">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-1.5 flex-wrap mb-1">
                                <span class="font-medium text-gray-800 text-sm">{{ $task->title }}</span>
                                <span class="text-xs px-1.5 py-0.5 rounded-full font-medium
                                    {{ $task->status == 'completed' ? 'bg-green-100 text-green-600' : 
                                       ($task->status == 'in-progress' ? 'bg-yellow-100 text-yellow-600' : 'bg-gray-100 text-gray-500') }}">
                                    @if($task->status == 'in-progress')
                                        <i class="fas fa-spinner fa-pulse text-xs mr-0.5"></i> 
                                    @elseif($task->status == 'completed')
                                        <i class="fas fa-check-circle text-xs mr-0.5"></i> 
                                    @else
                                        <i class="fas fa-clock text-xs mr-0.5"></i> 
                                    @endif
                                    {{ $task->status == 'in-progress' ? 'Progress' : ucfirst($task->status) }}
                                </span>
                            </div>
                            
                            @if(isset($task->description) && $task->description)
                            <p class="text-xs text-gray-500 mt-1">{{ Str::limit($task->description, 100) }}</p>
                            @endif
                            
                            @if(isset($task->due_date) && $task->due_date)
                            <div class="flex items-center gap-1 mt-1 text-xs {{ \Carbon\Carbon::parse($task->due_date)->isPast() && $task->status != 'completed' ? 'text-red-500' : 'text-gray-400' }}">
                                <i class="fas fa-calendar-alt text-xs"></i>
                                <span>{{ \Carbon\Carbon::parse($task->due_date)->format('d M Y') }}</span>
                            </div>
                            @endif
                        </div>
                        
                        <!-- Task action buttons -->
                        @if(auth()->check() && auth()->user()->canAccess('family', 'edit'))
                        <div class="flex items-center gap-1.5">
                            @if($task->status != 'completed')
                            <button onclick="updateTaskStatus({{ $task->id }}, 'completed')" 
                                    class="px-2 py-1 text-xs bg-green-50 hover:bg-green-100 text-green-600 rounded transition" title="Mark Complete">
                                <i class="fas fa-check"></i>
                            </button>
                            @endif
                            @if($task->status == 'pending')
                            <button onclick="updateTaskStatus({{ $task->id }}, 'in-progress')" 
                                    class="px-2 py-1 text-xs bg-yellow-50 hover:bg-yellow-100 text-yellow-600 rounded transition" title="Start Progress">
                                <i class="fas fa-play"></i>
                            </button>
                            @endif
                           
                            
                        </div>
                        @endif
                    </div>
                </div>
                @empty
                <div class="p-8 text-center text-gray-400 text-sm">
                    <i class="fas fa-check-circle text-3xl mb-2 block"></i>
                    No tasks assigned
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Add Member Modal -->
    <div id="addMemberModal" class="modal fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
        <div class="relative top-10 mx-auto p-5 border w-full max-w-md shadow-lg rounded-lg bg-white">
            <div class="flex justify-between items-center pb-3 border-b">
                <h3 class="text-lg font-bold text-gray-800">Add Family Member</h3>
                <button onclick="closeModal('addMemberModal')" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>
            <form id="addMemberForm" onsubmit="submitAddMember(event)">
                @csrf
                <div class="mt-4 space-y-3">
                    <div>
                        <label class="block text-sm font-medium mb-1">Name *</label>
                        <input type="text" name="name" id="memberName" required class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Role</label>
                        <select name="role" id="memberRole" class="w-full px-3 py-2 border rounded-lg">
                            <option value="member">Member</option>
                            <option value="parent">Parent</option>
                            <option value="child">Child</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Phone</label>
                        <input type="text" name="phone" id="memberPhone" class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Email</label>
                        <input type="email" name="email" id="memberEmail" class="w-full px-3 py-2 border rounded-lg">
                    </div>
                </div>
                <div class="flex justify-end gap-2 mt-5 pt-3 border-t">
                    <button type="button" onclick="closeModal('addMemberModal')" class="px-4 py-2 border rounded-lg">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Add Member</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Member Modal -->
    <div id="editMemberModal" class="modal fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
        <div class="relative top-10 mx-auto p-5 border w-full max-w-md shadow-lg rounded-lg bg-white">
            <div class="flex justify-between items-center pb-3 border-b">
                <h3 class="text-lg font-bold text-gray-800">Edit Family Member</h3>
                <button onclick="closeModal('editMemberModal')" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>
            <form id="editMemberForm" onsubmit="submitEditMember(event)">
                @csrf
                @method('PUT')
                <input type="hidden" name="member_id" id="editMemberId">
                <div class="mt-4 space-y-3">
                    <div>
                        <label class="block text-sm font-medium mb-1">Name *</label>
                        <input type="text" name="name" id="editMemberName" required class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Role</label>
                        <select name="role" id="editMemberRole" class="w-full px-3 py-2 border rounded-lg">
                            <option value="member">Member</option>
                            <option value="parent">Parent</option>
                            <option value="child">Child</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Phone</label>
                        <input type="text" name="phone" id="editMemberPhone" class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Email</label>
                        <input type="email" name="email" id="editMemberEmail" class="w-full px-3 py-2 border rounded-lg">
                    </div>
                </div>
                <div class="flex justify-end gap-2 mt-5 pt-3 border-t">
                    <button type="button" onclick="closeModal('editMemberModal')" class="px-4 py-2 border rounded-lg">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Update Member</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Task Modal -->
    <div id="addTaskModal" class="modal fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
        <div class="relative top-10 mx-auto p-5 border w-full max-w-md shadow-lg rounded-lg bg-white">
            <div class="flex justify-between items-center pb-3 border-b">
                <h3 class="text-lg font-bold text-gray-800">Add New Task</h3>
                <button onclick="closeModal('addTaskModal')" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>
            <form id="addTaskForm" onsubmit="submitAddTask(event)">
                @csrf
                <div class="mt-4 space-y-3">
                    <div>
                        <label class="block text-sm font-medium mb-1">Title *</label>
                        <input type="text" name="title" id="taskTitle" required class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Description</label>
                        <textarea name="description" id="taskDescription" rows="2" class="w-full px-3 py-2 border rounded-lg"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Due Date</label>
                        <input type="date" name="due_date" id="taskDueDate" class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Assigned To</label>
                        <select name="assigned_to" id="taskAssignedTo" class="w-full px-3 py-2 border rounded-lg">
                            <option value="">-- Select Member --</option>
                            @foreach($familyMembers as $member)
                            <option value="{{ $member->id }}">{{ $member->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="flex justify-end gap-2 mt-5 pt-3 border-t">
                    <button type="button" onclick="closeModal('addTaskModal')" class="px-4 py-2 border rounded-lg">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg">Add Task</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Task Modal -->
    <div id="editTaskModal" class="modal fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
        <div class="relative top-10 mx-auto p-5 border w-full max-w-md shadow-lg rounded-lg bg-white">
            <div class="flex justify-between items-center pb-3 border-b">
                <h3 class="text-lg font-bold text-gray-800">Edit Task</h3>
                <button onclick="closeModal('editTaskModal')" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>
            <form id="editTaskForm" onsubmit="submitEditTask(event)">
                @csrf
                @method('PUT')
                <input type="hidden" name="task_id" id="editTaskId">
                <div class="mt-4 space-y-3">
                    <div>
                        <label class="block text-sm font-medium mb-1">Title *</label>
                        <input type="text" name="title" id="editTaskTitle" required class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Description</label>
                        <textarea name="description" id="editTaskDescription" rows="2" class="w-full px-3 py-2 border rounded-lg"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Due Date</label>
                        <input type="date" name="due_date" id="editTaskDueDate" class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Status</label>
                        <select name="status" id="editTaskStatus" class="w-full px-3 py-2 border rounded-lg">
                            <option value="pending">Pending</option>
                            <option value="in-progress">In Progress</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Assigned To</label>
                        <select name="assigned_to" id="editTaskAssignedTo" class="w-full px-3 py-2 border rounded-lg">
                            <option value="">-- Select Member --</option>
                            @foreach($familyMembers as $member)
                            <option value="{{ $member->id }}">{{ $member->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="flex justify-end gap-2 mt-5 pt-3 border-t">
                    <button type="button" onclick="closeModal('editTaskModal')" class="px-4 py-2 border rounded-lg">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Update Task</button>
                </div>
            </form>
        </div>
    </div>

    @else
    <!-- Empty State -->
    <div class="bg-white rounded-xl shadow-sm p-8 text-center border border-gray-100">
        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
            <i class="fas fa-home text-gray-400 text-2xl"></i>
        </div>
        <h2 class="text-lg font-semibold text-gray-800 mb-1">No Family Assigned</h2>
        <p class="text-gray-500 text-sm mb-4">You are not yet assigned to any family.</p>
    </div>
    @endif

</div>

<script>
    // Mobile toggle between members and tasks
    const membersPanel = document.getElementById('membersPanel');
    const tasksPanel = document.getElementById('tasksPanel');
    const showMembersBtn = document.getElementById('showMembersBtn');
    const showTasksBtn = document.getElementById('showTasksBtn');
    
    function showMembers() {
        if (membersPanel) membersPanel.style.display = 'block';
        if (tasksPanel) tasksPanel.style.display = 'none';
        if (showMembersBtn) {
            showMembersBtn.classList.add('bg-white', 'text-blue-600', 'shadow-sm');
            showMembersBtn.classList.remove('text-gray-600');
        }
        if (showTasksBtn) {
            showTasksBtn.classList.remove('bg-white', 'text-blue-600', 'shadow-sm');
            showTasksBtn.classList.add('text-gray-600');
        }
    }
    
    function showTasks() {
        if (membersPanel) membersPanel.style.display = 'none';
        if (tasksPanel) tasksPanel.style.display = 'block';
        if (showTasksBtn) {
            showTasksBtn.classList.add('bg-white', 'text-blue-600', 'shadow-sm');
            showTasksBtn.classList.remove('text-gray-600');
        }
        if (showMembersBtn) {
            showMembersBtn.classList.remove('bg-white', 'text-blue-600', 'shadow-sm');
            showMembersBtn.classList.add('text-gray-600');
        }
    }
    
    if (window.innerWidth < 768) {
        showMembers();
        if (showMembersBtn) showMembersBtn.addEventListener('click', showMembers);
        if (showTasksBtn) showTasksBtn.addEventListener('click', showTasks);
    } else {
        if (membersPanel) membersPanel.style.display = 'block';
        if (tasksPanel) tasksPanel.style.display = 'block';
    }
    
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 768) {
            if (membersPanel) membersPanel.style.display = 'block';
            if (tasksPanel) tasksPanel.style.display = 'block';
        } else {
            if (membersPanel.style.display !== 'none' && tasksPanel.style.display !== 'none') {
                showMembers();
            }
        }
    });

    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
    }

    function showNotification(type, message) {
        const notification = document.createElement('div');
        notification.className = `fixed top-20 right-4 z-50 px-4 py-3 rounded-lg shadow-lg flex items-center gap-3 animate-slide-in`;
        notification.style.backgroundColor = type === 'success' ? '#10b981' : '#ef4444';
        notification.innerHTML = `
            <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} text-white"></i>
            <span class="text-white text-sm">${message}</span>
            <button onclick="this.parentElement.remove()" class="text-white hover:text-gray-200"><i class="fas fa-times"></i></button>
        `;
        document.body.appendChild(notification);
        setTimeout(() => notification.remove(), 3000);
    }

    @if(auth()->check() && auth()->user()->canAccess('family', 'export'))
    function exportMembersToCSV() {
        var members = @json($familyMembers);
        var csv = [];
        csv.push(['Name', 'Role', 'Phone', 'Email'].join(','));
        
        members.forEach(function(member) {
            var row = [];
            row.push('"' + (member.name || '').replace(/"/g, '""') + '"');
            row.push('"' + (member.role || 'Member').replace(/"/g, '""') + '"');
            row.push('"' + (member.phone || '').replace(/"/g, '""') + '"');
            row.push('"' + (member.email || '').replace(/"/g, '""') + '"');
            csv.push(row.join(','));
        });
        
        var blob = new Blob(["\uFEFF" + csv.join('\n')], { type: 'text/csv;charset=utf-8;' });
        var link = document.createElement('a');
        var url = URL.createObjectURL(blob);
        link.setAttribute('href', url);
        link.setAttribute('download', 'family_members_{{ $userFamily->name ?? 'family' }}.csv');
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);
        showNotification('success', 'Export completed!');
    }
    @endif

    @if(auth()->check() && auth()->user()->canAccess('family', 'create'))
    function openAddMemberModal() {
        document.getElementById('addMemberModal').classList.remove('hidden');
    }

    function submitAddMember(event) {
        event.preventDefault();
        const formData = new FormData();
        formData.append('name', document.getElementById('memberName').value);
        formData.append('role', document.getElementById('memberRole').value);
        formData.append('phone', document.getElementById('memberPhone').value);
        formData.append('email', document.getElementById('memberEmail').value);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

        fetch('/my-family/member', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeModal('addMemberModal');
                showNotification('success', 'Member added successfully!');
                setTimeout(() => location.reload(), 1500);
            } else {
                showNotification('error', data.message || 'Failed to add member');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('error', 'Network error');
        });
    }

    function openAddTaskModal() {
        document.getElementById('addTaskModal').classList.remove('hidden');
    }

    function submitAddTask(event) {
        event.preventDefault();
        const formData = new FormData();
        formData.append('title', document.getElementById('taskTitle').value);
        formData.append('description', document.getElementById('taskDescription').value);
        formData.append('due_date', document.getElementById('taskDueDate').value);
        formData.append('assigned_to', document.getElementById('taskAssignedTo').value);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

        fetch('/my-family/task', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeModal('addTaskModal');
                showNotification('success', 'Task added successfully!');
                setTimeout(() => location.reload(), 1500);
            } else {
                showNotification('error', data.message || 'Failed to add task');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('error', 'Network error');
        });
    }
    @endif

    @if(auth()->check() && auth()->user()->canAccess('family', 'edit'))
    function editMember(memberId) {
        fetch(`/my-family/member/${memberId}/json`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('editMemberId').value = data.id;
                document.getElementById('editMemberName').value = data.name;
                document.getElementById('editMemberRole').value = data.role || 'member';
                document.getElementById('editMemberPhone').value = data.phone || '';
                document.getElementById('editMemberEmail').value = data.email || '';
                document.getElementById('editMemberModal').classList.remove('hidden');
            });
    }

    function submitEditMember(event) {
        event.preventDefault();
        const memberId = document.getElementById('editMemberId').value;
        const formData = new FormData();
        formData.append('name', document.getElementById('editMemberName').value);
        formData.append('role', document.getElementById('editMemberRole').value);
        formData.append('phone', document.getElementById('editMemberPhone').value);
        formData.append('email', document.getElementById('editMemberEmail').value);
        formData.append('_method', 'PUT');
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

        fetch(`/my-family/member/${memberId}`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeModal('editMemberModal');
                showNotification('success', 'Member updated successfully!');
                setTimeout(() => location.reload(), 1500);
            } else {
                showNotification('error', data.message || 'Failed to update member');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('error', 'Network error');
        });
    }

    function updateTaskStatus(taskId, status) {
        fetch(`/my-family/task/${taskId}/status`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ status: status })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('success', 'Task updated successfully!');
                setTimeout(() => location.reload(), 1500);
            } else {
                showNotification('error', data.message || 'Failed to update task');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('error', 'Network error');
        });
    }

    function editTask(taskId) {
        fetch(`/my-family/task/${taskId}/json`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('editTaskId').value = data.id;
                document.getElementById('editTaskTitle').value = data.title;
                document.getElementById('editTaskDescription').value = data.description || '';
                document.getElementById('editTaskDueDate').value = data.due_date || '';
                document.getElementById('editTaskStatus').value = data.status;
                document.getElementById('editTaskAssignedTo').value = data.assigned_to || '';
                document.getElementById('editTaskModal').classList.remove('hidden');
            });
    }

    function submitEditTask(event) {
        event.preventDefault();
        const taskId = document.getElementById('editTaskId').value;
        const formData = new FormData();
        formData.append('title', document.getElementById('editTaskTitle').value);
        formData.append('description', document.getElementById('editTaskDescription').value);
        formData.append('due_date', document.getElementById('editTaskDueDate').value);
        formData.append('status', document.getElementById('editTaskStatus').value);
        formData.append('assigned_to', document.getElementById('editTaskAssignedTo').value);
        formData.append('_method', 'PUT');
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

        fetch(`/my-family/task/${taskId}`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeModal('editTaskModal');
                showNotification('success', 'Task updated successfully!');
                setTimeout(() => location.reload(), 1500);
            } else {
                showNotification('error', data.message || 'Failed to update task');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('error', 'Network error');
        });
    }
    @endif

    @if(auth()->check() && auth()->user()->canAccess('family', 'delete'))
    function deleteMember(memberId, memberName) {
        if (confirm(`Are you sure you want to delete "${memberName}" from the family?`)) {
            fetch(`/my-family/member/${memberId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('success', 'Member deleted successfully!');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showNotification('error', data.message || 'Failed to delete member');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('error', 'Network error');
            });
        }
    }

    function deleteTask(taskId, taskTitle) {
        if (confirm(`Are you sure you want to delete task "${taskTitle}"?`)) {
            fetch(`/my-family/task/${taskId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('success', 'Task deleted successfully!');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showNotification('error', data.message || 'Failed to delete task');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('error', 'Network error');
            });
        }
    }
    @endif

    // Task filter functionality
    document.addEventListener('DOMContentLoaded', function() {
        var filterBtns = document.querySelectorAll('.task-filter');
        if (filterBtns.length > 0) {
            filterBtns.forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var filterValue = this.getAttribute('data-filter');
                    var tasks = document.querySelectorAll('.task-item');
                    
                    filterBtns.forEach(function(b) {
                        b.classList.remove('bg-blue-600', 'text-white');
                        b.classList.add('bg-gray-100', 'text-gray-600');
                    });
                    this.classList.remove('bg-gray-100', 'text-gray-600');
                    this.classList.add('bg-blue-600', 'text-white');
                    
                    tasks.forEach(function(task) {
                        var taskStatus = task.getAttribute('data-status');
                        if (filterValue === 'all' || taskStatus === filterValue) {
                            task.style.display = '';
                        } else {
                            task.style.display = 'none';
                        }
                    });
                });
            });
        }
    });
</script>

<style>
    .modal { display: none; }
    .modal:not(.hidden) { display: block !important; }
    @keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
    .animate-slide-in { animation: slideIn 0.3s ease-out; }
</style>
@endsection