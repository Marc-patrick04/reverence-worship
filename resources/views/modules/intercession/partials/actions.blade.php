<div class="bg-white rounded-xl shadow-md p-6">
    
    <!-- Header Section -->
    <div class="mb-6">
        <h2 class="text-xl font-bold text-gray-800">Intercession & Spiritual Growth Action Plans</h2>
        <p class="text-gray-500 text-sm mt-1">Manage and track departmental action plans and tasks</p>
    </div>

    <!-- Action Plans Overview -->
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-5 mb-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="font-semibold text-gray-700">Action Plans Overview</h3>
            <div class="flex items-center gap-2">
                <span class="text-sm text-gray-500">Intercession & Spiritual Growth DPT</span>
                <i class="fas fa-chevron-down text-gray-400 text-sm"></i>
            </div>
        </div>
        
        <!-- Stats Cards Row -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <!-- Total Action Plans -->
            <div class="bg-white rounded-lg p-4 text-center shadow-sm">
                <div class="flex items-center justify-center mb-2">
                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-tasks text-blue-600"></i>
                    </div>
                </div>
                <p class="text-2xl font-bold text-gray-800">{{ $totalActionPlans ?? 0 }}</p>
                <p class="text-xs text-gray-500">Total Action Plans</p>
            </div>
            
            <!-- Completed Tasks -->
            <div class="bg-white rounded-lg p-4 text-center shadow-sm">
                <div class="flex items-center justify-center mb-2">
                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                </div>
                <p class="text-2xl font-bold text-gray-800">{{ $completedPlans ?? 0 }}</p>
                <p class="text-xs text-gray-500">Completed Tasks</p>
            </div>
            
            <!-- Overall Progress -->
            <div class="bg-white rounded-lg p-4 text-center shadow-sm">
                <div class="flex items-center justify-center mb-2">
                    <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-chart-line text-purple-600"></i>
                    </div>
                </div>
                <p class="text-2xl font-bold text-purple-600">{{ $overallProgress ?? 0 }}%</p>
                <p class="text-xs text-gray-500">Overall Progress</p>
            </div>
            
            <!-- In Progress -->
            <div class="bg-white rounded-lg p-4 text-center shadow-sm">
                <div class="flex items-center justify-center mb-2">
                    <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-spinner text-yellow-600"></i>
                    </div>
                </div>
                <p class="text-2xl font-bold text-gray-800">{{ $inProgressPlans ?? 0 }}</p>
                <p class="text-xs text-gray-500">In Progress</p>
            </div>
        </div>
    </div>

    <!-- Summary Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-gray-50 rounded-lg p-4 text-center">
            <p class="text-2xl font-bold text-blue-600">{{ $totalActionPlans ?? 0 }}</p>
            <p class="text-xs text-gray-500">Total Plans</p>
        </div>
        <div class="bg-gray-50 rounded-lg p-4 text-center">
            <p class="text-2xl font-bold text-green-600">{{ $completedPlans ?? 0 }}</p>
            <p class="text-xs text-gray-500">Completed</p>
        </div>
        <div class="bg-gray-50 rounded-lg p-4 text-center">
            <p class="text-2xl font-bold text-purple-600">{{ $overallProgress ?? 0 }}%</p>
            <p class="text-xs text-gray-500">Average Progress</p>
        </div>
        <div class="bg-gray-50 rounded-lg p-4 text-center">
            <p class="text-2xl font-bold text-orange-600">{{ $pendingPlans ?? 0 }}</p>
            <p class="text-xs text-gray-500">Pending</p>
        </div>
    </div>

    <!-- Action Plans List Header -->
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-bold text-gray-800">All Action Plans</h3>
        <button onclick="openCreateActionPlanModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm flex items-center gap-2">
            <i class="fas fa-plus"></i> New Action Plan
        </button>
    </div>

    <!-- Progress Bar for Overall -->
    <div class="mb-6">
        <div class="flex justify-between text-sm text-gray-600 mb-1">
            <span>Overall Progress</span>
            <span>{{ $overallProgress ?? 0 }}%</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2">
            <div class="bg-blue-600 h-2 rounded-full transition-all duration-500" style="width: {{ $overallProgress ?? 0 }}%"></div>
        </div>
    </div>

    <!-- Action Plans List -->
    <div class="space-y-3">
        @forelse($actionPlans ?? [] as $plan)
        <div class="border rounded-lg p-4 hover:shadow-md transition-all duration-300">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-3">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-1">
                        <h4 class="font-semibold text-gray-800">{{ $plan->title }}</h4>
                        @if($plan->status == 'completed')
                            <span class="px-2 py-0.5 text-xs rounded-full bg-green-100 text-green-700">
                                <i class="fas fa-check-circle mr-1"></i> Completed
                            </span>
                        @elseif($plan->status == 'in-progress')
                            <span class="px-2 py-0.5 text-xs rounded-full bg-yellow-100 text-yellow-700">
                                <i class="fas fa-spinner mr-1"></i> In Progress
                            </span>
                        @else
                            <span class="px-2 py-0.5 text-xs rounded-full bg-gray-100 text-gray-600">
                                <i class="fas fa-clock mr-1"></i> Pending
                            </span>
                        @endif
                    </div>
                    <p class="text-sm text-gray-600">{{ $plan->description ?? 'No description' }}</p>
                    <div class="flex flex-wrap gap-4 mt-2 text-xs text-gray-500">
                        @if($plan->due_date)
                        <span class="flex items-center gap-1">
                            <i class="fas fa-calendar"></i> Due: {{ \Carbon\Carbon::parse($plan->due_date)->format('d/m/Y') }}
                        </span>
                        @endif
                        @if($plan->assignedUser)
                        <span class="flex items-center gap-1">
                            <i class="fas fa-user"></i> Assigned to: {{ $plan->assignedUser->name }}
                        </span>
                        @endif
                        <span class="flex items-center gap-1">
                            <i class="fas fa-clock"></i> Created: {{ \Carbon\Carbon::parse($plan->created_at)->format('d/m/Y') }}
                        </span>
                    </div>
                </div>
                
                <!-- Status Update Dropdown -->
                <div class="flex items-center gap-2">
                    <select onchange="updatePlanStatus({{ $plan->id }}, this.value)" 
                            class="text-sm border rounded-lg px-3 py-1 focus:ring-blue-500 focus:border-blue-500">
                        <option value="pending" {{ $plan->status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="in-progress" {{ $plan->status == 'in-progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed" {{ $plan->status == 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                    
                    <div class="relative">
                        <button onclick="togglePlanMenu({{ $plan->id }})" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <div id="plan-menu-{{ $plan->id }}" class="hidden absolute right-0 mt-2 w-32 bg-white rounded-md shadow-lg z-10 border">
                            <a href="#" onclick="editPlan({{ $plan->id }})" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-edit mr-2"></i> Edit
                            </a>
                            <button onclick="deletePlan({{ $plan->id }})" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                <i class="fas fa-trash mr-2"></i> Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-12">
            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-tasks text-3xl text-gray-400"></i>
            </div>
            <p class="text-gray-500">No action plans yet</p>
            <p class="text-sm text-gray-400 mt-1">Create your first action plan to get started</p>
            <button onclick="openCreateActionPlanModal()" class="mt-4 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
                <i class="fas fa-plus mr-2"></i> Create Action Plan
            </button>
        </div>
        @endforelse
    </div>
</div>

<script>
function updatePlanStatus(planId, status) {
    fetch(`/intercession/action-plans/${planId}/status`, {
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
            alert('Error updating status');
        }
    });
}

function togglePlanMenu(planId) {
    const menu = document.getElementById(`plan-menu-${planId}`);
    if (menu) {
        menu.classList.toggle('hidden');
    }
}

function editPlan(planId) {
    // Implement edit functionality
    window.location.href = `/intercession/action-plans/${planId}/edit`;
}

function deletePlan(planId) {
    if (confirm('Are you sure you want to delete this action plan?')) {
        fetch(`/intercession/action-plans/${planId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error deleting plan');
            }
        });
    }
}

// Close menu when clicking outside
document.addEventListener('click', function(event) {
    if (!event.target.closest('[onclick*="togglePlanMenu"]')) {
        document.querySelectorAll('[id^="plan-menu-"]').forEach(menu => {
            menu.classList.add('hidden');
        });
    }
});
</script>
@include('modules.intercession.partials.modals')