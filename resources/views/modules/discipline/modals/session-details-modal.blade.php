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
(function() {
    'use strict';
    
    var currentSessionData = null;
    
    function closeSessionModal() {
        const modal = document.getElementById('sessionDetailsModal');
        if (modal) {
            modal.classList.add('hidden');
        }
        if (typeof window.loadAttendanceData === 'function') {
            window.loadAttendanceData();
        }
    }
    
    function openSessionDetailsModal(date, sessionType) {
    const modal = document.getElementById('sessionDetailsModal');
    if (!modal) {
        console.error('Session details modal not found');
        alert('Modal not found. Please refresh the page.');
        return;
    }
    
    // Decode the session type if it was encoded
    const decodedSessionType = decodeURIComponent(sessionType);
    
    modal.classList.remove('hidden');
    document.getElementById('session_modal_title').textContent = 'Mark Attendance';
    document.getElementById('session_info').innerHTML = `<strong>Session for:</strong> ${date} - ${escapeHtml(decodedSessionType)}`;
    
    document.getElementById('session_members_body').innerHTML = `
        <tr>
            <td colspan="7" class="text-center py-12 text-gray-500">
                <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                <p>Loading attendance data...</p>
            </td>
        </tr>
    `;
    
    // Use the original encoded type for the API call
    fetch(`/discipline/attendance/session/${date}/${sessionType}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            currentSessionData = data;
            
            document.getElementById('total_users').textContent = data.total_users || 0;
            document.getElementById('approved_permissions').textContent = data.approved_permissions || 0;
            
            if (data.is_completed) {
                document.getElementById('session_completed_warning').classList.remove('hidden');
                document.getElementById('complete_session_btn').classList.add('hidden');
                document.getElementById('save_session_btn').disabled = true;
            } else {
                document.getElementById('session_completed_warning').classList.add('hidden');
                document.getElementById('complete_session_btn').classList.remove('hidden');
                document.getElementById('save_session_btn').disabled = false;
            }
            
            renderMembersTable(data.members || [], data.is_completed);
        } else {
            document.getElementById('session_members_body').innerHTML = `
                <tr>
                    <td colspan="7" class="text-center py-12 text-red-500">
                        <p>Error: ${data.message || 'Failed to load session data'}</p>
                    </td>
                </tr>
            `;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('session_members_body').innerHTML = `
            <tr>
                <td colspan="7" class="text-center py-12 text-red-500">
                    <p>Error loading session details. Please try again.</p>
                </td>
            </tr>
        `;
    });
}
    
    function renderMembersTable(members, isCompleted) {
        const tbody = document.getElementById('session_members_body');
        if (!tbody) return;
        
        if (!members || members.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" class="text-center py-12 text-gray-500">No members found</td></tr>';
            return;
        }
        
        tbody.innerHTML = members.map(member => {
            const permissionText = member.has_permission ? (member.permission_reason || 'Approved') : 'No approved permission';
            const permissionClass = member.has_permission ? 'text-yellow-600' : 'text-gray-400';
            
            if (isCompleted) {
                return `
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-800">${escapeHtml(member.user_name)}</td>
                        <td class="px-4 py-3 text-sm ${permissionClass}">${escapeHtml(permissionText)}</td>
                        <td class="px-4 py-3 text-center">${member.present ? '✓' : '✗'}</td>
                        <td class="px-4 py-3 text-center">${member.on_time ? '✓' : '✗'}</td>
                        <td class="px-4 py-3 text-center">${member.communicated ? '✓' : '✗'}</td>
                        <td class="px-4 py-3 text-center">${member.discipline ? '✓' : '✗'}</td>
                        <td class="px-4 py-3 text-center font-bold">${member.total_points}</td>
                    </tr>
                `;
            } else {
                return `
                    <tr class="border-b hover:bg-gray-50" data-user-id="${member.user_id}">
                        <td class="px-4 py-3 text-sm text-gray-800">${escapeHtml(member.user_name)}</td>
                        <td class="px-4 py-3 text-sm ${permissionClass}">${escapeHtml(permissionText)}</td>
                        <td class="px-4 py-3 text-center">
                            <input type="checkbox" class="present-checkbox" data-user-id="${member.user_id}" ${member.present ? 'checked' : ''} onchange="window.updateMemberPoints('${member.user_id}')">
                        </td>
                        <td class="px-4 py-3 text-center">
                            <input type="checkbox" class="ontime-checkbox" data-user-id="${member.user_id}" ${member.on_time ? 'checked' : ''} ${!member.present ? 'disabled' : ''} onchange="window.updateMemberPoints('${member.user_id}')">
                        </td>
                        <td class="px-4 py-3 text-center">
                            <input type="checkbox" class="communicated-checkbox" data-user-id="${member.user_id}" ${member.communicated ? 'checked' : ''} onchange="window.updateMemberPoints('${member.user_id}')">
                        </td>
                        <td class="px-4 py-3 text-center">
                            <input type="checkbox" class="discipline-checkbox" data-user-id="${member.user_id}" ${member.discipline ? 'checked' : ''} onchange="window.updateMemberPoints('${member.user_id}')">
                        </td>
                        <td class="px-4 py-3 text-center font-bold points-display-${member.user_id}">${member.total_points}</td>
                    </tr>
                `;
            }
        }).join('');
        
        if (!isCompleted) {
            document.querySelectorAll('#session_members_body .present-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', function(e) {
                    const userId = this.dataset.userId;
                    const row = this.closest('tr');
                    const ontimeCheckbox = row.querySelector('.ontime-checkbox');
                    if (!this.checked) {
                        ontimeCheckbox.checked = false;
                        ontimeCheckbox.disabled = true;
                    } else {
                        ontimeCheckbox.disabled = false;
                    }
                    window.updateMemberPoints(userId);
                });
            });
        }
    }
    
    function updateMemberPoints(userId) {
        const row = document.querySelector(`#session_members_body tr[data-user-id="${userId}"]`);
        if (!row) return;
        
        const present = row.querySelector('.present-checkbox')?.checked || false;
        const onTime = row.querySelector('.ontime-checkbox')?.checked || false;
        const communicated = row.querySelector('.communicated-checkbox')?.checked || false;
        const discipline = row.querySelector('.discipline-checkbox')?.checked || false;
        
        let points = 0;
        if (present) points++;
        if (onTime) points++;
        if (communicated) points++;
        if (discipline) points++;
        
        const pointsDisplay = document.querySelector(`.points-display-${userId}`);
        if (pointsDisplay) pointsDisplay.textContent = points;
    }
    
    function saveSessionChanges() {
        if (!currentSessionData) {
            alert('No session data to save');
            return;
        }
        
        const records = [];
        const rows = document.querySelectorAll('#session_members_body tr[data-user-id]');
        
        rows.forEach(row => {
            const userId = row.getAttribute('data-user-id');
            const present = row.querySelector('.present-checkbox')?.checked || false;
            const onTime = row.querySelector('.ontime-checkbox')?.checked || false;
            const communicated = row.querySelector('.communicated-checkbox')?.checked || false;
            const discipline = row.querySelector('.discipline-checkbox')?.checked || false;
            
            let status = 'absent';
            let lateMinutes = 0;
            
            if (present) {
                if (onTime) {
                    status = 'present';
                    lateMinutes = 0;
                } else {
                    status = 'late';
                    lateMinutes = 15;
                }
            }
            
            records.push({
                user_id: userId,
                status: status,
                late_minutes: lateMinutes,
                communicated: communicated,
                discipline_points: discipline ? 1 : 0
            });
        });
        
        const saveBtn = document.getElementById('save_session_btn');
        const originalText = saveBtn.innerHTML;
        saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Saving...';
        saveBtn.disabled = true;
        
        fetch('/discipline/attendance/bulk-update', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
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
                alert('Attendance records saved successfully!');
                openSessionDetailsModal(currentSessionData.date, currentSessionData.session_type);
                if (typeof window.loadAttendanceData === 'function') {
                    window.loadAttendanceData();
                }
            } else {
                alert('Error: ' + (data.message || 'Failed to save records'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error saving attendance records');
        })
        .finally(() => {
            saveBtn.innerHTML = originalText;
            saveBtn.disabled = false;
        });
    }
    
    function completeSession() {
        if (!currentSessionData) return;
        
        if (confirm('⚠️ Complete this session? This will lock all records.')) {
            const completeBtn = document.getElementById('complete_session_btn');
            const originalText = completeBtn.innerHTML;
            completeBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Completing...';
            completeBtn.disabled = true;
            
            fetch('/discipline/attendance/complete-session', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
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
                    closeSessionModal();
                    if (typeof window.loadAttendanceData === 'function') {
                        window.loadAttendanceData();
                    }
                } else {
                    alert('Error: ' + (data.message || 'Failed to complete session'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error completing session');
            })
            .finally(() => {
                completeBtn.innerHTML = originalText;
                completeBtn.disabled = false;
            });
        }
    }
    
    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    // Expose functions globally
    window.openSessionDetailsModal = openSessionDetailsModal;
    window.closeSessionModal = closeSessionModal;
    window.saveSessionChanges = saveSessionChanges;
    window.completeSession = completeSession;
    window.updateMemberPoints = updateMemberPoints;
})();
</script>