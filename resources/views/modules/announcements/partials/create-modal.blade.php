<div id="createAnnouncementModal" class="modal hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-6 border w-full max-w-2xl shadow-xl rounded-2xl bg-white">
        <div class="flex justify-between items-center pb-4 border-b">
            <h3 class="text-xl font-bold text-gray-800">Create New Announcement</h3>
            <button onclick="closeModal('createAnnouncementModal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form id="createAnnouncementForm" onsubmit="submitAnnouncement(event)">
            @csrf
            <div class="mt-4 space-y-4 max-h-96 overflow-y-auto">
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
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                        <select id="announcementType" name="type" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            <option value="general">General</option>
                            <option value="event">Event</option>
                            <option value="alert">Alert</option>
                            <option value="maintenance">Maintenance</option>
                            <option value="update">Update</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                        <select id="announcementPriority" name="priority" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            <option value="low">Low</option>
                            <option value="normal" selected>Normal</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
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
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Target Audience</label>
                    <input type="text" id="announcementTarget" name="target_audience" 
                           placeholder="e.g., All, Admin, Users, Specific Department"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Image (Optional)</label>
                    <input type="file" id="announcementImage" name="image" accept="image/*"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select id="announcementStatus" name="status" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        <option value="draft">Draft</option>
                        <option value="active">Active</option>
                        <option value="scheduled">Scheduled</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6 pt-4 border-t">
                <button type="button" onclick="closeModal('createAnnouncementModal')" class="px-4 py-2 border rounded-lg text-sm">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm">Create Announcement</button>
            </div>
        </form>
    </div>
</div>

<script>
function openCreateModal() {
    document.getElementById('createAnnouncementForm').reset();
    document.getElementById('createAnnouncementModal').classList.remove('hidden');
}

function submitAnnouncement(event) {
    event.preventDefault();
    
    const formData = new FormData();
    formData.append('title', document.getElementById('announcementTitle').value);
    formData.append('content', document.getElementById('announcementContent').value);
    formData.append('type', document.getElementById('announcementType').value);
    formData.append('priority', document.getElementById('announcementPriority').value);
    formData.append('scheduled_date', document.getElementById('announcementScheduledDate').value);
    formData.append('expiry_date', document.getElementById('announcementExpiryDate').value);
    formData.append('target_audience', document.getElementById('announcementTarget').value);
    formData.append('status', document.getElementById('announcementStatus').value);
    
    const imageFile = document.getElementById('announcementImage').files[0];
    if (imageFile) {
        formData.append('image', imageFile);
    }
    
    fetch('/announcements/store', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeModal('createAnnouncementModal');
            filterAnnouncements();
            if (typeof loadOverviewStats === 'function') loadOverviewStats();
            alert('Announcement created successfully!');
        } else {
            alert('Error: ' + (data.message || 'Failed to create announcement'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Network error: ' + error.message);
    });
}
</script>