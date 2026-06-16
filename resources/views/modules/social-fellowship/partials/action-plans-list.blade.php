{{-- Debug info --}}
@php
    $planCount = isset($actionPlans) ? $actionPlans->count() : 0;
@endphp


<div class="bg-white rounded-xl shadow-md p-6">
    
    <div class="mb-6">
        <h2 class="text-xl font-bold text-gray-800">Action Plans</h2>
        <p class="text-gray-500 text-sm mt-1">Manage departmental action plans and tasks</p>
    </div>
    
    <!-- Action Plans Overview -->
    <div class="bg-gray-50 rounded-xl p-5 mb-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="font-semibold text-gray-700">Action Plans Overview</h3>
            <div class="flex items-center gap-2">
                <span class="text-sm text-gray-500">Social Fellowship DPT</span>
                <i class="fas fa-chevron-down text-gray-400 text-sm"></i>
            </div>
        </div>
        
        <!-- Stats Cards Row -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-lg p-4 text-center shadow-sm">
                <div class="flex items-center justify-center mb-2">
                    <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-tasks text-gray-600"></i>
                    </div>
                </div>
                <p class="text-2xl font-bold text-gray-800">{{ $totalActionPlans ?? 0 }}</p>
                <p class="text-xs text-gray-500">Total Action Plans</p>
            </div>
            
            <div class="bg-white rounded-lg p-4 text-center shadow-sm">
                <div class="flex items-center justify-center mb-2">
                    <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-check-circle text-gray-600"></i>
                    </div>
                </div>
                <p class="text-2xl font-bold text-gray-800">{{ $completedPlans ?? 0 }}</p>
                <p class="text-xs text-gray-500">Completed Tasks</p>
            </div>
            
            <div class="bg-white rounded-lg p-4 text-center shadow-sm">
                <div class="flex items-center justify-center mb-2">
                    <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-chart-line text-gray-600"></i>
                    </div>
                </div>
                <p class="text-2xl font-bold text-gray-800">{{ $overallProgress ?? 0 }}%</p>
                <p class="text-xs text-gray-500">Overall Progress</p>
            </div>
            
            <div class="bg-white rounded-lg p-4 text-center shadow-sm">
                <div class="flex items-center justify-center mb-2">
                    <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-spinner text-gray-600"></i>
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
            <p class="text-2xl font-bold text-gray-800">{{ $totalActionPlans ?? 0 }}</p>
            <p class="text-xs text-gray-500">Total Plans</p>
        </div>
        <div class="bg-gray-50 rounded-lg p-4 text-center">
            <p class="text-2xl font-bold text-gray-800">{{ $completedPlans ?? 0 }}</p>
            <p class="text-xs text-gray-500">Completed</p>
        </div>
        <div class="bg-gray-50 rounded-lg p-4 text-center">
            <p class="text-2xl font-bold text-gray-800">{{ $overallProgress ?? 0 }}%</p>
            <p class="text-xs text-gray-500">Average Progress</p>
        </div>
        <div class="bg-gray-50 rounded-lg p-4 text-center">
            <p class="text-2xl font-bold text-gray-800">{{ $pendingPlans ?? 0 }}</p>
            <p class="text-xs text-gray-500">Pending</p>
        </div>
    </div>
    
    <!-- Action Plans List Header -->
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-bold text-gray-800">All Action Plans</h3>
        <div class="flex gap-2">
            <button onclick="refreshActionPlans()" class="text-gray-500 hover:text-gray-700 text-sm">
                <i class="fas fa-sync-alt mr-1"></i> Refresh
            </button>
            <button onclick="openActionPlanModal()" class="bg-gray-800 hover:bg-gray-900 text-white px-3 py-1.5 rounded-lg text-sm flex items-center gap-1">
                <i class="fas fa-plus"></i> New Plan
            </button>
        </div>
    </div>
    
    <!-- Progress Bar for Overall -->
    <div class="mb-6">
        <div class="flex justify-between text-sm text-gray-600 mb-1">
            <span>Overall Progress</span>
            <span>{{ $overallProgress ?? 0 }}%</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2">
            <div class="bg-gray-700 h-2 rounded-full transition-all duration-500" style="width: {{ $overallProgress ?? 0 }}%"></div>
        </div>
    </div>
    
    <!-- Action Plans List -->
    <div id="actionPlansList" class="space-y-3">
        @forelse($actionPlans ?? [] as $plan)
        <div class="border rounded-lg p-4 hover:shadow-md transition">
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
                        <span class="flex items-center gap-1">
                            <i class="fas fa-chart-line"></i> Progress: {{ $plan->progress ?? 0 }}%
                        </span>
                    </div>
                    <div class="mt-2 w-full bg-gray-200 rounded-full h-1.5">
                        <div class="bg-gray-700 h-1.5 rounded-full" style="width: {{ $plan->progress ?? 0 }}%"></div>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button onclick="editActionPlan({{ $plan->id }})" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button onclick="deleteActionPlan({{ $plan->id }})" class="text-gray-400 hover:text-red-600">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-12">
            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-clipboard-list text-3xl text-gray-400"></i>
            </div>
            <p class="text-gray-500">No action plans yet</p>
            <p class="text-sm text-gray-400 mt-1">Create your first action plan to get started</p>
            <button onclick="openActionPlanModal()" class="mt-4 bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded-lg text-sm">
                <i class="fas fa-plus mr-2"></i> Create Action Plan
            </button>
        </div>
        @endforelse
    </div>
</div>

<!-- Action Plan Modal -->
<div id="actionPlanModal" class="modal hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-6 border w-full max-w-md shadow-lg rounded-xl bg-white">
        <div class="flex justify-between items-center pb-4 border-b">
            <h3 id="actionPlanModalTitle" class="text-xl font-bold text-gray-800">Create New Action Plan</h3>
            <button onclick="closeModal('actionPlanModal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <form id="actionPlanForm" method="POST">
            @csrf
            <input type="hidden" id="action_plan_id" name="action_plan_id">
            <input type="hidden" id="form_method" name="_method" value="POST">
            
            <div class="mt-5 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Title <span class="text-red-500">*</span></label>
                    <input type="text" id="action_plan_title" name="title" required 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-gray-500 focus:border-gray-500"
                           placeholder="Enter action plan title">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea id="action_plan_description" name="description" rows="3" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-gray-500 focus:border-gray-500"
                              placeholder="Describe the action plan..."></textarea>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Select Family <span class="text-red-500">*</span></label>
                    <select id="action_plan_family_id" name="family_id" required 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-gray-500 focus:border-gray-500">
                        <option value="">Select a family</option>
                        @foreach($families ?? [] as $family)
                            <option value="{{ $family->id }}">{{ $family->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Due Date</label>
                        <input type="date" id="action_plan_due_date" name="due_date" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-gray-500 focus:border-gray-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Progress (%)</label>
                        <input type="number" id="action_plan_progress" name="progress" min="0" max="100" value="0"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-gray-500 focus:border-gray-500">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select id="action_plan_status" name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-gray-500 focus:border-gray-500">
                        <option value="pending">Pending</option>
                        <option value="in-progress">In Progress</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>
            </div>
            
            <div class="flex justify-end gap-3 mt-6 pt-4 border-t">
                <button type="button" onclick="closeModal('actionPlanModal')" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-gray-800 hover:bg-gray-900 text-white rounded-lg transition">Save Plan</button>
            </div>
        </form>
    </div>
</div>

<script>
// Action Plan CRUD functions
function openActionPlanModal() {
    document.getElementById('actionPlanModalTitle').textContent = 'Create New Action Plan';
    document.getElementById('action_plan_id').value = '';
    document.getElementById('form_method').value = 'POST';
    document.getElementById('action_plan_title').value = '';
    document.getElementById('action_plan_description').value = '';
    document.getElementById('action_plan_family_id').value = '';
    document.getElementById('action_plan_due_date').value = '';
    document.getElementById('action_plan_progress').value = '0';
    document.getElementById('action_plan_status').value = 'pending';
    document.getElementById('actionPlanModal').classList.remove('hidden');
}

function editActionPlan(id) {
    fetch(`/social-fellowship/action-plans/${id}/edit`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('actionPlanModalTitle').textContent = 'Edit Action Plan';
            document.getElementById('action_plan_id').value = data.plan.id;
            document.getElementById('form_method').value = 'PUT';
            document.getElementById('action_plan_title').value = data.plan.title;
            document.getElementById('action_plan_description').value = data.plan.description || '';
            document.getElementById('action_plan_family_id').value = data.plan.family_id;
            document.getElementById('action_plan_due_date').value = data.plan.due_date;
            document.getElementById('action_plan_progress').value = data.plan.progress || 0;
            document.getElementById('action_plan_status').value = data.plan.status;
            document.getElementById('actionPlanModal').classList.remove('hidden');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error loading action plan');
    });
}

function deleteActionPlan(id) {
    if (confirm('Are you sure you want to delete this action plan?')) {
        fetch(`/social-fellowship/action-plans/${id}`, {
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
                showNotification('Action plan deleted successfully!', 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                alert('Error deleting action plan');
            }
        });
    }
}

function refreshActionPlans() {
    location.reload();
}

// Action Plan form submission
document.getElementById('actionPlanForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const planId = document.getElementById('action_plan_id').value;
    const method = document.getElementById('form_method').value;
    
    let url = '{{ route("social-fellowship.action-plans.store") }}';
    if (method === 'PUT' && planId) {
        url = `/social-fellowship/action-plans/${planId}`;
        formData.append('_method', 'PUT');
    }
    
    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeModal('actionPlanModal');
            showNotification('Action plan saved successfully!', 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            alert('Error: ' + data.message);
        }
    });
});

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
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
</script>