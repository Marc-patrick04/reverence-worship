<div>
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold text-gray-800">All Announcements</h3>
        <button onclick="openCreateModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm flex items-center gap-2">
            <i class="fas fa-plus"></i> New Announcement
        </button>
    </div>
    
    <!-- Filters -->
    <div class="bg-gray-50 rounded-lg p-4 mb-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" id="searchAnnouncement" placeholder="Search by title or content..." 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                <select id="typeFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="all">All Types</option>
                    <option value="general">General</option>
                    <option value="event">Event</option>
                    <option value="alert">Alert</option>
                    <option value="maintenance">Maintenance</option>
                    <option value="update">Update</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select id="statusFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="all">All Status</option>
                    <option value="active">Active</option>
                    <option value="scheduled">Scheduled</option>
                    <option value="expired">Expired</option>
                    <option value="draft">Draft</option>
                </select>
            </div>
            <div class="flex items-end">
                <button onclick="filterAnnouncements()" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
                    <i class="fas fa-search mr-2"></i> Apply Filters
                </button>
            </div>
        </div>
    </div>
    
    <!-- Announcements List -->
    <div id="announcementsList" class="space-y-4">
        <div class="text-center py-12">
            <i class="fas fa-spinner fa-spin text-3xl text-gray-400 mb-3"></i>
            <p class="text-gray-500">Loading announcements...</p>
        </div>
    </div>
</div>

<script>
function loadAnnouncements() {
    filterAnnouncements();
}

function filterAnnouncements() {
    const search = document.getElementById('searchAnnouncement')?.value || '';
    const type = document.getElementById('typeFilter')?.value || 'all';
    const status = document.getElementById('statusFilter')?.value || 'all';
    
    fetch(`/announcements/filter?search=${encodeURIComponent(search)}&type=${type}&status=${status}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateAnnouncementsList(data.announcements);
        }
    })
    .catch(error => console.error('Error:', error));
}

function updateAnnouncementsList(announcements) {
    const container = document.getElementById('announcementsList');
    
    if (!announcements || announcements.length === 0) {
        container.innerHTML = `
            <div class="text-center py-12">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-bullhorn text-3xl text-gray-400"></i>
                </div>
                <p class="text-gray-500">No announcements found</p>
                <button onclick="openCreateModal()" class="mt-3 text-blue-600 hover:text-blue-700 text-sm">
                    <i class="fas fa-plus"></i> Create your first announcement
                </button>
            </div>
        `;
        return;
    }
    
    container.innerHTML = announcements.map(announcement => {
        let statusClass = '';
        let statusIcon = '';
        
        switch(announcement.status) {
            case 'active':
                statusClass = 'bg-green-100 text-green-700';
                statusIcon = 'fa-check-circle';
                break;
            case 'scheduled':
                statusClass = 'bg-yellow-100 text-yellow-700';
                statusIcon = 'fa-clock';
                break;
            case 'expired':
                statusClass = 'bg-red-100 text-red-700';
                statusIcon = 'fa-calendar-times';
                break;
            default:
                statusClass = 'bg-gray-100 text-gray-600';
                statusIcon = 'fa-pen';
        }
        
        let priorityClass = '';
        switch(announcement.priority) {
            case 'urgent':
                priorityClass = 'bg-red-100 text-red-700';
                break;
            case 'high':
                priorityClass = 'bg-orange-100 text-orange-700';
                break;
            case 'normal':
                priorityClass = 'bg-blue-100 text-blue-700';
                break;
            default:
                priorityClass = 'bg-gray-100 text-gray-600';
        }
        
        return `
            <div class="bg-white border rounded-lg p-4 hover:shadow-md transition">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2 flex-wrap">
                            <h4 class="font-semibold text-gray-800">${escapeHtml(announcement.title)}</h4>
                            <span class="px-2 py-0.5 text-xs rounded-full ${statusClass}">
                                <i class="fas ${statusIcon} mr-1"></i> ${announcement.status}
                            </span>
                            <span class="px-2 py-0.5 text-xs rounded-full ${priorityClass}">
                                ${announcement.priority}
                            </span>
                            <span class="px-2 py-0.5 text-xs rounded-full bg-purple-100 text-purple-700">
                                ${announcement.type}
                            </span>
                        </div>
                        <p class="text-sm text-gray-600">${escapeHtml(announcement.content.substring(0, 200))}${announcement.content.length > 200 ? '...' : ''}</p>
                        <div class="flex flex-wrap gap-4 mt-3 text-xs text-gray-500">
                            <span><i class="fas fa-user"></i> Created by: ${escapeHtml(announcement.created_by_name || 'Unknown')}</span>
                            <span><i class="fas fa-calendar"></i> Created: ${formatDate(announcement.created_at)}</span>
                            ${announcement.scheduled_date ? `<span><i class="fas fa-calendar-plus"></i> Scheduled: ${formatDate(announcement.scheduled_date)}</span>` : ''}
                            ${announcement.expiry_date ? `<span><i class="fas fa-calendar-times"></i> Expires: ${formatDate(announcement.expiry_date)}</span>` : ''}
                            ${announcement.published_at ? `<span><i class="fas fa-check-circle"></i> Published: ${formatDate(announcement.published_at)}</span>` : ''}
                        </div>
                    </div>
                    <div class="flex gap-2 ml-4">
                        <button onclick="editAnnouncement(${announcement.id})" class="text-blue-500 hover:text-blue-700 transition" title="Edit">
                            <i class="fas fa-edit text-lg"></i>
                        </button>
                        <button onclick="toggleStatus(${announcement.id})" class="text-green-500 hover:text-green-700 transition" title="Toggle Status">
                            <i class="fas fa-power-off text-lg"></i>
                        </button>
                        <button onclick="deleteAnnouncement(${announcement.id})" class="text-red-500 hover:text-red-700 transition" title="Delete">
                            <i class="fas fa-trash-alt text-lg"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
    }).join('');
}

function formatDate(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-GB', { day: '2-digit', month: '2-digit', year: 'numeric' });
}

function toggleStatus(id) {
    if (confirm('Change announcement status?')) {
        fetch(`/announcements/${id}/toggle-status`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                filterAnnouncements();
                if (typeof loadOverviewStats === 'function') loadOverviewStats();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => console.error('Error:', error));
    }
}

function deleteAnnouncement(id) {
    if (confirm('Are you sure you want to delete this announcement?')) {
        fetch(`/announcements/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                filterAnnouncements();
                if (typeof loadOverviewStats === 'function') loadOverviewStats();
                alert('Announcement deleted successfully');
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => console.error('Error:', error));
    }
}
function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('hidden');
    }
}
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Event listeners for filters
document.getElementById('searchAnnouncement')?.addEventListener('keyup', filterAnnouncements);
document.getElementById('typeFilter')?.addEventListener('change', filterAnnouncements);
document.getElementById('statusFilter')?.addEventListener('change', filterAnnouncements);
</script>