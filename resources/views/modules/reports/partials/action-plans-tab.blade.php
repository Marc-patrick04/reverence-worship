<div>
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold text-gray-800">Action Plans Report</h3>
    </div>
    
    <!-- Filters -->
    <div class="bg-gray-50 rounded-lg p-4 mb-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select id="apStatusFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="all">All</option>
                    <option value="pending">Pending</option>
                    <option value="in-progress">In Progress</option>
                    <option value="completed">Completed</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                <select id="apDepartmentFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="all">All Departments</option>
                    <option value="finance">Finance</option>
                    <option value="music">Music</option>
                    <option value="intercession">Intercession</option>
                    <option value="social-fellowship">Social Fellowship</option>
                    <option value="discipline">Discipline</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                <input type="date" id="apStartDate" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                <input type="date" id="apEndDate" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
            </div>
        </div>
        <div class="flex justify-end mt-4">
            <button onclick="filterActionPlansReport()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
                <i class="fas fa-search mr-2"></i> Apply Filters
            </button>
        </div>
    </div>
    
    <!-- Summary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-blue-50 rounded-lg p-3 text-center">
            <p class="text-2xl font-bold text-blue-600" id="apTotal">0</p>
            <p class="text-xs text-gray-600">Total Plans</p>
        </div>
        <div class="bg-green-50 rounded-lg p-3 text-center">
            <p class="text-2xl font-bold text-green-600" id="apCompleted">0</p>
            <p class="text-xs text-gray-600">Completed</p>
        </div>
        <div class="bg-yellow-50 rounded-lg p-3 text-center">
            <p class="text-2xl font-bold text-yellow-600" id="apInProgress">0</p>
            <p class="text-xs text-gray-600">In Progress</p>
        </div>
        <div class="bg-gray-50 rounded-lg p-3 text-center">
            <p class="text-2xl font-bold text-gray-600" id="apPending">0</p>
            <p class="text-xs text-gray-600">Pending</p>
        </div>
        <div class="bg-purple-50 rounded-lg p-3 text-center">
            <p class="text-2xl font-bold text-purple-600" id="apAvgProgress">0%</p>
            <p class="text-xs text-gray-600">Avg Progress</p>
        </div>
    </div>
    
    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Department</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Progress</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Due Date</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Assigned To</th>
                </tr>
            </thead>
            <tbody id="apTableBody">
                <tr>
                    <td colspan="6" class="text-center py-8 text-gray-500">Loading...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script>
function loadActionPlansReport() {
    filterActionPlansReport();
}

function filterActionPlansReport() {
    const status = document.getElementById('apStatusFilter')?.value || 'all';
    const department = document.getElementById('apDepartmentFilter')?.value || 'all';
    const startDate = document.getElementById('apStartDate')?.value || '';
    const endDate = document.getElementById('apEndDate')?.value || '';
    
    fetch(`/reports/action-plans?status=${status}&department=${department}&start_date=${startDate}&end_date=${endDate}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateAPTable(data.reports);
            updateAPSummary(data.summary);
        }
    });
}

function updateAPTable(reports) {
    const tbody = document.getElementById('apTableBody');
    if (!reports || reports.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center py-8 text-gray-500">No action plans found</td></tr>';
        return;
    }
    
    tbody.innerHTML = reports.map(plan => `
        <tr class="border-b hover:bg-gray-50">
            <td class="px-4 py-3 text-sm">${escapeHtml(plan.title)}</td>
            <td class="px-4 py-3 text-sm capitalize">${plan.department || '-'}</td>
            <td class="px-4 py-3"><span class="px-2 py-1 rounded-full text-xs ${getStatusClass(plan.status)}">${plan.status || 'Pending'}</span></td>
            <td class="px-4 py-3 text-sm">
                <div class="flex items-center gap-2">
                    <span>${plan.progress || 0}%</span>
                    <div class="w-16 bg-gray-200 rounded-full h-1.5"><div class="bg-blue-600 h-1.5 rounded-full" style="width: ${plan.progress || 0}%"></div></div>
                </div>
            </td>
            <td class="px-4 py-3 text-sm">${plan.due_date || '-'}</td>
            <td class="px-4 py-3 text-sm">${plan.assigned_to_name || '-'}</td>
        </tr>
    `).join('');
}

function updateAPSummary(summary) {
    document.getElementById('apTotal').textContent = summary.total || 0;
    document.getElementById('apCompleted').textContent = summary.completed || 0;
    document.getElementById('apInProgress').textContent = summary.in_progress || 0;
    document.getElementById('apPending').textContent = summary.pending || 0;
    document.getElementById('apAvgProgress').textContent = (summary.avg_progress || 0) + '%';
}

function getStatusClass(status) {
    switch(status) {
        case 'completed': return 'bg-green-100 text-green-700';
        case 'in-progress': return 'bg-yellow-100 text-yellow-700';
        default: return 'bg-gray-100 text-gray-600';
    }
}
</script>