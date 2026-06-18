<div>
    
    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl shadow-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm">Total</p>
                    <p class="text-3xl font-bold" id="statTotal">0</p>
                </div>
                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                    <i class="fas fa-bullhorn text-white text-lg"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl shadow-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm">Active</p>
                    <p class="text-3xl font-bold" id="statActive">0</p>
                </div>
                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                    <i class="fas fa-check-circle text-white text-lg"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-xl shadow-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-yellow-100 text-sm">Scheduled</p>
                    <p class="text-3xl font-bold" id="statScheduled">0</p>
                </div>
                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                    <i class="fas fa-clock text-white text-lg"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-xl shadow-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-red-100 text-sm">Expired</p>
                    <p class="text-3xl font-bold" id="statExpired">0</p>
                </div>
                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                    <i class="fas fa-calendar-times text-white text-lg"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-gradient-to-r from-gray-500 to-gray-600 rounded-xl shadow-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-100 text-sm">Draft</p>
                    <p class="text-3xl font-bold" id="statDraft">0</p>
                </div>
                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                    <i class="fas fa-pen text-white text-lg"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Announcements -->
    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        <div class="px-5 py-3 border-b bg-gray-50">
            <h4 class="font-medium text-gray-800">Recent Announcements</h4>
        </div>
        <div id="recentAnnouncements" class="divide-y divide-gray-100">
            <div class="text-center py-8 text-gray-400 text-sm">Loading...</div>
        </div>
    </div>
</div>

<script>
// Helper function for status classes
function getStatusClass(status) {
    const classes = {
        active: 'bg-green-100 text-green-700',
        scheduled: 'bg-yellow-100 text-yellow-700',
        expired: 'bg-red-100 text-red-700',
        draft: 'bg-gray-100 text-gray-600'
    };
    return classes[status] || 'bg-gray-100 text-gray-600';
}

function formatDate(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-GB', { 
        day: '2-digit', 
        month: 'short', 
        year: 'numeric' 
    });
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

window.loadOverviewStats = function() {
    // Load stats
    fetch('/announcements/stats', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Check if elements exist before setting textContent
            const totalEl = document.getElementById('statTotal');
            const activeEl = document.getElementById('statActive');
            const scheduledEl = document.getElementById('statScheduled');
            const expiredEl = document.getElementById('statExpired');
            const draftEl = document.getElementById('statDraft');
            
            if (totalEl) totalEl.textContent = data.stats.total || 0;
            if (activeEl) activeEl.textContent = data.stats.active || 0;
            if (scheduledEl) scheduledEl.textContent = data.stats.scheduled || 0;
            if (expiredEl) expiredEl.textContent = data.stats.expired || 0;
            if (draftEl) draftEl.textContent = data.stats.draft || 0;
        }
    })
    .catch(error => console.error('Error loading stats:', error));
    
    // Load recent announcements
    fetch('/announcements/filter', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const announcements = data.announcements || [];
            const recent = announcements.slice(0, 5);
            const container = document.getElementById('recentAnnouncements');
            
            if (container) {
                if (recent.length > 0) {
                    container.innerHTML = recent.map(a => {
                        const statusClass = getStatusClass(a.status);
                        return `
                            <div class="flex items-center justify-between px-5 py-3 hover:bg-gray-50 transition cursor-pointer" 
                                 onclick="window.viewMessage && window.viewMessage(${a.id})">
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-gray-800 text-sm truncate">${escapeHtml(a.title)}</p>
                                    <div class="flex items-center gap-3 mt-0.5 text-xs text-gray-500">
                                        <span><i class="fas fa-user mr-1"></i>${escapeHtml(a.created_by_name || 'Unknown')}</span>
                                        <span><i class="far fa-calendar mr-1"></i>${formatDate(a.created_at)}</span>
                                    </div>
                                </div>
                                <div class="flex-shrink-0 ml-3">
                                    <span class="inline-flex px-2 py-0.5 text-xs rounded-full ${statusClass}">
                                        ${a.status}
                                    </span>
                                </div>
                            </div>
                        `;
                    }).join('');
                } else {
                    container.innerHTML = `
                        <div class="text-center py-8 text-gray-400 text-sm">
                            <i class="fas fa-inbox text-2xl mb-2 block"></i>
                            No announcements found
                        </div>
                    `;
                }
            }
        }
    })
    .catch(error => console.error('Error loading announcements:', error));
};

// Make functions globally available
window.getStatusClass = getStatusClass;
window.formatDate = formatDate;
window.escapeHtml = escapeHtml;

// Load stats when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    window.loadOverviewStats();
});
</script>