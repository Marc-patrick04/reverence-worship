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
                    <div class="flex items-center gap-2 flex-wrap">
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
                    @if($plan->due_date)
                        <div class="flex items-center gap-1 mt-1 text-[10px] text-gray-400">
                            <i class="fas fa-calendar"></i>
                            <span>Due: {{ \Carbon\Carbon::parse($plan->due_date)->format('M d, Y') }}</span>
                        </div>
                    @endif
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

<!-- ==================== CREATE ACTION PLAN MODAL ==================== -->
<div id="createActionPlanModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 backdrop-blur-sm overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-6 border w-full max-w-md shadow-2xl rounded-2xl bg-white">
        <div class="flex justify-between items-center pb-4 border-b">
            <h3 class="text-xl font-bold text-gray-800">Create Action Plan</h3>
            <button onclick="closeModal('createActionPlanModal')" class="text-gray-400 hover:text-gray-600 transition">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form id="createActionPlanForm" onsubmit="submitActionPlan(event)" class="mt-4">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Title <span class="text-red-500">*</span></label>
                    <input type="text" id="planTitle" name="title" placeholder="Enter plan title" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea id="planDescription" name="description" rows="3" placeholder="Describe your action plan..." 
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Due Date</label>
                    <input type="date" id="planDueDate" name="due_date" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6 pt-4 border-t">
                <button type="button" onclick="closeModal('createActionPlanModal')" class="px-5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm transition">
                    Cancel
                </button>
                <button type="submit" id="submitPlanBtn" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm transition flex items-center gap-2">
                    <i class="fas fa-plus"></i> Create Plan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ==================== EDIT ACTION PLAN MODAL ==================== -->
<div id="editActionPlanModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 backdrop-blur-sm overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-6 border w-full max-w-md shadow-2xl rounded-2xl bg-white">
        <div class="flex justify-between items-center pb-4 border-b">
            <h3 class="text-xl font-bold text-gray-800">Edit Action Plan</h3>
            <button onclick="closeModal('editActionPlanModal')" class="text-gray-400 hover:text-gray-600 transition">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form id="editActionPlanForm" onsubmit="updateActionPlan(event)" class="mt-4">
            @csrf
            @method('PUT')
            <input type="hidden" id="editPlanId" name="plan_id">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Title <span class="text-red-500">*</span></label>
                    <input type="text" id="editPlanTitle" name="title" placeholder="Enter plan title" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea id="editPlanDescription" name="description" rows="3" placeholder="Describe your action plan..." 
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Due Date</label>
                    <input type="date" id="editPlanDueDate" name="due_date" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6 pt-4 border-t">
                <button type="button" onclick="closeModal('editActionPlanModal')" class="px-5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm transition">
                    Cancel
                </button>
                <button type="submit" id="updatePlanBtn" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm transition flex items-center gap-2">
                    <i class="fas fa-save"></i> Update Plan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// ==================== MODAL FUNCTIONS ====================
function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }
}

function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
}

// ==================== CREATE ACTION PLAN ====================
function openCreateActionPlanModal() {
    document.getElementById('createActionPlanForm').reset();
    openModal('createActionPlanModal');
}

function submitActionPlan(event) {
    event.preventDefault();
    
    const form = document.getElementById('createActionPlanForm');
    const formData = new FormData(form);
    
    const submitBtn = document.getElementById('submitPlanBtn');
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
    submitBtn.disabled = true;
    
    fetch('/intercession/action-plans', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeModal('createActionPlanModal');
            showNotification('Action plan created successfully!', 'success');
            setTimeout(() => location.reload(), 800);
        } else {
            showNotification('Error: ' + (data.message || 'Failed to create plan'), 'error');
            submitBtn.innerHTML = '<i class="fas fa-plus"></i> Create Plan';
            submitBtn.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Network error: ' + error.message, 'error');
        submitBtn.innerHTML = '<i class="fas fa-plus"></i> Create Plan';
        submitBtn.disabled = false;
    });
}

// ==================== EDIT ACTION PLAN ====================
function editPlan(planId) {
    // Show loading state on the edit button
    const btn = event?.target?.closest('button');
    if (btn) {
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        btn.disabled = true;
    }
    
    fetch(`/intercession/action-plans/${planId}/edit`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (btn) {
            btn.innerHTML = '<i class="fas fa-edit"></i>';
            btn.disabled = false;
        }
        
        if (data.success && data.plan) {
            // Populate the edit form with existing data
            document.getElementById('editPlanId').value = data.plan.id;
            document.getElementById('editPlanTitle').value = data.plan.title || '';
            document.getElementById('editPlanDescription').value = data.plan.description || '';
            document.getElementById('editPlanDueDate').value = data.plan.due_date || '';
            
            // Open the modal
            openModal('editActionPlanModal');
        } else {
            showNotification('Error: ' + (data.message || 'Failed to load plan'), 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Network error: ' + error.message, 'error');
        if (btn) {
            btn.innerHTML = '<i class="fas fa-edit"></i>';
            btn.disabled = false;
        }
    });
}

function updateActionPlan(event) {
    event.preventDefault();
    
    const planId = document.getElementById('editPlanId').value;
    const form = document.getElementById('editActionPlanForm');
    const formData = new FormData(form);
    
    const submitBtn = document.getElementById('updatePlanBtn');
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
    submitBtn.disabled = true;
    
    fetch(`/intercession/action-plans/${planId}`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeModal('editActionPlanModal');
            showNotification('Plan updated successfully!', 'success');
            setTimeout(() => location.reload(), 800);
        } else {
            showNotification('Error: ' + (data.message || 'Failed to update plan'), 'error');
            submitBtn.innerHTML = '<i class="fas fa-save"></i> Update Plan';
            submitBtn.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Network error: ' + error.message, 'error');
        submitBtn.innerHTML = '<i class="fas fa-save"></i> Update Plan';
        submitBtn.disabled = false;
    });
}

// ==================== VIEW PLAN ====================
function viewPlan(planId) {
    window.location.href = `/intercession/action-plans/${planId}`;
}

// ==================== DELETE PLAN ====================
function deletePlan(planId) {
    if (!confirm('Delete this action plan?')) return;
    
    const btn = event?.target?.closest('button');
    if (btn) {
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        btn.disabled = true;
    }
    
    fetch(`/intercession/action-plans/${planId}`, {
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
            showNotification('Plan deleted successfully!', 'success');
            setTimeout(() => location.reload(), 800);
        } else {
            showNotification('Error: ' + (data.message || 'Failed to delete plan'), 'error');
            if (btn) {
                btn.innerHTML = '<i class="fas fa-trash"></i>';
                btn.disabled = false;
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Network error: ' + error.message, 'error');
        if (btn) {
            btn.innerHTML = '<i class="fas fa-trash"></i>';
            btn.disabled = false;
        }
    });
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

// ==================== ADD ANIMATION STYLES ====================
(function addStyles() {
    if (!document.getElementById('intercession-animation-styles')) {
        const styleEl = document.createElement('style');
        styleEl.id = 'intercession-animation-styles';
        styleEl.textContent = `
            @keyframes slideIn {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
            .animate-slide-in {
                animation: slideIn 0.3s ease-out;
            }
        `;
        document.head.appendChild(styleEl);
    }
})();
</script>