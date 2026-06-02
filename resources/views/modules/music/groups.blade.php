@props(['canManage' => false, 'generations' => [], 'singers' => [], 'voiceParts' => [], 'performanceLevels' => []])

<div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">

    <!-- Top Bar -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
        
        <h2 class="text-3xl font-bold text-gray-900">
            Groups
        </h2>

        <div class="flex flex-wrap gap-3">
            <button onclick="openSettingsModal()"
                class="flex items-center gap-2 px-5 py-3 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 transition shadow-sm text-gray-700 font-medium">
                <i class="fas fa-cog"></i>
                Settings
            </button>

            <button onclick="openPreviousModal()"
                class="flex items-center gap-2 px-5 py-3 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 transition shadow-sm text-gray-700 font-medium">
                <i class="fas fa-history"></i>
                View Previous
            </button>

            <button onclick="openGeneratedListModal()"
                class="flex items-center gap-2 px-5 py-3 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 transition shadow-sm text-gray-700 font-medium">
                <i class="fas fa-eye"></i>
                View Generated List
            </button>

            <form id="generateGroupsForm" method="POST" action="{{ route('music.teams.generate') }}" class="inline">
                @csrf
                <input type="hidden" name="service_name" id="serviceName" value="Sunday Service">
                <input type="hidden" name="number_of_teams" id="numberOfTeams" value="2">
                <button type="submit"
                    class="flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-r from-blue-600 to-purple-600 hover:opacity-90 transition text-white font-semibold shadow-md">
                    <i class="fas fa-sync-alt"></i>
                    Generate Groups
                </button>
            </form>
        </div>
    </div>

    <!-- Display Generated Groups -->
    @if($generations && $generations->count() > 0)
        @php
            $latestGeneration = $generations->first();
            $teams = $latestGeneration->members->groupBy('team_number');
        @endphp

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @foreach($teams as $teamNum => $members)
                <div class="bg-gray-50 border border-gray-200 rounded-2xl p-8 flex items-center justify-between hover:shadow-md transition">
                    <div>
                        <h3 class="text-3xl font-bold text-gray-900 mb-2">
                            Service {{ chr(64 + $teamNum) }}
                        </h3>
                        <p class="text-gray-500 text-lg">
                            {{ $members->count() }} members
                        </p>
                    </div>
                    <button onclick="viewTeamDetails({{ $latestGeneration->id }}, {{ $teamNum }})"
                        class="flex items-center gap-3 px-6 py-4 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 rounded-xl font-semibold transition">
                        View List
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-gray-50 border border-gray-200 rounded-2xl p-10 text-center">
            <i class="fas fa-users text-5xl text-gray-300 mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">No Groups Generated</h3>
            <p class="text-gray-500">Click Generate Groups to create teams.</p>
        </div>
    @endif
</div>

<!-- Settings Modal -->
<div id="settingsModal" class="modal hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-6 border w-full max-w-5xl shadow-xl rounded-2xl bg-white">
        <div class="flex justify-between items-center pb-4 border-b">
            <div>
                <h3 class="text-2xl font-bold text-gray-900">Settings</h3>
                <p class="text-sm text-gray-500 mt-1">
                    Assign voice parts and performance levels to automatically group singers. 
                    Only users with both assigned will be included in generated groups.
                </p>
            </div>
            <button onclick="closeModal('settingsModal')" class="text-gray-400 hover:text-gray-600 transition">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <div class="relative my-5">
            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input type="text" id="singerSearchInput" placeholder="Search singers by name or email..." 
                   class="w-full pl-11 pr-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>
        
        <div class="overflow-x-auto rounded-xl border border-gray-200 max-h-96 overflow-y-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50 sticky top-0">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">NAME/EMAIL</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">VOICE PART</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">PERFORMANCE LEVEL</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @foreach($singers ?? [] as $singer)
                    <tr class="hover:bg-gray-50 transition singer-row" data-name="{{ strtolower($singer->name) }}" data-email="{{ strtolower($singer->email) }}">
                        <td class="px-6 py-4">
                            <div>
                                <p class="font-medium text-gray-900">{{ $singer->name }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">{{ $singer->email }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <select data-user-id="{{ $singer->id }}" data-field="voice_part" 
                                    class="singer-select border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                                <option value="">Select Voice</option>
                                @foreach($voiceParts ?? [] as $voice)
                                    <option value="{{ $voice }}" {{ $singer->voice_part == $voice ? 'selected' : '' }}>{{ $voice }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="px-6 py-4">
                            <select data-user-id="{{ $singer->id }}" data-field="singer_level" 
                                    class="singer-select border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                                <option value="">Select Level</option>
                                @foreach($performanceLevels ?? [] as $level)
                                    <option value="{{ $level }}" {{ $singer->singer_level == $level ? 'selected' : '' }}>{{ $level }}</option>
                                @endforeach
                            </select>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="flex justify-end gap-3 mt-6 pt-4 border-t">
            <button onclick="closeModal('settingsModal')" class="px-6 py-2.5 border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-xl font-medium transition">
                Cancel
            </button>
            <button onclick="saveAllSettings()" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-medium transition shadow-sm">
                <i class="fas fa-save mr-2"></i> Save All Changes
            </button>
        </div>
    </div>
</div>

<!-- View Previous Generations Modal -->
<div id="previousModal" class="modal hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-5 border w-full max-w-3xl shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center pb-3 border-b">
            <h3 class="text-xl font-bold text-gray-800">Group Generation History</h3>
            <button onclick="closeModal('previousModal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="mt-4 max-h-96 overflow-y-auto space-y-4">
            @forelse($generations ?? [] as $gen)
            <div class="border rounded-lg p-4 hover:shadow-md transition">
                <div class="flex justify-between items-start flex-wrap gap-3">
                    <div>
                        <p class="text-xs text-gray-500">GENERATED ON</p>
                        <p class="font-semibold text-gray-900">{{ \Carbon\Carbon::parse($gen->created_at)->format('d/m/Y, H:i:s') }}</p>
                        <p class="text-sm text-gray-600 mt-1">
                            {{ $gen->members->where('team_number', 1)->count() }} in Service A • 
                            {{ $gen->members->where('team_number', 2)->count() }} in Service B
                        </p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <button onclick="viewFullGenerationDetails({{ $gen->id }})" 
                                class="px-3 py-1.5 text-blue-600 border border-blue-600 rounded-lg text-sm hover:bg-blue-50 transition">
                            View Details
                        </button>
                        <button onclick="restoreGeneration({{ $gen->id }})" 
                                class="px-3 py-1.5 text-green-600 border border-green-600 rounded-lg text-sm hover:bg-green-50 transition">
                            Restore Instantly
                        </button>
                        <a href="{{ route('music.teams.export', $gen->id) }}" 
                           class="px-3 py-1.5 text-purple-600 border border-purple-600 rounded-lg text-sm hover:bg-purple-50 transition">
                            Export
                        </a>
                        <form action="{{ route('music.teams.delete', $gen->id) }}" method="POST" class="inline" 
                              onsubmit="return confirm('Delete this generation?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-3 py-1.5 text-red-600 border border-red-600 rounded-lg text-sm hover:bg-red-50 transition">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center text-gray-500 py-8">
                <i class="fas fa-history fa-3x mb-3 text-gray-300"></i>
                <p>No previous generations</p>
            </div>
            @endforelse
        </div>
        <div class="flex justify-end mt-4 pt-3 border-t">
            <button onclick="closeModal('previousModal')" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Close</button>
        </div>
    </div>
</div>

<!-- View Generated List Modal (Current Generation) -->
<div id="generatedListModal" class="modal hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-5 border w-full max-w-4xl shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center pb-3 border-b">
            <h3 class="text-xl font-bold text-gray-800">Generated Service Groups</h3>
            <button onclick="closeModal('generatedListModal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div id="generatedListContent" class="mt-4 max-h-96 overflow-y-auto"></div>
        <div class="flex justify-between items-center mt-4 pt-3 border-t">
            <div id="exportButtonContainer"></div>
            <button onclick="closeModal('generatedListModal')" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Close</button>
        </div>
    </div>
</div>

<!-- View Team Details Modal -->
<div id="teamDetailsModal" class="modal hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center pb-3 border-b">
            <h3 id="teamDetailsTitle" class="text-xl font-bold text-gray-800">Team Members</h3>
            <button onclick="closeModal('teamDetailsModal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div id="teamDetailsContent" class="mt-4 max-h-96 overflow-y-auto"></div>
        <div class="flex justify-end mt-4 pt-3 border-t">
            <button onclick="closeModal('teamDetailsModal')" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Close</button>
        </div>
    </div>
</div>

<script>
// ==================== MODAL FUNCTIONS ====================
function openSettingsModal() {
    document.getElementById('settingsModal').classList.remove('hidden');
}

function openPreviousModal() {
    document.getElementById('previousModal').classList.remove('hidden');
}

function openGeneratedListModal() {
    @if($generations && $generations->count() > 0)
        viewFullGenerationDetails({{ $generations->first()->id }});
    @else
        alert('No groups generated yet');
    @endif
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

// ==================== SEARCH FUNCTIONALITY ====================
const singerSearchInput = document.getElementById('singerSearchInput');
if (singerSearchInput) {
    singerSearchInput.addEventListener('keyup', function() {
        const searchTerm = this.value.toLowerCase();
        document.querySelectorAll('.singer-row').forEach(row => {
            const name = row.dataset.name || '';
            const email = row.dataset.email || '';
            if (name.includes(searchTerm) || email.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
}

// ==================== SAVE ALL SETTINGS ====================
function saveAllSettings() {
    const selects = document.querySelectorAll('#settingsModal .singer-select');
    const saveBtn = document.querySelector('#settingsModal .bg-blue-600');
    const originalText = saveBtn.innerHTML;
    
    const changes = [];
    selects.forEach(select => {
        const userId = select.dataset.userId;
        const field = select.dataset.field;
        const value = select.value;
        changes.push({ user_id: userId, field: field, value: value });
    });
    
    if (changes.length === 0) {
        alert('No changes to save');
        return;
    }
    
    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Saving...';
    saveBtn.disabled = true;
    
    let completed = 0;
    let errors = 0;
    
    changes.forEach(change => {
        fetch('/music/singers/update-settings', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify(change)
        })
        .then(response => response.json())
        .then(data => {
            completed++;
            if (!data.success) errors++;
            
            if (completed === changes.length) {
                saveBtn.innerHTML = originalText;
                saveBtn.disabled = false;
                
                if (errors === 0) {
                    alert('All settings saved successfully!');
                    location.reload();
                } else {
                    alert('Some settings failed to save. Please try again.');
                }
            }
        })
        .catch(error => {
            completed++;
            errors++;
            console.error('Error:', error);
            
            if (completed === changes.length) {
                saveBtn.innerHTML = originalText;
                saveBtn.disabled = false;
                alert('Error saving settings. Please try again.');
            }
        });
    });
}

// ==================== GENERATE GROUPS ====================
document.getElementById('generateGroupsForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const form = this;
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Generating...';
    submitBtn.disabled = true;
    
    fetch(form.action, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            service_name: document.getElementById('serviceName').value,
            number_of_teams: parseInt(document.getElementById('numberOfTeams').value)
        })
    })
    .then(response => response.json())
    .then(data => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
        
        if (data.success) {
            alert('Success! Groups generated successfully');
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
        console.error('Error:', error);
        alert('Network error: ' + error.message);
    });
});

// ==================== VIEW TEAM DETAILS ====================
function viewTeamDetails(id, teamNum) {
    fetch(`/music/teams/${id}/details`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const teamLetter = String.fromCharCode(64 + teamNum);
                const team = data.teams[teamNum - 1];
                
                document.getElementById('teamDetailsTitle').innerHTML = `Service ${teamLetter} - ${data.service_name}`;
                
                let html = '<div class="space-y-2">';
                team.members.forEach(member => {
                    html += `
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <p class="font-medium text-gray-800">${escapeHtml(member.name)}</p>
                                <p class="text-sm text-gray-500">${member.voice_part}</p>
                            </div>
                            <span class="px-2 py-1 text-xs rounded-full 
                                ${member.performance_level == 'Professional' ? 'bg-purple-100 text-purple-800' : ''}
                                ${member.performance_level == 'Advanced' ? 'bg-blue-100 text-blue-800' : ''}
                                ${member.performance_level == 'Good' ? 'bg-green-100 text-green-800' : ''}
                                ${member.performance_level == 'Normal' ? 'bg-gray-100 text-gray-800' : ''}">
                                ${member.performance_level}
                            </span>
                        </div>
                    `;
                });
                html += '</div>';
                
                document.getElementById('teamDetailsContent').innerHTML = html;
                document.getElementById('teamDetailsModal').classList.remove('hidden');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading team details');
        });
}

// ==================== VIEW FULL GENERATION DETAILS ====================
function viewFullGenerationDetails(id) {
    fetch(`/music/teams/${id}/details`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let html = `
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-lg font-bold text-blue-600 mb-3">Service A</h4>
                            <div class="space-y-2 max-h-96 overflow-y-auto">
                `;
                
                if (data.teams[0]) {
                    data.teams[0].members.forEach(member => {
                        html += `
                            <div class="flex items-center justify-between p-2 border-b hover:bg-gray-50">
                                <div>
                                    <span class="font-medium text-gray-800">${escapeHtml(member.name)}</span>
                                    <div class="flex gap-2 mt-0.5">
                                        <span class="text-xs text-purple-600">${member.voice_part}</span>
                                        <span class="text-xs px-1.5 py-0.5 rounded-full 
                                            ${member.performance_level == 'Good' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'}">
                                            ${member.performance_level}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    html += `</div><p class="text-sm font-semibold text-gray-600 mt-3 border-t pt-2">Total: ${data.teams[0].members.length} members</p></div>`;
                } else {
                    html += `<div class="text-gray-500">No members</div></div>`;
                }
                
                html += `<div><h4 class="text-lg font-bold text-purple-600 mb-3">Service B</h4><div class="space-y-2 max-h-96 overflow-y-auto">`;
                
                if (data.teams[1]) {
                    data.teams[1].members.forEach(member => {
                        html += `
                            <div class="flex items-center justify-between p-2 border-b hover:bg-gray-50">
                                <div>
                                    <span class="font-medium text-gray-800">${escapeHtml(member.name)}</span>
                                    <div class="flex gap-2 mt-0.5">
                                        <span class="text-xs text-purple-600">${member.voice_part}</span>
                                        <span class="text-xs px-1.5 py-0.5 rounded-full 
                                            ${member.performance_level == 'Good' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'}">
                                            ${member.performance_level}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    html += `</div><p class="text-sm font-semibold text-gray-600 mt-3 border-t pt-2">Total: ${data.teams[1].members.length} members</p></div>`;
                } else {
                    html += `<div class="text-gray-500">No members</div></div>`;
                }
                
                html += `</div>`;
                html += `<div class="flex justify-end mt-6 pt-4 border-t">
                            <a href="/music/teams/${id}/export" class="px-4 py-2 bg-green-600 text-white rounded-lg">Export CSV</a>
                        </div>`;
                
                document.getElementById('generatedListContent').innerHTML = html;
                document.getElementById('generatedListModal').classList.remove('hidden');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading generation details');
        });
}

// ==================== RESTORE GENERATION ====================
function restoreGeneration(id) {
    if (confirm('Restore this generation? It will create a new copy.')) {
        window.location.href = `/music/teams/${id}/restore`;
    }
}

// ==================== HELPER FUNCTIONS ====================
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>

<style>
.modal { display: none; }
.modal:not(.hidden) { display: block !important; }
</style>