<div class="bg-white rounded-xl shadow-sm p-4">
    
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-4">
        <div>
            <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-tasks text-blue-600"></i>
                Action Plans
            </h2>
            <p class="text-xs text-gray-500 mt-0.5">Track and manage your spiritual growth action plans</p>
        </div>
        <button onclick="openCreateActionPlanModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg text-xs flex items-center gap-1.5 transition hover:shadow-md">
            <i class="fas fa-plus"></i> New Plan
        </button>
    </div>

    <!-- Stats Cards - Compact -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-2 mb-4">
        <div class="bg-blue-50 rounded-lg p-2.5 text-center">
            <p class="text-lg font-bold text-blue-600">{{ $totalActionPlans ?? 0 }}</p>
            <p class="text-[10px] text-gray-500">Total Plans</p>
        </div>
        <div class="bg-green-50 rounded-lg p-2.5 text-center">
            <p class="text-lg font-bold text-green-600">{{ $completedPlans ?? 0 }}</p>
            <p class="text-[10px] text-gray-500">Completed</p>
        </div>
        <div class="bg-yellow-50 rounded-lg p-2.5 text-center">
            <p class="text-lg font-bold text-yellow-600">{{ $inProgressPlans ?? 0 }}</p>
            <p class="text-[10px] text-gray-500">In Progress</p>
        </div>
        <div class="bg-gray-50 rounded-lg p-2.5 text-center">
            <p class="text-lg font-bold text-gray-600">{{ $pendingPlans ?? 0 }}</p>
            <p class="text-[10px] text-gray-500">Pending</p>
        </div>
    </div>

    <!-- Progress Bar - Compact -->
    <div class="mb-4">
        <div class="flex justify-between text-xs text-gray-500 mb-0.5">
            <span>Progress</span>
            <span class="font-medium">{{ $overallProgress ?? 0 }}%</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-1.5">
            <div class="bg-blue-600 h-1.5 rounded-full transition-all duration-500" style="width: {{ $overallProgress ?? 0 }}%"></div>
        </div>
    </div>

    <!-- Action Plans List -->
    <div class="space-y-2">
        @forelse($actionPlans ?? [] as $plan)
        <div class="border rounded-lg p-3 hover:shadow-sm transition-all duration-200 hover:border-blue-200">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <h4 class="font-medium text-gray-800 text-sm truncate">{{ $plan->title }}</h4>
                        @if($plan->status == 'completed')
                            <span class="px-1.5 py-0.5 text-[10px] rounded-full bg-green-100 text-green-700 flex-shrink-0">
                                <i class="fas fa-check-circle mr-0.5"></i> Done
                            </span>
                        @elseif($plan->status == 'in-progress')
                            <span class="px-1.5 py-0.5 text-[10px] rounded-full bg-yellow-100 text-yellow-700 flex-shrink-0">
                                <i class="fas fa-spinner mr-0.5"></i> Progress
                            </span>
                        @else
                            <span class="px-1.5 py-0.5 text-[10px] rounded-full bg-gray-100 text-gray-600 flex-shrink-0">
                                <i class="fas fa-clock mr-0.5"></i> Pending
                            </span>
                        @endif
                    </div>
                    @if($plan->description)
                        <p class="text-xs text-gray-500 mt-0.5 truncate">{{ Str::limit($plan->description, 80) }}</p>
                    @endif
                    <div class="flex flex-wrap items-center gap-3 mt-1 text-[10px] text-gray-400">
                        <span class="flex items-center gap-0.5">
                            <i class="fas fa-tasks"></i> {{ $plan->tasks_count ?? 0 }} tasks
                        </span>
                        <span class="flex items-center gap-0.5">
                            <i class="fas fa-check-circle text-green-500"></i> {{ $plan->completed_tasks ?? 0 }} done
                        </span>
                    </div>
                </div>
                
                <!-- Actions -->
                <div class="flex items-center gap-1.5 flex-shrink-0">
                    <button onclick="viewPlan({{ $plan->id }})" class="text-blue-600 hover:text-blue-800 text-xs p-1 transition" title="View Details">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button onclick="editPlan({{ $plan->id }})" class="text-gray-400 hover:text-blue-600 text-xs p-1 transition" title="Edit">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button onclick="deletePlan({{ $plan->id }})" class="text-gray-400 hover:text-red-600 text-xs p-1 transition" title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-8">
            <div class="w-14 h-14 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-2">
                <i class="fas fa-tasks text-2xl text-gray-400"></i>
            </div>
            <p class="text-gray-500 text-sm">No action plans yet</p>
            <p class="text-xs text-gray-400 mt-0.5">Create your first action plan</p>
            <button onclick="openCreateActionPlanModal()" class="mt-2 bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-lg text-xs transition">
                <i class="fas fa-plus mr-1"></i> Create Plan
            </button>
        </div>
        @endforelse
    </div>
</div>

<script>
// ==================== DELETE PLAN ====================
function deletePlan(planId) {
    if (confirm('Delete this action plan and all its tasks?')) {
        const btn = event.target.closest('button');
        if (btn) {
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            btn.disabled = true;
        }
        
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
                showNotification('Plan deleted successfully!', 'success');
                setTimeout(() => location.reload(), 800);
            } else {
                showNotification('Error deleting plan', 'error');
            }
        });
    }
}

// ==================== TOAST NOTIFICATION ====================
function showNotification(message, type = 'info') {
    const types = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        warning: 'bg-yellow-500',
        info: 'bg-blue-500'
    };
    const icons = {
        success: 'fa-check-circle',
        error: 'fa-exclamation-circle',
        warning: 'fa-exclamation-triangle',
        info: 'fa-info-circle'
    };
    
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-3 py-2 rounded-lg shadow-lg text-white z-50 ${types[type] || 'bg-gray-700'} flex items-center gap-2 animate-slide-in text-sm`;
    notification.innerHTML = `
        <i class="fas ${icons[type] || 'fa-bell'}"></i>
        <span>${message}</span>
        <button onclick="this.parentElement.remove()" class="text-white/70 hover:text-white ml-2">×</button>
    `;
    document.body.appendChild(notification);
    setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transition = 'opacity 0.3s';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Add animation styles
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    .animate-slide-in {
        animation: slideIn 0.3s ease-out;
    }
`;
document.head.appendChild(style);
</script>

@include('modules.intercession.partials.modals')