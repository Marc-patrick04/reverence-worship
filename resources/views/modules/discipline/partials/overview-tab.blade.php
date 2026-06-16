<div>
    <div class="space-y-6">

        <!-- Statistics Cards - White background -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Discipline Sessions Card -->
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-xs uppercase tracking-wide">Discipline Sessions</p>
                        <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats->total_discipline_sessions ?? 0 }}</p>
                    </div>
                    <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center">
                        <i class="fas fa-gavel text-blue-500 text-lg"></i>
                    </div>
                </div>
            </div>

            <!-- Avg Good Behavior Card -->
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-xs uppercase tracking-wide">Avg Good Behavior</p>
                        <p class="text-2xl font-bold text-gray-800 mt-1">{{ round($stats->avg_good_behavior ?? 0) }}%</p>
                    </div>
                    <div class="w-10 h-10 bg-green-50 rounded-lg flex items-center justify-center">
                        <i class="fas fa-smile text-green-500 text-lg"></i>
                    </div>
                </div>
            </div>

            <!-- Attendance Sessions Card -->
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-xs uppercase tracking-wide">Attendance Sessions</p>
                        <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats->attendance_sessions ?? 0 }}</p>
                    </div>
                    <div class="w-10 h-10 bg-purple-50 rounded-lg flex items-center justify-center">
                        <i class="fas fa-calendar-check text-purple-500 text-lg"></i>
                    </div>
                </div>
            </div>

            <!-- Permission Requests Card -->
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-xs uppercase tracking-wide">Permission Requests</p>
                        <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats->permission_requests ?? 0 }}</p>
                    </div>
                    <div class="w-10 h-10 bg-indigo-50 rounded-lg flex items-center justify-center">
                        <i class="fas fa-envelope text-indigo-500 text-lg"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Management Cards - 3 Column Grid White Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Attendance Management Card -->
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center">
                        <i class="fas fa-calendar-alt text-blue-500 text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-800">Attendance Management</h3>
                        <p class="text-xs text-gray-500">Track and manage team attendance</p>
                    </div>
                </div>
                <button onclick="switchTab('attendance')"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm py-2 rounded-lg font-medium transition">
                    Manage Attendance
                </button>
            </div>

            <!-- Permission Requests Card -->
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 bg-green-50 rounded-lg flex items-center justify-center">
                        <i class="fas fa-envelope-open-text text-green-500 text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-800">Permission Requests</h3>
                        <p class="text-xs text-gray-500">Manage user permission requests</p>
                    </div>
                </div>
                <button onclick="switchTab('permission')"
                    class="w-full bg-green-600 hover:bg-green-700 text-white text-sm py-2 rounded-lg font-medium transition">
                    Manage Requests
                </button>
            </div>

            <!-- Discipline Records Card -->
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 bg-purple-50 rounded-lg flex items-center justify-center">
                        <i class="fas fa-book text-purple-500 text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-800">Discipline Records</h3>
                        <p class="text-xs text-gray-500">Record and track discipline</p>
                    </div>
                </div>
                <button onclick="switchTab('discipline-records')"
                    class="w-full bg-purple-600 hover:bg-purple-700 text-white text-sm py-2 rounded-lg font-medium transition">
                    Manage Discipline
                </button>
            </div>
        </div>

        <!-- Recent Sections - 2 Column Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
            <!-- Recent Discipline Sessions -->
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-100 bg-gray-50">
                    <h2 class="text-sm font-semibold text-gray-800">
                        <i class="fas fa-gavel text-blue-500 mr-2"></i> Recent Discipline Sessions
                    </h2>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($recentSessions ?? [] as $session)
                    <div class="px-4 py-3 hover:bg-gray-50 transition">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-sm font-medium text-gray-800">{{ $session->title ?? 'Discipline Session' }}</h4>
                                <p class="text-xs text-gray-400 mt-0.5">
                                    <i class="far fa-calendar-alt mr-1"></i>
                                    {{ $session->formatted_date ?? date('d/m/Y', strtotime($session->created_at ?? 'now')) }}
                                </p>
                            </div>
                            <div class="flex items-center gap-2">
                                <button onclick="viewDisciplineRecord({{ $session->id }})"
                                    class="text-blue-600 hover:text-blue-800 text-xs font-medium transition">
                                    View
                                </button>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8 text-gray-400 text-sm">
                        <i class="fas fa-inbox text-2xl mb-2 block"></i>
                        <p>No discipline sessions found</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Recent Permission Requests -->
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-100 bg-gray-50">
                    <h2 class="text-sm font-semibold text-gray-800">
                        <i class="fas fa-envelope text-green-500 mr-2"></i> Recent Permission Requests
                    </h2>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($recentPermissions ?? [] as $permission)
                    <div class="px-4 py-3 hover:bg-gray-50 transition">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <h4 class="text-sm font-medium text-gray-800">{{ $permission->user_name ?? 'Unknown' }}</h4>
                                    <span class="px-1.5 py-0.5 text-xs rounded-full font-medium
                                        {{ ($permission->status ?? 'pending') === 'approved' ? 'bg-green-100 text-green-700' : 
                                           (($permission->status ?? 'pending') === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                                        {{ ucfirst($permission->status ?? 'Pending') }}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500 line-clamp-1">
                                    {{ Str::limit($permission->reason ?? 'No reason provided', 80) }}
                                </p>
                                <p class="text-xs text-gray-400 mt-1">
                                    <i class="far fa-calendar-alt mr-1"></i>
                                    {{ $permission->formatted_date ?? date('d/m/Y', strtotime($permission->created_at ?? 'now')) }}
                                </p>
                            </div>
                            <button onclick="viewPermission({{ $permission->id }})"
                                class="text-blue-600 hover:text-blue-800 text-xs font-medium ml-2 transition">
                                View
                            </button>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8 text-gray-400 text-sm">
                        <i class="fas fa-inbox text-2xl mb-2 block"></i>
                        <p>No permission requests found</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
</div>

<script>
function switchTab(tabName) {
    const tabButtons = document.querySelectorAll('.tab-btn');
    tabButtons.forEach(btn => {
        if (btn.getAttribute('data-tab') === tabName) {
            btn.click();
        }
    });
}

function viewDisciplineRecord(id) {
    window.location.href = `/discipline/records/${id}`;
}

function viewPermission(id) {
    window.location.href = `/discipline/permission/${id}`;
}
</script>