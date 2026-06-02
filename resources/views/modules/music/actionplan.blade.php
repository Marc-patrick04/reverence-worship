<div class="bg-white rounded-lg shadow-lg p-6">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-bold text-gray-800">Action Plan</h3>
        @if(auth()->user()->canAccess('music-ministry', 'manage-actionplan'))
        <button onclick="openCreateTaskModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
            <i class="fas fa-plus-circle mr-2"></i> Add Task
        </button>
        @endif
    </div>
    
    <div class="space-y-3">
        @forelse($tasks ?? [] as $task)
        <div class="border rounded-lg p-4 hover:bg-gray-50 transition">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <div class="flex items-center space-x-3">
                        <h4 class="font-bold text-gray-800">{{ $task->title }}</h4>
                        <span class="px-2 py-1 text-xs rounded-full 
                            {{ $task->status == 'completed' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $task->status == 'in-progress' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $task->status == 'pending' ? 'bg-red-100 text-red-800' : '' }}">
                            {{ ucfirst($task->status) }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-600 mt-1">{{ $task->description }}</p>
                    <div class="flex items-center space-x-4 mt-2 text-xs text-gray-500">
                        @if($task->assignedUser)
                            <span><i class="fas fa-user mr-1"></i> Assigned to: {{ $task->assignedUser->name }}</span>
                        @endif
                        @if($task->due_date)
                            <span><i class="fas fa-calendar mr-1"></i> Due: {{ date('M d, Y', strtotime($task->due_date)) }}</span>
                        @endif
                    </div>
                </div>
                @if(auth()->user()->canAccess('music-ministry', 'manage-actionplan'))
                <div class="flex space-x-2">
                    <select onchange="updateStatus({{ $task->id }}, this.value)" class="text-sm border rounded px-2 py-1">
                        <option value="pending" {{ $task->status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="in-progress" {{ $task->status == 'in-progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed" {{ $task->status == 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                    <button onclick="deleteTask({{ $task->id }})" class="text-red-600 hover:text-red-800">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                @endif
            </div>
        </div>
        @empty
        <div class="text-center text-gray-500 py-8">
            <i class="fas fa-tasks fa-3x mb-3 text-gray-300"></i>
            <p>No tasks created yet</p>
        </div>
        @endforelse
    </div>
</div>

<!-- Create Task Modal -->
<div id="createTaskModal" class="modal hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center pb-3 border-b">
            <h3 class="text-xl font-bold text-gray-800">Create Task</h3>
            <button onclick="closeModal('createTaskModal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('music.actionplan.store') }}">
            @csrf
            <div class="mt-4 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Task Title *</label>
                    <input type="text" name="title" required class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Assigned To</label>
                    <select name="assigned_to" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        <option value="">Select Member</option>
                        @foreach($users ?? [] as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Due Date</label>
                    <input type="date" name="due_date" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>
            </div>
            <div class="flex justify-end space-x-3 mt-6 pt-4 border-t">
                <button type="button" onclick="closeModal('createTaskModal')" class="px-4 py-2 border rounded-lg">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Create Task</button>
            </div>
        </form>
    </div>
</div>

<script>
function openCreateTaskModal() {
    document.getElementById('createTaskModal').classList.remove('hidden');
}

function updateStatus(id, status) {
    fetch(`/music/actionplan/${id}/status`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ status: status })
    }).then(() => location.reload());
}

function deleteTask(id) {
    if (confirm('Delete this task?')) {
        fetch(`/music/actionplan/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        }).then(() => location.reload());
    }
}
</script>