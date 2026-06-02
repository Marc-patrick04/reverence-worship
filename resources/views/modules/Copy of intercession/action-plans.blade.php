@extends('layouts.app')

@section('title', 'Action Plans')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Action Plans</h1>
            <p class="text-gray-600 mt-1">Manage your spiritual growth action items</p>
        </div>
        <button onclick="openCreateModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
            <i class="fas fa-plus mr-2"></i> New Action Plan
        </button>
    </div>

    <div class="bg-white rounded-xl shadow-md p-6">
        <div class="space-y-3">
            @forelse($actionPlans as $plan)
            <div class="border rounded-lg p-4 hover:shadow-md transition">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <h4 class="font-semibold text-gray-800">{{ $plan->title }}</h4>
                        <p class="text-sm text-gray-600 mt-1">{{ $plan->description ?? 'No description' }}</p>
                        <div class="flex flex-wrap gap-3 mt-2 text-xs text-gray-500">
                            @if($plan->due_date)
                            <span><i class="fas fa-calendar mr-1"></i> Due: {{ \Carbon\Carbon::parse($plan->due_date)->format('d/m/Y') }}</span>
                            @endif
                            @if($plan->assignedUser)
                            <span><i class="fas fa-user mr-1"></i> Assigned to: {{ $plan->assignedUser->name }}</span>
                            @endif
                            <span><i class="fas fa-user-plus mr-1"></i> Created by: {{ $plan->creator->name ?? 'Unknown' }}</span>
                        </div>
                    </div>
                    <div class="text-right">
                        <select onchange="updateStatus({{ $plan->id }}, this.value)" 
                                class="text-sm border rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="pending" {{ $plan->status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="in-progress" {{ $plan->status == 'in-progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="completed" {{ $plan->status == 'completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center text-gray-500 py-8">
                <i class="fas fa-tasks text-4xl text-gray-300 mb-3"></i>
                <p>No action plans yet</p>
                <button onclick="openCreateModal()" class="mt-3 text-blue-600 hover:underline">Create your first plan</button>
            </div>
            @endforelse
        </div>
        
        <div class="mt-4">
            {{ $actionPlans->links() }}
        </div>
    </div>
</div>

<!-- Create Action Plan Modal -->
<div id="createModal" class="modal hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center pb-3 border-b">
            <h3 class="text-lg font-bold text-gray-800">Create Action Plan</h3>
            <button onclick="closeModal('createModal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('intercession.action-plans.store') }}">
            @csrf
            <div class="mt-4 space-y-3">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Title *</label>
                    <input type="text" name="title" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Due Date</label>
                    <input type="date" name="due_date" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Assign To</label>
                    <select name="assigned_to" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        <option value="">Myself</option>
                        @foreach(\App\Models\User\User::where('is_active', true)->get() as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="flex justify-end space-x-3 mt-5 pt-3 border-t">
                <button type="button" onclick="closeModal('createModal')" class="px-4 py-2 border rounded-lg text-sm">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm">Create</button>
            </div>
        </form>
    </div>
</div>

<script>
function openCreateModal() {
    document.getElementById('createModal').classList.remove('hidden');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

function updateStatus(id, status) {
    fetch(`/intercession/action-plans/${id}/status`, {
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
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}
</script>

<style>
.modal { display: none; }
.modal:not(.hidden) { display: block !important; }
</style>
@endsection