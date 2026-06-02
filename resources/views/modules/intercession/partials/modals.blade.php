{{-- Create Action Plan Modal --}}
<div id="createActionPlanModal" class="modal hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-5 border w-full max-w-3xl shadow-lg rounded-lg bg-white mb-10">
        <div class="flex justify-between items-center pb-3 border-b">
            <h3 class="text-xl font-bold text-gray-800">Create New Action Plan</h3>
            <button onclick="closeModal('createActionPlanModal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <form id="create-action-plan-form" method="POST" action="{{ route('intercession.action-plans.store') }}">
            @csrf
            
            <div class="mt-4 space-y-6">
                <!-- Plan Details -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Plan Title <span class="text-red-500">*</span></label>
                    <input type="text" name="title" required 
                           placeholder="Enter action plan title"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Target Date</label>
                        <input type="date" name="due_date" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Assign To</label>
                        <select name="assigned_to" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Myself</option>
                            @foreach($users ?? [] as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="3" 
                              placeholder="Describe the action plan..."
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 mt-5 pt-3 border-t">
                <button type="button" onclick="closeModal('createActionPlanModal')" class="px-4 py-2 border rounded-lg text-sm hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700">
                    Create Action Plan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Make sure functions are available globally
window.openCreateActionPlanModal = function() {
    const modal = document.getElementById('createActionPlanModal');
    if (modal) {
        modal.classList.remove('hidden');
    }
}

window.closeModal = function(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('hidden');
    }
}

// Action plan form submission
document.getElementById('create-action-plan-form')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch(this.action, {
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
            window.closeModal('createActionPlanModal');
            this.reset();
            // Show success notification
            const notification = document.createElement('div');
            notification.className = 'fixed top-4 right-4 px-4 py-2 bg-green-500 text-white rounded-lg shadow-lg z-50';
            notification.innerHTML = 'Action plan created successfully!';
            document.body.appendChild(notification);
            setTimeout(() => notification.remove(), 3000);
            setTimeout(() => location.reload(), 1000);
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error creating action plan');
    });
});
</script>