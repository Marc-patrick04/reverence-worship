<div class="space-y-6">
    <!-- Header with Title Only -->
    <div class="flex justify-between items-center">
        <div>
            <h3 class="text-2xl font-bold text-gray-800">Attendance Management</h3>
            <p class="text-sm text-gray-500 mt-1">Track and manage member attendance</p>
        </div>
           </div>

    <!-- Stats Cards - Clean and Minimal -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-2xl font-bold text-gray-800" id="total_sessions">0</p>
                    <p class="text-xs text-gray-500">Total Sessions</p>
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar-alt text-blue-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-2xl font-bold text-green-600" id="present_count">0</p>
                    <p class="text-xs text-gray-500">Present</p>
                </div>
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-2xl font-bold text-yellow-600" id="late_count">0</p>
                    <p class="text-xs text-gray-500">Late</p>
                </div>
                <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-2xl font-bold text-red-600" id="absent_count">0</p>
                    <p class="text-xs text-gray-500">Absent</p>
                </div>
                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-times-circle text-red-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Session Start - Simplified -->
    <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl shadow-lg p-5 text-white">
        <div class="flex flex-col md:flex-row gap-4 items-end">
            <div class="flex-1">
                <label class="block text-xs font-medium mb-1 opacity-80">Session Date</label>
                <input type="date" id="quick_date" value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 rounded-lg text-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-white">
            </div>
            <div class="flex-1">
                <label class="block text-xs font-medium mb-1 opacity-80">Session Name</label>
                <input type="text" id="quick_session_type" placeholder="Sunday Service" class="w-full px-3 py-2 rounded-lg text-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-white">
            </div>
            <button onclick="quickMarkAttendance()" class="bg-white text-blue-600 px-6 py-2 rounded-lg text-sm font-semibold hover:bg-gray-100 transition shadow-md">
                <i class="fas fa-play mr-2"></i> Start Session
            </button>
        </div>
    </div>

    <!-- Filters - Simple Row -->
    <div class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs text-gray-600 mb-1">From</label>
            <input type="date" id="attendance_start_date" value="{{ date('Y-m-01') }}" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
        </div>
        <div>
            <label class="block text-xs text-gray-600 mb-1">To</label>
            <input type="date" id="attendance_end_date" value="{{ date('Y-m-t') }}" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
        </div>
        <div>
            <label class="block text-xs text-gray-600 mb-1">Session</label>
            <select id="attendance_session_filter" class="px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white">
                <option value="">All Sessions</option>
            </select>
        </div>
        <button onclick="applyFilter()" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm transition">
            <i class="fas fa-search mr-1"></i> Filter
        </button>
    </div>

    <!-- Sessions Table - Clean -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase">DATE</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase">SESSION</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-600 uppercase">PRESENT</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-600 uppercase">ABSENT</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-600 uppercase">RATE</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-600 uppercase">ACTIONS</th>
                </tr>
            </thead>
            <tbody id="attendance-table-body">
                <tr>
                    <td colspan="6" class="text-center py-12 text-gray-400">
                        <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                        <p>Loading sessions...</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script>
var currentAttendanceData = [];

document.addEventListener('DOMContentLoaded', function() {
    loadAttendanceData();
    loadTotalMembers();
});

function loadTotalMembers() {
    fetch('/users/list', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.users) {
            const totalMembersEl = document.getElementById('total_members');
            if (totalMembersEl) totalMembersEl.textContent = data.users.length;
        }
    })
    .catch(error => console.error('Error loading members:', error));
}

function loadAttendanceData() {
    const startDate = document.getElementById('attendance_start_date')?.value || '';
    const endDate = document.getElementById('attendance_end_date')?.value || '';
    const sessionType = document.getElementById('attendance_session_filter')?.value || '';
    
    const url = `/discipline/attendance?start_date=${startDate}&end_date=${endDate}&session_type=${sessionType}`;
    
    fetch(url, {
        headers: { 
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            currentAttendanceData = data.attendances || [];
            renderAttendanceTable(currentAttendanceData);
            renderAttendanceStats(currentAttendanceData);
            renderSessionFilter(currentAttendanceData);
        }
    })
    .catch(error => {
        console.error('Error loading attendance:', error);
        const tbody = document.getElementById('attendance-table-body');
        if (tbody) {
            tbody.innerHTML = `<tr><td colspan="6" class="text-center py-12 text-red-400">Failed to load data</td></tr>`;
        }
    });
}

function renderAttendanceTable(attendances) {
    const tbody = document.getElementById('attendance-table-body');
    if (!tbody) return;
    
    if (!attendances || attendances.length === 0) {
        tbody.innerHTML = `<tr><td colspan="6" class="text-center py-12 text-gray-400">No attendance records found</td></tr>`;
        return;
    }
    
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
        if (att.status === 'present') groupedSessions[key].present++;
        else if (att.status === 'absent') groupedSessions[key].absent++;
        else if (att.status === 'late') groupedSessions[key].late++;
        else if (att.status === 'excused') groupedSessions[key].excused++;
        groupedSessions[key].total++;
    });
    
    tbody.innerHTML = Object.values(groupedSessions).map(session => {
        const totalPresent = session.present + session.late;
        const totalAbsent = session.absent + session.excused;
        const attendanceRate = session.total > 0 ? ((totalPresent / session.total) * 100).toFixed(0) : 0;
        const rateColor = attendanceRate >= 75 ? 'text-green-600' : (attendanceRate >= 50 ? 'text-yellow-600' : 'text-red-600');
        const formattedDate = session.date.split('-').reverse().join('/');
        
        return `
            <tr class="border-b hover:bg-gray-50 transition">
                <td class="px-5 py-3 text-sm text-gray-600">${escapeHtml(formattedDate)}</td>
                <td class="px-5 py-3 text-sm font-medium text-gray-800">${escapeHtml(session.session)}</td>
                <td class="px-5 py-3 text-center text-sm font-semibold text-green-600">${totalPresent}</td>
                <td class="px-5 py-3 text-center text-sm text-red-500">${totalAbsent}</td>
                <td class="px-5 py-3 text-center">
                    <span class="text-sm font-semibold ${rateColor}">${attendanceRate}%</span>
                </td>
                <td class="px-5 py-3 text-center">
                    <div class="flex items-center justify-center gap-2">
                        <button onclick="window.viewSession('${session.date}', '${escapeHtml(session.session)}')" class="text-blue-500 hover:text-blue-700 transition" title="View">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button onclick="window.deleteSessionRecord('${session.date}', '${escapeHtml(session.session)}')" class="text-red-400 hover:text-red-600 transition" title="Delete">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }).join('');
}

function renderAttendanceStats(attendances) {
    const total = attendances.length;
    const present = attendances.filter(a => a.status === 'present').length;
    const absent = attendances.filter(a => a.status === 'absent').length;
    const late = attendances.filter(a => a.status === 'late').length;
    
    const uniqueSessions = new Set(attendances.map(a => `${a.session_date}_${a.session_type}`));
    const totalSessions = uniqueSessions.size;
    
    const totalSessionsEl = document.getElementById('total_sessions');
    const presentCountEl = document.getElementById('present_count');
    const absentCountEl = document.getElementById('absent_count');
    const lateCountEl = document.getElementById('late_count');
    
    if (totalSessionsEl) totalSessionsEl.textContent = totalSessions;
    if (presentCountEl) presentCountEl.textContent = present;
    if (absentCountEl) absentCountEl.textContent = absent;
    if (lateCountEl) lateCountEl.textContent = late;
}

function renderSessionFilter(attendances) {
    const sessionTypes = [...new Set(attendances.map(a => a.session_type))];
    const filterSelect = document.getElementById('attendance_session_filter');
    if (!filterSelect) return;
    
    const currentValue = filterSelect.value;
    filterSelect.innerHTML = '<option value="">All Sessions</option>';
    sessionTypes.forEach(type => {
        filterSelect.innerHTML += `<option value="${escapeHtml(type)}">${escapeHtml(type)}</option>`;
    });
    filterSelect.value = currentValue;
}

function quickMarkAttendance() {
    const sessionDate = document.getElementById('quick_date')?.value;
    const sessionType = document.getElementById('quick_session_type')?.value;
    
    if (!sessionDate || !sessionType) {
        alert('Please enter session date and name');
        return;
    }
    
    if (typeof window.openSessionDetailsModal === 'function') {
        // Encode the session type for URL
        const encodedType = encodeURIComponent(sessionType);
        window.openSessionDetailsModal(sessionDate, encodedType);
    } else {
        alert('Please use the "Mark Attendance" button');
    }
}

function openAttendanceModal(attendanceIdParam = null) {
    const modal = document.getElementById('attendanceModal');
    if (!modal) {
        alert('Form not ready. Please refresh.');
        return;
    }
    
    const modalTitle = document.getElementById('attendance_modal_title');
    const attendanceIdField = document.getElementById('attendance_id');
    const userIdField = document.getElementById('attendance_user_id');
    const sessionDateField = document.getElementById('attendance_session_date');
    const sessionTypeField = document.getElementById('attendance_session_type');
    const statusField = document.getElementById('attendance_status');
    const checkInTime = document.getElementById('attendance_check_in_time');
    const lateMinutes = document.getElementById('attendance_late_minutes');
    const notes = document.getElementById('attendance_notes');
    
    if (attendanceIdParam) {
        fetch(`/discipline/attendance/${attendanceIdParam}/edit`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.attendance) {
                if (modalTitle) modalTitle.textContent = 'Edit Attendance';
                if (attendanceIdField) attendanceIdField.value = data.attendance.id;
                if (userIdField) userIdField.value = data.attendance.user_id;
                if (sessionDateField) sessionDateField.value = data.attendance.session_date;
                if (sessionTypeField) sessionTypeField.value = data.attendance.session_type;
                if (statusField) statusField.value = data.attendance.status;
                if (checkInTime) checkInTime.value = data.attendance.check_in_time || '';
                if (lateMinutes) lateMinutes.value = data.attendance.late_minutes || 0;
                if (notes) notes.value = data.attendance.notes || '';
                modal.classList.remove('hidden');
            }
        });
    } else {
        if (modalTitle) modalTitle.textContent = 'Mark Attendance';
        if (attendanceIdField) attendanceIdField.value = '';
        if (userIdField) userIdField.value = '';
        if (sessionDateField) sessionDateField.value = new Date().toISOString().split('T')[0];
        if (sessionTypeField) sessionTypeField.value = '';
        if (statusField) statusField.value = 'present';
        if (checkInTime) checkInTime.value = '';
        if (lateMinutes) lateMinutes.value = '0';
        if (notes) notes.value = '';
        modal.classList.remove('hidden');
    }
}

function viewSession(date, sessionType) {
    if (typeof window.openSessionDetailsModal === 'function') {
        // Encode the session type for URL
        const encodedType = encodeURIComponent(sessionType);
        window.openSessionDetailsModal(date, encodedType);
    } else {
        // Use the correct URL format with slashes
        const encodedType = encodeURIComponent(sessionType);
        fetch(`/discipline/attendance/session-summary?date=${date}&type=${encodedType}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(`Session: ${sessionType}\nDate: ${date}\nPresent: ${data.present}\nAbsent: ${data.absent}\nRate: ${data.rate}%`);
            }
        })
        .catch(error => console.error('Error:', error));
    }
}

function deleteSessionRecord(date, sessionType) {
    if (confirm(`Delete "${sessionType}" on ${date}?`)) {
        const encodedType = encodeURIComponent(sessionType);
        fetch(`/discipline/attendance/session?date=${date}&type=${encodedType}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadAttendanceData();
                alert('Session deleted');
            }
        })
        .catch(error => console.error('Error:', error));
    }
}

function applyFilter() {
    loadAttendanceData();
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) modal.classList.add('hidden');
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Global exports
window.loadAttendanceData = loadAttendanceData;
window.applyFilter = applyFilter;
window.viewSession = viewSession;
window.deleteSessionRecord = deleteSessionRecord;
window.openAttendanceModal = openAttendanceModal;
window.quickMarkAttendance = quickMarkAttendance;
window.closeModal = closeModal;
</script>