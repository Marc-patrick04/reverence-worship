@extends('layouts.app')

@section('title', 'Form Submissions')
@section('page-title', 'Form Submissions')

@section('content')
<div class="max-w-7xl mx-auto py-6">
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        
        <!-- Header -->
        <div class="bg-gradient-to-r from-indigo-600 to-blue-600 px-6 py-5">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <a href="{{ route('forms.manage.index') }}" class="text-white/80 hover:text-white transition flex items-center gap-2 text-sm mb-2">
                        <i class="fas fa-arrow-left"></i> Back to Forms
                    </a>
                    <h1 class="text-2xl font-bold text-white">{{ $form->title }}</h1>
                    <p class="text-indigo-100 text-sm mt-1">Manage Submissions</p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="bg-white/20 text-white px-3 py-1 rounded-full text-sm">
                        <i class="fas fa-users mr-1"></i> {{ count($submissions) }} Responses
                    </span>
                    @if(count($submissions) > 0)
                    <button onclick="exportSubmissions()" class="bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-lg transition flex items-center gap-2 text-sm">
                        <i class="fas fa-download"></i> Export CSV
                    </button>
                    @endif
                </div>
            </div>
        </div>
        
        @php
            // Get settings
            $settings = json_decode($form->settings, true) ?? [];
            $isQuiz = $settings['is_quiz'] ?? false;
            $releaseGrade = $settings['release_grade'] ?? 'immediately';
            $allowExport = $settings['allow_export'] ?? true;
            $includeTimestamps = $settings['include_timestamps'] ?? true;
        @endphp
        
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 p-6 bg-gray-50 border-b">
            <div class="bg-white rounded-lg p-4 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-file-alt text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Total Submissions</p>
                        <p class="text-xl font-bold text-gray-800">{{ count($submissions) }}</p>
                    </div>
                </div>
            </div>
            
            @if($isQuiz)
            <div class="bg-white rounded-lg p-4 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-star text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Average Score</p>
                        <p class="text-xl font-bold text-gray-800">
                            @php
                                $avgScore = count($submissions) > 0 ? round($submissions->avg('score'), 1) : 0;
                            @endphp
                            {{ number_format($avgScore, 1) }}%
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg p-4 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-trophy text-purple-600"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Highest Score</p>
                        <p class="text-xl font-bold text-gray-800">
                            @php
                                $highestScore = count($submissions) > 0 ? round($submissions->max('score'), 1) : 0;
                            @endphp
                            {{ number_format($highestScore, 1) }}%
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg p-4 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-clock text-yellow-600"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Status</p>
                        <p class="text-sm font-semibold text-gray-800">
                            @if($releaseGrade == 'immediately')
                                <span class="text-green-600">Auto-graded</span>
                            @elseif($releaseGrade == 'later')
                                <span class="text-yellow-600">Pending Review</span>
                            @else
                                <span class="text-gray-600">Private</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
            @endif
        </div>
        
        <!-- Filters -->
        <div class="p-4 border-b bg-white flex flex-wrap items-center gap-3">
            <div class="flex items-center gap-2">
                <i class="fas fa-filter text-gray-400 text-sm"></i>
                <span class="text-sm text-gray-600">Filter:</span>
            </div>
            <select id="filterScore" class="text-sm border rounded-lg px-3 py-1.5 focus:ring-2 focus:ring-indigo-500 focus:border-transparent" onchange="filterSubmissions()">
                <option value="all">All Scores</option>
                <option value="high">High (≥ 80%)</option>
                <option value="medium">Medium (60-79%)</option>
                <option value="low">Low (40-59%)</option>
                <option value="fail">Fail (&lt; 40%)</option>
            </select>
            <input type="text" id="searchInput" placeholder="Search by user..." class="text-sm border rounded-lg px-3 py-1.5 focus:ring-2 focus:ring-indigo-500 focus:border-transparent" onkeyup="filterSubmissions()">
            <span class="text-xs text-gray-400 ml-auto" id="resultCount">Showing {{ count($submissions) }} results</span>
        </div>
        
        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" id="submissionsTable">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        @if($isQuiz)
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Score</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Points</th>
                        @endif
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submitted</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($submissions as $index => $sub)
                    <tr class="border-t hover:bg-gray-50 transition submission-row" data-score="{{ $sub->score ?? 0 }}" data-user="{{ strtolower($sub->user_name ?? 'User #' . $sub->user_id) }}">
                        <td class="px-4 py-3 text-sm text-gray-400">{{ $index + 1 }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white text-xs font-medium">
                                    {{ strtoupper(substr($sub->user_name ?? 'User', 0, 2)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-800">{{ $sub->user_name ?? 'User #' . $sub->user_id }}</p>
                                    <p class="text-xs text-gray-400">{{ $sub->email ?? 'No email' }}</p>
                                </div>
                            </div>
                        </td>
                        @if($isQuiz)
                        <td class="px-4 py-3">
                            @if($sub->score !== null)
                                @php
                                    $score = round($sub->score, 1);
                                    $scoreColor = $score >= 80 ? 'bg-green-100 text-green-700' : ($score >= 60 ? 'bg-blue-100 text-blue-700' : ($score >= 40 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700'));
                                @endphp
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $scoreColor }}">
                                    {{ number_format($score, 1) }}%
                                </span>
                            @else
                                <span class="text-xs text-gray-400">Pending</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm">
                            @if($sub->score !== null)
                                @php
                                    // Calculate points based on score percentage
                                    $totalPoints = 0;
                                    foreach($questions as $q) {
                                        $qType = $q['type'] ?? 'short_answer';
                                        if ($qType != 'title_section' && $qType != 'section_break') {
                                            $totalPoints += isset($q['points']) ? (int)$q['points'] : 1;
                                        }
                                    }
                                    $earnedPoints = round(($sub->score / 100) * $totalPoints, 1);
                                @endphp
                                <span class="text-sm font-medium text-gray-700">{{ number_format($earnedPoints, 1) }}</span>
                                <span class="text-xs text-gray-400">/ {{ $totalPoints }}</span>
                            @else
                                <span class="text-sm text-gray-400">-</span>
                            @endif
                        </td>
                        @endif
                        <td class="px-4 py-3 text-sm text-gray-600">
                            <div class="flex items-center gap-1">
                                <i class="fas fa-calendar-alt text-gray-400 text-xs"></i>
                                {{ \Carbon\Carbon::parse($sub->submitted_at)->format('M d, Y') }}
                            </div>
                            <div class="text-xs text-gray-400">
                                {{ \Carbon\Carbon::parse($sub->submitted_at)->format('h:i A') }}
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            @if($isQuiz && $sub->score !== null)
                                @php
                                    $score = round($sub->score, 1);
                                    $status = $score >= 80 ? 'Excellent' : ($score >= 60 ? 'Good' : ($score >= 40 ? 'Average' : 'Needs Improvement'));
                                    $statusColor = $score >= 80 ? 'text-green-600' : ($score >= 60 ? 'text-blue-600' : ($score >= 40 ? 'text-yellow-600' : 'text-red-600'));
                                @endphp
                                <span class="text-xs font-medium {{ $statusColor }}">
                                    <i class="fas {{ $score >= 80 ? 'fa-star' : ($score >= 60 ? 'fa-thumbs-up' : ($score >= 40 ? 'fa-minus-circle' : 'fa-exclamation-triangle')) }} mr-1"></i>
                                    {{ $status }}
                                </span>
                            @elseif($isQuiz)
                                <span class="text-xs text-yellow-600">
                                    <i class="fas fa-clock mr-1"></i> Pending Review
                                </span>
                            @else
                                <span class="text-xs text-green-600">
                                    <i class="fas fa-check-circle mr-1"></i> Completed
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('forms.results', $sub->form_id) }}" class="text-blue-600 hover:text-blue-800 text-sm transition flex items-center gap-1">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                @if($isQuiz && $releaseGrade == 'later' && $sub->score === null)
                                <button onclick="gradeSubmission({{ $sub->id }})" class="text-green-600 hover:text-green-800 text-sm transition flex items-center gap-1">
                                    <i class="fas fa-check"></i> Grade
                                </button>
                                @endif
                                @if(auth()->user()->canAccess('intercession', 'delete-forms'))
                                <button onclick="deleteSubmission({{ $sub->id }})" class="text-red-600 hover:text-red-800 text-sm transition flex items-center gap-1">
                                    <i class="fas fa-trash"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ $isQuiz ? 6 : 4 }}" class="px-4 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                    <i class="fas fa-inbox text-gray-400 text-2xl"></i>
                                </div>
                                <p class="text-gray-500 font-medium">No submissions yet</p>
                                <p class="text-sm text-gray-400">Be the first to submit this form</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Footer -->
        <div class="bg-gray-50 px-6 py-4 border-t flex justify-between items-center">
            <div class="text-sm text-gray-500">
                <i class="fas fa-info-circle mr-1"></i> 
                Showing {{ count($submissions) }} submission{{ count($submissions) > 1 ? 's' : '' }}
            </div>
            <div class="flex items-center gap-3">
                @if($allowExport && count($submissions) > 0)
                <button onclick="exportSubmissions()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition text-sm flex items-center gap-2">
                    <i class="fas fa-file-csv"></i> Export CSV
                </button>
                @endif
               
            </div>
        </div>
    </div>
</div>

<script>
function filterSubmissions() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const filterScore = document.getElementById('filterScore').value;
    const rows = document.querySelectorAll('.submission-row');
    let visibleCount = 0;
    
    rows.forEach(row => {
        const user = row.dataset.user || '';
        const score = parseFloat(row.dataset.score) || 0;
        let show = true;
        
        // Search filter
        if (searchTerm && !user.includes(searchTerm)) {
            show = false;
        }
        
        // Score filter
        if (show && filterScore !== 'all') {
            if (filterScore === 'high' && score < 80) show = false;
            else if (filterScore === 'medium' && (score < 60 || score >= 80)) show = false;
            else if (filterScore === 'low' && (score < 40 || score >= 60)) show = false;
            else if (filterScore === 'fail' && score >= 40) show = false;
        }
        
        row.style.display = show ? '' : 'none';
        if (show) visibleCount++;
    });
    
    document.getElementById('resultCount').textContent = `Showing ${visibleCount} results`;
}

function exportSubmissions() {
    // Get table data
    const rows = document.querySelectorAll('.submission-row');
    let csv = [];
    
    // Headers
    @if($isQuiz)
    csv.push(['#', 'User', 'Email', 'Score', 'Points', 'Submitted', 'Status'].join(','));
    @else
    csv.push(['#', 'User', 'Email', 'Submitted'].join(','));
    @endif
    
    // Data rows
    rows.forEach((row, index) => {
        if (row.style.display === 'none') return;
        
        const cells = row.querySelectorAll('td');
        let rowData = [];
        
        rowData.push(index + 1);
        rowData.push(`"${cells[1]?.textContent?.trim() || ''}"`);
        rowData.push(`"${cells[1]?.querySelector('.text-xs')?.textContent?.trim() || ''}"`);
        
        @if($isQuiz)
        const scoreText = cells[2]?.textContent?.trim() || '';
        rowData.push(scoreText.replace('%', ''));
        rowData.push(cells[3]?.textContent?.trim() || '');
        rowData.push(cells[4]?.textContent?.trim() || '');
        rowData.push(cells[5]?.textContent?.trim() || '');
        @else
        rowData.push(cells[2]?.textContent?.trim() || '');
        @endif
        
        csv.push(rowData.join(','));
    });
    
    // Download CSV
    const blob = new Blob(['\uFEFF' + csv.join('\n')], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', 'submissions_{{ $form->title }}.csv');
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);
    
    // Show notification
    showNotification('Export completed!', 'success');
}

function gradeSubmission(id) {
    if (confirm('Are you sure you want to grade this submission?')) {
        // Implement grading logic here
        showNotification('Grading feature coming soon!', 'info');
    }
}

function deleteSubmission(id) {
    if (confirm('Are you sure you want to delete this submission? This action cannot be undone.')) {
        fetch(`/forms/submissions/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Submission deleted successfully!', 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                showNotification('Error: ' + data.message, 'error');
            }
        })
        .catch(error => {
            showNotification('Error deleting submission', 'error');
        });
    }
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `fixed top-20 right-4 z-50 px-4 py-3 rounded-lg shadow-lg flex items-center gap-3 animate-slide-in max-w-md`;
    notification.style.backgroundColor = type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#3b82f6';
    notification.innerHTML = `
        <i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'} text-white"></i>
        <span class="text-white text-sm">${message}</span>
        <button onclick="this.parentElement.remove()" class="text-white/70 hover:text-white"><i class="fas fa-times"></i></button>
    `;
    document.body.appendChild(notification);
    setTimeout(() => notification.remove(), 3000);
}

// Add animation styles
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    .animate-slide-in {
        animation: slideIn 0.3s ease-out;
    }
`;
document.head.appendChild(style);
</script>

<style>
/* Table hover effect */
tbody tr {
    transition: background-color 0.2s ease;
}

/* Stats cards hover */
.bg-white.rounded-lg {
    transition: all 0.2s ease;
}
.bg-white.rounded-lg:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

</style>
@endsection