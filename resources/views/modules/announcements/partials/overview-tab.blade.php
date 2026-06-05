<div>
    <div class="mb-6">
        <h3 class="text-lg font-semibold text-gray-800">Announcement Statistics</h3>
        <p class="text-sm text-gray-500">Overview of all announcements</p>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-8">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl shadow-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm">Total</p>
                    <p class="text-2xl font-bold" id="statTotal">0</p>
                </div>
                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                    <i class="fas fa-bullhorn text-white"></i>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl shadow-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm">Active</p>
                    <p class="text-2xl font-bold" id="statActive">0</p>
                </div>
                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                    <i class="fas fa-check-circle text-white"></i>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-xl shadow-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-yellow-100 text-sm">Scheduled</p>
                    <p class="text-2xl font-bold" id="statScheduled">0</p>
                </div>
                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                    <i class="fas fa-clock text-white"></i>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-xl shadow-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-red-100 text-sm">Expired</p>
                    <p class="text-2xl font-bold" id="statExpired">0</p>
                </div>
                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                    <i class="fas fa-calendar-times text-white"></i>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-r from-gray-500 to-gray-600 rounded-xl shadow-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-100 text-sm">Draft</p>
                    <p class="text-2xl font-bold" id="statDraft">0</p>
                </div>
                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                    <i class="fas fa-pen text-white"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-md p-6 border">
            <h4 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                <i class="fas fa-chart-pie text-blue-600"></i> Announcement Types
            </h4>
            <div id="typeDistribution" class="space-y-2">
                <div class="flex justify-between text-sm">
                    <span>General</span>
                    <span id="typeGeneral">0</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span>Event</span>
                    <span id="typeEvent">0</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span>Alert</span>
                    <span id="typeAlert">0</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span>Maintenance</span>
                    <span id="typeMaintenance">0</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span>Update</span>
                    <span id="typeUpdate">0</span>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-md p-6 border">
            <h4 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                <i class="fas fa-chart-bar text-purple-600"></i> Recent Activity
            </h4>
            <div id="recentActivity" class="space-y-2">
                <p class="text-gray-500 text-sm">Loading...</p>
            </div>
        </div>
    </div>
</div>

<script>
function loadOverviewStats() {
    fetch('/announcements/stats', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('statTotal').textContent = data.stats.total;
            document.getElementById('statActive').textContent = data.stats.active;
            document.getElementById('statScheduled').textContent = data.stats.scheduled;
            document.getElementById('statExpired').textContent = data.stats.expired;
            document.getElementById('statDraft').textContent = data.stats.draft;
        }
    })
    .catch(error => console.error('Error:', error));
    
    // Load type distribution
    fetch('/announcements/filter', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const types = { general: 0, event: 0, alert: 0, maintenance: 0, update: 0 };
            data.announcements.forEach(a => {
                if (types[a.type] !== undefined) types[a.type]++;
            });
            document.getElementById('typeGeneral').textContent = types.general;
            document.getElementById('typeEvent').textContent = types.event;
            document.getElementById('typeAlert').textContent = types.alert;
            document.getElementById('typeMaintenance').textContent = types.maintenance;
            document.getElementById('typeUpdate').textContent = types.update;
            
            // Recent activity
            const recent = data.announcements.slice(0, 5);
            const container = document.getElementById('recentActivity');
            if (recent.length > 0) {
                container.innerHTML = recent.map(a => `
                    <div class="flex justify-between items-center text-sm border-b pb-2">
                        <span class="font-medium">${escapeHtml(a.title)}</span>
                        <span class="text-gray-400">${new Date(a.created_at).toLocaleDateString()}</span>
                    </div>
                `).join('');
            } else {
                container.innerHTML = '<p class="text-gray-500 text-sm">No recent announcements</p>';
            }
        }
    })
    .catch(error => console.error('Error:', error));
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>