<form method="POST" action="{{ route('users.update', $user->id) }}" id="editUserForm">
    @csrf
    @method('PUT')
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-h-96 overflow-y-auto p-2">
        <!-- Basic Information -->
        <div class="md:col-span-2">
            <h4 class="text-md font-bold text-gray-700 mb-2 border-b pb-1">Basic Information</h4>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
            <input type="text" name="name" required value="{{ $user->name }}"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email Address *</label>
            <input type="email" name="email" required value="{{ $user->email }}"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
            <input type="text" name="phone" value="{{ $user->phone }}"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
            <input type="date" name="date_of_birth" value="{{ $user->date_of_birth }}"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        
        <!-- Address Information -->
        <div class="md:col-span-2">
            <h4 class="text-md font-bold text-gray-700 mb-2 border-b pb-1 mt-2">Address Information</h4>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Province</label>
            <select name="province" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Select Province</option>
                <option value="Kigali" {{ $user->province == 'Kigali' ? 'selected' : '' }}>Kigali</option>
                <option value="Northern" {{ $user->province == 'Northern' ? 'selected' : '' }}>Northern</option>
                <option value="Southern" {{ $user->province == 'Southern' ? 'selected' : '' }}>Southern</option>
                <option value="Eastern" {{ $user->province == 'Eastern' ? 'selected' : '' }}>Eastern</option>
                <option value="Western" {{ $user->province == 'Western' ? 'selected' : '' }}>Western</option>
            </select>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">District</label>
            <input type="text" name="district" value="{{ $user->district }}"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Sector</label>
            <input type="text" name="sector" value="{{ $user->sector }}"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Village</label>
            <input type="text" name="village" value="{{ $user->village }}"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        
        <!-- Personal Information -->
        <div class="md:col-span-2">
            <h4 class="text-md font-bold text-gray-700 mb-2 border-b pb-1 mt-2">Personal Information</h4>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
            <select name="gender" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                <option value="">Select Gender</option>
                <option value="Male" {{ $user->gender == 'Male' ? 'selected' : '' }}>Male</option>
                <option value="Female" {{ $user->gender == 'Female' ? 'selected' : '' }}>Female</option>
                <option value="Other" {{ $user->gender == 'Other' ? 'selected' : '' }}>Other</option>
            </select>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Marital Status</label>
            <select name="marital_status" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                <option value="">Select Status</option>
                <option value="Single" {{ $user->marital_status == 'Single' ? 'selected' : '' }}>Single</option>
                <option value="Married" {{ $user->marital_status == 'Married' ? 'selected' : '' }}>Married</option>
                <option value="Divorced" {{ $user->marital_status == 'Divorced' ? 'selected' : '' }}>Divorced</option>
                <option value="Widowed" {{ $user->marital_status == 'Widowed' ? 'selected' : '' }}>Widowed</option>
            </select>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Membership Type</label>
            <select name="membership_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                <option value="Regular" {{ $user->membership_type == 'Regular' ? 'selected' : '' }}>Regular</option>
                <option value="Permanent" {{ $user->membership_type == 'Permanent' ? 'selected' : '' }}>Permanent</option>
                <option value="Visitor" {{ $user->membership_type == 'Visitor' ? 'selected' : '' }}>Visitor</option>
                <option value="Associate" {{ $user->membership_type == 'Associate' ? 'selected' : '' }}>Associate</option>
            </select>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Occupation</label>
            <input type="text" name="occupation" value="{{ $user->occupation }}"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Ministry Role</label>
            <input type="text" name="ministry_role" value="{{ $user->ministry_role }}"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg">
        </div>
        
        <!-- Emergency Contact -->
        <div class="md:col-span-2">
            <h4 class="text-md font-bold text-gray-700 mb-2 border-b pb-1 mt-2">Emergency Contact</h4>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Emergency Name</label>
            <input type="text" name="emergency_name" value="{{ $user->emergency_name }}"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Emergency Phone</label>
            <input type="text" name="emergency_contact" value="{{ $user->emergency_contact }}"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg">
        </div>
        
        <!-- Singer Information -->
        <div class="md:col-span-2">
            <h4 class="text-md font-bold text-purple-700 mb-2 border-b pb-1 mt-2">Singer Information</h4>
        </div>
        
        <div class="md:col-span-2">
            <label class="flex items-center space-x-2">
                <input type="checkbox" name="is_singer" value="1" id="isSingerCheckbox"
                       {{ $user->is_singer ? 'checked' : '' }}
                       class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                <span class="text-sm font-medium text-gray-700">This user is a singer in the worship team</span>
            </label>
        </div>
        
        <div id="singerFields" class="md:col-span-2 {{ $user->is_singer ? '' : 'hidden' }}">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2 p-3 bg-purple-50 rounded-lg">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Voice Part</label>
                    <select name="voice_part" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        <option value="">Select Voice Part</option>
                        <option value="Soprano" {{ $user->voice_part == 'Soprano' ? 'selected' : '' }}>Soprano</option>
                        <option value="Alto" {{ $user->voice_part == 'Alto' ? 'selected' : '' }}>Alto</option>
                        <option value="Tenor" {{ $user->voice_part == 'Tenor' ? 'selected' : '' }}>Tenor</option>
                        <option value="Bass" {{ $user->voice_part == 'Bass' ? 'selected' : '' }}>Bass</option>
                        <option value="Lead" {{ $user->voice_part == 'Lead' ? 'selected' : '' }}>Lead Vocalist</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Singer Level</label>
                    <select name="singer_level" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        <option value="">Select Level</option>
                        <option value="Normal" {{ $user->singer_level == 'Normal' ? 'selected' : '' }}>Normal</option>
                        <option value="Good" {{ $user->singer_level == 'Good' ? 'selected' : '' }}>Good</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Singer Notes</label>
                    <textarea name="singer_notes" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg" 
                              placeholder="Special skills, available days, etc...">{{ $user->singer_notes }}</textarea>
                </div>
            </div>
        </div>
        
        <!-- Additional Info -->
        <div class="md:col-span-2">
            <h4 class="text-md font-bold text-gray-700 mb-2 border-b pb-1 mt-2">Additional Information</h4>
        </div>
        
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Skills / Talents</label>
            <textarea name="skills" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg">{{ $user->skills }}</textarea>
        </div>
        
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
            <textarea name="notes" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg">{{ $user->notes }}</textarea>
        </div>
        
        <!-- Password -->
        <div class="md:col-span-2">
            <h4 class="text-md font-bold text-gray-700 mb-2 border-b pb-1 mt-2">Security</h4>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">New Password (Optional)</label>
            <input type="password" name="password" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
            <p class="text-xs text-gray-500 mt-1">Leave blank to keep current password</p>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
            <input type="password" name="password_confirmation" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
        </div>
        
        <!-- Roles -->
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-2">Assign Roles</label>
            <div class="grid grid-cols-2 gap-2 max-h-32 overflow-y-auto border rounded-lg p-3">
                @foreach($roles as $role)
                    <label class="flex items-center space-x-2 p-1 hover:bg-gray-50 rounded cursor-pointer">
                        <input type="checkbox" name="roles[]" value="{{ $role->id }}" 
                               {{ $user->roles->contains($role->id) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm text-gray-700">{{ $role->display_name }}</span>
                    </label>
                @endforeach
            </div>
        </div>
    </div>
    
    <div class="flex justify-end space-x-3 mt-6 pt-6 border-t">
        <button type="button" onclick="closeModal('editModal')" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
            Cancel
        </button>
        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
            <i class="fas fa-save mr-2"></i>
            Update User
        </button>
    </div>
</form>

<script>
// Script to handle singer fields toggle
(function() {
    // Wait for DOM to be ready
    function initSingerFields() {
        const isSingerCheckbox = document.getElementById('isSingerCheckbox');
        const singerFields = document.getElementById('singerFields');
        
        if (isSingerCheckbox && singerFields) {
            // Set initial state
            if (isSingerCheckbox.checked) {
                singerFields.classList.remove('hidden');
            } else {
                singerFields.classList.add('hidden');
            }
            
            // Add change event listener
            isSingerCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    singerFields.classList.remove('hidden');
                } else {
                    singerFields.classList.add('hidden');
                }
            });
        }
    }
    
    // Run when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSingerFields);
    } else {
        initSingerFields();
    }
})();
</script>