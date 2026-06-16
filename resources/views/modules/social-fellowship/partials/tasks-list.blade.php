{{-- Debug info --}}
@php
    $taskCount = isset($tasks) ? $tasks->count() : 0;
@endphp


<div class="bg-white rounded-xl shadow-md p-6">
    
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Family Tasks</h2>
            <p class="text-gray-500 text-sm mt-1">Manage tasks assigned to families</p>
        </div>
        <button onclick="openTaskModal()" class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded-lg text-sm flex items-center gap-2 transition">
            <i class="fas fa-plus"></i> New Task
        </button>
    </div>
    
    <!-- Filter Bar -->
    <div class="mb-6 flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Due Date</label>
            <select id="filterDueDate" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-gray-500 focus:border-gray-500">
                <option value="all">All Tasks</option>
                <option value="today">Today</option>
                <option value="tomorrow">Tomorrow</option>
                <option value="week">This Week</option>
                <option value="overdue">Overdue</option>
                <option value="completed">Completed</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Family</label>
            <select id="filterFamily" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-gray-500 focus:border-gray-500">
                <option value="all">All Families</option>
                @foreach($families ?? [] as $family)
                    <option value="{{ $family->id }}">{{ $family->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
            <select id="filterStatus" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-gray-500 focus:border-gray-500">
                <option value="all">All Status</option>
                <option value="pending">Pending</option>
                <option value="in-progress">In Progress</option>
                <option value="completed">Completed</option>
            </select>
        </div>
        <button onclick="applyFilters()" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm transition">
            <i class="fas fa-filter mr-1"></i> Filter
        </button>
        <button onclick="resetFilters()" class="text-gray-500 hover:text-gray-700 text-sm px-2">
            Reset
        </button>
    </div>
    
    <!-- Tasks List -->
    <div id="tasksList" class="space-y-3">
        @forelse($tasks ?? [] as $task)
        <div class="task-item border rounded-lg p-4 hover:shadow-md transition" 
             data-task-id="{{ $task->id }}"
             data-family-id="{{ $task->family_id }}"
             data-status="{{ $task->status }}"
             data-due-date="{{ $task->due_date }}">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-1 flex-wrap">
                        <h4 class="font-semibold text-gray-800">{{ $task->title }}</h4>
                        <span class="px-2 py-0.5 text-xs rounded-full 
                            {{ $task->status == 'completed' ? 'bg-green-100 text-green-700' : 
                               ($task->status == 'in-progress' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-600') }}">
                            {{ ucfirst($task->status == 'in-progress' ? 'In Progress' : $task->status) }}
                        </span>
                        <span class="px-2 py-0.5 text-xs rounded-full bg-gray-100 text-gray-600">
                            {{ ucfirst($task->priority) }} Priority
                        </span>
                    </div>
                    <p class="text-sm text-gray-600 mt-1">{{ $task->description ?? 'No description' }}</p>
                    <div class="flex flex-wrap gap-4 mt-2 text-xs text-gray-500">
                        <span><i class="fas fa-users"></i> {{ $task->family_name }}</span>
                        @if($task->due_date)
                            @php
                                $dueDate = \Carbon\Carbon::parse($task->due_date);
                                $isOverdue = $dueDate->isPast() && $task->status != 'completed';
                            @endphp
                            <span class="flex items-center gap-1 {{ $isOverdue ? 'text-red-500 font-medium' : '' }}">
                                <i class="fas fa-calendar"></i> 
                                Due: {{ $dueDate->format('d M Y') }}
                                @if($isOverdue)
                                    <span class="text-red-500">(Overdue)</span>
                                @endif
                            </span>
                        @endif
                        <span><i class="fas fa-user-check"></i> {{ $task->assigned_count ?? 0 }} assigned</span>
                    </div>
                </div>
                <div class="flex gap-2 ml-4">
                    <button onclick="viewTask({{ $task->id }})" class="text-gray-600 hover:text-gray-900" title="View">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button onclick="editTask({{ $task->id }})" class="text-gray-400 hover:text-gray-700" title="Edit">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button onclick="deleteTask({{ $task->id }})" class="text-gray-400 hover:text-red-600" title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div id="noTasksMessage" class="text-center py-12">
            <i class="fas fa-tasks text-5xl text-gray-300 mb-3"></i>
            <p class="text-gray-500">No tasks yet</p>
            <button onclick="openTaskModal()" class="mt-3 text-gray-600 hover:text-gray-800 text-sm">
                <i class="fas fa-plus"></i> Create your first task
            </button>
        </div>
        @endforelse
    </div>
</div>

<!-- Task Modal (Create/Edit) -->
<div id="taskModal" class="modal hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-6 border w-full max-w-md shadow-lg rounded-xl bg-white">
        <div class="flex justify-between items-center pb-4 border-b">
            <h3 id="taskModalTitle" class="text-xl font-bold text-gray-800">Create New Task</h3>
            <button onclick="closeModal('taskModal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <form id="taskForm" method="POST">
            @csrf
            <input type="hidden" id="task_id" name="task_id">
            <input type="hidden" id="form_method" name="_method" value="POST">
            
            <div class="mt-5 space-y-5">
                <!-- Task Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Task Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="task_title" name="title" required 
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-gray-500 focus:border-gray-500"
                           placeholder="Enter task name">
                </div>
                
                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Description
                    </label>
                    <textarea id="task_description" name="description" rows="4" 
                              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-gray-500 focus:border-gray-500 resize-none"
                              placeholder="Enter task description"></textarea>
                </div>
                
                <!-- Family -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Family <span class="text-red-500">*</span>
                    </label>
                    <select id="task_family_id" name="family_id" required 
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-gray-500 focus:border-gray-500 bg-white">
                        <option value="">Select a family...</option>
                        @foreach($families ?? [] as $family)
                            <option value="{{ $family->id }}">{{ $family->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Due Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Due Date
                    </label>
                    <input type="date" id="task_due_date" name="due_date" 
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-gray-500 focus:border-gray-500">
                </div>
                
                <!-- Priority -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                    <select id="task_priority" name="priority" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-gray-500 focus:border-gray-500">
                        <option value="low">Low</option>
                        <option value="medium" selected>Medium</option>
                        <option value="high">High</option>
                    </select>
                </div>
                
                <!-- Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select id="task_status" name="status" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-gray-500 focus:border-gray-500">
                        <option value="pending">Pending</option>
                        <option value="in-progress">In Progress</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>
            </div>
            
            <div class="flex justify-end gap-3 mt-6 pt-4 border-t">
                <button type="button" onclick="closeModal('taskModal')" 
                        class="px-5 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    Cancel
                </button>
                <button type="submit" 
                        class="px-5 py-2 bg-gray-800 hover:bg-gray-900 text-white rounded-lg transition">
                    Create Task
                </button>
            </div>
        </form>
    </div>
</div>

<!-- View Task Modal -->
<div id="viewTaskModal" class="modal hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center pb-3 border-b">
            <h3 id="viewTaskTitle" class="text-lg font-bold text-gray-800">Task Details</h3>
            <button onclick="closeModal('viewTaskModal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div id="viewTaskContent" class="mt-4"></div>
        <div class="flex justify-end gap-3 mt-5 pt-3 border-t">
            <button onclick="closeModal('viewTaskModal')" class="px-4 py-2 border rounded-lg text-sm">Close</button>
        </div>
    </div>
</div>
<script>
let currentFilter = {
    dueDate: 'all',
    family: 'all',
    status: 'all'
};

// Apply filters
function applyFilters() {
    currentFilter.dueDate = document.getElementById('filterDueDate')?.value || 'all';
    currentFilter.family = document.getElementById('filterFamily')?.value || 'all';
    currentFilter.status = document.getElementById('filterStatus')?.value || 'all';
    
    const tasks = document.querySelectorAll('.task-item');
    let visibleCount = 0;
    
    tasks.forEach(task => {
        let show = true;
        const familyId = task.dataset.familyId;
        const status = task.dataset.status;
        const dueDate = task.dataset.dueDate;
        
        // Filter by family
        if (currentFilter.family !== 'all' && familyId != currentFilter.family) {
            show = false;
        }
        
        // Filter by status
        if (currentFilter.status !== 'all' && status !== currentFilter.status) {
            show = false;
        }
        
        // Filter by due date
        if (currentFilter.dueDate !== 'all' && dueDate) {
            const taskDueDate = new Date(dueDate);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            const tomorrow = new Date(today);
            tomorrow.setDate(tomorrow.getDate() + 1);
            const weekEnd = new Date(today);
            weekEnd.setDate(weekEnd.getDate() + 7);
            
            switch(currentFilter.dueDate) {
                case 'today':
                    if (taskDueDate.toDateString() !== today.toDateString()) show = false;
                    break;
                case 'tomorrow':
                    if (taskDueDate.toDateString() !== tomorrow.toDateString()) show = false;
                    break;
                case 'week':
                    if (taskDueDate < today || taskDueDate > weekEnd) show = false;
                    break;
                case 'overdue':
                    if (status === 'completed' || taskDueDate >= today) show = false;
                    break;
                case 'completed':
                    if (status !== 'completed') show = false;
                    break;
            }
        }
        
        if (show) {
            task.style.display = '';
            visibleCount++;
        } else {
            task.style.display = 'none';
        }
    });
    
    // Show/hide no results message
    const noResultsMsg = document.getElementById('noResultsMsg');
    if (visibleCount === 0) {
        if (!noResultsMsg) {
            const msg = document.createElement('div');
            msg.id = 'noResultsMsg';
            msg.className = 'text-center py-12 text-gray-500';
            msg.innerHTML = '<i class="fas fa-search fa-3x mb-3 text-gray-300"></i><p>No tasks match your filters</p>';
            document.getElementById('tasksList')?.appendChild(msg);
        }
    } else if (noResultsMsg) {
        noResultsMsg.remove();
    }
}

// Reset filters
function resetFilters() {
    const dueDateFilter = document.getElementById('filterDueDate');
    const familyFilter = document.getElementById('filterFamily');
    const statusFilter = document.getElementById('filterStatus');
    
    if (dueDateFilter) dueDateFilter.value = 'all';
    if (familyFilter) familyFilter.value = 'all';
    if (statusFilter) statusFilter.value = 'all';
    applyFilters();
}

// Open create task modal - Fixed version
function openTaskModal() {
    const modal = document.getElementById('taskModal');
    if (!modal) {
        console.error('Task modal not found');
        return;
    }
    
    const titleInput = document.getElementById('task_title');
    const descriptionInput = document.getElementById('task_description');
    const familySelect = document.getElementById('task_family_id');
    const dueDateInput = document.getElementById('task_due_date');
    const prioritySelect = document.getElementById('task_priority');
    const statusSelect = document.getElementById('task_status');
    
    // Set modal title
    const modalTitle = document.getElementById('taskModalTitle');
    if (modalTitle) modalTitle.textContent = 'Create New Task';
    
    // Reset form values
    if (titleInput) titleInput.value = '';
    if (descriptionInput) descriptionInput.value = '';
    if (familySelect) familySelect.value = '';
    if (dueDateInput) dueDateInput.value = '';
    if (prioritySelect) prioritySelect.value = 'medium';
    if (statusSelect) statusSelect.value = 'pending';
    
    // Reset hidden fields
    const taskIdInput = document.getElementById('task_id');
    const formMethodInput = document.getElementById('form_method');
    if (taskIdInput) taskIdInput.value = '';
    if (formMethodInput) formMethodInput.value = 'POST';
    
    // Show modal
    modal.classList.remove('hidden');
}

// View task details
function viewTask(id) {
    fetch(`/social-fellowship/tasks/${id}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const viewTitle = document.getElementById('viewTaskTitle');
            const viewContent = document.getElementById('viewTaskContent');
            
            if (viewTitle) viewTitle.textContent = data.task.title;
            if (viewContent) {
                viewContent.innerHTML = `
                    <div class="space-y-3">
                        <div>
                            <label class="text-xs text-gray-500 block">Description</label>
                            <p class="text-gray-700">${data.task.description || 'No description'}</p>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="text-xs text-gray-500 block">Family</label>
                                <p class="text-gray-700 font-medium">${data.task.family_name}</p>
                            </div>
                            <div>
                                <label class="text-xs text-gray-500 block">Due Date</label>
                                <p class="text-gray-700">${data.task.due_date ? new Date(data.task.due_date).toLocaleDateString() : 'No due date'}</p>
                            </div>
                            <div>
                                <label class="text-xs text-gray-500 block">Priority</label>
                                <p class="text-gray-700 capitalize">${data.task.priority}</p>
                            </div>
                            <div>
                                <label class="text-xs text-gray-500 block">Status</label>
                                <p class="text-gray-700 capitalize">${data.task.status}</p>
                            </div>
                            <div>
                                <label class="text-xs text-gray-500 block">Assigned To</label>
                                <p class="text-gray-700">${data.task.assigned_name || 'Not assigned'}</p>
                            </div>
                            <div>
                                <label class="text-xs text-gray-500 block">Created</label>
                                <p class="text-gray-700">${new Date(data.task.created_at).toLocaleDateString()}</p>
                            </div>
                        </div>
                    </div>
                `;
            }
            
            const viewModal = document.getElementById('viewTaskModal');
            if (viewModal) viewModal.classList.remove('hidden');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error loading task details');
    });
}

// Edit task
function editTask(id) {
    fetch(`/social-fellowship/tasks/${id}/edit`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const modalTitle = document.getElementById('taskModalTitle');
            const taskIdInput = document.getElementById('task_id');
            const formMethodInput = document.getElementById('form_method');
            const titleInput = document.getElementById('task_title');
            const descriptionInput = document.getElementById('task_description');
            const familySelect = document.getElementById('task_family_id');
            const dueDateInput = document.getElementById('task_due_date');
            const prioritySelect = document.getElementById('task_priority');
            const statusSelect = document.getElementById('task_status');
            
            if (modalTitle) modalTitle.textContent = 'Edit Task';
            if (taskIdInput) taskIdInput.value = data.task.id;
            if (formMethodInput) formMethodInput.value = 'PUT';
            if (titleInput) titleInput.value = data.task.title;
            if (descriptionInput) descriptionInput.value = data.task.description || '';
            if (familySelect) familySelect.value = data.task.family_id;
            if (dueDateInput) dueDateInput.value = data.task.due_date;
            if (prioritySelect) prioritySelect.value = data.task.priority || 'medium';
            if (statusSelect) statusSelect.value = data.task.status || 'pending';
            
            const modal = document.getElementById('taskModal');
            if (modal) modal.classList.remove('hidden');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error loading task for editing');
    });
}

// Delete task
function deleteTask(id) {
    if (confirm('Are you sure you want to delete this task?')) {
        fetch(`/social-fellowship/tasks/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Task deleted successfully!', 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                alert('Error deleting task: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting task');
        });
    }
}

// Task form submission
document.getElementById('taskForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const taskId = document.getElementById('task_id')?.value;
    const method = document.getElementById('form_method')?.value;
    
    let url = '{{ route("social-fellowship.tasks.store") }}';
    if (method === 'PUT' && taskId) {
        url = `/social-fellowship/tasks/${taskId}`;
    }
    
    const formData = new FormData(this);
    if (method === 'PUT') {
        formData.append('_method', 'PUT');
    }
    
    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeModal('taskModal');
            showNotification('Task saved successfully!', 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            alert('Error: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error saving task');
    });
});

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) modal.classList.add('hidden');
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

// Initialize event listeners when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Attach filter event listeners
    const filterDueDate = document.getElementById('filterDueDate');
    const filterFamily = document.getElementById('filterFamily');
    const filterStatus = document.getElementById('filterStatus');
    
    if (filterDueDate) filterDueDate.addEventListener('change', applyFilters);
    if (filterFamily) filterFamily.addEventListener('change', applyFilters);
    if (filterStatus) filterStatus.addEventListener('change', applyFilters);
});
</script>