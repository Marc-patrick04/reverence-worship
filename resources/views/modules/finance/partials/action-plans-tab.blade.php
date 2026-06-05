<div class="bg-white rounded-xl shadow-md p-6">
    
    <!-- Header Section -->
    <div class="mb-6">
        <h2 class="text-xl font-bold text-gray-800">Financial Management Action Plans</h2>
        <p class="text-gray-500 text-sm mt-1">Manage and track departmental action plans and tasks</p>
    </div>

    <!-- Action Plans Overview -->
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-5 mb-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="font-semibold text-gray-700">Action Plans Overview</h3>
            <div class="flex items-center gap-2">
                <span class="text-sm text-gray-500">Financial Management DPT</span>
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
                <p class="text-2xl font-bold text-gray-800" id="statTotalPlans">0</p>
                <p class="text-xs text-gray-500">Total Action Plans</p>
            </div>
            
            <!-- Completed Tasks -->
            <div class="bg-white rounded-lg p-4 text-center shadow-sm">
                <div class="flex items-center justify-center mb-2">
                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                </div>
                <p class="text-2xl font-bold text-gray-800" id="statCompletedPlans">0</p>
                <p class="text-xs text-gray-500">Completed Tasks</p>
            </div>
            
            <!-- Overall Progress -->
            <div class="bg-white rounded-lg p-4 text-center shadow-sm">
                <div class="flex items-center justify-center mb-2">
                    <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-chart-line text-purple-600"></i>
                    </div>
                </div>
                <p class="text-2xl font-bold text-purple-600" id="statOverallProgress">0%</p>
                <p class="text-xs text-gray-500">Overall Progress</p>
            </div>
            
            <!-- In Progress -->
            <div class="bg-white rounded-lg p-4 text-center shadow-sm">
                <div class="flex items-center justify-center mb-2">
                    <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-spinner text-yellow-600"></i>
                    </div>
                </div>
                <p class="text-2xl font-bold text-gray-800" id="statInProgressPlans">0</p>
                <p class="text-xs text-gray-500">In Progress</p>
            </div>
        </div>
    </div>

    <!-- Summary Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-gray-50 rounded-lg p-4 text-center">
            <p class="text-2xl font-bold text-blue-600" id="summaryTotalPlans">0</p>
            <p class="text-xs text-gray-500">Total Plans</p>
        </div>
        <div class="bg-gray-50 rounded-lg p-4 text-center">
            <p class="text-2xl font-bold text-green-600" id="summaryCompletedPlans">0</p>
            <p class="text-xs text-gray-500">Completed</p>
        </div>
        <div class="bg-gray-50 rounded-lg p-4 text-center">
            <p class="text-2xl font-bold text-purple-600" id="summaryAvgProgress">0%</p>
            <p class="text-xs text-gray-500">Average Progress</p>
        </div>
        <div class="bg-gray-50 rounded-lg p-4 text-center">
            <p class="text-2xl font-bold text-orange-600" id="summaryPendingPlans">0</p>
            <p class="text-xs text-gray-500">Pending</p>
        </div>
    </div>

    <!-- All Action Plans Header -->
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-bold text-gray-800">All Action Plans</h3>
        <button onclick="openActionPlanModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm flex items-center gap-2">
            <i class="fas fa-plus"></i> New Action Plan
        </button>
    </div>

    <!-- Progress Bar for Overall -->
    <div class="mb-6">
        <div class="flex justify-between text-sm text-gray-600 mb-1">
            <span>Overall Progress</span>
            <span id="overallProgressText">0%</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2">
            <div id="overallProgressBar" class="bg-indigo-600 h-2 rounded-full transition-all duration-500" style="width: 0%"></div>
        </div>
    </div>

    <!-- Action Plans List -->
    <div id="actionPlansList" class="space-y-3">
        <div class="text-center py-12">
            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-tasks text-3xl text-gray-400"></i>
            </div>
            <p class="text-gray-500">Loading action plans...</p>
        </div>
    </div>
</div>

<!-- Create/Edit Action Plan Modal -->
<div id="actionPlanModal" class="modal hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-6 border w-full max-w-lg shadow-xl rounded-2xl bg-white">
        <div class="flex justify-between items-center pb-4 border-b">
            <h3 id="actionPlanModalTitle" class="text-xl font-bold text-gray-800">Create Action Plan</h3>
            <button onclick="closeModal('actionPlanModal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form id="actionPlanForm" onsubmit="submitActionPlan(event)">
            @csrf
            <input type="hidden" id="planId" name="plan_id">
            <div class="mt-4 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
                    <input type="text" id="planTitle" name="title" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea id="planDescription" name="description" rows="3" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Due Date</label>
                        <input type="date" id="planDueDate" name="due_date" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                        <select id="planPriority" name="priority" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                            <option value="low">Low</option>
                            <option value="medium" selected>Medium</option>
                            <option value="high">High</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Budget (RWF)</label>
                    <input type="number" id="planBudget" name="budget" step="0.01" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Assigned To</label>
                    <select id="planAssignedTo" name="assigned_to" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        <option value="">Select Member</option>
                        @foreach($users ?? [] as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6 pt-4 border-t">
                <button type="button" onclick="closeModal('actionPlanModal')" class="px-4 py-2 border rounded-lg text-sm">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm">Save Plan</button>
            </div>
        </form>
    </div>
</div>

<!-- Update Progress Modal -->
<div id="progressModal" class="modal hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-6 border w-full max-w-md shadow-xl rounded-2xl bg-white">
        <div class="flex justify-between items-center pb-4 border-b">
            <h3 class="text-xl font-bold text-gray-800">Update Progress</h3>
            <button onclick="closeModal('progressModal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="mt-4 space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Progress Percentage</label>
                <input type="range" id="progressSlider" min="0" max="100" value="0" 
                       class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                <div class="flex justify-between mt-2">
                    <span class="text-sm text-gray-500">0%</span>
                    <span id="progressValue" class="text-lg font-bold text-indigo-600">0%</span>
                    <span class="text-sm text-gray-500">100%</span>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select id="progressStatus" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="pending">Pending</option>
                    <option value="in-progress">In Progress</option>
                    <option value="completed">Completed</option>
                </select>
            </div>
        </div>
        <div class="flex justify-end gap-3 mt-6 pt-4 border-t">
            <button type="button" onclick="closeModal('progressModal')" class="px-4 py-2 border rounded-lg text-sm">Cancel</button>
            <button type="button" onclick="saveProgress()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm">Update Progress</button>
        </div>
    </div>
</div>

<script>
let currentPlanId = null;

function filterActionPlans() {
    fetch('/finance/action-plans/filter?status=all&priority=all', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateActionPlansList(data.plans);
            updateStats(data.plans);
        }
    })
    .catch(error => console.error('Error:', error));
}

function updateActionPlansList(plans) {
    const container = document.getElementById('actionPlansList');
    if (!plans || plans.length === 0) {
        container.innerHTML = `
            <div class="text-center py-12">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-tasks text-3xl text-gray-400"></i>
                </div>
                <p class="text-gray-500">No action plans yet</p>
                <p class="text-sm text-gray-400 mt-1">Create your first action plan to get started</p>
                <button onclick="openActionPlanModal()" class="mt-4 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm">
                    <i class="fas fa-plus mr-2"></i> Create Action Plan
                </button>
            </div>
        `;
        return;
    }
    
    container.innerHTML = plans.map(plan => `
        <div class="border rounded-lg p-4 hover:shadow-md transition-all duration-300">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-3">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-1 flex-wrap">
                        <h4 class="font-semibold text-gray-800">${escapeHtml(plan.title)}</h4>
                        <span class="px-2 py-0.5 text-xs rounded-full ${getStatusBadgeClass(plan.status)}">
                            ${getStatusIcon(plan.status)} ${getStatusText(plan.status)}
                        </span>
                        <span class="px-2 py-0.5 text-xs rounded-full ${getPriorityBadgeClass(plan.priority)}">
                            <i class="fas ${getPriorityIcon(plan.priority)} mr-1"></i> ${getPriorityText(plan.priority)}
                        </span>
                    </div>
                    <p class="text-sm text-gray-600">${escapeHtml(plan.description || 'No description')}</p>
                    <div class="flex flex-wrap gap-4 mt-2 text-xs text-gray-500">
                        ${plan.due_date ? `<span class="flex items-center gap-1"><i class="fas fa-calendar"></i> Due: ${formatDate(plan.due_date)}</span>` : ''}
                        <span class="flex items-center gap-1"><i class="fas fa-dollar-sign"></i> Budget: RWF ${parseFloat(plan.budget || 0).toLocaleString()}</span>
                        <span class="flex items-center gap-1"><i class="fas fa-clock"></i> Created: ${formatDate(plan.created_at)}</span>
                    </div>
                    <div class="mt-2">
                        <div class="flex justify-between text-xs mb-1">
                            <span>Progress</span>
                            <span>${plan.progress || 0}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-indigo-600 h-2 rounded-full" style="width: ${plan.progress || 0}%"></div>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center gap-2">
                    <select onchange="updatePlanStatus(${plan.id}, this.value)" 
                            class="text-sm border rounded-lg px-3 py-1 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="pending" ${plan.status == 'pending' ? 'selected' : ''}>Pending</option>
                        <option value="in-progress" ${plan.status == 'in-progress' ? 'selected' : ''}>In Progress</option>
                        <option value="completed" ${plan.status == 'completed' ? 'selected' : ''}>Completed</option>
                    </select>
                    
                    <div class="relative">
                        <button onclick="togglePlanMenu(${plan.id})" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <div id="plan-menu-${plan.id}" class="hidden absolute right-0 mt-2 w-36 bg-white rounded-md shadow-lg z-10 border">
                            <button onclick="openProgressModal(${plan.id}, ${plan.progress || 0}, '${plan.status}')" 
                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-chart-line mr-2"></i> Update Progress
                            </button>
                            <button onclick="editActionPlan(${plan.id})" 
                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-edit mr-2"></i> Edit
                            </button>
                            <button onclick="deleteActionPlan(${plan.id})" 
                                    class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                <i class="fas fa-trash mr-2"></i> Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `).join('');
}

function updateStats(plans) {
    const total = plans.length;
    const completed = plans.filter(p => p.status === 'completed').length;
    const inProgress = plans.filter(p => p.status === 'in-progress').length;
    const pending = plans.filter(p => p.status === 'pending').length;
    const totalProgress = plans.reduce((sum, p) => sum + (p.progress || 0), 0);
    const avgProgress = total > 0 ? Math.round(totalProgress / total) : 0;
    
    document.getElementById('statTotalPlans').textContent = total;
    document.getElementById('statCompletedPlans').textContent = completed;
    document.getElementById('statOverallProgress').textContent = avgProgress + '%';
    document.getElementById('statInProgressPlans').textContent = inProgress;
    
    document.getElementById('summaryTotalPlans').textContent = total;
    document.getElementById('summaryCompletedPlans').textContent = completed;
    document.getElementById('summaryAvgProgress').textContent = avgProgress + '%';
    document.getElementById('summaryPendingPlans').textContent = pending;
    
    document.getElementById('overallProgressText').textContent = avgProgress + '%';
    document.getElementById('overallProgressBar').style.width = avgProgress + '%';
}

function updatePlanStatus(planId, status) {
    fetch(`/finance/action-plans/${planId}`, {
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
            filterActionPlans();
        } else {
            alert('Error updating status');
        }
    });
}

function openProgressModal(planId, currentProgress, currentStatus) {
    currentPlanId = planId;
    document.getElementById('progressSlider').value = currentProgress;
    document.getElementById('progressValue').textContent = currentProgress + '%';
    document.getElementById('progressStatus').value = currentStatus;
    document.getElementById('progressModal').classList.remove('hidden');
}

function saveProgress() {
    const progress = document.getElementById('progressSlider').value;
    const status = document.getElementById('progressStatus').value;
    
    fetch(`/finance/action-plans/${currentPlanId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ progress: parseInt(progress), status: status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeModal('progressModal');
            filterActionPlans();
            alert('Progress updated successfully!');
        } else {
            alert('Error updating progress');
        }
    });
}

function togglePlanMenu(planId) {
    const menu = document.getElementById(`plan-menu-${planId}`);
    if (menu) {
        menu.classList.toggle('hidden');
    }
}

function openActionPlanModal(planId = null) {
    if (planId) {
        document.getElementById('actionPlanModalTitle').textContent = 'Edit Action Plan';
        fetch(`/finance/action-plans/${planId}/edit`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('planId').value = data.plan.id;
                document.getElementById('planTitle').value = data.plan.title;
                document.getElementById('planDescription').value = data.plan.description || '';
                document.getElementById('planDueDate').value = data.plan.due_date || '';
                document.getElementById('planPriority').value = data.plan.priority || 'medium';
                document.getElementById('planBudget').value = data.plan.budget || 0;
                document.getElementById('planAssignedTo').value = data.plan.assigned_to || '';
                document.getElementById('actionPlanModal').classList.remove('hidden');
            }
        });
    } else {
        document.getElementById('actionPlanModalTitle').textContent = 'Create Action Plan';
        document.getElementById('planId').value = '';
        document.getElementById('planTitle').value = '';
        document.getElementById('planDescription').value = '';
        document.getElementById('planDueDate').value = '';
        document.getElementById('planPriority').value = 'medium';
        document.getElementById('planBudget').value = '';
        document.getElementById('planAssignedTo').value = '';
        document.getElementById('actionPlanModal').classList.remove('hidden');
    }
}

function editActionPlan(planId) {
    openActionPlanModal(planId);
}

function submitActionPlan(event) {
    event.preventDefault();
    
    const planId = document.getElementById('planId').value;
    const formData = new FormData();
    formData.append('title', document.getElementById('planTitle').value);
    formData.append('description', document.getElementById('planDescription').value);
    formData.append('due_date', document.getElementById('planDueDate').value);
    formData.append('priority', document.getElementById('planPriority').value);
    formData.append('budget', document.getElementById('planBudget').value);
    formData.append('assigned_to', document.getElementById('planAssignedTo').value);
    
    const url = planId ? `/finance/action-plans/${planId}` : '/finance/action-plans/store';
    const method = planId ? 'PUT' : 'POST';
    
    fetch(url, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeModal('actionPlanModal');
            filterActionPlans();
            alert(planId ? 'Action plan updated successfully!' : 'Action plan created successfully!');
        } else {
            alert('Error: ' + (data.message || 'Failed to save plan'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Network error: ' + error.message);
    });
}

function deleteActionPlan(planId) {
    if (confirm('Are you sure you want to delete this action plan?')) {
        fetch(`/finance/action-plans/${planId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                filterActionPlans();
                alert('Action plan deleted successfully');
            } else {
                alert('Error deleting plan');
            }
        });
    }
}

function getStatusBadgeClass(status) {
    switch(status) {
        case 'completed': return 'bg-green-100 text-green-700';
        case 'in-progress': return 'bg-yellow-100 text-yellow-700';
        default: return 'bg-gray-100 text-gray-600';
    }
}

function getStatusIcon(status) {
    switch(status) {
        case 'completed': return '<i class="fas fa-check-circle mr-1"></i>';
        case 'in-progress': return '<i class="fas fa-spinner mr-1"></i>';
        default: return '<i class="fas fa-clock mr-1"></i>';
    }
}

function getStatusText(status) {
    switch(status) {
        case 'completed': return 'Completed';
        case 'in-progress': return 'In Progress';
        default: return 'Pending';
    }
}

function getPriorityBadgeClass(priority) {
    switch(priority) {
        case 'high': return 'bg-red-100 text-red-700';
        case 'medium': return 'bg-yellow-100 text-yellow-700';
        case 'low': return 'bg-green-100 text-green-700';
        default: return 'bg-gray-100 text-gray-700';
    }
}

function getPriorityIcon(priority) {
    switch(priority) {
        case 'high': return 'fa-arrow-up';
        case 'medium': return 'fa-minus';
        case 'low': return 'fa-arrow-down';
        default: return 'fa-minus';
    }
}

function getPriorityText(priority) {
    switch(priority) {
        case 'high': return 'High';
        case 'medium': return 'Medium';
        case 'low': return 'Low';
        default: return 'Medium';
    }
}

function formatDate(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-GB', { day: '2-digit', month: '2-digit', year: 'numeric' });
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}


// Progress slider listener
document.getElementById('progressSlider')?.addEventListener('input', function() {
    document.getElementById('progressValue').textContent = this.value + '%';
});

// Close menu when clicking outside
document.addEventListener('click', function(event) {
    if (!event.target.closest('[onclick*="togglePlanMenu"]')) {
        document.querySelectorAll('[id^="plan-menu-"]').forEach(menu => {
            menu.classList.add('hidden');
        });
    }
});

// Load initial data
filterActionPlans();
</script>