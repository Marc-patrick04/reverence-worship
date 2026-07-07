<div id="sessionDetailsModal" class="modal hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative mx-auto flex w-full max-w-6xl max-h-[92vh] flex-col overflow-hidden rounded-2xl border bg-white shadow-2xl">
        <div class="sticky top-0 z-10 flex justify-between items-start gap-3 border-b bg-white px-4 py-3 sm:px-5">
            <div>
                <h3 id="session_modal_title" class="text-lg font-bold text-gray-800">Session Details</h3>
                <p id="session_info" class="text-sm text-gray-500 mt-1"></p>
            </div>
            <button onclick="closeModal('sessionDetailsModal')" class="rounded-full bg-gray-100 px-3 py-2 text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <div class="overflow-y-auto px-4 py-4 sm:px-5">
        
        <div id="session_completed_warning" class="hidden mb-4 p-3 bg-yellow-100 text-yellow-700 rounded-lg">
            <i class="fas fa-exclamation-triangle mr-2"></i> 
            This session is completed and cannot be edited.
        </div>
        
        <!-- Stats Summary -->
        <div class="grid grid-cols-2 gap-3 mb-4">
            <div class="bg-blue-50 rounded-xl p-3 sm:p-4">
                <p class="text-sm text-gray-600">Total Users</p>
                <p id="total_users" class="text-xl sm:text-2xl font-bold text-blue-600">0</p>
            </div>
            <div class="bg-green-50 rounded-xl p-3 sm:p-4">
                <p class="text-sm text-gray-600">Approved Permissions</p>
                <p id="approved_permissions" class="text-xl sm:text-2xl font-bold text-green-600">0</p>
            </div>
        </div>
        
        <!-- User Search + Export -->
        <div class="mb-3 flex flex-col sm:flex-row sm:items-end gap-3">
            <div class="relative w-full sm:max-w-sm">
                <label for="session_user_search" class="sr-only">Search users</label>
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input id="session_user_search" type="search" placeholder="Search user..."
                       oninput="window.filterSessionUsers && window.filterSessionUsers()"
                       class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
            <button type="button" onclick="exportSessionAttendance()"
                    class="inline-flex w-full sm:w-auto items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm transition shadow-sm">
                <i class="fas fa-file-export"></i>
                Export
            </button>
        </div>

        <!-- Members Table -->
        <div class="hidden md:block overflow-x-auto rounded-xl border border-gray-100">
            <table class="w-full">
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
        <div id="session_members_cards" class="md:hidden max-h-[52vh] overflow-auto rounded-xl border border-gray-100 bg-white"></div>
        </div>
        
        <!-- Action Buttons -->
        <div class="sticky bottom-0 flex flex-col-reverse sm:flex-row sm:justify-end gap-3 border-t bg-white px-4 py-3 sm:px-5">
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
        disciplineAlert('Modal not found. Please refresh the page.');
        return;
    }
    
    // Decode the session type if it was encoded
    const decodedSessionType = decodeURIComponent(sessionType);
    
    modal.classList.remove('hidden');
    document.getElementById('session_modal_title').textContent = 'Mark Attendance';
    document.getElementById('session_info').innerHTML = `<strong>Session for:</strong> ${date} - ${escapeHtml(decodedSessionType)}`;
    const searchInput = document.getElementById('session_user_search');
    if (searchInput) {
        searchInput.value = '';
    }
    
    document.getElementById('session_members_body').innerHTML = `
        <tr>
            <td colspan="7" class="text-center py-12 text-gray-500">
                <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                <p>Loading attendance data...</p>
            </td>
        </tr>
    `;
    const mobileMembers = document.getElementById('session_members_cards');
    if (mobileMembers) {
        mobileMembers.innerHTML = `
            <div class="p-8 text-center text-gray-500">
                <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                <p>Loading attendance data...</p>
            </div>
        `;
    }
    
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
                document.getElementById('save_session_btn').classList.add('hidden');
            } else {
                document.getElementById('session_completed_warning').classList.add('hidden');
                document.getElementById('complete_session_btn').classList.remove('hidden');
                document.getElementById('save_session_btn').classList.remove('hidden');
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
            if (mobileMembers) {
                mobileMembers.innerHTML = `<div class="p-8 text-center text-red-500">Error: ${data.message || 'Failed to load session data'}</div>`;
            }
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
        if (mobileMembers) {
            mobileMembers.innerHTML = '<div class="p-8 text-center text-red-500">Error loading session details. Please try again.</div>';
        }
    });
}
    
    function renderMembersTable(members, isCompleted) {
        const tbody = document.getElementById('session_members_body');
        const cards = document.getElementById('session_members_cards');
        if (!tbody) return;
        
        if (!members || members.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" class="text-center py-12 text-gray-500">No members found</td></tr>';
            if (cards) {
            cards.innerHTML = '<div class="p-8 text-center text-gray-500">No members found</div>';
            }
            return;
        }
        
        tbody.innerHTML = members.map(member => {
            const permissionText = member.has_permission ? (member.permission_reason || 'Approved') : 'No approved permission';
            const permissionClass = member.has_permission ? 'text-yellow-600' : 'text-gray-400';
            const searchableName = String(member.user_name || '').toLowerCase();
            
            if (isCompleted) {
                return `
                    <tr class="border-b hover:bg-gray-50" data-user-id="${member.user_id}" data-user-name="${escapeHtml(searchableName)}">
                        <td class="px-4 py-3 text-sm text-gray-800">${escapeHtml(member.user_name)}</td>
                        <td class="px-4 py-3 text-sm ${permissionClass}">${escapeHtml(permissionText)}</td>
                        <td class="px-4 py-3 text-center">${member.present ? 'Yes' : 'No'}</td>
                        <td class="px-4 py-3 text-center">${member.on_time ? 'Yes' : 'No'}</td>
                        <td class="px-4 py-3 text-center">${member.communicated ? 'Yes' : 'No'}</td>
                        <td class="px-4 py-3 text-center">${member.discipline ? 'Yes' : 'No'}</td>
                        <td class="px-4 py-3 text-center font-bold">${member.total_points}</td>
                    </tr>
                `;
            } else {
                return `
                    <tr class="border-b hover:bg-gray-50" data-user-id="${member.user_id}" data-user-name="${escapeHtml(searchableName)}">
                        <td class="px-4 py-3 text-sm text-gray-800">${escapeHtml(member.user_name)}</td>
                        <td class="px-4 py-3 text-sm ${permissionClass}">${escapeHtml(permissionText)}</td>
                        <td class="px-4 py-3 text-center">
                            ${renderYesNoToggle('present', member.user_id, member.permission?.status === 'approved' ? false : (member.has_attendance ? member.present : true))}
                        </td>
                        <td class="px-4 py-3 text-center">
                            ${renderYesNoToggle('ontime', member.user_id, member.has_attendance ? member.on_time : true)}
                        </td>
                        <td class="px-4 py-3 text-center">
                            ${renderYesNoToggle('communicated', member.user_id, member.has_attendance ? member.communicated : true)}
                        </td>
                        <td class="px-4 py-3 text-center">
                            ${renderYesNoToggle('discipline', member.user_id, member.has_attendance ? member.discipline : true)}
                        </td>
                        <td class="px-4 py-3 text-center font-bold points-display-${member.user_id}">${member.total_points}</td>
                    </tr>
                `;
            }
        }).join('') + `
            <tr id="session_user_search_empty" class="hidden">
                <td colspan="7" class="text-center py-8 text-sm text-gray-500">No users match your search.</td>
            </tr>
        `;

        if (cards) {
            const mobileRows = members.map(member => {
                const permissionText = member.has_permission ? (member.permission_reason || 'Approved') : 'No approved permission';
                const searchableName = String(member.user_name || '').toLowerCase();
                const values = {
                    present: member.permission?.status === 'approved' ? false : (member.has_attendance ? member.present : true),
                    ontime: member.has_attendance ? member.on_time : true,
                    communicated: member.has_attendance ? member.communicated : true,
                    discipline: member.has_attendance ? member.discipline : true
                };

                return `
                    <tr class="session-member-card border-b border-gray-100 last:border-b-0" data-user-id="${member.user_id}" data-user-name="${escapeHtml(searchableName)}">
                        <td class="min-w-[118px] max-w-[140px] px-2 py-2 align-middle">
                            <div class="truncate text-[13px] font-semibold text-gray-900">${escapeHtml(member.user_name)}</div>
                            ${member.has_permission ? `<div class="truncate text-[10px] text-yellow-700" title="${escapeHtml(permissionText)}">Permission</div>` : ''}
                        </td>
                        ${isCompleted ? `
                            <td class="px-1 py-2 text-center">${renderReadonlyMiniValue(member.present)}</td>
                            <td class="px-1 py-2 text-center">${renderReadonlyMiniValue(member.on_time)}</td>
                            <td class="px-1 py-2 text-center">${renderReadonlyMiniValue(member.communicated)}</td>
                            <td class="px-1 py-2 text-center">${renderReadonlyMiniValue(member.discipline)}</td>
                        ` : `
                            <td class="px-1 py-2 text-center">${renderMobileMiniToggle('present', member.user_id, values.present)}</td>
                            <td class="px-1 py-2 text-center">${renderMobileMiniToggle('ontime', member.user_id, values.ontime)}</td>
                            <td class="px-1 py-2 text-center">${renderMobileMiniToggle('communicated', member.user_id, values.communicated)}</td>
                            <td class="px-1 py-2 text-center">${renderMobileMiniToggle('discipline', member.user_id, values.discipline)}</td>
                        `}
                        <td class="points-card-${member.user_id} px-2 py-2 text-center text-xs font-bold text-blue-700">${member.total_points}</td>
                    </tr>
                `;
            }).join('');

            cards.innerHTML = `
                <table class="w-full min-w-[420px] text-xs">
                    <thead class="sticky top-0 z-10 bg-gray-50 text-[10px] uppercase tracking-wide text-gray-500 shadow-sm">
                        <tr>
                            <th class="px-2 py-2 text-left font-semibold">Name</th>
                            <th class="px-1 py-2 text-center font-semibold" title="Present">Present</th>
                            <th class="px-1 py-2 text-center font-semibold" title="On time">Time</th>
                            <th class="px-1 py-2 text-center font-semibold" title="Communicated">Comm.</th>
                            <th class="px-1 py-2 text-center font-semibold" title="Discipline">Disc.</th>
                            <th class="px-2 py-2 text-center font-semibold">Pts</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${mobileRows}
                        <tr id="session_user_search_empty_mobile" class="hidden">
                            <td colspan="6" class="px-3 py-6 text-center text-sm text-gray-500">No users match your search.</td>
                        </tr>
                    </tbody>
                </table>
            `;
        }
        
        if (!isCompleted) {
            document.querySelectorAll('#session_members_body .attendance-toggle').forEach(button => {
                button.addEventListener('click', function() {
                    if (this.disabled) return;
                    const currentValue = this.dataset.value === 'true';
                    setToggleValue(this.closest('tr'), this.dataset.field, !currentValue);
                    window.updateMemberPoints(this.dataset.userId);
                });
            });
        }

        applyUserSearch();
    }

    function renderReadonlyMobileField(label, value) {
        return `
            <div class="rounded-lg ${value ? 'bg-green-50 text-green-700' : 'bg-gray-50 text-gray-500'} p-2">
                <span class="block text-xs">${label}</span>
                <strong>${value ? 'Yes' : 'No'}</strong>
            </div>
        `;
    }

    function renderReadonlyMiniValue(value) {
        return `<span class="inline-flex h-7 w-7 items-center justify-center rounded-md text-[11px] font-bold ${value ? 'bg-blue-50 text-blue-700' : 'bg-gray-100 text-gray-500'}">${value ? 'Y' : 'N'}</span>`;
    }

    function renderMobileMiniToggle(field, userId, value) {
        return `
            <button type="button"
                onclick="window.setAttendanceMobileValue(${userId}, '${field}', ${value ? 'false' : 'true'})"
                data-mobile-field="${field}"
                data-mobile-user-id="${userId}"
                data-mobile-value="${value ? 'true' : 'false'}"
                class="mobile-attendance-toggle inline-flex h-8 w-8 items-center justify-center rounded-md text-[11px] font-bold ${value ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-500'}">
                ${value ? 'Y' : 'N'}
            </button>
        `;
    }

    function renderMobileToggle(label, field, userId, value) {
        return `
            <div class="flex items-center justify-between gap-3 rounded-lg bg-gray-50 p-2">
                <span class="text-sm font-medium text-gray-700">${label}</span>
                <div class="flex rounded-lg border border-gray-200 bg-white p-1">
                    <button type="button" onclick="window.setAttendanceMobileValue(${userId}, '${field}', true)" data-mobile-field="${field}" data-mobile-user-id="${userId}" data-mobile-value="true" class="mobile-attendance-toggle rounded-md px-3 py-1 text-xs font-semibold ${value ? 'bg-blue-600 text-white' : 'text-gray-500'}">Yes</button>
                    <button type="button" onclick="window.setAttendanceMobileValue(${userId}, '${field}', false)" data-mobile-field="${field}" data-mobile-user-id="${userId}" data-mobile-value="false" class="mobile-attendance-toggle rounded-md px-3 py-1 text-xs font-semibold ${!value ? 'bg-gray-700 text-white' : 'text-gray-500'}">No</button>
                </div>
            </div>
        `;
    }

    function renderYesNoToggle(field, userId, value, disabled = false) {
        const activeClass = value ? 'bg-blue-600 text-white border-blue-600' : 'bg-gray-700 text-white border-gray-700';
        const disabledAttrs = disabled ? 'disabled aria-disabled="true"' : '';
        const disabledClass = disabled ? ' opacity-50 cursor-not-allowed' : '';
        const label = value ? 'Yes' : 'No';

        return `
            <button type="button" class="attendance-toggle ${field}-toggle inline-flex items-center justify-center px-3 py-1 text-xs font-semibold border rounded-md ${activeClass}${disabledClass}" data-field="${field}" data-user-id="${userId}" data-value="${value ? 'true' : 'false'}" ${disabledAttrs}>${label}</button>
        `;
    }

    function setToggleValue(row, field, value) {
        const toggle = row.querySelector(`[data-field="${field}"][data-value]`);
        if (!toggle) return;

        toggle.dataset.value = value ? 'true' : 'false';
        toggle.textContent = value ? 'Yes' : 'No';
        toggle.className = [
            'attendance-toggle',
            `${field}-toggle`,
            'inline-flex items-center justify-center px-3 py-1 text-xs font-semibold border rounded-md',
            value ? 'bg-blue-600 text-white border-blue-600' : 'bg-gray-700 text-white border-gray-700',
            toggle.disabled ? 'opacity-50 cursor-not-allowed' : ''
        ].join(' ');

        syncMobileToggle(row.getAttribute('data-user-id'), field, value);
    }

    function syncMobileToggle(userId, field, value) {
        document.querySelectorAll(`[data-mobile-user-id="${userId}"][data-mobile-field="${field}"]`).forEach(button => {
            button.dataset.mobileValue = value ? 'true' : 'false';
            button.textContent = value ? 'Y' : 'N';
            button.setAttribute('onclick', `window.setAttendanceMobileValue(${userId}, '${field}', ${value ? 'false' : 'true'})`);
            button.classList.remove('bg-blue-600', 'bg-gray-100', 'text-white', 'text-gray-500');
            button.classList.add(value ? 'bg-blue-600' : 'bg-gray-100', value ? 'text-white' : 'text-gray-500');
        });
    }

    function setAttendanceMobileValue(userId, field, value) {
        const row = document.querySelector(`#session_members_body tr[data-user-id="${userId}"]`);
        if (!row) return;
        setToggleValue(row, field, value);
        window.updateMemberPoints(userId);
    }

    function getToggleValue(row, field) {
        return row.querySelector(`[data-field="${field}"][data-value]`)?.dataset.value === 'true';
    }

    function applyUserSearch() {
        const searchInput = document.getElementById('session_user_search');
        const query = (searchInput?.value || '').trim().toLowerCase();
        const rows = document.querySelectorAll('#session_members_body tr[data-user-name]');
        const cards = document.querySelectorAll('#session_members_cards .session-member-card');
        let visibleCount = 0;

        rows.forEach(row => {
            const matches = !query || row.dataset.userName.includes(query);
            row.classList.toggle('hidden', !matches);
            if (matches) visibleCount++;
        });

        cards.forEach(card => {
            const matches = !query || (card.dataset.userName || '').includes(query);
            card.classList.toggle('hidden', !matches);
        });

        const noResultsRow = document.getElementById('session_user_search_empty');
        if (noResultsRow) {
            noResultsRow.classList.toggle('hidden', visibleCount > 0 || rows.length === 0);
        }

        const noResultsCard = document.getElementById('session_user_search_empty_mobile');
        if (noResultsCard) {
            noResultsCard.classList.toggle('hidden', visibleCount > 0 || rows.length === 0);
        }
    }
    
    function updateMemberPoints(userId) {
        const row = document.querySelector(`#session_members_body tr[data-user-id="${userId}"]`);
        if (!row) return;
        
        const present = getToggleValue(row, 'present');
        const onTime = getToggleValue(row, 'ontime');
        const communicated = getToggleValue(row, 'communicated');
        const discipline = getToggleValue(row, 'discipline');
        
        let points = 0;
        if (present) points++;
        if (onTime) points++;
        if (communicated) points++;
        if (discipline) points++;
        
        const pointsDisplay = document.querySelector(`.points-display-${userId}`);
        if (pointsDisplay) pointsDisplay.textContent = points;

        const mobilePointsDisplay = document.querySelector(`.points-card-${userId}`);
        if (mobilePointsDisplay) mobilePointsDisplay.textContent = points;
    }

    function exportSessionAttendance() {
        const tableRows = document.querySelectorAll('#session_members_body tr[data-user-id]');
        if (!tableRows.length) {
            disciplineAlert('No session data to export');
            return;
        }
        const rows = [];

        tableRows.forEach((row, index) => {
            const hasLiveToggles = !!row.querySelector('[data-field="present"][data-value]');
            const cells = row.querySelectorAll('td');
            const name = (row.dataset.userName || cells[0]?.textContent || '').trim();
            const permissionStatus = (cells[1]?.textContent || 'No approved permission').trim();
            const presence = hasLiveToggles ? (getToggleValue(row, 'present') ? 1 : 0) : ((cells[2]?.textContent || '').trim().toLowerCase() === 'yes' ? 1 : 0);
            const timeliness = hasLiveToggles ? (getToggleValue(row, 'ontime') ? 1 : 0) : ((cells[3]?.textContent || '').trim().toLowerCase() === 'yes' ? 1 : 0);
            const communication = hasLiveToggles ? (getToggleValue(row, 'communicated') ? 1 : 0) : ((cells[4]?.textContent || '').trim().toLowerCase() === 'yes' ? 1 : 0);
            const discipline = hasLiveToggles ? (getToggleValue(row, 'discipline') ? 1 : 0) : ((cells[5]?.textContent || '').trim().toLowerCase() === 'yes' ? 1 : 0);
            const totalPointsCell = cells[6]?.textContent || '';
            const totalPoints = hasLiveToggles ? (presence + timeliness + communication + discipline) : parseInt(totalPointsCell, 10) || 0;

            rows.push({
                'No': index + 1,
                'Names': name || 'N/A',
                'Permission Status': permissionStatus,
                'Points of Presence': presence,
                'Timeliness': timeliness,
                'Communication': communication,
                'Discipline': discipline,
                'Total Points': totalPoints
            });
        });

        if (rows.length === 0) {
            disciplineAlert('No session data to export');
            return;
        }

        const headers = ['No', 'Names', 'Permission Status', 'Points of Presence', 'Timeliness', 'Communication', 'Discipline', 'Total Points'];
        const csvLines = [
            headers.join(','),
            ...rows.map(row => headers.map(header => escapeCsvValue(row[header])).join(','))
        ];

        const blob = new Blob([csvLines.join('\n')], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        const sessionInfo = document.getElementById('session_info')?.textContent?.trim() || 'attendance';
        const sessionLabel = currentSessionData?.date
            ? `${currentSessionData.date}_${String(currentSessionData.session_type || 'attendance').replace(/[^a-zA-Z0-9]+/g, '_')}`
            : sessionInfo.replace(/[^a-zA-Z0-9]+/g, '_') || 'attendance';

        link.href = url;
        link.download = `attendance_session_${sessionLabel}.csv`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);
    }
    
    function saveSessionChanges() {
        if (!currentSessionData) {
            disciplineAlert('No session data to save');
            return;
        }
        
        const records = [];
        const rows = document.querySelectorAll('#session_members_body tr[data-user-id]');
        
        rows.forEach(row => {
            const userId = row.getAttribute('data-user-id');
            const present = getToggleValue(row, 'present');
            const onTime = getToggleValue(row, 'ontime');
            const communicated = getToggleValue(row, 'communicated');
            const discipline = getToggleValue(row, 'discipline');
            
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
                on_time: onTime,
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
                disciplineAlert('Attendance records saved successfully!');
                openSessionDetailsModal(currentSessionData.date, currentSessionData.session_type);
                if (typeof window.loadAttendanceData === 'function') {
                    window.loadAttendanceData();
                }
            } else {
                disciplineAlert('Error: ' + (data.message || 'Failed to save records'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            disciplineAlert('Error saving attendance records');
        })
        .finally(() => {
            saveBtn.innerHTML = originalText;
            saveBtn.disabled = false;
        });
    }
    
    async function completeSession() {
        if (!currentSessionData) return;
        
        if (await disciplineConfirm('Complete this session? This will lock all records.', 'Complete session', 'Complete', 'Cancel', 'danger')) {
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
                    disciplineAlert('Session completed successfully!');
                    openSessionDetailsModal(currentSessionData.date, currentSessionData.session_type);
                    if (typeof window.loadAttendanceData === 'function') {
                        window.loadAttendanceData();
                    }
                } else {
                    disciplineAlert('Error: ' + (data.message || 'Failed to complete session'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                disciplineAlert('Error completing session');
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

    function escapeCsvValue(value) {
        const text = value === null || value === undefined ? '' : String(value);
        return `"${text.replace(/"/g, '""')}"`;
    }
    
    // Expose functions globally
    window.openSessionDetailsModal = openSessionDetailsModal;
    window.closeSessionModal = closeSessionModal;
    window.saveSessionChanges = saveSessionChanges;
    window.completeSession = completeSession;
    window.updateMemberPoints = updateMemberPoints;
    window.exportSessionAttendance = exportSessionAttendance;
    window.filterSessionUsers = applyUserSearch;
    window.setAttendanceMobileValue = setAttendanceMobileValue;
})();
</script>
