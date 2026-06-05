<div>
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-xl font-bold text-gray-800">Attendance Management</h3>
        <button onclick="openAttendanceModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm flex items-center gap-2">
            <i class="fas fa-plus"></i> Mark Attendance
        </button>
    </div>
    
    <!-- Quick Actions Card -->
    <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl shadow-lg p-6 mb-8 text-white">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block text-sm font-medium mb-2 opacity-90">Select Date for Attendance</label>
                <input type="date" id="quick_date" value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 rounded-lg text-gray-800 focus:outline-none focus:ring-2 focus:ring-white">
            </div>
            <div>
                <label class="block text-sm font-medium mb-2 opacity-90">Session Name</label>
                <input type="text" id="quick_session_type" placeholder="e.g., Sunday Service" class="w-full px-3 py-2 rounded-lg text-gray-800 focus:outline-none focus:ring-2 focus:ring-white">
            </div>
            <div class="flex items-end">
                <button onclick="quickMarkAttendance()" class="w-full bg-white text-blue-600 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-gray-100 transition">
                    <i class="fas fa-check-circle mr-2"></i> Quick Mark
                </button>
            </div>
        </div>
    </div>
    
    <!-- Stats Cards Row -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm mb-1">Total Sessions</p>
                    <p class="text-4xl font-bold text-gray-800" id="total_sessions">0</p>
                </div>
                <div class="w-14 h-14 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-calendar-check text-blue-600 text-2xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm mb-1">Average Attendance</p>
                    <p class="text-4xl font-bold text-gray-800" id="avg_attendance">0%</p>
                </div>
                <div class="w-14 h-14 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-chart-line text-green-600 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Detailed Stats Row -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
        <div class="bg-gray-50 rounded-lg p-4 text-center">
            <p class="text-2xl font-bold text-blue-600" id="present_count">0</p>
            <p class="text-xs text-gray-600">Present</p>
        </div>
        <div class="bg-gray-50 rounded-lg p-4 text-center">
            <p class="text-2xl font-bold text-red-600" id="absent_count">0</p>
            <p class="text-xs text-gray-600">Absent</p>
        </div>
        <div class="bg-gray-50 rounded-lg p-4 text-center">
            <p class="text-2xl font-bold text-yellow-600" id="late_count">0</p>
            <p class="text-xs text-gray-600">Late</p>
        </div>
        <div class="bg-gray-50 rounded-lg p-4 text-center">
            <p class="text-2xl font-bold text-purple-600" id="excused_count">0</p>
            <p class="text-xs text-gray-600">Excused</p>
        </div>
        <div class="bg-gray-50 rounded-lg p-4 text-center">
            <p class="text-2xl font-bold text-indigo-600" id="total_members">0</p>
            <p class="text-xs text-gray-600">Total Members</p>
        </div>
    </div>
    
    <!-- Filters -->
    <div class="bg-gray-50 rounded-lg p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date From</label>
                <input type="date" id="attendance_start_date" value="{{ date('Y-m-01') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date To</label>
                <input type="date" id="attendance_end_date" value="{{ date('Y-m-t') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Session Type</label>
                <select id="attendance_session_filter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Sessions</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">&nbsp;</label>
                <button onclick="filterAttendance()" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
                    <i class="fas fa-search"></i> Apply Filter
                </button>
            </div>
        </div>
    </div>
    
    <!-- Recent Attendance Sessions Table -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 border-b">
            <h4 class="font-semibold text-gray-800">Recent Attendance Sessions</h4>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">DATE</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">SESSION</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">PRESENT</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">ABSENT</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">ATTENDANCE %</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">STATUS</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">ACTIONS</th>
                    </tr>
                </thead>
                <tbody id="attendance-table-body">
                    <tr>
                        <td colspan="7" class="text-center py-12 text-gray-500">
                            <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                            <p>Loading attendance records...</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function quickMarkAttendance() {
    const sessionDate = document.getElementById('quick_date').value;
    const sessionType = document.getElementById('quick_session_type').value;
    
    if (!sessionDate || !sessionType) {
        alert('Please fill in both date and session name');
        return;
    }
    
    // Open modal with pre-filled data
    document.getElementById('attendance_modal_title').textContent = 'Mark Attendance';
    document.getElementById('attendance_id').value = '';
    document.getElementById('attendance_user_id').value = '';
    document.getElementById('attendance_session_date').value = sessionDate;
    document.getElementById('attendance_session_type').value = sessionType;
    document.getElementById('attendance_status').value = 'present';
    document.getElementById('attendance_check_in_time').value = '';
    document.getElementById('attendance_check_out_time').value = '';
    document.getElementById('attendance_late_minutes').value = '0';
    document.getElementById('attendance_notes').value = '';
    document.getElementById('attendanceModal').classList.remove('hidden');
}

function openAttendanceModal(attendanceId = null) {
    if (attendanceId) {
        fetch(`/discipline/attendance/${attendanceId}/edit`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('attendance_modal_title').textContent = 'Edit Attendance';
                document.getElementById('attendance_id').value = data.attendance.id;
                document.getElementById('attendance_user_id').value = data.attendance.user_id;
                document.getElementById('attendance_session_date').value = data.attendance.session_date;
                document.getElementById('attendance_session_type').value = data.attendance.session_type;
                document.getElementById('attendance_status').value = data.attendance.status;
                document.getElementById('attendance_check_in_time').value = data.attendance.check_in_time;
                document.getElementById('attendance_check_out_time').value = data.attendance.check_out_time;
                document.getElementById('attendance_late_minutes').value = data.attendance.late_minutes;
                document.getElementById('attendance_notes').value = data.attendance.notes;
                document.getElementById('attendanceModal').classList.remove('hidden');
            }
        });
    } else {
        document.getElementById('attendance_modal_title').textContent = 'Mark Attendance';
        document.getElementById('attendance_id').value = '';
        document.getElementById('attendance_user_id').value = '';
        document.getElementById('attendance_session_date').value = new Date().toISOString().split('T')[0];
        document.getElementById('attendance_session_type').value = '';
        document.getElementById('attendance_status').value = 'present';
        document.getElementById('attendance_check_in_time').value = '';
        document.getElementById('attendance_check_out_time').value = '';
        document.getElementById('attendance_late_minutes').value = '0';
        document.getElementById('attendance_notes').value = '';
        document.getElementById('attendanceModal').classList.remove('hidden');
    }
}

function filterAttendance() {
    const startDate = document.getElementById('attendance_start_date').value;
    const endDate = document.getElementById('attendance_end_date').value;
    const sessionType = document.getElementById('attendance_session_filter').value;
    
    fetch(`/discipline/attendance?start_date=${startDate}&end_date=${endDate}&session_type=${sessionType}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateAttendanceTable(data.attendances);
            updateAttendanceStats(data.attendances);
            updateSessionFilter(data.attendances);
        }
    });
}

function updateAttendanceTable(attendances) {
    const tbody = document.getElementById('attendance-table-body');
    
    if (!attendances || attendances.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center py-12 text-gray-500">No attendance records found</td></tr>';
        return;
    }
    
    // Group by session date and type
    const groupedSessions = {};
    attendances.forEach(att => {
        const key = `${att.session_date}_${att.session_type}`;
        if (!groupedSessions[key]) {
            groupedSessions[key] = {
                date: att.session_date,
                session: att.session_type,
                present: 0,
                absent: 0,
                late: 0,
                excused: 0,
                total: 0
            };
        }
        groupedSessions[key][att.status]++;
        groupedSessions[key].total++;
    });
    
    tbody.innerHTML = Object.values(groupedSessions).map(session => {
        const attendanceRate = session.total > 0 ? ((session.present + session.late) / session.total * 100).toFixed(1) : 0;
        const status = attendanceRate >= 75 ? 'Completed' : (attendanceRate >= 50 ? 'Partial' : 'Low');
        const statusColor = attendanceRate >= 75 ? 'text-green-600 bg-green-50' : (attendanceRate >= 50 ? 'text-yellow-600 bg-yellow-50' : 'text-red-600 bg-red-50');
        
        return `
            <tr class="border-b hover:bg-gray-50">
                <td class="px-6 py-4 text-sm text-gray-600">${session.date}</td>
                <td class="px-6 py-4 text-sm font-medium text-gray-800">${escapeHtml(session.session)}</td>
                <td class="px-6 py-4 text-center text-sm font-semibold text-green-600">${session.present + session.late}</td>
                <td class="px-6 py-4 text-center text-sm text-red-600">${session.absent + session.excused}</td>
                <td class="px-6 py-4 text-center">
                    <div class="flex items-center justify-center gap-2">
                                        <span class="text-sm font-semibold ${attendanceRate >= 75 ? 'text-green-600' : (attendanceRate >= 50 ? 'text-yellow-600' : 'text-red-600')}">
                            ${attendanceRate}%
                        </span>
                        <div class="w-16 bg-gray-200 rounded-full h-1.5">
                            <div class="h-1.5 rounded-full ${attendanceRate >= 75 ? 'bg-green-500' : (attendanceRate >= 50 ? 'bg-yellow-500' : 'bg-red-500')}" style="width: ${attendanceRate}%"></div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 text-center">
                    <span class="px-2 py-1 rounded-full text-xs ${statusColor}">${status}</span>
                </td>
                <td class="px-6 py-4 text-center">
                    <div class="flex items-center justify-center gap-2">
                        <button onclick="viewSessionDetails('${session.date}', '${escapeHtml(session.session)}')" class="text-blue-500 hover:text-blue-700">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button onclick="deleteSession('${session.date}', '${escapeHtml(session.session)}')" class="text-red-500 hover:text-red-700">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }).join('');
}

function updateAttendanceStats(attendances) {
    const total = attendances.length;
    const present = attendances.filter(a => a.status === 'present').length;
    const absent = attendances.filter(a => a.status === 'absent').length;
    const late = attendances.filter(a => a.status === 'late').length;
    const excused = attendances.filter(a => a.status === 'excused').length;
    
    // Get unique users
    const uniqueUsers = [...new Set(attendances.map(a => a.user_id))];
    const totalMembers = uniqueUsers.length;
    
    // Calculate average attendance rate
    const presentRate = total > 0 ? ((present + late) / total * 100).toFixed(1) : 0;
    
    document.getElementById('total_sessions').textContent = total;
    document.getElementById('avg_attendance').textContent = `${presentRate}%`;
    document.getElementById('present_count').textContent = present;
    document.getElementById('absent_count').textContent = absent;
    document.getElementById('late_count').textContent = late;
    document.getElementById('excused_count').textContent = excused;
    document.getElementById('total_members').textContent = totalMembers;
}

function updateSessionFilter(attendances) {
    const sessionTypes = [...new Set(attendances.map(a => a.session_type))];
    const filterSelect = document.getElementById('attendance_session_filter');
    
    const currentValue = filterSelect.value;
    filterSelect.innerHTML = '<option value="">All Sessions</option>';
    sessionTypes.forEach(type => {
        filterSelect.innerHTML += `<option value="${escapeHtml(type)}">${escapeHtml(type)}</option>`;
    });
    filterSelect.value = currentValue;
}

function viewSessionDetails(date, sessionType) {
    // Open modal or navigate to session details
    window.location.href = `/discipline/attendance/session?date=${date}&type=${encodeURIComponent(sessionType)}`;
}

function deleteSession(date, sessionType) {
    if (confirm(`Delete all attendance records for "${sessionType}" on ${date}?`)) {
        fetch(`/discipline/attendance/session?date=${date}&type=${encodeURIComponent(sessionType)}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                filterAttendance();
            } else {
                alert('Error deleting session records');
            }
        });
    }
}

function deleteAttendance(id) {
    if (confirm('Are you sure you want to delete this attendance record?')) {
        fetch(`/discipline/attendance/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                filterAttendance();
            } else {
                alert('Error deleting attendance record');
            }
        });
    }
}

function getStatusBadge(status) {
    const badges = {
        'present': 'bg-green-100 text-green-700',
        'absent': 'bg-red-100 text-red-700',
        'late': 'bg-yellow-100 text-yellow-700',
        'excused': 'bg-blue-100 text-blue-700'
    };
    return badges[status] || 'bg-gray-100 text-gray-700';
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

// Load initial data
setTimeout(() => {
    filterAttendance();
}, 100);
</script>