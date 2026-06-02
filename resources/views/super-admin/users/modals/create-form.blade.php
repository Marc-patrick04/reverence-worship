<form method="POST" action="{{ route('users.store') }}" id="createUserForm">
    @csrf
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-h-96 overflow-y-auto p-2">
        <!-- Basic Information -->
        <div class="md:col-span-2">
            <h4 class="text-md font-bold text-blue-700 mb-2 border-b pb-1">Basic Information</h4>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
            <input type="text" name="name" required 
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email Address *</label>
            <input type="email" name="email" required 
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
            <input type="text" name="phone" 
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
            <input type="date" name="date_of_birth" 
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        
        <!-- Address Information -->
        <div class="md:col-span-2">
            <h4 class="text-md font-bold text-blue-700 mb-2 border-b pb-1 mt-2">Address Information</h4>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Province</label>
            <select name="province" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                <option value="">Select Province</option>
                <option value="Kigali">Kigali</option>
                <option value="Northern">Northern</option>
                <option value="Southern">Southern</option>
                <option value="Eastern">Eastern</option>
                <option value="Western">Western</option>
            </select>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">District</label>
            <input type="text" name="district" 
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Sector</label>
            <input type="text" name="sector" 
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Village</label>
            <input type="text" name="village" 
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg">
        </div>
        
        <!-- Personal Information -->
        <div class="md:col-span-2">
            <h4 class="text-md font-bold text-blue-700 mb-2 border-b pb-1 mt-2">Personal Information</h4>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
            <select name="gender" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                <option value="">Select Gender</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
            </select>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Marital Status</label>
            <select name="marital_status" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                <option value="">Select Status</option>
                <option value="Single">Single</option>
                <option value="Married">Married</option>
                <option value="Divorced">Divorced</option>
                <option value="Widowed">Widowed</option>
            </select>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Membership Type</label>
            <select name="membership_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                <option value="Regular">Regular</option>
                <option value="Permanent">Permanent</option>
                <option value="Visitor">Visitor</option>
                <option value="Associate">Associate</option>
            </select>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Occupation</label>
            <input type="text" name="occupation" 
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Ministry Role</label>
            <input type="text" name="ministry_role" 
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                   placeholder="e.g., Worship Leader, Musician, Media">
        </div>
        
        <!-- Emergency Contact -->
        <div class="md:col-span-2">
            <h4 class="text-md font-bold text-blue-700 mb-2 border-b pb-1 mt-2">Emergency Contact</h4>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Emergency Name</label>
            <input type="text" name="emergency_name" 
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Emergency Phone</label>
            <input type="text" name="emergency_contact" 
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg">
        </div>
        
        <!-- Singer Information -->
        <div class="md:col-span-2">
            <h4 class="text-md font-bold text-purple-700 mb-2 border-b pb-1 mt-2">Singer Information</h4>
        </div>
        
        <div class="md:col-span-2">
            <label class="flex items-center space-x-2">
                <input type="checkbox" name="is_singer" value="1" id="isSingerCheckbox"
                       class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                <span class="text-sm font-medium text-gray-700">This user is a singer in the worship team</span>
            </label>
        </div>
        
        <div id="singerFields" class="md:col-span-2 hidden">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2 p-3 bg-purple-50 rounded-lg">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Voice Part</label>
                    <select name="voice_part" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        <option value="">Select Voice Part</option>
                        <option value="Soprano">Soprano</option>
                        <option value="Alto">Alto</option>
                        <option value="Tenor">Tenor</option>
                        <option value="Bass">Bass</option>
                        <option value="Lead">Lead Vocalist</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Singer Level</label>
                    <select name="singer_level" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        <option value="">Select Level</option>
                        <option value="Normal">Normal</option>
                        <option value="Good">Good</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Singer Notes</label>
                    <textarea name="singer_notes" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg" 
                              placeholder="Special skills, available days, etc..."></textarea>
                </div>
            </div>
        </div>
        
        <!-- Additional Information -->
        <div class="md:col-span-2">
            <h4 class="text-md font-bold text-blue-700 mb-2 border-b pb-1 mt-2">Additional Information</h4>
        </div>
        
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Skills / Talents</label>
            <textarea name="skills" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg" 
                      placeholder="e.g., Singing, Guitar, Drums, Keyboard, Dancing, etc."></textarea>
        </div>
        
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
            <textarea name="notes" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                      placeholder="Any additional notes about this member"></textarea>
        </div>
        
        <!-- Password -->
        <div class="md:col-span-2">
            <h4 class="text-md font-bold text-blue-700 mb-2 border-b pb-1 mt-2">Security</h4>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Password *</label>
            <input type="password" name="password" required 
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password *</label>
            <input type="password" name="password_confirmation" required 
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg">
        </div>
        
        <!-- Roles -->
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-2">Assign Roles</label>
            <div class="grid grid-cols-2 gap-2 max-h-32 overflow-y-auto border rounded-lg p-3">
                @foreach($roles as $role)
                    <label class="flex items-center space-x-2 p-1 hover:bg-gray-50 rounded cursor-pointer">
                        <input type="checkbox" name="roles[]" value="{{ $role->id }}" 
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm text-gray-700">{{ $role->display_name }}</span>
                    </label>
                @endforeach
            </div>
        </div>
    </div>
    
    <div class="flex justify-end space-x-3 mt-6 pt-6 border-t">
        <button type="button" onclick="closeModal('createModal')" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
            Cancel
        </button>
        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
            <i class="fas fa-save mr-2"></i>
            Create User
        </button>
    </div>
</form>

<script>
// Handle form submission via AJAX (NO singer toggle here - handled by parent)
document.getElementById('createUserForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const form = this;
    const formData = new FormData(form);
    
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeModal('createModal');
            location.reload();
        } else {
            alert(data.message || 'Error creating user');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error creating user');
    });
});
</script>