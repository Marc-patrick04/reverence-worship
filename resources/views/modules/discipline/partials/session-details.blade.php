<div id="sessionDetailsModal" class="modal hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-5 border w-full max-w-7xl shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center pb-3 border-b">
            <div>
                <h3 id="session_modal_title" class="text-lg font-bold text-gray-800">Session Details</h3>
                <p id="session_info" class="text-sm text-gray-500 mt-1"></p>
            </div>
            <button onclick="closeModal('sessionDetailsModal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <div id="session_completed_warning" class="hidden mb-4 p-3 bg-yellow-100 text-yellow-700 rounded-lg">
            <i class="fas fa-exclamation-triangle mr-2"></i> 
            This session is completed and cannot be edited.
        </div>
        
        <!-- Stats Summary -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6 mt-4">
            <div class="bg-blue-50 rounded-lg p-4">
                <p class="text-sm text-gray-600">Total Users</p>
                <p id="total_users" class="text-2xl font-bold text-blue-600">0</p>
            </div>
            <div class="bg-green-50 rounded-lg p-4">
                <p class="text-sm text-gray-600">Approved Permissions for this session date</p>
                <p id="approved_permissions" class="text-2xl font-bold text-green-600">0</p>
            </div>
        </div>
        
        <!-- Members Table -->
        <div class="overflow-x-auto">
            <table class="w-full border">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">USER</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">PERMISSION</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">PRESENT</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">ON TIME</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">COMMUNICATED</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">DISCIPLINE</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">TOTAL POINTS</th>
                    </tr>
                </thead>
                <tbody id="session_members_body">
                    <tr>
                        <td colspan="7" class="text-center py-12 text-gray-500">
                            <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                            <p>Loading...</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- Action Buttons -->
        <div class="flex justify-end gap-3 mt-6 pt-4 border-t">
            <button type="button" onclick="closeModal('sessionDetailsModal')" class="px-4 py-2 border rounded-lg text-sm hover:bg-gray-50">
                Close
            </button>
            <button id="complete_session_btn" onclick="completeSession()" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm hover:bg-green-700 hidden">
                Complete Session
            </button>
            <button id="save_session_btn" onclick="saveSessionChanges()" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700">
                Save Changes
            </button>
        </div>
    </div>
</div>

<script>
let currentSessionData = null;

function viewSessionDetails(date, sessionType) {
    document.getElementById('sessionDetailsModal').classList.remove('hidden');
    document.getElementById('session_modal_title').textContent = sessionType;
    document.getElementById('session_info').textContent = `Session for: ${date}`;
    
    fetch(`/discipline/attendance/session/${date}/${encodeURIComponent(sessionType)}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            currentSessionData = data;
            
            document.getElementById('total_users').textContent = data.total_users;
            document.getElementById('approved_permissions').textContent = data.approved_permissions;
            
            if (data.is_completed) {
                document.getElementById('session_completed_warning').classList.remove('hidden');
                document.getElementById('complete_session_btn').classList.add('hidden');
                document.getElementById('save_session_btn').disabled = true;
                document.getElementById('save_session_btn').classList.add('opacity-50', 'cursor-not-allowed');
            } else {
                document.getElementById('session_completed_warning').classList.add('hidden');
                document.getElementById('complete_session_btn').classList.remove('hidden');
                document.getElementById('save_session_btn').disabled = false;
                document.getElementById('save_session_btn').classList.remove('opacity-50', 'cursor-not-allowed');
            }
            
            renderMembersTable(data.members, data.is_completed);
        }
    });
}

function renderMembersTable(members, isCompleted) {
    const tbody = document.getElementById('session_members_body');
    
    if (!members || members.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center py-12 text-gray-500">No members found</td></tr>';
        return;
    }
    
    tbody.innerHTML = members.map(member => {
        const permissionText = member.has_permission ? member.permission_reason : 'No approved permission';
        const permissionClass = member.has_permission ? 'text-yellow-600' : 'text-gray-400';
        
        if (isCompleted) {
            // Read-only display for completed sessions
            return `
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm text-gray-800">${escapeHtml(member.user_name)}</td>
                    <td class="px-4 py-3 text-sm ${permissionClass}">${escapeHtml(permissionText)}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="${member.present ? 'text-green-600' : 'text-red-600'}">
                            ${member.present ? 'Yes' : 'No'}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="${member.on_time ? 'text-green-600' : 'text-red-600'}">
                            ${member.on_time ? 'Yes' : 'No'}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="${member.communicated ? 'text-green-600' : 'text-red-600'}">
                            ${member.communicated ? 'Yes' : 'No'}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="${member.discipline ? 'text-green-600' : 'text-red-600'}">
                            ${member.discipline ? 'Yes' : 'No'}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center font-bold ${member.total_points >= 3 ? 'text-green-600' : 'text-yellow-600'}">
                        ${member.total_points}
                    </td>
                </tr>
            `;
        } else {
            // Editable display for active sessions
            return `
                <tr class="border-b hover:bg-gray-50" data-user-id="${member.user_id}">
                    <td class="px-4 py-3 text-sm text-gray-800">${escapeHtml(member.user_name)}</td>
                    <td class="px-4 py-3 text-sm ${permissionClass}">${escapeHtml(permissionText)}</td>
                    <td class="px-4 py-3 text-center">
                        <input type="checkbox" class="present-checkbox w-5 h-5" 
                               ${member.present ? 'checked' : ''}
                               onchange="updatePoints(this, '${member.user_id}')">
                    </td>
                    <td class="px-4 py-3 text-center">
                        <input type="checkbox" class="ontime-checkbox w-5 h-5" 
                               ${member.on_time ? 'checked' : ''}
                               ${!member.present ? 'disabled' : ''}
                               onchange="updatePoints(this, '${member.user_id}')">
                    </td>
                    <td class="px-4 py-3 text-center">
                        <input type="checkbox" class="communicated-checkbox w-5 h-5" 
                               ${member.communicated ? 'checked' : ''}
                               onchange="updatePoints(this, '${member.user_id}')">
                    </td>
                    <td class="px-4 py-3 text-center">
                        <input type="checkbox" class="discipline-checkbox w-5 h-5" 
                               ${member.discipline ? 'checked' : ''}
                               onchange="updatePoints(this, '${member.user_id}')">
                    </td>
                    <td class="px-4 py-3 text-center font-bold points-display-${member.user_id} ${member.total_points >= 3 ? 'text-green-600' : 'text-yellow-600'}">
                        ${member.total_points}
                    </td>
                </tr>
            `;
        }
    }).join('');
    
    // Add event listeners for checkboxes in editable mode
    if (!isCompleted) {
        document.querySelectorAll('.present-checkbox').forEach(cb => {
            cb.addEventListener('change', function() {
                const row = this.closest('tr');
                const ontimeCheckbox = row.querySelector('.ontime-checkbox');
                if (!this.checked) {
                    ontimeCheckbox.checked = false;
                    ontimeCheckbox.disabled = true;
                } else {
                    ontimeCheckbox.disabled = false;
                }
            });
        });
    }
}

function updatePoints(checkbox, userId) {
    const row = checkbox.closest('tr');
    const present = row.querySelector('.present-checkbox').checked;
    const onTime = row.querySelector('.ontime-checkbox').checked;
    const communicated = row.querySelector('.communicated-checkbox').checked;
    const discipline = row.querySelector('.discipline-checkbox').checked;
    
    let points = 0;
    if (present) points++;
    if (onTime) points++;
    if (communicated) points++;
    if (discipline) points++;
    
    const pointsDisplay = document.querySelector(`.points-display-${userId}`);
    pointsDisplay.textContent = points;
    pointsDisplay.className = `px-4 py-3 text-center font-bold points-display-${userId} ${points >= 3 ? 'text-green-600' : 'text-yellow-600'}`;
}

function saveSessionChanges() {
    if (!currentSessionData) return;
    
    const records = [];
    const rows = document.querySelectorAll('#session_members_body tr[data-user-id]');
    
    rows.forEach(row => {
        const userId = row.dataset.userId;
        const present = row.querySelector('.present-checkbox').checked;
        const onTime = row.querySelector('.ontime-checkbox').checked;
        const communicated = row.querySelector('.communicated-checkbox').checked;
        const discipline = row.querySelector('.discipline-checkbox').checked;
        
        let status = 'absent';
        if (present) {
            status = onTime ? 'present' : 'late';
        }
        
        records.push({
            user_id: userId,
            status: status,
            late_minutes: (!onTime && present) ? 15 : 0,
            communicated: communicated,
            discipline_points: discipline ? 1 : 0
        });
    });
    
    fetch('/discipline/attendance/bulk-update', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            session_date: currentSessionData.date,
            session_type: currentSessionData.session_type,
            records: records
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Changes saved successfully!');
            viewSessionDetails(currentSessionData.date, currentSessionData.session_type);
            filterAttendance();
        } else {
            alert('Error: ' + data.message);
        }
    });
}

function completeSession() {
    if (!currentSessionData) return;
    
    if (confirm('Are you sure you want to complete this session? This will lock all attendance records and prevent further edits.')) {
        fetch('/discipline/attendance/complete-session', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                session_date: currentSessionData.date,
                session_type: currentSessionData.session_type
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Session completed successfully!');
                viewSessionDetails(currentSessionData.date, currentSessionData.session_type);
                filterAttendance();
            } else {
                alert('Error: ' + data.message);
            }
        });
    }
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>