<div class="flex justify-between items-center mb-4">
    <h3 class="text-lg font-bold text-gray-800">Intercession & Spiritual Growth Action Plans</h3>
    <button onclick="openCreateActionPlanModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm flex items-center gap-2">
        <i class="fas fa-plus mr-2"></i> New Action Plan
    </button>
</div>

{{-- Stats Cards --}}
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-blue-50 rounded-xl p-4 text-center">
        <p class="text-3xl font-bold text-blue-600">{{ $actionPlans->count() }}</p>
        <p class="text-xs text-gray-600">Total Action Plans</p>
    </div>
    <div class="bg-green-50 rounded-xl p-4 text-center">
        <p class="text-3xl font-bold text-green-600">{{ $actionPlans->where('status', 'completed')->count() }}</p>
        <p class="text-xs text-gray-600">Completed Tasks</p>
    </div>
    <div class="bg-purple-50 rounded-xl p-4 text-center">
        <p class="text-3xl font-bold text-purple-600">
            {{ $actionPlans->count() > 0 ? round(($actionPlans->where('status', 'completed')->count() / $actionPlans->count()) * 100) : 0 }}%
        </p>
        <p class="text-xs text-gray-600">Overall Progress</p>
    </div>
    <div class="bg-yellow-50 rounded-xl p-4 text-center">
        <p class="text-3xl font-bold text-yellow-600">{{ $actionPlans->where('status', 'in-progress')->count() }}</p>
        <p class="text-xs text-gray-600">In Progress</p>
    </div>
</div>

{{-- Action Plans List --}}
<div class="mt-6">
    <h4 class="font-semibold text-gray-700 mb-3">All Action Plans</h4>
    <div id="action-plans-list" class="space-y-3">
        @forelse($actionPlans ?? [] as $plan)
        <div class="border rounded-lg p-4 hover:shadow-md transition">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <h4 class="font-semibold text-gray-800">{{ $plan->title }}</h4>
                    <p class="text-sm text-gray-600 mt-1">{{ $plan->description ?? 'No description' }}</p>
                    <div class="flex gap-3 mt-2 text-xs text-gray-500">
                        @if($plan->due_date)
                        <span><i class="fas fa-calendar mr-1"></i> Due: {{ \Carbon\Carbon::parse($plan->due_date)->format('d/m/Y') }}</span>
                        @endif
                        @if($plan->assigned_to)
                        <span><i class="fas fa-user mr-1"></i> Assigned to: {{ $plan->assignedUser->name ?? 'Unknown' }}</span>
                        @endif
                    </div>
                </div>
                <div class="text-right">
                    <span class="px-2 py-1 text-xs rounded-full 
                        {{ $plan->status == 'completed' ? 'bg-green-100 text-green-700' : '' }}
                        {{ $plan->status == 'in-progress' ? 'bg-yellow-100 text-yellow-700' : '' }}
                        {{ $plan->status == 'pending' ? 'bg-gray-100 text-gray-600' : '' }}">
                        {{ ucfirst($plan->status) }}
                    </span>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center text-gray-500 py-8">
            <i class="fas fa-tasks text-4xl text-gray-300 mb-3"></i>
            <p>No action plans yet. Create your first action plan!</p>
        </div>
        @endforelse
    </div>
</div>