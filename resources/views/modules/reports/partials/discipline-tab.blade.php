<div>
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold text-gray-800">Discipline Report</h3>
    </div>
    
    <div class="bg-gray-50 rounded-lg p-4 mb-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                <select id="discTypeFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="all">All</option><option value="positive">Positive</option>
                    <option value="warning">Warning</option><option value="penalty">Penalty</option>
                    <option value="suspension">Suspension</option>
                </select>
            </div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select id="discStatusFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="all">All</option><option value="active">Active</option><option value="resolved">Resolved</option>
                </select>
            </div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label><input type="date" id="discStartDate" class="w-full px-3 py-2 border border-gray-300 rounded-lg"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">End Date</label><input type="date" id="discEndDate" class="w-full px-3 py-2 border border-gray-300 rounded-lg"></div>
        </div>
        <div class="flex justify-end mt-4"><button onclick="filterDisciplineReport()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm"><i class="fas fa-search mr-2"></i> Apply Filters</button></div>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-6 gap-4 mb-6">
        <div class="bg-blue-50 p-3 text-center"><p class="text-2xl font-bold text-blue-600" id="discTotal">0</p><p class="text-xs">Total</p></div>
        <div class="bg-green-50 p-3 text-center"><p class="text-2xl font-bold text-green-600" id="discPositive">0</p><p class="text-xs">Positive</p></div>
        <div class="bg-yellow-50 p-3 text-center"><p class="text-2xl font-bold text-yellow-600" id="discWarning">0</p><p class="text-xs">Warnings</p></div>
        <div class="bg-red-50 p-3 text-center"><p class="text-2xl font-bold text-red-600" id="discPenalty">0</p><p class="text-xs">Penalties</p></div>
        <div class="bg-purple-50 p-3 text-center"><p class="text-2xl font-bold text-purple-600" id="discSuspension">0</p><p class="text-xs">Suspensions</p></div>
        <div class="bg-teal-50 p-3 text-center"><p class="text-2xl font-bold text-teal-600" id="discResolved">0</p><p class="text-xs">Resolved</p></div>
    </div>
    
    <div class="overflow-x-auto"><table class="min-w-full divide-y divide-gray-200"><thead class="bg-gray-50"><tr><th class="px-4 py-3 text-left text-xs font-medium text-gray-500">User</th><th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Title</th><th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Type</th><th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Status</th><th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Date</th></tr></thead><tbody id="discTableBody"><tr><td colspan="5" class="text-center py-8 text-gray-500">Loading...</td></tr></tbody></table></div>
</div>

<script>
function loadDisciplineReport() { filterDisciplineReport(); }
function filterDisciplineReport() {
    const type = document.getElementById('discTypeFilter')?.value || 'all';
    const status = document.getElementById('discStatusFilter')?.value || 'all';
    const startDate = document.getElementById('discStartDate')?.value || '';
    const endDate = document.getElementById('discEndDate')?.value || '';
    fetch(`/reports/discipline?type=${type}&status=${status}&start_date=${startDate}&end_date=${endDate}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    .then(r => r.json()).then(data => { if(data.success){ updateDiscTable(data.reports); updateDiscSummary(data.summary); } });
}
function updateDiscTable(reports) {
    const tbody = document.getElementById('discTableBody');
    if(!reports || reports.length === 0){ tbody.innerHTML='<tr><td colspan="5" class="text-center py-8 text-gray-500">No records found</td></tr>'; return; }
    tbody.innerHTML = reports.map(r => `<tr class="border-b hover:bg-gray-50"><td class="px-4 py-3 text-sm">${escapeHtml(r.user_name)}</td><td class="px-4 py-3 text-sm">${escapeHtml(r.title)}</td><td class="px-4 py-3"><span class="px-2 py-1 rounded-full text-xs ${getTypeClass(r.type)}">${r.type}</span></td><td class="px-4 py-3"><span class="px-2 py-1 rounded-full text-xs ${r.status === 'resolved' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'}">${r.status}</span></td><td class="px-4 py-3 text-sm">${new Date(r.created_at).toLocaleDateString()}</td></tr>`).join('');
}
function updateDiscSummary(s){ document.getElementById('discTotal').textContent=s.total||0; document.getElementById('discPositive').textContent=s.positive||0; document.getElementById('discWarning').textContent=s.warning||0; document.getElementById('discPenalty').textContent=s.penalty||0; document.getElementById('discSuspension').textContent=s.suspension||0; document.getElementById('discResolved').textContent=s.resolved||0; }
function getTypeClass(type){ return type==='positive'?'bg-green-100 text-green-700':type==='warning'?'bg-yellow-100 text-yellow-700':type==='penalty'?'bg-red-100 text-red-700':'bg-purple-100 text-purple-700'; }
</script>