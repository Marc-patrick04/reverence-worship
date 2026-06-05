<div id="editAnnouncementModal" class="modal hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-6 border w-full max-w-2xl shadow-xl rounded-2xl bg-white">
        <div class="flex justify-between items-center pb-4 border-b">
            <h3 class="text-xl font-bold text-gray-800">Edit Announcement</h3>
            <button onclick="closeModal('editAnnouncementModal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form id="editAnnouncementForm" onsubmit="updateAnnouncement(event)">
            @csrf
            @method('PUT')
            <input type="hidden" id="editAnnouncementId" name="id">
            <div class="mt-4 space-y-4 max-h-96 overflow-y-auto">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
                    <input type="text" id="editAnnouncementTitle" name="title" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Content *</label>
                    <textarea id="editAnnouncementContent" name="content" rows="5" required 
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                        <select id="editAnnouncementType" name="type" 
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
                        <select id="editAnnouncementPriority" name="priority" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            <option value="low">Low</option>
                            <option value="normal">Normal</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Scheduled Date</label>
                        <input type="date" id="editAnnouncementScheduledDate" name="scheduled_date" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Expiry Date</label>
                        <input type="date" id="editAnnouncementExpiryDate" name="expiry_date" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Target Audience</label>
                    <input type="text" id="editAnnouncementTarget" name="target_audience" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Image (Optional)</label>
                    <input type="file" id="editAnnouncementImage" name="image" accept="image/*"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <div id="currentImagePreview" class="mt-2 hidden">
                        <img id="editImagePreview" src="" class="w-32 h-32 object-cover rounded-lg">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select id="editAnnouncementStatus" name="status" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        <option value="draft">Draft</option>
                        <option value="active">Active</option>
                        <option value="scheduled">Scheduled</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6 pt-4 border-t">
                <button type="button" onclick="closeModal('editAnnouncementModal')" class="px-4 py-2 border rounded-lg text-sm">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm">Update Announcement</button>
            </div>
        </form>
    </div>
</div>

<script>
function editAnnouncement(id) {
    fetch(`/announcements/${id}/edit`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const a = data.announcement;
            document.getElementById('editAnnouncementId').value = a.id;
            document.getElementById('editAnnouncementTitle').value = a.title;
            document.getElementById('editAnnouncementContent').value = a.content;
            document.getElementById('editAnnouncementType').value = a.type || 'general';
            document.getElementById('editAnnouncementPriority').value = a.priority || 'normal';
            document.getElementById('editAnnouncementScheduledDate').value = a.scheduled_date || '';
            document.getElementById('editAnnouncementExpiryDate').value = a.expiry_date || '';
            document.getElementById('editAnnouncementTarget').value = a.target_audience || '';
            document.getElementById('editAnnouncementStatus').value = a.status || 'draft';
            
            if (a.image_path) {
                document.getElementById('editImagePreview').src = '/' + a.image_path;
                document.getElementById('currentImagePreview').classList.remove('hidden');
            } else {
                document.getElementById('currentImagePreview').classList.add('hidden');
            }
            
            document.getElementById('editAnnouncementModal').classList.remove('hidden');
        }
    })
    .catch(error => console.error('Error:', error));
}

function updateAnnouncement(event) {
    event.preventDefault();
    
    const id = document.getElementById('editAnnouncementId').value;
    const formData = new FormData();
    formData.append('title', document.getElementById('editAnnouncementTitle').value);
    formData.append('content', document.getElementById('editAnnouncementContent').value);
    formData.append('type', document.getElementById('editAnnouncementType').value);
    formData.append('priority', document.getElementById('editAnnouncementPriority').value);
    formData.append('scheduled_date', document.getElementById('editAnnouncementScheduledDate').value);
    formData.append('expiry_date', document.getElementById('editAnnouncementExpiryDate').value);
    formData.append('target_audience', document.getElementById('editAnnouncementTarget').value);
    formData.append('status', document.getElementById('editAnnouncementStatus').value);
    formData.append('_method', 'PUT');
    
    const imageFile = document.getElementById('editAnnouncementImage').files[0];
    if (imageFile) {
        formData.append('image', imageFile);
    }
    
    fetch(`/announcements/${id}`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeModal('editAnnouncementModal');
            filterAnnouncements();
            if (typeof loadOverviewStats === 'function') loadOverviewStats();
            alert('Announcement updated successfully!');
        } else {
            alert('Error: ' + (data.message || 'Failed to update announcement'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Network error: ' + error.message);
    });
}
</script>