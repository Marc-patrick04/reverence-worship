@extends('layouts.app')

@section('title', $userFamily->name ?? 'My Family')
@section('page-title', $userFamily->name ?? 'My Family')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    @if($userFamily)
    <!-- Family Header -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-2xl shadow-lg overflow-hidden">
        <div class="px-6 py-6">
            <div>
                <h1 class="text-2xl font-bold text-white">{{ $userFamily->name }}</h1>
                <div class="flex items-center gap-2 mt-2">
                    <i class="fas fa-users text-blue-200 text-sm"></i>
                    <span class="text-blue-100 text-sm">{{ $familyMembers->count() }} Members</span>
                    @if($userFamily->parent_name)
                    <span class="text-blue-200 mx-1">•</span>
                    <i class="fas fa-user-check text-blue-200 text-sm"></i>
                    <span class="text-blue-100 text-sm">Parent: {{ $userFamily->parent_name }}</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white rounded-xl shadow-sm p-5 text-center border border-gray-100">
            <p class="text-3xl font-bold text-blue-600">{{ $familyMembers->count() }}</p>
            <p class="text-sm text-gray-500 mt-1">Total Members</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 text-center border border-gray-100">
            <p class="text-3xl font-bold text-green-600">{{ $taskStats['completed'] ?? 0 }}</p>
            <p class="text-sm text-gray-500 mt-1">Completed Tasks</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 text-center border border-gray-100">
            <p class="text-3xl font-bold text-yellow-600">{{ ($taskStats['in_progress'] ?? 0) + ($taskStats['pending'] ?? 0) }}</p>
            <p class="text-sm text-gray-500 mt-1">Pending/In-Progress</p>
        </div>
    </div>

    <!-- Family Members Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-lg font-semibold text-gray-800">Family Members</h2>
        </div>

        <div class="p-4">
            <!-- Avatar Circles -->
            <div class="flex flex-wrap gap-3 mb-4">
                @foreach($familyMembers->take(8) as $member)
                <div class="relative group">
                    <div class="w-14 h-14 bg-gradient-to-br from-gray-600 to-gray-700 rounded-full flex items-center justify-center cursor-pointer hover:from-gray-700 hover:to-gray-800 transition shadow-sm"
                        onclick="showMemberDetails({{ $member->user_id }}, '{{ addslashes($member->name) }}')">
                        <span class="text-white text-base font-bold">{{ substr($member->name, 0, 2) }}</span>
                    </div>
                    <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 hidden group-hover:block bg-gray-800 text-white text-xs rounded px-2 py-1 whitespace-nowrap z-10">
                        {{ $member->name }}
                    </div>
                </div>
                @endforeach
                @if($familyMembers->count() > 8)
                <div class="w-14 h-14 bg-gray-100 rounded-full flex items-center justify-center border border-gray-200">
                    <span class="text-gray-500 text-sm font-medium">+{{ $familyMembers->count() - 8 }}</span>
                </div>
                @endif
            </div>

            <!-- View All Button -->
            <button id="viewAllMembersBtn" class="text-blue-600 hover:text-blue-700 text-sm font-medium flex items-center gap-1">
                View All {{ $familyMembers->count() }} Members
                <i class="fas fa-arrow-right text-xs"></i>
            </button>

            <p class="text-xs text-gray-400 mt-3">Tap to see contact details and roles</p>
        </div>
    </div>

    <!-- Task Board -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-lg font-semibold text-gray-800">Task Board</h2>
        </div>

        <!-- Filter Buttons -->
        <div class="px-6 py-3 bg-gray-50 border-b border-gray-100">
            <div class="flex gap-2">
                <button class="task-filter active px-4 py-1.5 text-sm rounded-lg bg-blue-600 text-white transition" data-filter="all">All</button>
                <button class="task-filter px-4 py-1.5 text-sm rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300 transition" data-filter="pending">Pending</button>
                <button class="task-filter px-4 py-1.5 text-sm rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300 transition" data-filter="in-progress">In Progress</button>
                <button class="task-filter px-4 py-1.5 text-sm rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300 transition" data-filter="completed">Completed</button>
            </div>
        </div>

        <!-- Tasks List -->
        <div id="tasks-container" class="divide-y divide-gray-100 max-h-96 overflow-y-auto">
            @forelse($familyTasks as $task)
            <div class="task-item p-4 hover:bg-gray-50 transition-all" data-status="{{ $task->status }}">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <h3 class="font-medium text-gray-800">{{ $task->title }}</h3>
                            <span class="px-2 py-0.5 text-xs rounded-full 
                                {{ $task->status == 'completed' ? 'bg-green-100 text-green-700' : 
                                   ($task->status == 'in-progress' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-600') }}">
                                {{ ucfirst($task->status == 'in-progress' ? 'In Progress' : $task->status) }}
                            </span>
                        </div>
                        @if($task->description)
                        <p class="text-sm text-gray-500">{{ Str::limit($task->description, 100) }}</p>
                        @endif
                        @if($task->due_date)
                        <p class="text-xs text-gray-400 mt-1 flex items-center gap-1">
                            <i class="fas fa-calendar-alt text-xs"></i> Due: {{ \Carbon\Carbon::parse($task->due_date)->format('d M Y') }}
                        </p>
                        @endif
                    </div>
                    
                </div>
            </div>
            @empty
            <div class="text-center py-12 text-gray-500">
                <i class="fas fa-check-circle text-4xl text-gray-300 mb-3"></i>
                <p>No tasks assigned to your family yet</p>
            </div>
            @endforelse
        </div>
    </div>

    @else
    <!-- No Family Assigned -->
    <div class="bg-white rounded-2xl shadow-sm p-12 text-center border border-gray-100">
        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-home text-gray-400 text-3xl"></i>
        </div>
        <h2 class="text-xl font-bold text-gray-800 mb-2">No Family Assigned</h2>
        <p class="text-gray-500 mb-4">You are not yet assigned to any family.</p>
        <a href="{{ route('social-fellowship.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-xl text-sm transition">
            Go to Social Fellowship
        </a>
    </div>
    @endif

</div>

<!-- Member Details Modal -->
<div id="memberModal" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
    <div style="background: white; max-width: 500px; width: 90%; margin: auto; border-radius: 16px; padding: 24px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);">
        <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 16px; border-bottom: 1px solid #e5e7eb;">
            <h3 id="memberModalTitle" style="font-size: 20px; font-weight: bold; color: #1f2937;">Member Details</h3>
            <button onclick="closeModal('memberModal')" style="color: #9ca3af; font-size: 24px; background: none; border: none; cursor: pointer;">&times;</button>
        </div>
        <div id="memberModalContent" style="margin-top: 16px;"></div>
        <div style="display: flex; justify-content: flex-end; margin-top: 24px; padding-top: 16px; border-top: 1px solid #e5e7eb;">
            <button onclick="closeModal('memberModal')" style="background: #4b5563; color: white; padding: 8px 20px; border-radius: 12px; font-size: 14px; border: none; cursor: pointer;">Close</button>
        </div>
    </div>
</div>

<!-- All Members Modal -->
<div id="allMembersModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; overflow-y: auto; padding: 20px;">
    <div style="max-width: 1000px; width: 100%; margin: 0 auto; background: white; border-radius: 24px; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);">

        <!-- Header -->
        <div style="background: linear-gradient(135deg, #4f46e5, #4f46e5, #4f46e5); padding: 20px 24px; display: flex; justify-content: space-between; align-items: center;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 40px; height: 40px; background: rgba(255,255,255,0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white;">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <h3 style="color: white; font-size: 20px; font-weight: bold; margin: 0;">Family Members</h3>
                    <p style="color: rgba(255,255,255,0.8); font-size: 12px; margin: 4px 0 0 0;">All members in {{ $userFamily->name }}</p>
                </div>
            </div>
            <div style="display: flex; align-items: center; gap: 16px;">
                <button onclick="exportMembersToCSV()" style="color: white; background: none; border: none; cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 14px;">
                    <i class="fas fa-download"></i> Export
                </button>
                <button onclick="closeModal('allMembersModal')" style="color: white; background: none; border: none; font-size: 24px; cursor: pointer;">&times;</button>
            </div>
        </div>

        <!-- Members Grid -->
        <div style="background: #f3f4f6; padding: 20px; max-height: 400px; overflow-y: auto;">
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 10px;">
                @foreach($familyMembers as $member)
                @php
                $locationParts = array_filter([
                    $member->village ?? '',
                    $member->sector ?? '',
                    $member->district ?? '',
                    $member->province ?? ''
                ]);
                $location = !empty($locationParts) ? implode(', ', array_reverse($locationParts)) : '';
                $firstLetter = strtoupper(substr($member->name, 0, 1));
                $isParent = strtolower($member->role ?? '') === 'parent';
                @endphp

                <div onclick="showMemberDetails({{ $member->user_id }}, '{{ addslashes($member->name) }}'); closeModal('allMembersModal')"
                    style="background: white; border-radius: 16px; padding: 16px; cursor: pointer; border: 1px solid #e5e7eb; transition: all 0.2s;"
                    onmouseover="this.style.boxShadow='0 10px 15px -3px rgba(0,0,0,0.1)'" onmouseout="this.style.boxShadow='none'">
                    <div style="display: flex; gap: 12px;">
                        <!-- Avatar -->
                        <div style="width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 18px; flex-shrink: 0; background: {{ $isParent ? 'linear-gradient(135deg, #4f46e5, #7c3aed)' : 'linear-gradient(135deg, #3b82f6, #2563eb)' }};">
                            {{ $firstLetter }}
                        </div>

                        <!-- Content -->
                        <div style="flex: 1; min-width: 0;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                                <div>
                                    <h4 style="font-weight: bold; color: #1f2937; font-size: 16px; margin: 0;">{{ $member->name }}</h4>
                                    <p style="font-size: 12px; margin: 4px 0 0 0; color: {{ $isParent ? '#7c3aed' : '#3b82f6' }};">
                                        {{ ucfirst($member->role ?? 'Member') }}
                                    </p>
                                </div>
                                @if($isParent)
                                <div style="width: 24px; height: 24px; background: #f3e8ff; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-star" style="color: #8b5cf6; font-size: 10px;"></i>
                                </div>
                                @endif
                            </div>

                            @if($member->phone)
                            <div style="display: flex; align-items: center; gap: 8px; margin-top: 12px; color: #6b7280; font-size: 12px;">
                                <i class="fas fa-phone" style="width: 12px;"></i>
                                <span style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $member->phone }}</span>
                            </div>
                            @endif

                            @if($location)
                            <div style="display: flex; align-items: flex-start; gap: 8px; margin-top: 8px; color: #6b7280; font-size: 12px;">
                                <i class="fas fa-map-marker-alt" style="width: 12px; margin-top: 2px;"></i>
                                <span style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">{{ Str::limit($location, 60) }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            @if($familyMembers->isEmpty())
            <div style="text-align: center; padding: 48px;">
                <i class="fas fa-users" style="font-size: 48px; color: #9ca3af; margin-bottom: 12px;"></i>
                <p style="color: #6b7280;">No members found in this family</p>
            </div>
            @endif
        </div>

        <!-- Footer -->
        <div style="background: white; padding: 12px 24px; border-top: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center;">
            <div style="font-size: 12px; color: #6b7280;">
                Total: <span style="font-weight: 600; color: #374151;">{{ $familyMembers->count() }}</span> members
            </div>
            <button onclick="closeModal('allMembersModal')" style="background: #4b5563; color: white; padding: 6px 16px; border-radius: 8px; font-size: 14px; border: none; cursor: pointer;">
                Close
            </button>
        </div>
    </div>
</div>

<script>
    function showModal(modalId) {
        var modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'flex';
        }
    }

    function closeModal(modalId) {
        var modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'none';
        }
    }

    function exportMembersToCSV() {
        var members = @json($familyMembers);
        var csv = [];

        csv.push(['Name', 'Role', 'Phone', 'Province', 'District', 'Sector', 'Village', 'Email'].join(','));

        members.forEach(function(member) {
            var row = [];
            row.push('"' + (member.name || '').replace(/"/g, '""') + '"');
            row.push('"' + (member.role || 'Member').replace(/"/g, '""') + '"');
            row.push('"' + (member.phone || '').replace(/"/g, '""') + '"');
            row.push('"' + (member.province || '').replace(/"/g, '""') + '"');
            row.push('"' + (member.district || '').replace(/"/g, '""') + '"');
            row.push('"' + (member.sector || '').replace(/"/g, '""') + '"');
            row.push('"' + (member.village || '').replace(/"/g, '""') + '"');
            row.push('"' + (member.email || '').replace(/"/g, '""') + '"');
            csv.push(row.join(','));
        });

        var blob = new Blob(["\uFEFF" + csv.join('\n')], {
            type: 'text/csv;charset=utf-8;'
        });
        var link = document.createElement('a');
        var url = URL.createObjectURL(blob);
        link.setAttribute('href', url);
        link.setAttribute('download', 'family_members_{{ $userFamily->name }}.csv');
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);
    }

    function showMemberDetails(userId, name) {
        fetch('/my-family/member/' + userId + '/details', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(function(response) {
                return response.json();
            })
            .then(function(data) {
                if (data.success) {
                    var address = '';
                    if (data.member.province) address += data.member.province;
                    if (data.member.district) address += (address ? ', ' : '') + data.member.district;
                    if (data.member.sector) address += (address ? ', ' : '') + data.member.sector;
                    if (data.member.village) address += (address ? ', ' : '') + data.member.village;

                    document.getElementById('memberModalTitle').textContent = name;
                    document.getElementById('memberModalContent').innerHTML =
                        '<div style="space-y: 4;">' +
                        '<div style="display: flex; align-items: center; gap: 12px; padding-bottom: 12px; border-bottom: 1px solid #e5e7eb;">' +
                        '<div style="width: 48px; height: 48px; background: linear-gradient(135deg, #3b82f6, #2563eb); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 18px;">' + name.substring(0, 2) + '</div>' +
                        '<div>' +
                        '<p style="font-weight: 600; color: #1f2937; margin: 0;">' + (data.member.email || 'No email') + '</p>' +
                        '<p style="font-size: 14px; color: #6b7280; margin: 4px 0 0 0; text-transform: capitalize;">' + (data.role || 'Member') + '</p>' +
                        '</div>' +
                        '</div>' +
                        '<div style="space-y: 2;">' +
                        '<div style="display: flex; align-items: center; gap: 8px; font-size: 14px;">' +
                        '<i class="fas fa-phone" style="color: #9ca3af; width: 16px;"></i>' +
                        '<span>' + (data.member.phone || 'Not provided') + '</span>' +
                        '</div>' +
                        '<div style="display: flex; align-items: center; gap: 8px; font-size: 14px;">' +
                        '<i class="fas fa-map-marker-alt" style="color: #9ca3af; width: 16px;"></i>' +
                        '<span>' + (address || 'Not provided') + '</span>' +
                        '</div>' +
                        '<div style="display: flex; align-items: center; gap: 8px; font-size: 14px;">' +
                        '<i class="fas fa-birthday-cake" style="color: #9ca3af; width: 16px;"></i>' +
                        '<span>' + (data.member.date_of_birth || 'Not provided') + '</span>' +
                        '</div>' +
                        '</div>' +
                        '</div>';
                    showModal('memberModal');
                }
            })
            .catch(function(error) {
                console.error('Error:', error);
                alert('Error loading member details');
            });
    }

    function updateTaskStatus(taskId, status) {
        fetch('/my-family/task/' + taskId + '/status', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    status: status
                })
            })
            .then(function(response) {
                return response.json();
            })
            .then(function(data) {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error updating task status');
                }
            })
            .catch(function(error) {
                console.error('Error:', error);
                alert('Error updating task status');
            });
    }

    // Set up event listeners when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        var viewAllBtn = document.getElementById('viewAllMembersBtn');
        if (viewAllBtn) {
            viewAllBtn.addEventListener('click', function(e) {
                e.preventDefault();
                showModal('allMembersModal');
            });
        }

        // Task filter functionality
        var filterBtns = document.querySelectorAll('.task-filter');
        if (filterBtns.length > 0) {
            filterBtns.forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var filterValue = this.getAttribute('data-filter');
                    var tasks = document.querySelectorAll('.task-item');
                    
                    // Update active button styling
                    filterBtns.forEach(function(b) {
                        b.classList.remove('bg-blue-600', 'text-white');
                        b.classList.add('bg-gray-200', 'text-gray-700');
                    });
                    this.classList.remove('bg-gray-200', 'text-gray-700');
                    this.classList.add('bg-blue-600', 'text-white');
                    
                    // Filter tasks
                    tasks.forEach(function(task) {
                        var taskStatus = task.getAttribute('data-status');
                        if (filterValue === 'all' || taskStatus === filterValue) {
                            task.style.display = '';
                        } else {
                            task.style.display = 'none';
                        }
                    });
                });
            });
        }
    });
</script>
@endsection