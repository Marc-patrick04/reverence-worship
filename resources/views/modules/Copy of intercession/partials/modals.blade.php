{{-- Create Action Plan Modal --}}
<div id="createActionPlanModal" class="modal hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center pb-3 border-b">
            <h3 class="text-lg font-bold text-gray-800">Create Action Plan</h3>
            <button onclick="closeModal('createActionPlanModal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form id="create-action-plan-form" method="POST" action="{{ route('intercession.action-plans.store') }}">
            @csrf
            <div class="mt-4 space-y-3">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Title *</label>
                    <input type="text" name="title" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Due Date</label>
                    <input type="date" name="due_date" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Assign To</label>
                    <select name="assigned_to" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Myself</option>
                        @foreach($users ?? [] as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="pending">Pending</option>
                        <option value="in-progress">In Progress</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-end space-x-3 mt-5 pt-3 border-t">
                <button type="button" onclick="closeModal('createActionPlanModal')" class="px-4 py-2 border rounded-lg text-sm hover:bg-gray-50">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700">Create Plan</button>
            </div>
        </form>
    </div>
</div>