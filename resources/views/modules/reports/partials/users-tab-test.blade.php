<div>
    <div class="mb-6">
        <h3 class="text-lg font-semibold text-gray-800">Users Report</h3>
        <p class="text-sm text-gray-500 mt-0.5">Analytics and statistics for all users</p>
    </div>
    
    <!-- Date Range Filter -->
    <div class="bg-white rounded-lg shadow-sm border p-4 mb-6">
        <div class="flex flex-wrap items-end gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">From Date</label>
                <input type="date" id="startDate" class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">To Date</label>
                <input type="date" id="endDate" class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm">
            </div>
            <button onclick="loadReport()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-1.5 rounded-lg text-sm transition">
                <i class="fas fa-chart-line mr-1"></i> Generate Report
            </button>
            <button onclick="exportReport()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-1.5 rounded-lg text-sm transition">
                <i class="fas fa-download mr-1"></i> Export CSV
            </button>
        </div>
    </div>
    
    <!-- Loading State -->
    <div id="loadingState" class="text-center py-12">
        <i class="fas fa-spinner fa-spin text-3xl text-gray-400 mb-3"></i>
        <p class="text-gray-500">Loading report data...</p>
    </div>
    
    <!-- Report Content -->
    <div id="reportContent" style="display: none;">
        <!-- Summary Statistics Cards -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl shadow-lg p-4 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm">Total Users</p>
                        <p class="text-3xl font-bold" id="statTotal">0</p>
                    </div>
                    <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                        <i class="fas fa-users text-white"></i>
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
                        <i class="fas fa-check-circle text-white"></i>
                    </div>
                </div>
            </div>
            <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-xl shadow-lg p-4 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-red-100 text-sm">Inactive</p>
                        <p class="text-3xl font-bold" id="statInactive">0</p>
                    </div>
                    <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                        <i class="fas fa-user-slash text-white"></i>
                    </div>
                </div>
            </div>
            <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-xl shadow-lg p-4 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-yellow-100 text-sm">Pending</p>
                        <p class="text-3xl font-bold" id="statPending">0</p>
                    </div>
                    <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                        <i class="fas fa-clock text-white"></i>
                    </div>
                </div>
            </div>
            <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl shadow-lg p-4 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-sm">New (This Month)</p>
                        <p class="text-3xl font-bold" id="statNewThisMonth">0</p>
                    </div>
                    <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                        <i class="fas fa-user-plus text-white"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Gender Distribution -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="bg-white rounded-xl shadow-sm border p-5">
                <h4 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-venus-mars text-pink-500"></i> Gender Distribution
                </h4>
                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-600">Male</span>
                            <span class="font-semibold text-gray-800"><span id="genderMale">0</span> (<span id="genderMalePercent">0</span>%)</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div id="maleBar" class="bg-blue-500 h-2.5 rounded-full" style="width: 0%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-600">Female</span>
                            <span class="font-semibold text-gray-800"><span id="genderFemale">0</span> (<span id="genderFemalePercent">0</span>%)</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div id="femaleBar" class="bg-pink-500 h-2.5 rounded-full" style="width: 0%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-600">Not Specified</span>
                            <span class="font-semibold text-gray-800"><span id="genderUnspecified">0</span> (<span id="genderUnspecifiedPercent">0</span>%)</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div id="unspecifiedBar" class="bg-gray-400 h-2.5 rounded-full" style="width: 0%"></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm border p-5">
                <h4 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-tags text-purple-500"></i> Role Distribution
                </h4>
                <div id="roleDistribution" class="space-y-4">
                    <div class="text-center py-4 text-gray-500">Loading roles...</div>
                </div>
            </div>
        </div>
        
        <!-- Registration Trend Chart -->
        <div class="bg-white rounded-xl shadow-sm border p-5 mb-6">
            <h4 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-chart-line text-green-500"></i> User Registration Trend (Last 12 Months)
            </h4>
            <div class="relative h-64">
                <canvas id="registrationChart" class="w-full h-full"></canvas>
            </div>
        </div>
        
        <!-- Recent Users Table -->
        <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
            <div class="px-5 py-3 border-b bg-gray-50">
                <h4 class="font-semibold text-gray-800">Recent Users</h4>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-4 py-3 text-left">Name</th>
                            <th class="px-4 py-3 text-left">Email</th>
                            <th class="px-4 py-3 text-left">Role</th>
                            <th class="px-4 py-3 text-left">Status</th>
                            <th class="px-4 py-3 text-left">Registered</th>
                        </tr>
                    </thead>
                    <tbody id="recentUsersTable">
                        <tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">Loading users...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
let chart = null;

// Set default dates
const endDate = new Date();
const startDate = new Date();
startDate.setMonth(startDate.getMonth() - 11);
startDate.setDate(1);

document.getElementById('startDate').value = formatDateForInput(startDate);
document.getElementById('endDate').value = formatDateForInput(endDate);

// Load report on page load
document.addEventListener('DOMContentLoaded', function() {
    loadReport();
});

async function loadReport() {
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    
    // Show loading, hide content
    document.getElementById('loadingState').style.display = 'block';
    document.getElementById('reportContent').style.display = 'none';
    
    try {
        const params = new URLSearchParams({
            start_date: startDate,
            end_date: endDate
        });
        
        const response = await fetch(`/reports/users/data?${params.toString()}`, {
            headers: { 
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }
        
        const data = await response.json();
        console.log('Report data:', data);
        
        if (data.success) {
            // Update stats cards
            document.getElementById('statTotal').textContent = data.stats.total;
            document.getElementById('statActive').textContent = data.stats.active;
            document.getElementById('statInactive').textContent = data.stats.inactive;
            document.getElementById('statPending').textContent = data.stats.pending;
            document.getElementById('statNewThisMonth').textContent = data.stats.newThisMonth;
            
            // Update gender stats
            const total = data.stats.total;
            const malePercent = total > 0 ? Math.round((data.stats.male / total) * 100) : 0;
            const femalePercent = total > 0 ? Math.round((data.stats.female / total) * 100) : 0;
            const unspecifiedPercent = total > 0 ? Math.round((data.stats.unspecified / total) * 100) : 0;
            
            document.getElementById('genderMale').textContent = data.stats.male;
            document.getElementById('genderMalePercent').textContent = malePercent;
            document.getElementById('genderFemale').textContent = data.stats.female;
            document.getElementById('genderFemalePercent').textContent = femalePercent;
            document.getElementById('genderUnspecified').textContent = data.stats.unspecified;
            document.getElementById('genderUnspecifiedPercent').textContent = unspecifiedPercent;
            
            document.getElementById('maleBar').style.width = malePercent + '%';
            document.getElementById('femaleBar').style.width = femalePercent + '%';
            document.getElementById('unspecifiedBar').style.width = unspecifiedPercent + '%';
            
            // Update role distribution
            if (data.topRoles && data.topRoles.length > 0) {
                const roleHtml = data.topRoles.map(role => `
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-600">${role.display_name || role.name}</span>
                            <span class="font-semibold text-gray-800">${role.count} users</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="bg-purple-500 h-2.5 rounded-full" style="width: ${(role.count / total) * 100}%"></div>
                        </div>
                    </div>
                `).join('');
                document.getElementById('roleDistribution').innerHTML = roleHtml;
            } else {
                document.getElementById('roleDistribution').innerHTML = '<div class="text-center py-4 text-gray-500">No role data available</div>';
            }
            
            // Update recent users table
            if (data.recentUsers && data.recentUsers.length > 0) {
                const usersHtml = data.recentUsers.map(user => `
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium">${escapeHtml(user.name)}</td>
                        <td class="px-4 py-3 text-gray-500">${escapeHtml(user.email)}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs rounded-full ${user.role !== 'No Role' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-500'}">
                                ${escapeHtml(user.role)}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            ${user.is_active ? 
                                '<span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">Active</span>' : 
                                '<span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-700">Inactive</span>'}
                        </td>
                        <td class="px-4 py-3 text-gray-500">${formatDate(user.created_at)}</td>
                    </tr>
                `).join('');
                document.getElementById('recentUsersTable').innerHTML = usersHtml;
            } else {
                document.getElementById('recentUsersTable').innerHTML = '<tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">No users found</td></tr>';
            }
            
            // Initialize chart
            if (data.registrationData && data.registrationData.months) {
                initChart(data.registrationData.months, data.registrationData.counts);
            }
            
            // Hide loading, show content
            document.getElementById('loadingState').style.display = 'none';
            document.getElementById('reportContent').style.display = 'block';
        } else {
            throw new Error(data.message || 'Failed to load report');
        }
    } catch (error) {
        console.error('Error:', error);
        document.getElementById('loadingState').innerHTML = `
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex items-center gap-3">
                    <i class="fas fa-exclamation-triangle text-red-500 text-xl"></i>
                    <div class="flex-1">
                        <p class="text-red-700 font-medium">Error loading report</p>
                        <p class="text-red-600 text-sm">${error.message}</p>
                    </div>
                    <button onclick="loadReport()" class="bg-red-100 hover:bg-red-200 text-red-700 px-3 py-1 rounded text-sm">
                        Retry
                    </button>
                </div>
            </div>
        `;
    }
}

function initChart(months, counts) {
    const canvas = document.getElementById('registrationChart');
    if (!canvas) return;
    
    if (chart) {
        chart.destroy();
    }
    
    const ctx = canvas.getContext('2d');
    chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: months,
            datasets: [{
                label: 'New Users',
                data: counts,
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#3b82f6',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return `${context.parsed.y} new users`;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#e5e7eb' },
                    title: { display: true, text: 'Number of Users', color: '#6b7280' },
                    ticks: { stepSize: 1 }
                },
                x: {
                    grid: { display: false },
                    title: { display: true, text: 'Month', color: '#6b7280' }
                }
            }
        }
    });
}

function exportReport() {
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    window.location.href = `/reports/users/export?start_date=${startDate}&end_date=${endDate}&format=csv`;
}

function formatDate(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
}

function formatDateForInput(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>