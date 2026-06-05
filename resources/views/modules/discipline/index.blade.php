@extends('layouts.app')

@section('title', 'Discipline Management')
@section('page-title', 'Discipline Management')

@section('content')
<div class="container mx-auto px-4 py-8">
   
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Discipline Sessions</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $stats->total_discipline_sessions ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-gavel text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Avg Good Behavior</p>
                    <p class="text-3xl font-bold text-green-600">{{ round($stats->avg_good_behavior ?? 0) }}%</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-smile text-green-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Attendance Sessions</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $stats->attendance_sessions ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-calendar-check text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Permission Requests</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $stats->permission_requests ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-envelope text-orange-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Tabs Navigation -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="border-b border-gray-200">
            <nav class="flex flex-wrap -mb-px">
                <button class="tab-btn active px-6 py-3 text-sm font-medium text-blue-600 border-b-2 border-blue-600" data-tab="overview">
                    <i class="fas fa-chart-line mr-2"></i> Overview
                </button>
                <button class="tab-btn px-6 py-3 text-sm font-medium text-gray-500 border-b-2 border-transparent hover:text-gray-700 hover:border-gray-300" data-tab="attendance">
                    <i class="fas fa-calendar-alt mr-2"></i> Attendance
                </button>
                <button class="tab-btn px-6 py-3 text-sm font-medium text-gray-500 border-b-2 border-transparent hover:text-gray-700 hover:border-gray-300" data-tab="permission">
                    <i class="fas fa-envelope-open-text mr-2"></i> Permission Requests
                </button>
                <button class="tab-btn px-6 py-3 text-sm font-medium text-gray-500 border-b-2 border-transparent hover:text-gray-700 hover:border-gray-300" data-tab="discipline-records">
                    <i class="fas fa-book mr-2"></i> Discipline Records
                </button>
                <button class="tab-btn px-6 py-3 text-sm font-medium text-gray-500 border-b-2 border-transparent hover:text-gray-700 hover:border-gray-300" data-tab="action-plans">
                    <i class="fas fa-tasks mr-2"></i> Action Plans
                </button>
                <button class="tab-btn px-6 py-3 text-sm font-medium text-gray-500 border-b-2 border-transparent hover:text-gray-700 hover:border-gray-300" data-tab="reports">
                    <i class="fas fa-chart-bar mr-2"></i> Reports
                </button>
            </nav>
        </div>
        
        <!-- Tab Content -->
        <div class="p-6">
            <div id="overview-tab" class="tab-content" style="display: block;">
                @include('modules.discipline.partials.overview-tab')
            </div>
            <div id="attendance-tab" class="tab-content" style="display: none;">
                @include('modules.discipline.partials.attendance-tab')
            </div>
            <div id="permission-tab" class="tab-content" style="display: none;">
                @include('modules.discipline.partials.permission-tab')
            </div>
            <div id="discipline-records-tab" class="tab-content" style="display: none;">
                @include('modules.discipline.partials.discipline-records-tab')
            </div>
            <div id="action-plans-tab" class="tab-content" style="display: none;">
                @include('modules.discipline.partials.action-plans-tab')
            </div>
            <div id="reports-tab" class="tab-content" style="display: none;">
                @include('modules.discipline.partials.reports-tab')
            </div>
        </div>
    </div>
</div>

@include('modules.discipline.modals.discipline-modal')
@include('modules.discipline.modals.attendance-modal')
@include('modules.discipline.modals.permission-modal')
@include('modules.discipline.modals.action-plan-modal')

<script>
// Tab Management with localStorage persistence
const STORAGE_KEY = 'discipline_active_tab';

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing tabs...');
    
    // Get saved tab from localStorage
    const savedTab = localStorage.getItem(STORAGE_KEY);
    const defaultTab = 'overview';
    const activeTab = savedTab && isValidTab(savedTab) ? savedTab : defaultTab;
    
    console.log('Active tab from storage:', activeTab);
    
    // Activate the saved or default tab
    activateTab(activeTab);
    
    // Set up tab click handlers
    setupTabClickHandlers();
});

function isValidTab(tabName) {
    const validTabs = ['overview', 'attendance', 'permission', 'discipline-records', 'action-plans', 'reports'];
    return validTabs.includes(tabName);
}

function setupTabClickHandlers() {
    const tabs = document.querySelectorAll('.tab-btn');
    console.log('Found tabs:', tabs.length);
    
    tabs.forEach(tab => {
        // Remove existing listener to avoid duplicates
        tab.removeEventListener('click', handleTabClick);
        tab.addEventListener('click', handleTabClick);
    });
}

function handleTabClick(event) {
    const tab = event.currentTarget;
    const tabName = tab.getAttribute('data-tab');
    
    console.log('Tab clicked:', tabName);
    
    // Save to localStorage
    localStorage.setItem(STORAGE_KEY, tabName);
    
    // Activate the tab
    activateTab(tabName);
}

function activateTab(tabName) {
    console.log('Activating tab:', tabName);
    
    // Update tab buttons styles
    const tabs = document.querySelectorAll('.tab-btn');
    
    tabs.forEach(tab => {
        const tabBtnName = tab.getAttribute('data-tab');
        
        // Remove all active classes
        tab.classList.remove('text-blue-600', 'border-blue-600');
        tab.classList.add('text-gray-500', 'border-transparent');
        
        // Add active class to selected tab
        if (tabBtnName === tabName) {
            tab.classList.remove('text-gray-500', 'border-transparent');
            tab.classList.add('text-blue-600', 'border-blue-600');
        }
    });
    
    // Update tab content visibility
    const tabContents = document.querySelectorAll('.tab-content');
    console.log('Found tab contents:', tabContents.length);
    
    tabContents.forEach(content => {
        content.style.display = 'none';
    });
    
    const activeContent = document.getElementById(`${tabName}-tab`);
    if (activeContent) {
        console.log('Showing content for:', tabName);
        activeContent.style.display = 'block';
        
        // Load tab-specific data when tab is activated
        setTimeout(() => {
            loadTabData(tabName);
        }, 100);
    } else {
        console.error('Content not found for tab:', tabName);
    }
}

function loadTabData(tabName) {
    console.log('Loading data for tab:', tabName);
    
    switch(tabName) {
        case 'attendance':
            if (typeof window.loadAttendance === 'function') {
                window.loadAttendance();
            } else {
                console.warn('loadAttendance function not found');
            }
            break;
        case 'permission':
            if (typeof window.loadPermissions === 'function') {
                window.loadPermissions();
            } else {
                console.warn('loadPermissions function not found');
            }
            break;
        case 'discipline-records':
            if (typeof window.loadDisciplineRecords === 'function') {
                window.loadDisciplineRecords();
            } else {
                console.warn('loadDisciplineRecords function not found');
            }
            break;
        case 'action-plans':
            if (typeof window.loadActionPlans === 'function') {
                window.loadActionPlans();
            } else {
                console.warn('loadActionPlans function not found');
            }
            break;
        case 'reports':
            if (typeof window.loadReports === 'function') {
                window.loadReports();
            } else {
                console.warn('loadReports function not found');
            }
            break;
        default:
            // overview tab - no additional data needed
            console.log('Overview tab - no data to load');
            break;
    }
}

// Load functions for each tab
window.loadAttendance = function() {
    console.log('Loading attendance data...');
    
    const startDate = document.getElementById('attendance_start_date')?.value || '';
    const endDate = document.getElementById('attendance_end_date')?.value || '';
    const sessionType = document.getElementById('attendance_session_filter')?.value || '';
    const userId = document.getElementById('attendance_user_filter')?.value || '';
    
    let url = '/discipline/attendance';
    const params = new URLSearchParams();
    if (startDate) params.append('start_date', startDate);
    if (endDate) params.append('end_date', endDate);
    if (sessionType) params.append('session_type', sessionType);
    if (userId) params.append('user_id', userId);
    
    if (params.toString()) {
        url += '?' + params.toString();
    }
    
    fetch(url, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(data => {
        console.log('Attendance data loaded:', data);
        if (data.success) {
            if (typeof window.updateAttendanceTable === 'function') {
                window.updateAttendanceTable(data.attendances);
            }
            if (typeof window.updateAttendanceStats === 'function') {
                window.updateAttendanceStats(data.attendances);
            }
            if (typeof window.updateSessionFilter === 'function' && data.session_types) {
                window.updateSessionFilter(data.session_types);
            }
        } else {
            console.error('Error loading attendance:', data.message);
        }
    })
    .catch(error => console.error('Error loading attendance:', error));
};

window.loadPermissions = function() {
    console.log('Loading permissions data...');
    
    const status = document.getElementById('permission_status_filter')?.value || 'all';
    const userId = document.getElementById('permission_user_filter')?.value || '';
    
    let url = '/discipline/permission';
    const params = new URLSearchParams();
    if (status !== 'all') params.append('status', status);
    if (userId) params.append('user_id', userId);
    
    if (params.toString()) {
        url += '?' + params.toString();
    }
    
    fetch(url, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(data => {
        console.log('Permissions data loaded:', data);
        if (data.success && typeof window.updatePermissionTable === 'function') {
            window.updatePermissionTable(data.permissions);
        } else if (!data.success) {
            console.error('Error loading permissions:', data.message);
        }
    })
    .catch(error => console.error('Error loading permissions:', error));
};

window.loadDisciplineRecords = function() {
    console.log('Loading discipline records...');
    
    const type = document.getElementById('discipline_type_filter')?.value || 'all';
    const userId = document.getElementById('discipline_user_filter')?.value || '';
    const status = document.getElementById('discipline_status_filter')?.value || 'all';
    
    let url = '/discipline/records';
    const params = new URLSearchParams();
    if (type !== 'all') params.append('type', type);
    if (userId) params.append('user_id', userId);
    if (status !== 'all') params.append('status', status);
    
    if (params.toString()) {
        url += '?' + params.toString();
    }
    
    fetch(url, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(data => {
        console.log('Discipline records loaded:', data);
        if (data.success && typeof window.updateDisciplineTable === 'function') {
            window.updateDisciplineTable(data.records);
        } else if (!data.success) {
            console.error('Error loading discipline records:', data.message);
        }
    })
    .catch(error => console.error('Error loading discipline records:', error));
};

window.loadActionPlans = function() {
    console.log('Loading action plans...');
    
    const status = document.getElementById('action_plan_status_filter')?.value || 'all';
    const userId = document.getElementById('action_plan_user_filter')?.value || '';
    
    let url = '/discipline/action-plans';
    const params = new URLSearchParams();
    if (status !== 'all') params.append('status', status);
    if (userId) params.append('user_id', userId);
    
    if (params.toString()) {
        url += '?' + params.toString();
    }
    
    fetch(url, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(data => {
        console.log('Action plans loaded:', data);
        if (data.success && typeof window.updateActionPlansList === 'function') {
            window.updateActionPlansList(data.action_plans);
        } else if (!data.success) {
            console.error('Error loading action plans:', data.message);
            const container = document.getElementById('action-plans-list');
            if (container) {
                container.innerHTML = `
                    <div class="text-center py-8 text-red-500">
                        <i class="fas fa-exclamation-circle text-2xl mb-2"></i>
                        <p>Error loading action plans: ${data.message || 'Unknown error'}</p>
                    </div>
                `;
            }
        }
    })
    .catch(error => {
        console.error('Error loading action plans:', error);
        const container = document.getElementById('action-plans-list');
        if (container) {
            container.innerHTML = `
                <div class="text-center py-8 text-red-500">
                    <i class="fas fa-exclamation-circle text-2xl mb-2"></i>
                    <p>Error loading action plans. Please refresh the page.</p>
                </div>
            `;
        }
    });
};

window.loadReports = function() {
    console.log('Loading reports...');
    if (typeof window.loadReportsData === 'function') {
        window.loadReportsData();
    }
};

// Generic helper functions
window.getStatusBadge = function(status) {
    const badges = {
        'present': 'bg-green-100 text-green-700',
        'absent': 'bg-red-100 text-red-700',
        'late': 'bg-yellow-100 text-yellow-700',
        'excused': 'bg-blue-100 text-blue-700',
        'pending': 'bg-yellow-100 text-yellow-700',
        'approved': 'bg-green-100 text-green-700',
        'rejected': 'bg-red-100 text-red-700',
        'active': 'bg-red-100 text-red-700',
        'resolved': 'bg-green-100 text-green-700',
        'in_progress': 'bg-blue-100 text-blue-700',
        'completed': 'bg-green-100 text-green-700'
    };
    return badges[status] || 'bg-gray-100 text-gray-700';
};

window.closeModal = function(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('hidden');
    }
};

window.escapeHtml = function(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
};

// Close modal when clicking outside
window.onclick = function(event) {
    if (event.target.classList && event.target.classList.contains('modal')) {
        event.target.classList.add('hidden');
    }
};

// Update the updateAttendanceTable function to handle grouped sessions
window.updateAttendanceTable = function(attendances) {
    console.log('Updating attendance table with:', attendances?.length, 'records');
    
    const tbody = document.getElementById('attendance-table-body');
    if (!tbody) {
        console.warn('Attendance table body not found');
        return;
    }
    
    if (!attendances || attendances.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center py-8 text-gray-500">No attendance records found</td></tr>';
        return;
    }
    
    // Group by session date and type
    const groupedSessions = {};
    attendances.forEach(att => {
        const key = `${att.session_date}_${att.session_type}`;
        if (!groupedSessions[key]) {
            groupedSessions[key] = {
                id: att.id,
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
        if (att.status === 'absent') groupedSessions[key].absent++;
        if (att.status === 'late') groupedSessions[key].late++;
        if (att.status === 'excused') groupedSessions[key].excused++;
        groupedSessions[key].total++;
    });
    
    tbody.innerHTML = Object.values(groupedSessions).map(session => {
        const attendanceRate = session.total > 0 ? ((session.present + session.late) / session.total * 100).toFixed(1) : 0;
        const status = attendanceRate >= 75 ? 'Completed' : (attendanceRate >= 50 ? 'Partial' : 'Low');
        const statusColor = attendanceRate >= 75 ? 'text-green-600 bg-green-50' : (attendanceRate >= 50 ? 'text-yellow-600 bg-yellow-50' : 'text-red-600 bg-red-50');
        
        return `
            <tr class="border-b hover:bg-gray-50">
                <td class="px-6 py-4 text-sm text-gray-600">${session.date}</td>
                <td class="px-6 py-4 text-sm font-medium text-gray-800">${window.escapeHtml(session.session)}</td>
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
                        <button onclick="window.viewSessionDetails('${session.date}', '${window.escapeHtml(session.session)}')" class="text-blue-500 hover:text-blue-700">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button onclick="window.deleteSession('${session.date}', '${window.escapeHtml(session.session)}')" class="text-red-500 hover:text-red-700">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }).join('');
};

window.updateAttendanceStats = function(attendances) {
    if (!attendances) return;
    
    const total = attendances.length;
    const present = attendances.filter(a => a.status === 'present').length;
    const absent = attendances.filter(a => a.status === 'absent').length;
    const late = attendances.filter(a => a.status === 'late').length;
    const excused = attendances.filter(a => a.status === 'excused').length;
    
    // Calculate average attendance rate
    const presentRate = total > 0 ? ((present + late) / total * 100).toFixed(1) : 0;
    
    const totalSessionsEl = document.getElementById('total_sessions');
    const avgAttendanceEl = document.getElementById('avg_attendance');
    const presentCountEl = document.getElementById('present_count');
    const absentCountEl = document.getElementById('absent_count');
    const lateCountEl = document.getElementById('late_count');
    const excusedCountEl = document.getElementById('excused_count');
    
    if (totalSessionsEl) totalSessionsEl.textContent = total;
    if (avgAttendanceEl) avgAttendanceEl.textContent = `${presentRate}%`;
    if (presentCountEl) presentCountEl.textContent = present;
    if (absentCountEl) absentCountEl.textContent = absent;
    if (lateCountEl) lateCountEl.textContent = late;
    if (excusedCountEl) excusedCountEl.textContent = excused;
};

window.updateSessionFilter = function(sessionTypes) {
    const filterSelect = document.getElementById('attendance_session_filter');
    if (!filterSelect) return;
    
    const currentValue = filterSelect.value;
    filterSelect.innerHTML = '<option value="">All Sessions</option>';
    if (sessionTypes && sessionTypes.length) {
        sessionTypes.forEach(type => {
            filterSelect.innerHTML += `<option value="${window.escapeHtml(type.session_type)}">${window.escapeHtml(type.session_type)}</option>`;
        });
    }
    filterSelect.value = currentValue;
};

window.viewSessionDetails = function(date, sessionType) {
    window.location.href = `/discipline/attendance/session?date=${date}&type=${encodeURIComponent(sessionType)}`;
};

window.deleteSession = function(date, sessionType) {
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
                window.loadAttendance();
            } else {
                alert('Error deleting session records: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting session records');
        });
    }
};
</script>
@endsection