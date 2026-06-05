<div>
    <!-- Stats Cards Row -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Discipline Sessions Card -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-2">
                <i class="fas fa-gavel text-3xl opacity-80"></i>
                <span class="text-xs opacity-80">Total</span>
            </div>
            <p class="text-4xl font-bold">{{ $stats->total_discipline_sessions ?? 0 }}</p>
            <p class="text-sm mt-2 opacity-90">Discipline Sessions</p>
        </div>
        
        <!-- Avg Good Behavior Card -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-2">
                <i class="fas fa-smile text-3xl opacity-80"></i>
                <span class="text-xs opacity-80">Average</span>
            </div>
            <p class="text-4xl font-bold">{{ round($stats->avg_good_behavior ?? 0) }}%</p>
            <p class="text-sm mt-2 opacity-90">Avg Good Behavior</p>
        </div>
        
        <!-- Attendance Sessions Card -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-2">
                <i class="fas fa-calendar-check text-3xl opacity-80"></i>
                <span class="text-xs opacity-80">Total</span>
            </div>
            <p class="text-4xl font-bold">{{ $stats->attendance_sessions ?? 0 }}</p>
            <p class="text-sm mt-2 opacity-90">Attendance Sessions</p>
        </div>
        
        <!-- Permission Requests Card -->
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-2">
                <i class="fas fa-envelope text-3xl opacity-80"></i>
                <span class="text-xs opacity-80">Total</span>
            </div>
            <p class="text-4xl font-bold">{{ $stats->permission_requests ?? 0 }}</p>
            <p class="text-sm mt-2 opacity-90">Permission requests</p>
        </div>
    </div>
    
    <!-- Management Cards Row -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Attendance Management Card -->
        <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100 hover:shadow-lg transition">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-calendar-alt text-blue-600 text-xl"></i>
                </div>
                <i class="fas fa-chevron-right text-gray-300"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-800 mb-1">Attendance Management</h3>
            <p class="text-sm text-gray-500 mb-4">Track and manage team attendance</p>
            <button onclick="switchTab('attendance')" class="text-blue-600 hover:text-blue-700 text-sm font-medium flex items-center gap-1">
                Manage Attendance <i class="fas fa-arrow-right"></i>
            </button>
        </div>
        
        <!-- Permission Requests Card -->
        <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100 hover:shadow-lg transition">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-envelope-open-text text-green-600 text-xl"></i>
                </div>
                <i class="fas fa-chevron-right text-gray-300"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-800 mb-1">Permission requests</h3>
            <p class="text-sm text-gray-500 mb-4">Manage user Permission requests</p>
            <button onclick="switchTab('permission')" class="text-green-600 hover:text-green-700 text-sm font-medium flex items-center gap-1">
                Manage Permission requests <i class="fas fa-arrow-right"></i>
            </button>
        </div>
        
        <!-- Discipline Records Card -->
        <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100 hover:shadow-lg transition">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-book text-purple-600 text-xl"></i>
                </div>
                <i class="fas fa-chevron-right text-gray-300"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-800 mb-1">Discipline Records</h3>
            <p class="text-sm text-gray-500 mb-4">Record and track discipline</p>
            <button onclick="switchTab('discipline-records')" class="text-purple-600 hover:text-purple-700 text-sm font-medium flex items-center gap-1">
                Manage Discipline <i class="fas fa-arrow-right"></i>
            </button>
        </div>
    </div>
    
    <!-- Recent Activities Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Discipline Sessions -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="bg-gray-50 px-6 py-4 border-b">
                <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-gavel text-blue-600"></i> Recent Discipline Sessions
                </h3>
            </div>
            <div class="divide-y">
                @forelse($recentSessions as $session)
                <div class="p-4 hover:bg-gray-50 transition flex items-center justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <p class="font-medium text-gray-800">{{ $session->title }}</p>
                            <span class="text-xs px-2 py-0.5 rounded-full {{ $session->type === 'positive' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ ucfirst($session->type) }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-500">{{ $session->user_name }} • {{ $session->formatted_date ?? date('d/m/Y', strtotime($session->created_at)) }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-green-600 bg-green-50 px-2 py-1 rounded">Completed</span>
                        <button onclick="viewDisciplineRecord({{ $session->id }})" class="text-blue-500 hover:text-blue-700">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button onclick="deleteDisciplineRecord({{ $session->id }})" class="text-red-500 hover:text-red-700">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                @empty
                <div class="p-8 text-center text-gray-500">
                    <i class="fas fa-inbox text-4xl mb-2 opacity-50"></i>
                    <p>No discipline sessions found</p>
                </div>
                @endforelse
            </div>
        </div>
        
        <!-- Recent Permission Requests -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="bg-gray-50 px-6 py-4 border-b">
                <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-envelope-open-text text-green-600"></i> Recent Permission requests
                </h3>
            </div>
            <div class="divide-y">
                @forelse($recentPermissions as $permission)
                <div class="p-4 hover:bg-gray-50 transition">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <p class="font-medium text-gray-800">{{ $permission->user_name }}</p>
                                <span class="text-xs px-2 py-0.5 rounded-full {{ $permission->status === 'approved' ? 'bg-green-100 text-green-700' : ($permission->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                    {{ ucfirst($permission->status) }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-600 mb-1">{{ Str::limit($permission->reason, 100) }}</p>
                            <p class="text-xs text-gray-400">{{ $permission->formatted_date ?? date('d/m/Y', strtotime($permission->created_at)) }}</p>
                        </div>
                        <div class="flex items-center gap-2 ml-4">
                            <button onclick="viewPermission({{ $permission->id }})" class="text-blue-500 hover:text-blue-700">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button onclick="deletePermission({{ $permission->id }})" class="text-red-500 hover:text-red-700">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
                @empty
                <div class="p-8 text-center text-gray-500">
                    <i class="fas fa-inbox text-4xl mb-2 opacity-50"></i>
                    <p>No permission requests found</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<script>
function switchTab(tabName) {
    // Trigger click on the corresponding tab button
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