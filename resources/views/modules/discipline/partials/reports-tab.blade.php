<div>
    <!-- Header -->
    <div class="mb-6">
        <h3 class="text-xl font-bold text-gray-800 mb-1">Discipline Management Reports</h3>
        <p class="text-gray-500 text-sm">View comprehensive reports for attendance and discipline management</p>
    </div>
    
    <!-- Report Type Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-4 text-white cursor-pointer hover:shadow-xl transition" onclick="switchReportType('attendance')">
            <div class="flex items-center justify-between mb-2">
                <i class="fas fa-calendar-check text-2xl opacity-80"></i>
                <i class="fas fa-chart-line text-xl opacity-60"></i>
            </div>
            <p class="font-semibold text-lg">Attendance Reports</p>
            <p class="text-xs opacity-80 mt-1">Track attendance analytics</p>
        </div>
        
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-4 text-white cursor-pointer hover:shadow-xl transition" onclick="switchReportType('discipline')">
            <div class="flex items-center justify-between mb-2">
                <i class="fas fa-gavel text-2xl opacity-80"></i>
                <i class="fas fa-chart-line text-xl opacity-60"></i>
            </div>
            <p class="font-semibold text-lg">Discipline Reports</p>
            <p class="text-xs opacity-80 mt-1">Track discipline analytics</p>
        </div>
        
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-4 text-white cursor-pointer hover:shadow-xl transition" onclick="switchReportType('combined')">
            <div class="flex items-center justify-between mb-2">
                <i class="fas fa-chart-pie text-2xl opacity-80"></i>
                <i class="fas fa-chart-line text-xl opacity-60"></i>
            </div>
            <p class="font-semibold text-lg">Combined Reports</p>
            <p class="text-xs opacity-80 mt-1">Complete overview</p>
        </div>
        
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg p-4 text-white cursor-pointer hover:shadow-xl transition" onclick="switchReportType('permission')">
            <div class="flex items-center justify-between mb-2">
                <i class="fas fa-envelope-open-text text-2xl opacity-80"></i>
                <i class="fas fa-chart-line text-xl opacity-60"></i>
            </div>
            <p class="font-semibold text-lg">Permission Reports</p>
            <p class="text-xs opacity-80 mt-1">Track permission analytics</p>
        </div>
    </div>
    
    <!-- Quick Actions Section -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden mb-8">
        <div class="bg-gray-50 px-6 py-4 border-b">
            <h4 class="font-semibold text-gray-800">Quick Actions</h4>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Quick Actions Left -->
                <div>
                    <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl p-5 text-white">
                        <h5 class="font-semibold mb-3">Attendance Management</h5>
                        <p class="text-sm opacity-90 mb-4">Track and manage team attendance</p>
                        
                        <div class="space-y-3">
                            <div>
                                <label class="block text-xs font-medium mb-1 opacity-80">Select Date for Attendance</label>
                                <input type="date" id="report_attendance_date" value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 rounded-lg text-gray-800 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-medium mb-1 opacity-80">Session Name</label>
                                <input type="text" id="report_session_name" placeholder="e.g., Sunday Service" class="w-full px-3 py-2 rounded-lg text-gray-800 text-sm">
                            </div>
                            <div class="flex gap-3 pt-2">
                                <button onclick="generateAttendanceReport()" class="flex-1 bg-white text-blue-600 px-3 py-2 rounded-lg text-sm font-semibold hover:bg-gray-100 transition">
                                    <i class="fas fa-chart-bar mr-2"></i> Attendance Report
                                </button>
                                <button onclick="startAttendanceSession()" class="flex-1 bg-green-600 text-white px-3 py-2 rounded-lg text-sm font-semibold hover:bg-green-700 transition">
                                    <i class="fas fa-play-circle mr-2"></i> Start Attendance Session
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Stats -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-blue-50 rounded-xl p-4 text-center">
                        <p class="text-3xl font-bold text-blue-600" id="report_total_sessions">0</p>
                        <p class="text-xs text-gray-600 mt-1">Total Sessions</p>
                    </div>
                    <div class="bg-green-50 rounded-xl p-4 text-center">
                        <p class="text-3xl font-bold text-green-600" id="report_avg_attendance">0%</p>
                        <p class="text-xs text-gray-600 mt-1">Average Attendance</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Report Content Area -->
    <div id="report-content" class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 border-b">
            <h4 class="font-semibold text-gray-800">Report Details</h4>
        </div>
        <div class="p-6" id="report-details">
            <div class="text-center py-12">
                <i class="fas fa-chart-line text-5xl text-gray-300 mb-3"></i>
                <p class="text-gray-500">Select a report type to view detailed analytics</p>
            </div>
        </div>
    </div>
</div>

<script>
let currentReportType = 'attendance';

// Make functions available globally
window.switchReportType = switchReportType;
window.generateAttendanceReport = generateAttendanceReport;
window.startAttendanceSession = startAttendanceSession;
window.loadReports = loadReports;
window.loadReportsData = loadReportsData;

function switchReportType(type) {
    currentReportType = type;
    
    // Update card styles (optional visual feedback)
    const cards = document.querySelectorAll('.grid-cols-1.md\\:grid-cols-4 > div');
    cards.forEach((card, index) => {
        card.style.opacity = index === getCardIndex(type) ? '1' : '0.7';
    });
    
    loadReportsData();
}

function getCardIndex(type) {
    const types = ['attendance', 'discipline', 'combined', 'permission'];
    return types.indexOf(type);
}

function loadReportsData() {
    const container = document.getElementById('report-details');
    container.innerHTML = `
        <div class="text-center py-12">
            <i class="fas fa-spinner fa-spin text-3xl text-gray-400 mb-3"></i>
            <p class="text-gray-500">Loading ${currentReportType} report...</p>
        </div>
    `;
    
    fetch(`/discipline/reports/generate?type=${currentReportType}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateReportContent(data);
            updateQuickStats(data);
        } else {
            container.innerHTML = `
                <div class="text-center py-12 text-red-500">
                    <i class="fas fa-exclamation-circle text-3xl mb-3"></i>
                    <p>Error loading report: ${data.message || 'Unknown error'}</p>
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        container.innerHTML = `
            <div class="text-center py-12 text-red-500">
                <i class="fas fa-exclamation-circle text-3xl mb-3"></i>
                <p>Error loading report. Please try again.</p>
            </div>
        `;
    });
}

function updateReportContent(data) {
    const container = document.getElementById('report-details');
    
    switch(currentReportType) {
        case 'attendance':
            container.innerHTML = generateAttendanceReportContent(data);
            break;
        case 'discipline':
            container.innerHTML = generateDisciplineReportContent(data);
            break;
        case 'combined':
            container.innerHTML = generateCombinedReportContent(data);
            break;
        case 'permission':
            container.innerHTML = generatePermissionReportContent(data);
            break;
        default:
            container.innerHTML = generateAttendanceReportContent(data);
    }
}

function generateAttendanceReportContent(data) {
    const attendance = data.attendance_summary || {};
    const total = attendance.total_sessions || 1;
    const presentPercent = ((attendance.present_count || 0) / total * 100).toFixed(1);
    const absentPercent = ((attendance.absent_count || 0) / total * 100).toFixed(1);
    
    return `
        <div class="space-y-6">
            <!-- Summary Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-blue-50 rounded-lg p-4 text-center">
                    <p class="text-2xl font-bold text-blue-600">${attendance.total_sessions || 0}</p>
                    <p class="text-xs text-gray-600">Total Sessions</p>
                </div>
                <div class="bg-green-50 rounded-lg p-4 text-center">
                    <p class="text-2xl font-bold text-green-600">${attendance.present_count || 0}</p>
                    <p class="text-xs text-gray-600">Present (${presentPercent}%)</p>
                </div>
                <div class="bg-red-50 rounded-lg p-4 text-center">
                    <p class="text-2xl font-bold text-red-600">${attendance.absent_count || 0}</p>
                    <p class="text-xs text-gray-600">Absent (${absentPercent}%)</p>
                </div>
                <div class="bg-yellow-50 rounded-lg p-4 text-center">
                    <p class="text-2xl font-bold text-yellow-600">${attendance.late_count || 0}</p>
                    <p class="text-xs text-gray-600">Late</p>
                </div>
            </div>
            
            <!-- Progress Bar -->
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="flex justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700">Attendance Rate</span>
                    <span class="text-sm font-semibold ${presentPercent >= 80 ? 'text-green-600' : (presentPercent >= 60 ? 'text-yellow-600' : 'text-red-600')}">${presentPercent}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5">
                    <div class="h-2.5 rounded-full ${presentPercent >= 80 ? 'bg-green-500' : (presentPercent >= 60 ? 'bg-yellow-500' : 'bg-red-500')}" style="width: ${presentPercent}%"></div>
                </div>
            </div>
            
            <!-- Export Button -->
            <div class="flex justify-end">
                <button onclick="exportReport('attendance')" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm">
                    <i class="fas fa-download mr-2"></i> Export Report
                </button>
            </div>
        </div>
    `;
}

function generateDisciplineReportContent(data) {
    const discipline = data.discipline_summary || {};
    const total = discipline.total_records || 1;
    const positivePercent = ((discipline.positive_count || 0) / total * 100).toFixed(1);
    
    return `
        <div class="space-y-6">
            <!-- Summary Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-purple-50 rounded-lg p-4 text-center">
                    <p class="text-2xl font-bold text-purple-600">${discipline.total_records || 0}</p>
                    <p class="text-xs text-gray-600">Total Records</p>
                </div>
                <div class="bg-green-50 rounded-lg p-4 text-center">
                    <p class="text-2xl font-bold text-green-600">${discipline.positive_count || 0}</p>
                    <p class="text-xs text-gray-600">Positive (${positivePercent}%)</p>
                </div>
                <div class="bg-yellow-50 rounded-lg p-4 text-center">
                    <p class="text-2xl font-bold text-yellow-600">${discipline.warning_count || 0}</p>
                    <p class="text-xs text-gray-600">Warnings</p>
                </div>
                <div class="bg-red-50 rounded-lg p-4 text-center">
                    <p class="text-2xl font-bold text-red-600">${discipline.penalty_count || 0}</p>
                    <p class="text-xs text-gray-600">Penalties</p>
                </div>
            </div>
            
            <!-- Progress Bar -->
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="flex justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700">Positive Behavior Rate</span>
                    <span class="text-sm font-semibold ${positivePercent >= 80 ? 'text-green-600' : (positivePercent >= 60 ? 'text-yellow-600' : 'text-red-600')}">${positivePercent}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5">
                    <div class="h-2.5 rounded-full ${positivePercent >= 80 ? 'bg-green-500' : (positivePercent >= 60 ? 'bg-yellow-500' : 'bg-red-500')}" style="width: ${positivePercent}%"></div>
                </div>
            </div>
            
            <!-- Status Breakdown -->
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-green-50 rounded-lg p-3">
                    <p class="text-xs text-gray-600">Resolved</p>
                    <p class="text-xl font-bold text-green-600">${discipline.resolved_count || 0}</p>
                </div>
                <div class="bg-red-50 rounded-lg p-3">
                    <p class="text-xs text-gray-600">Active</p>
                    <p class="text-xl font-bold text-red-600">${discipline.active_count || 0}</p>
                </div>
            </div>
            
            <!-- Export Button -->
            <div class="flex justify-end">
                <button onclick="exportReport('discipline')" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm">
                    <i class="fas fa-download mr-2"></i> Export Report
                </button>
            </div>
        </div>
    `;
}

function generateCombinedReportContent(data) {
    const attendance = data.attendance_summary || {};
    const discipline = data.discipline_summary || {};
    const totalSessions = attendance.total_sessions || 1;
    const presentPercent = ((attendance.present_count || 0) / totalSessions * 100).toFixed(1);
    const totalRecords = discipline.total_records || 1;
    const positivePercent = ((discipline.positive_count || 0) / totalRecords * 100).toFixed(1);
    
    return `
        <div class="space-y-6">
            <!-- Two Column Stats -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Attendance Stats -->
                <div class="bg-blue-50 rounded-lg p-4">
                    <h5 class="font-semibold text-blue-800 mb-3">Attendance Overview</h5>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Total Sessions:</span>
                            <span class="font-semibold">${attendance.total_sessions || 0}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Present:</span>
                            <span class="text-green-600">${attendance.present_count || 0}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Absent:</span>
                            <span class="text-red-600">${attendance.absent_count || 0}</span>
                        </div>
                        <div class="mt-2 pt-2 border-t">
                            <div class="flex justify-between">
                                <span class="text-sm font-medium">Attendance Rate:</span>
                                <span class="font-bold ${presentPercent >= 80 ? 'text-green-600' : 'text-yellow-600'}">${presentPercent}%</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Discipline Stats -->
                <div class="bg-purple-50 rounded-lg p-4">
                    <h5 class="font-semibold text-purple-800 mb-3">Discipline Overview</h5>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Total Records:</span>
                            <span class="font-semibold">${discipline.total_records || 0}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Positive:</span>
                            <span class="text-green-600">${discipline.positive_count || 0}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Warnings/Penalties:</span>
                            <span class="text-red-600">${(discipline.warning_count || 0) + (discipline.penalty_count || 0)}</span>
                        </div>
                        <div class="mt-2 pt-2 border-t">
                            <div class="flex justify-between">
                                <span class="text-sm font-medium">Positive Rate:</span>
                                <span class="font-bold ${positivePercent >= 80 ? 'text-green-600' : 'text-yellow-600'}">${positivePercent}%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Top Performers -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h5 class="font-semibold text-gray-800 mb-3">Top Performers</h5>
                <div id="combined-performers">
                    ${data.top_performers && data.top_performers.length > 0 ? `
                        <div class="space-y-2">
                            ${data.top_performers.slice(0, 5).map((performer, index) => `
                                <div class="flex justify-between items-center">
                                    <div class="flex items-center gap-2">
                                        <span class="text-lg font-bold text-gray-400">#${index + 1}</span>
                                        <span class="text-sm">${escapeHtml(performer.user_name)}</span>
                                    </div>
                                    <span class="text-green-600 font-semibold">+${performer.positive_points || 0}</span>
                                </div>
                            `).join('')}
                        </div>
                    ` : '<p class="text-center text-gray-500 py-4">No data available</p>'}
                </div>
            </div>
            
            <!-- Export Button -->
            <div class="flex justify-end">
                <button onclick="exportReport('combined')" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm">
                    <i class="fas fa-download mr-2"></i> Export Combined Report
                </button>
            </div>
        </div>
    `;
}

function generatePermissionReportContent(data) {
    const permissions = data.permission_summary || {};
    
    return `
        <div class="space-y-6">
            <!-- Summary Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-blue-50 rounded-lg p-4 text-center">
                    <p class="text-2xl font-bold text-blue-600">${permissions.total_requests || 0}</p>
                    <p class="text-xs text-gray-600">Total Requests</p>
                </div>
                <div class="bg-yellow-50 rounded-lg p-4 text-center">
                    <p class="text-2xl font-bold text-yellow-600">${permissions.pending_count || 0}</p>
                    <p class="text-xs text-gray-600">Pending</p>
                </div>
                <div class="bg-green-50 rounded-lg p-4 text-center">
                    <p class="text-2xl font-bold text-green-600">${permissions.approved_count || 0}</p>
                    <p class="text-xs text-gray-600">Approved</p>
                </div>
                <div class="bg-red-50 rounded-lg p-4 text-center">
                    <p class="text-2xl font-bold text-red-600">${permissions.rejected_count || 0}</p>
                    <p class="text-xs text-gray-600">Rejected</p>
                </div>
            </div>
            
            <!-- Approval Rate -->
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="flex justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700">Approval Rate</span>
                    <span class="text-sm font-semibold">${permissions.approval_rate || 0}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5">
                    <div class="h-2.5 rounded-full bg-green-500" style="width: ${permissions.approval_rate || 0}%"></div>
                </div>
            </div>
            
            <!-- Export Button -->
            <div class="flex justify-end">
                <button onclick="exportReport('permission')" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm">
                    <i class="fas fa-download mr-2"></i> Export Report
                </button>
            </div>
        </div>
    `;
}

function updateQuickStats(data) {
    const attendance = data.attendance_summary || {};
    const total = attendance.total_sessions || 0;
    const present = attendance.present_count || 0;
    const presentRate = total > 0 ? ((present / total) * 100).toFixed(1) : 0;
    
    const totalSessionsEl = document.getElementById('report_total_sessions');
    const avgAttendanceEl = document.getElementById('report_avg_attendance');
    
    if (totalSessionsEl) totalSessionsEl.textContent = total;
    if (avgAttendanceEl) avgAttendanceEl.textContent = `${presentRate}%`;
}

function generateAttendanceReport() {
    const date = document.getElementById('report_attendance_date').value;
    const sessionName = document.getElementById('report_session_name').value;
    
    if (!sessionName) {
        alert('Please enter a session name');
        return;
    }
    
    window.location.href = `/discipline/reports/attendance?date=${date}&session=${encodeURIComponent(sessionName)}`;
}

function startAttendanceSession() {
    const date = document.getElementById('report_attendance_date').value;
    const sessionName = document.getElementById('report_session_name').value;
    
    if (!sessionName) {
        alert('Please enter a session name');
        return;
    }
    
    // Switch to attendance tab and open modal
    const attendanceTab = document.querySelector('.tab-btn[data-tab="attendance"]');
    if (attendanceTab) {
        attendanceTab.click();
        setTimeout(() => {
            if (typeof window.openAttendanceModal === 'function') {
                window.openAttendanceModal();
                document.getElementById('attendance_session_date').value = date;
                document.getElementById('attendance_session_type').value = sessionName;
            }
        }, 500);
    }
}

function exportReport(type) {
    window.location.href = `/discipline/reports/export?type=${type}&period=monthly`;
}

function loadReports() {
    loadReportsData();
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Load initial data
setTimeout(() => {
    loadReportsData();
}, 100);
</script>