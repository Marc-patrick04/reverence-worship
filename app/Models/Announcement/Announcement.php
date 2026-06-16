<div id="createAnnouncementModal" class="modal hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-5 border w-[95%] sm:w-full max-w-4xl shadow-xl rounded-2xl bg-white max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center pb-4 border-b sticky top-0 bg-white z-10">
            <h3 class="text-xl font-bold text-gray-800">Create New Announcement</h3>
            <button onclick="window.closeModal('createAnnouncementModal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form id="createAnnouncementForm" onsubmit="window.submitCreateAnnouncement(event)">
            @csrf
            <div class="mt-4 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
                    <input type="text" id="announcementTitle" name="title" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Content *</label>
                    <textarea id="announcementContent" name="content" rows="5" required 
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                        <select id="announcementType" name="type" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            <option value="general">General</option>
                            <option value="event">Event</option>
                            <option value="alert">Alert</option>
                            <option value="maintenance">Maintenance</option>
                            <option value="update">Update</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                        <select id="announcementPriority" name="priority" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            <option value="low">Low</option>
                            <option value="normal" selected>Normal</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
                        </select>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Scheduled Date</label>
                        <input type="date" id="announcementScheduledDate" name="scheduled_date" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Expiry Date</label>
                        <input type="date" id="announcementExpiryDate" name="expiry_date" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    </div>
                </div>
                
                <!-- Target Audience Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Target Audience *</label>
                    <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                        <div class="flex items-center gap-4">
                            <label class="flex items-center gap-2">
                                <input type="radio" name="target_type" value="all" checked class="target-type-radio">
                                <span class="text-sm">All Users</span>
                            </label>
                            <label class="flex items-center gap-2">
                                <input type="radio" name="target_type" value="roles" class="target-type-radio">
                                <span class="text-sm">By Roles</span>
                            </label>
                            <label class="flex items-center gap-2">
                                <input type="radio" name="target_type" value="users" class="target-type-radio">
                                <span class="text-sm">Select Specific Users</span>
                            </label>
                        </div>
                        
                        <!-- Role Selection -->
                        <div id="rolesSelection" class="hidden">
                            <label class="block text-xs font-medium text-gray-600 mb-2">Select Roles:</label>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-2" id="rolesCheckboxes">
                                <!-- Roles will be loaded here -->
                            </div>
                        </div>
                        
                        <!-- User Selection -->
                        <div id="usersSelection" class="hidden">
                            <label class="block text-xs font-medium text-gray-600 mb-2">Select Users:</label>
                            <div class="border rounded-lg max-h-48 overflow-y-auto p-2" id="usersCheckboxes">
                                <!-- Users will be loaded here -->
                            </div>
                        </div>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Image (Optional)</label>
                    <input type="file" id="announcementImage" name="image" accept="image/*"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <p class="text-xs text-gray-500 mt-1">Recommended size: 1200x600px. Max 2MB.</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select id="announcementStatus" name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            <option value="draft">Draft</option>
                            <option value="active">Active</option>
                            <option value="scheduled">Scheduled</option>
                        </select>
                    </div>
                    <div>
                        <label class="flex items-center gap-2 pt-6">
                            <input type="checkbox" id="sendEmail" name="send_email" value="1" class="rounded">
                            <span class="text-sm text-gray-700">Send email notification to selected users</span>
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end gap-3 mt-6 pt-4 border-t">
                <button type="button" onclick="window.closeModal('createAnnouncementModal')" class="px-4 py-2 border rounded-lg text-sm hover:bg-gray-50">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700">Create Announcement</button>
            </div>
        </form>
    </div>
</div>

<script>
// Load roles and users for target selection
async function loadTargetOptions() {
    try {
        // Load roles
        const rolesResponse = await fetch('/announcements/roles', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const rolesData = await rolesResponse.json();
        
        if (rolesData.success) {
            const rolesContainer = document.getElementById('rolesCheckboxes');
            rolesContainer.innerHTML = rolesData.roles.map(role => `
                <label class="flex items-center gap-2 p-2 hover:bg-gray-50 rounded">
                    <input type="checkbox" name="target_roles[]" value="${role.id}" class="role-checkbox">
                    <span class="text-sm">${role.display_name}</span>
                </label>
            `).join('');
        }
        
        // Load users
        const usersResponse = await fetch('/announcements/users', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const usersData = await usersResponse.json();
        
        if (usersData.success) {
            const usersContainer = document.getElementById('usersCheckboxes');
            usersContainer.innerHTML = usersData.users.map(user => `
                <label class="flex items-center gap-2 p-2 hover:bg-gray-50 rounded">
                    <input type="checkbox" name="target_users[]" value="${user.id}" class="user-checkbox">
                    <div class="flex-1">
                        <span class="text-sm font-medium">${escapeHtml(user.name)}</span>
                        <span class="text-xs text-gray-500 ml-2">${escapeHtml(user.email)}</span>
                    </div>
                </label>
            `).join('');
        }
    } catch (error) {
        console.error('Error loading options:', error);
    }
}

// Target type radio change handler
document.querySelectorAll('.target-type-radio').forEach(radio => {
    radio.addEventListener('change', function() {
        const rolesDiv = document.getElementById('rolesSelection');
        const usersDiv = document.getElementById('usersSelection');
        
        if (this.value === 'roles') {
            rolesDiv.classList.remove('hidden');
            usersDiv.classList.add('hidden');
        } else if (this.value === 'users') {
            rolesDiv.classList.add('hidden');
            usersDiv.classList.remove('hidden');
        } else {
            rolesDiv.classList.add('hidden');
            usersDiv.classList.add('hidden');
        }
    });
});

// Override submit function to include target data
window.submitCreateAnnouncement = async function(event) {
    event.preventDefault();
    
    const formData = new FormData();
    formData.append('title', document.getElementById('announcementTitle').value);
    formData.append('content', document.getElementById('announcementContent').value);
    formData.append('type', document.getElementById('announcementType').value);
    formData.append('priority', document.getElementById('announcementPriority').value);
    formData.append('scheduled_date', document.getElementById('announcementScheduledDate').value);
    formData.append('expiry_date', document.getElementById('announcementExpiryDate').value);
    formData.append('status', document.getElementById('announcementStatus').value);
    
    // Target selection
    const targetType = document.querySelector('input[name="target_type"]:checked').value;
    formData.append('target_type', targetType);
    
    if (targetType === 'roles') {
        const selectedRoles = [];
        document.querySelectorAll('.role-checkbox:checked').forEach(cb => {
            selectedRoles.push(cb.value);
        });
        formData.append('target_roles', JSON.stringify(selectedRoles));
    } else if (targetType === 'users') {
        const selectedUsers = [];
        document.querySelectorAll('.user-checkbox:checked').forEach(cb => {
            selectedUsers.push(cb.value);
        });
        formData.append('target_users', JSON.stringify(selectedUsers));
    }
    
    // Send email
    if (document.getElementById('sendEmail').checked) {
        formData.append('send_email', '1');
    }
    
    const imageFile = document.getElementById('announcementImage').files[0];
    if (imageFile) {
        if (imageFile.size > 2 * 1024 * 1024) {
            alert('Image size must be less than 2MB');
            return;
        }
        formData.append('image', imageFile);
    }
    
    const submitBtn = event.submitter;
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Creating...';
    submitBtn.disabled = true;
    
    try {
        const response = await fetch('/announcements/store', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            window.closeModal('createAnnouncementModal');
            if (typeof window.refreshAnnouncementsList === 'function') {
                window.refreshAnnouncementsList();
            }
            if (typeof window.refreshOverviewStats === 'function') {
                window.refreshOverviewStats();
            }
            alert('Announcement created successfully!');
        } else {
            alert('Error: ' + (data.message || 'Failed to create announcement'));
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Network error: ' + error.message);
    } finally {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }
};

// Override openCreateModal
window.openCreateModal = function() {
    document.getElementById('createAnnouncementForm').reset();
    document.getElementById('createAnnouncementModal').classList.remove('hidden');
    loadTargetOptions();
};

// Load options when modal opens
</script>