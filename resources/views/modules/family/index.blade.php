

@extends('layouts.app')

@section('title', 'My Family')
@section('page-title', 'My Family')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    
    @if($userFamily)
    <!-- Family Header -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="bg-gradient-to-r from-gray-700 to-gray-800 px-6 py-5">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-white">{{ $userFamily->name }}</h1>
                    @if($userFamily->parent_name)
                    <p class="text-gray-300 text-sm mt-1">Parent: {{ $userFamily->parent_name }}</p>
                    @endif
                </div>
                <div class="text-right">
                    <p class="text-white text-3xl font-bold">{{ $familyMembers->count() }}</p>
                    <p class="text-gray-300 text-xs">Members</p>
                </div>
            </div>
        </div>
        
        @if($userFamily->motto)
        <div class="px-6 py-3 bg-gray-50 border-b">
            <p class="text-gray-600 italic text-sm">"{{ $userFamily->motto }}"</p>
        </div>
        @endif
    </div>
    
    <!-- Stats Cards -->
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white rounded-xl shadow-sm p-4 text-center border-l-4 border-green-500">
            <p class="text-2xl font-bold text-green-600">{{ $taskStats['completed'] }}</p>
            <p class="text-xs text-gray-500">Completed Tasks</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 text-center border-l-4 border-yellow-500">
            <p class="text-2xl font-bold text-yellow-600">{{ $taskStats['in_progress'] }}</p>
            <p class="text-xs text-gray-500">In Progress</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 text-center border-l-4 border-red-500">
            <p class="text-2xl font-bold text-red-600">{{ $taskStats['pending'] }}</p>
            <p class="text-xs text-gray-500">Pending</p>
        </div>
    </div>
    
    <!-- Family Members Section -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h2 class="text-lg font-bold text-gray-800">Family Members</h2>
        </div>
        
        <div class="p-4">
            <div class="flex flex-wrap gap-3 mb-4">
                @foreach($familyMembers->take(6) as $member)
                <div class="relative group">
                    <div class="w-14 h-14 bg-gray-600 rounded-full flex items-center justify-center cursor-pointer hover:bg-gray-700 transition"
                         onclick="showMemberDetails({{ $member->user_id }}, '{{ addslashes($member->name) }}')">
                        <span class="text-white text-base font-bold">{{ substr($member->name, 0, 2) }}</span>
                    </div>
                    <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 hidden group-hover:block bg-gray-800 text-white text-xs rounded px-2 py-1 whitespace-nowrap">
                        {{ $member->name }}
                    </div>
                </div>
                @endforeach
                @if($familyMembers->count() > 6)
                <div class="w-14 h-14 bg-gray-200 rounded-full flex items-center justify-center">
                    <span class="text-gray-600 text-sm font-bold">+{{ $familyMembers->count() - 6 }}</span>
                </div>
                @endif
            </div>
            
            @if($familyMembers->count() > 6)
            <button onclick="showAllMembers()" class="text-gray-600 hover:text-gray-800 text-sm font-medium">
                View All {{ $familyMembers->count() }} Members
                <i class="fas fa-arrow-right ml-1"></i>
            </button>
            @endif
        </div>
    </div>
    
    <!-- Task Board -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h2 class="text-lg font-bold text-gray-800">Task Board</h2>
        </div>
        
        <!-- Tasks List -->
        <div id="tasks-container" class="divide-y divide-gray-200 max-h-96 overflow-y-auto">
            @forelse($familyTasks as $task)
            <div class="task-item p-4 hover:bg-gray-50 transition" data-status="{{ $task->status }}">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <h3 class="font-semibold text-gray-800">{{ $task->title }}</h3>
                            <span class="px-2 py-0.5 text-xs rounded-full 
                                {{ $task->status == 'completed' ? 'bg-green-100 text-green-700' : 
                                   ($task->status == 'in-progress' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-600') }}">
                                {{ ucfirst($task->status == 'in-progress' ? 'In Progress' : $task->status) }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-600">{{ $task->description ?? 'No description' }}</p>
                        @if($task->due_date)
                        <p class="text-xs text-gray-400 mt-1">
                            <i class="fas fa-calendar mr-1"></i> Due: {{ \Carbon\Carbon::parse($task->due_date)->format('d M Y') }}
                        </p>
                        @endif
                    </div>
                    <div class="ml-4">
                        <select onchange="updateTaskStatus({{ $task->id }}, this.value)" 
                                class="text-sm border border-gray-300 rounded-lg px-2 py-1 focus:ring-gray-500 focus:border-gray-500">
                            <option value="pending" {{ $task->status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="in-progress" {{ $task->status == 'in-progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="completed" {{ $task->status == 'completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-8 text-gray-500">
                <i class="fas fa-tasks text-4xl text-gray-300 mb-2"></i>
                <p>No tasks assigned to your family yet</p>
            </div>
            @endforelse
        </div>
    </div>
    
    @else
    <!-- No Family Assigned -->
    <div class="bg-white rounded-xl shadow-md p-12 text-center">
        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-home text-gray-400 text-4xl"></i>
        </div>
        <h2 class="text-xl font-bold text-gray-800 mb-2">No Family Assigned</h2>
        <p class="text-gray-500 mb-4">You are not yet assigned to any family.</p>
        <a href="{{ route('social-fellowship.index') }}" class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded-lg text-sm">
            Go to Social Fellowship
        </a>
    </div>
    @endif
    
</div>

<!-- Member Details Modal -->
<div id="memberModal" class="modal hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-40 mx-auto p-5 border w-full max-w-md shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center pb-3 border-b">
            <h3 id="memberModalTitle" class="text-lg font-bold text-gray-800">Member Details</h3>
            <button onclick="closeModal('memberModal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div id="memberModalContent" class="mt-4"></div>
        <div class="flex justify-end mt-5 pt-3 border-t">
            <button onclick="closeModal('memberModal')" class="px-4 py-2 bg-gray-500 text-white rounded-lg text-sm">Close</button>
        </div>
    </div>
</div>

<!-- All Members Modal -->
@if($familyMembers->count() > 6)
<div id="allMembersModal" class="modal hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center pb-3 border-b">
            <h3 class="text-lg font-bold text-gray-800">All Family Members</h3>
            <button onclick="closeModal('allMembersModal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div id="allMembersContent" class="mt-4 max-h-96 overflow-y-auto">
            @foreach($familyMembers as $member)
            <div class="flex items-center justify-between p-3 border-b hover:bg-gray-50 cursor-pointer" onclick="showMemberDetails({{$member->user_id }}, '{{ addslashes($member->name) }}'); closeModal('allMembersModal')">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gray-600 rounded-full flex items-center justify-center">
                        <span class="text-white text-sm font-bold">{{ substr($member->name, 0, 2) }}</span>
                    </div>
                    <div>
                        <p class="font-medium text-gray-800">{{ $member->name }}</p>
                        <p class="text-xs text-gray-500">{{ $member->role ?? 'Member' }}</p>
                    </div>
                </div>
                <i class="fas fa-chevron-right text-gray-400 text-sm"></i>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

<script>
function showAllMembers() {
    document.getElementById('allMembersModal').classList.remove('hidden');
}

function showMemberDetails(userId, name) {
    fetch(`/my-family/member/${userId}/details`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('memberModalTitle').textContent = name;
            document.getElementById('memberModalContent').innerHTML = `
                <div class="space-y-3">
                    <div>
                        <label class="text-xs text-gray-500 block">Email</label>
                        <p class="text-gray-800">${data.member.email || 'Not provided'}</p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 block">Phone</label>
                        <p class="text-gray-800">${data.member.phone || 'Not provided'}</p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 block">Role in Family</label>
                        <p class="text-gray-800 capitalize">${data.role || 'Member'}</p>
                    </div>
                </div>
            `;
            document.getElementById('memberModal').classList.remove('hidden');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error loading member details');
    });
}

function updateTaskStatus(taskId, status) {
    fetch(`/my-family/task/${taskId}/status`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ status: status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error updating task status');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating task status');
    });
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}
</script>

<style>
.modal { display: none; }
.modal:not(.hidden) { display: block !important; }
</style>
@endsection