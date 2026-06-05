<div>
    <!-- Quick Actions Card -->
    <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl shadow-lg p-6 mb-8 text-white">
        <h4 class="text-lg font-semibold mb-4">Quick Actions</h4>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            <div>
                <label class="block text-sm font-medium mb-2 opacity-90">Select Date for Discipline Session</label>
                <input type="date" id="quick_discipline_date" value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 rounded-lg text-gray-800 focus:outline-none focus:ring-2 focus:ring-white">
            </div>
            <div>
                <label class="block text-sm font-medium mb-2 opacity-90">&nbsp;</label>
                <button onclick="quickStartDiscipline()" class="w-full bg-white text-blue-600 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-gray-100 transition">
                    <i class="fas fa-play-circle mr-2"></i> Start Discipline Session
                </button>
            </div>
        </div>
    </div>
    
    <!-- Management Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100 hover:shadow-lg transition cursor-pointer" onclick="window.location.href='/discipline/reports'">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-chart-line text-blue-600 text-xl"></i>
                </div>
                <i class="fas fa-arrow-right text-gray-300"></i>
            </div>
            <h4 class="text-lg font-bold text-gray-800 mb-1">Discipline Reports</h4>
            <p class="text-sm text-gray-500">View detailed discipline analytics</p>
        </div>
        
        <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100 hover:shadow-lg transition cursor-pointer" onclick="window.location.href='/discipline/history'">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-history text-purple-600 text-xl"></i>
                </div>
                <i class="fas fa-arrow-right text-gray-300"></i>
            </div>
            <h4 class="text-lg font-bold text-gray-800 mb-1">Discipline History</h4>
            <p class="text-sm text-gray-500">Browse historical discipline data</p>
        </div>
    </div>
    
    <!-- Filters -->
    <div class="bg-gray-50 rounded-lg p-4 mb-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                <select id="discipline_type_filter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="all">All Types</option>
                    <option value="positive">Positive</option>
                    <option value="warning">Warning</option>
                    <option value="penalty">Penalty</option>
                    <option value="suspension">Suspension</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">User</label>
                <select id="discipline_user_filter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Users</option>
                    @foreach($users ?? [] as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select id="discipline_status_filter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="all">All Status</option>
                    <option value="active">Active</option>
                    <option value="resolved">Resolved</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">&nbsp;</label>
                <button onclick="filterDisciplineRecords()" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
                    <i class="fas fa-search"></i> Apply Filter
                </button>
            </div>
        </div>
    </div>
    
    <!-- Add Record Button -->
    <div class="flex justify-end mb-4">
        <button onclick="openDisciplineModal()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm flex items-center gap-2">
            <i class="fas fa-plus"></i> Add Discipline Record
        </button>
    </div>
    
    <!-- Discipline Sessions Table -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">DATE</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">SESSION</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">GOOD BEHAVIOR</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">BAD BEHAVIOR</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">GOOD BEHAVIOR %</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">STATUS</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">ACTIONS</th>
                    </tr>
                </thead>
                <tbody id="discipline-table-body">
                    <tr>
                        <td colspan="7" class="text-center py-12 text-gray-500">
                            <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                            <p>Loading discipline sessions...</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
// Make functions available globally
window.openDisciplineModal = openDisciplineModal;
window.filterDisciplineRecords = filterDisciplineRecords;
window.resolveDiscipline = resolveDiscipline;
window.deleteDisciplineRecord = deleteDisciplineRecord;
window.quickStartDiscipline = quickStartDiscipline;

function quickStartDiscipline() {
    const sessionDate = document.getElementById('quick_discipline_date').value;
    
    if (!sessionDate) {
        alert('Please select a date');
        return;
    }
    
    // Open modal with pre-filled date
    openDisciplineModal(null, sessionDate);
}

function openDisciplineModal(recordId = null, presetDate = null) {
    if (recordId) {
        fetch(`/discipline/records/${recordId}/edit`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('discipline_modal_title').textContent = 'Edit Discipline Record';
                document.getElementById('discipline_id').value = data.record.id;
                document.getElementById('discipline_user_id').value = data.record.user_id;
                document.getElementById('discipline_section_id').value = data.record.section_id;
                document.getElementById('discipline_title').value = data.record.title;
                document.getElementById('discipline_description').value = data.record.description;
                document.getElementById('discipline_points').value = data.record.points;
                document.getElementById('discipline_type').value = data.record.type;
                document.getElementById('discipline_status').value = data.record.status;
                document.getElementById('disciplineModal').classList.remove('hidden');
            }
        });
    } else {
        document.getElementById('discipline_modal_title').textContent = 'Add Discipline Record';
        document.getElementById('discipline_id').value = '';
        document.getElementById('discipline_user_id').value = '';
        document.getElementById('discipline_section_id').value = '';
        document.getElementById('discipline_title').value = presetDate ? `Discipline Session - ${presetDate}` : '';
        document.getElementById('discipline_description').value = '';
        document.getElementById('discipline_points').value = '0';
        document.getElementById('discipline_type').value = 'positive';
        document.getElementById('discipline_status').value = 'active';
        document.getElementById('disciplineModal').classList.remove('hidden');
    }
}

function filterDisciplineRecords() {
    const type = document.getElementById('discipline_type_filter').value;
    const userId = document.getElementById('discipline_user_filter').value;
    const status = document.getElementById('discipline_status_filter').value;
    
    let url = `/discipline/records?type=${type}&user_id=${userId}&status=${status}`;
    
    fetch(url, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateDisciplineTable(data.records);
        } else {
            console.error('Error loading discipline records:', data.message);
        }
    })
    .catch(error => console.error('Error:', error));
}

function updateDisciplineTable(records) {
    const tbody = document.getElementById('discipline-table-body');
    
    if (!records || records.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center py-12 text-gray-500">
                    <i class="fas fa-inbox text-4xl mb-2 opacity-50"></i>
                    <p>No discipline sessions found</p>
                    <button onclick="openDisciplineModal()" class="mt-3 text-blue-600 hover:text-blue-700 text-sm">
                        <i class="fas fa-plus"></i> Create your first session
                    </button>
                </td>
            </tr>
        `;
        return;
    }
    
    // Group records by session date and title
    const groupedSessions = {};
    records.forEach(record => {
        const key = `${record.created_at?.split(' ')[0]}_${record.title}`;
        if (!groupedSessions[key]) {
            groupedSessions[key] = {
                id: record.id,
                date: record.created_at ? record.created_at.split('T')[0] : '',
                session_title: record.title,
                good_behavior: 0,
                bad_behavior: 0,
                status: record.status,
                records: []
            };
        }
        
        if (record.type === 'positive') {
            groupedSessions[key].good_behavior++;
        } else {
            groupedSessions[key].bad_behavior++;
        }
        groupedSessions[key].records.push(record);
    });
    
    tbody.innerHTML = Object.values(groupedSessions).map(session => {
        const total = session.good_behavior + session.bad_behavior;
        const goodPercent = total > 0 ? ((session.good_behavior / total) * 100).toFixed(1) : 100;
        const statusText = session.status === 'resolved' ? 'Completed' : 'Active';
        const statusColor = session.status === 'resolved' ? 'text-green-600 bg-green-50' : 'text-yellow-600 bg-yellow-50';
        
        return `
            <tr class="border-b hover:bg-gray-50">
                <td class="px-6 py-4 text-sm text-gray-600">${formatDate(session.date)}</td>
                <td class="px-6 py-4 text-sm font-medium text-gray-800">${escapeHtml(session.session_title)}</td>
                <td class="px-6 py-4 text-center text-sm font-semibold text-green-600">${session.good_behavior}</td>
                <td class="px-6 py-4 text-center text-sm text-red-600">${session.bad_behavior}</td>
                <td class="px-6 py-4 text-center">
                    <div class="flex items-center justify-center gap-2">
                        <span class="text-sm font-semibold ${goodPercent >= 80 ? 'text-green-600' : (goodPercent >= 60 ? 'text-yellow-600' : 'text-red-600')}">
                            ${goodPercent}%
                        </span>
                        <div class="w-16 bg-gray-200 rounded-full h-1.5">
                            <div class="h-1.5 rounded-full ${goodPercent >= 80 ? 'bg-green-500' : (goodPercent >= 60 ? 'bg-yellow-500' : 'bg-red-500')}" style="width: ${goodPercent}%"></div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 text-center">
                    <span class="px-2 py-1 rounded-full text-xs ${statusColor}">${statusText}</span>
                </td>
                <td class="px-6 py-4 text-center">
                    <div class="flex items-center justify-center gap-2">
                        <button onclick="viewSessionDetails('${session.date}', '${escapeHtml(session.session_title)}')" class="text-blue-500 hover:text-blue-700" title="View">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button onclick="deleteSessionRecords('${session.date}', '${escapeHtml(session.session_title)}')" class="text-red-500 hover:text-red-700" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }).join('');
}

function formatDate(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: '2-digit', 
        day: '2-digit' 
    }).replace(/\//g, '/');
}

function viewSessionDetails(date, sessionTitle) {
    window.location.href = `/discipline/records/session?date=${date}&title=${encodeURIComponent(sessionTitle)}`;
}

function deleteSessionRecords(date, sessionTitle) {
    if (confirm(`Delete all records for "${sessionTitle}" on ${date}?`)) {
        fetch(`/discipline/records/session?date=${date}&title=${encodeURIComponent(sessionTitle)}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                filterDisciplineRecords();
            } else {
                alert('Error deleting session records: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting session records');
        });
    }
}

function resolveDiscipline(id) {
    const notes = prompt('Enter resolution notes:');
    if (notes) {
        fetch(`/discipline/records/${id}/resolve`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ resolved_notes: notes })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                filterDisciplineRecords();
            } else {
                alert('Error resolving record: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error resolving record');
        });
    }
}

function deleteDisciplineRecord(id) {
    if (confirm('Are you sure you want to delete this discipline record?')) {
        fetch(`/discipline/records/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                filterDisciplineRecords();
            } else {
                alert('Error deleting discipline record: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting discipline record');
        });
    }
}

function getTypeBadge(type) {
    const badges = {
        'positive': 'bg-green-100 text-green-700',
        'warning': 'bg-yellow-100 text-yellow-700',
        'penalty': 'bg-red-100 text-red-700',
        'suspension': 'bg-purple-100 text-purple-700'
    };
    return badges[type] || 'bg-gray-100 text-gray-700';
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Load initial data
setTimeout(() => {
    filterDisciplineRecords();
}, 100);

// Add filter event listeners
document.getElementById('discipline_type_filter')?.addEventListener('change', filterDisciplineRecords);
document.getElementById('discipline_user_filter')?.addEventListener('change', filterDisciplineRecords);
document.getElementById('discipline_status_filter')?.addEventListener('change', filterDisciplineRecords);
</script>