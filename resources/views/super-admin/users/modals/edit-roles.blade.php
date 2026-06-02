<form method="POST" action="{{ route('users.update-roles', $user->id) }}">
    @csrf
    <div class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">User: <strong>{{ $user->name }}</strong></label>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Assign Roles</label>
            <div class="space-y-2 max-h-64 overflow-y-auto border rounded-lg p-3">
                @foreach($roles as $role)
                    <label class="flex items-center space-x-3 p-2 hover:bg-gray-50 rounded cursor-pointer">
                        <input type="checkbox" name="roles[]" value="{{ $role->id }}" 
                               {{ $user->roles->contains($role->id) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <div>
                            <span class="text-sm font-medium text-gray-700">{{ $role->display_name }}</span>
                            <p class="text-xs text-gray-500">{{ $role->description }}</p>
                        </div>
                    </label>
                @endforeach
            </div>
        </div>
    </div>
    
    <div class="flex justify-end space-x-3 mt-6 pt-6 border-t">
        <button type="button" onclick="closeModal('editRolesModal')" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
            Cancel
        </button>
        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
            <i class="fas fa-save mr-2"></i>
            Save Roles
        </button>
    </div>
</form>