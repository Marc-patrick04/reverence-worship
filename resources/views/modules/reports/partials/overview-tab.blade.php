<div>
    <div class="mb-6">
        <h3 class="text-lg font-semibold text-gray-800">System Overview</h3>
        <p class="text-sm text-gray-500">Key metrics across all modules</p>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl shadow-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm">Action Plans</p>
                    <p class="text-2xl font-bold">{{ $stats['total_action_plans'] ?? 0 }}</p>
                </div>
                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                    <i class="fas fa-tasks text-white"></i>
                </div>
            </div>
            <div class="mt-2 text-xs text-blue-100">
                {{ $stats['completed_action_plans'] ?? 0 }} completed
            </div>
        </div>
        
        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl shadow-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm">Discipline Records</p>
                    <p class="text-2xl font-bold">{{ $stats['total_discipline'] ?? 0 }}</p>
                </div>
                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                    <i class="fas fa-gavel text-white"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl shadow-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm">Permission Requests</p>
                    <p class="text-2xl font-bold">{{ $stats['total_permissions'] ?? 0 }}</p>
                </div>
                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                    <i class="fas fa-envelope text-white"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-xl shadow-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm">Events</p>
                    <p class="text-2xl font-bold">{{ $stats['total_events'] ?? 0 }}</p>
                </div>
                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                    <i class="fas fa-calendar text-white"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-gradient-to-r from-teal-500 to-teal-600 rounded-xl shadow-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-teal-100 text-sm">Total Users</p>
                    <p class="text-2xl font-bold">{{ $stats['total_users'] ?? 0 }}</p>
                </div>
                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                    <i class="fas fa-users text-white"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-xl shadow-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-yellow-100 text-sm">Attendance Records</p>
                    <p class="text-2xl font-bold">{{ $stats['total_attendance'] ?? 0 }}</p>
                </div>
                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                    <i class="fas fa-calendar-check text-white"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-gradient-to-r from-pink-500 to-pink-600 rounded-xl shadow-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-pink-100 text-sm">Forms Submitted</p>
                    <p class="text-2xl font-bold">{{ $stats['total_forms'] ?? 0 }}</p>
                </div>
                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                    <i class="fas fa-file-alt text-white"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-gradient-to-r from-indigo-500 to-indigo-600 rounded-xl shadow-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-indigo-100 text-sm">Financial Overview</p>
                    <p class="text-2xl font-bold">RWF 0</p>
                </div>
                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                    <i class="fas fa-chart-line text-white"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="bg-gray-50 rounded-lg p-4">
        <h4 class="font-semibold text-gray-700 mb-3">Quick Actions</h4>
        <div class="flex flex-wrap gap-3">
            <button onclick="window.location.href='/reports?tab=events'" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
                <i class="fas fa-calendar-plus mr-2"></i> Create Event Report
            </button>
            <button onclick="window.location.href='/reports?tab=financial'" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm">
                <i class="fas fa-chart-pie mr-2"></i> Financial Summary
            </button>
            <button onclick="window.location.href='/reports?tab=action-plans'" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-sm">
                <i class="fas fa-tasks mr-2"></i> Action Plans Report
            </button>
        </div>
    </div>
</div>