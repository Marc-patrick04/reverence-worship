@extends('layouts.app')

@section('title', 'Super Admin Dashboard')
@section('page-title', 'Super Admin Dashboard')

@section('content')

<div class="max-w-7xl mx-auto space-y-6">

<!-- Welcome Section with System Overview -->
<div class="bg-gradient-to-r from-blue-600 to-indigo-800 rounded-2xl shadow-xl overflow-hidden">
    <div class="px-8 py-8">
        <div class="flex justify-between items-start">
            <div>
                <h2 class="text-2xl font-bold text-white">
                    Super Admin Dashboard
                </h2>
                <p class="mt-2 text-blue-100">
                    Welcome back, {{ Auth::user()->name }}! Here's your complete system overview.
                </p>
            </div>
            <div class="hidden md:block">
                <div class="bg-white/10 rounded-full px-4 py-2">
                    <i class="fas fa-calendar-alt text-white mr-2"></i>
                    <span class="text-white text-sm">{{ date('l, F j, Y') }}</span>
                </div>
            </div>
        </div>
        
        <!-- System Quick Stats -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mt-6 pt-4 border-t border-white/10">
            <div class="text-center">
                <p class="text-2xl font-bold text-white">{{ $stats['total_users'] ?? 0 }}</p>
                <p class="text-xs text-blue-200">Total Users</p>
            </div>
            <div class="text-center">
                <p class="text-2xl font-bold text-white">{{ $stats['active_users'] ?? 0 }}</p>
                <p class="text-xs text-blue-200">Active Users</p>
            </div>
            <div class="text-center">
                <p class="text-2xl font-bold text-white">{{ $stats['total_roles'] ?? 0 }}</p>
                <p class="text-xs text-blue-200">System Roles</p>
            </div>
            <div class="text-center">
                <p class="text-2xl font-bold text-white">{{ $stats['total_families'] ?? 0 }}</p>
                <p class="text-xs text-blue-200">Families</p>
            </div>
            <div class="text-center">
                <p class="text-2xl font-bold text-white">{{ $stats['system_version'] ?? '1.0' }}</p>
                <p class="text-xs text-blue-200">System Version</p>
            </div>
        </div>
    </div>
</div>

<!-- System Performance Metrics -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
    <!-- Users Growth -->
    <div class="bg-white rounded-2xl shadow-md p-5 hover:shadow-lg transition">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-semibold text-gray-700">Users Growth</h3>
            <i class="fas fa-chart-line text-blue-400"></i>
        </div>
        <div class="flex justify-center">
            <div class="relative w-28 h-28">
                <svg class="w-28 h-28 transform -rotate-90">
                    <circle cx="56" cy="56" r="50" stroke="#e5e7eb" stroke-width="6" fill="none"/>
                    <circle cx="56" cy="56" r="50" stroke="#3b82f6" stroke-width="6" fill="none" 
                            stroke-dasharray="314" stroke-dashoffset="{{ 314 - (314 * (($stats['total_users'] - $stats['last_month_users']) / max($stats['total_users'], 1) * 100)) }}"
                            class="transition-all duration-500"/>
                </svg>
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="text-xl font-bold text-gray-800">{{ $stats['growth_rate'] ?? 0 }}%</span>
                </div>
            </div>
        </div>
        <p class="text-center text-xs text-gray-500 mt-3">
            +{{ $stats['new_users_month'] ?? 0 }} new users this month
        </p>
        <div class="mt-3 pt-2 border-t text-center">
            <span class="text-xs text-green-600">
                <i class="fas fa-arrow-up mr-1"></i> {{ $stats['growth_rate'] ?? 0 }}% from last month
            </span>
        </div>
    </div>

    <!-- System Health -->
    <div class="bg-white rounded-2xl shadow-md p-5 hover:shadow-lg transition">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-semibold text-gray-700">System Health</h3>
            <i class="fas fa-heartbeat text-green-400"></i>
        </div>
        <div class="flex justify-center">
            <div class="relative w-28 h-28">
                <svg class="w-28 h-28 transform -rotate-90">
                    <circle cx="56" cy="56" r="50" stroke="#e5e7eb" stroke-width="6" fill="none"/>
                    <circle cx="56" cy="56" r="50" stroke="#10b981" stroke-width="6" fill="none" 
                            stroke-dasharray="314" stroke-dashoffset="31.4"/>
                </svg>
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="text-xl font-bold text-gray-800">99%</span>
                </div>
            </div>
        </div>
        <p class="text-center text-xs text-gray-500 mt-3">All systems operational</p>
        <div class="mt-3 pt-2 border-t text-center">
            <span class="text-xs text-green-600">Last 30 days: No incidents</span>
        </div>
    </div>

    <!-- Active Sessions -->
    <div class="bg-white rounded-2xl shadow-md p-5 hover:shadow-lg transition">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-semibold text-gray-700">Active Sessions</h3>
            <i class="fas fa-users text-purple-400"></i>
        </div>
        <div class="flex justify-center">
            <div class="relative w-28 h-28">
                <svg class="w-28 h-28 transform -rotate-90">
                    <circle cx="56" cy="56" r="50" stroke="#e5e7eb" stroke-width="6" fill="none"/>
                    <circle cx="56" cy="56" r="50" stroke="#8b5cf6" stroke-width="6" fill="none" 
                            stroke-dasharray="314" stroke-dashoffset="{{ 314 - (314 * ($stats['online_users'] / max($stats['active_users'], 1)) * 100) }}"/>
                </svg>
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="text-xl font-bold text-gray-800">{{ $stats['online_users'] ?? 0 }}</span>
                </div>
            </div>
        </div>
        <p class="text-center text-xs text-gray-500 mt-3">Currently online</p>
        <div class="mt-3 pt-2 border-t text-center">
            <span class="text-xs text-purple-600">{{ round(($stats['online_users'] / max($stats['active_users'], 1)) * 100) ?? 0 }}% of active users</span>
        </div>
    </div>

    <!-- Storage Usage -->
    <div class="bg-white rounded-2xl shadow-md p-5 hover:shadow-lg transition">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-semibold text-gray-700">Storage Usage</h3>
            <i class="fas fa-database text-orange-400"></i>
        </div>
        <div class="flex justify-center">
            <div class="relative w-28 h-28">
                <svg class="w-28 h-28 transform -rotate-90">
                    <circle cx="56" cy="56" r="50" stroke="#e5e7eb" stroke-width="6" fill="none"/>
                    <circle cx="56" cy="56" r="50" stroke="#f59e0b" stroke-width="6" fill="none" 
                            stroke-dasharray="314" stroke-dashoffset="251.2"/>
                </svg>
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="text-xl font-bold text-gray-800">20%</span>
                </div>
            </div>
        </div>
        <p class="text-center text-xs text-gray-500 mt-3">2.4 GB / 12 GB used</p>
        <div class="mt-3 pt-2 border-t text-center">
            <span class="text-xs text-orange-600">80% available</span>
        </div>
    </div>
</div>

<!-- Department Performance & Module Stats -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    
    <!-- Department Performance -->
    <div class="bg-white rounded-2xl shadow-md overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 border-b">
            <h3 class="font-semibold text-gray-800">Department Performance</h3>
            <p class="text-xs text-gray-500 mt-0.5">Activity metrics by department</p>
        </div>
        <div class="p-5 space-y-4">
            <div>
                <div class="flex justify-between text-sm mb-1">
                    <span>Music & Evangelism</span>
                    <span class="font-medium">{{ $stats['music_activity'] ?? 85 }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-purple-600 h-2 rounded-full" style="width: {{ $stats['music_activity'] ?? 85 }}%"></div>
                </div>
            </div>
            <div>
                <div class="flex justify-between text-sm mb-1">
                    <span>Intercession & Spiritual Growth</span>
                    <span class="font-medium">{{ $stats['intercession_activity'] ?? 78 }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $stats['intercession_activity'] ?? 78 }}%"></div>
                </div>
            </div>
            <div>
                <div class="flex justify-between text-sm mb-1">
                    <span>Social Fellowship</span>
                    <span class="font-medium">{{ $stats['social_activity'] ?? 72 }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-green-600 h-2 rounded-full" style="width: {{ $stats['social_activity'] ?? 72 }}%"></div>
                </div>
            </div>
            <div>
                <div class="flex justify-between text-sm mb-1">
                    <span>Discipline Management</span>
                    <span class="font-medium">{{ $stats['discipline_activity'] ?? 68 }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-red-600 h-2 rounded-full" style="width: {{ $stats['discipline_activity'] ?? 68 }}%"></div>
                </div>
            </div>
            <div>
                <div class="flex justify-between text-sm mb-1">
                    <span>Financial Management</span>
                    <span class="font-medium">{{ $stats['finance_activity'] ?? 62 }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-yellow-600 h-2 rounded-full" style="width: {{ $stats['finance_activity'] ?? 62 }}%"></div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- System-wide Statistics -->
    <div class="bg-white rounded-2xl shadow-md overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 border-b">
            <h3 class="font-semibold text-gray-800">System-wide Statistics</h3>
            <p class="text-xs text-gray-500 mt-0.5">Key metrics across all modules</p>
        </div>
        <div class="p-5">
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-blue-50 rounded-xl p-3 text-center">
                    <p class="text-2xl font-bold text-blue-600">{{ $stats['total_forms'] ?? 0 }}</p>
                    <p class="text-xs text-gray-600">Spiritual Forms</p>
                </div>
                <div class="bg-green-50 rounded-xl p-3 text-center">
                    <p class="text-2xl font-bold text-green-600">{{ $stats['total_devotions'] ?? 0 }}</p>
                    <p class="text-xs text-gray-600">Daily Devotions</p>
                </div>
                <div class="bg-purple-50 rounded-xl p-3 text-center">
                    <p class="text-2xl font-bold text-purple-600">{{ $stats['total_songs'] ?? 0 }}</p>
                    <p class="text-xs text-gray-600">Songs in Library</p>
                </div>
                <div class="bg-orange-50 rounded-xl p-3 text-center">
                    <p class="text-2xl font-bold text-orange-600">{{ $stats['total_playlists'] ?? 0 }}</p>
                    <p class="text-xs text-gray-600">Music Playlists</p>
                </div>
                <div class="bg-red-50 rounded-xl p-3 text-center">
                    <p class="text-2xl font-bold text-red-600">{{ $stats['total_discipline'] ?? 0 }}</p>
                    <p class="text-xs text-gray-600">Discipline Records</p>
                </div>
                <div class="bg-teal-50 rounded-xl p-3 text-center">
                    <p class="text-2xl font-bold text-teal-600">{{ $stats['total_permissions'] ?? 0 }}</p>
                    <p class="text-xs text-gray-600">Permission Requests</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Financial Overview -->
<div class="bg-white rounded-2xl shadow-md overflow-hidden">
    <div class="bg-gray-50 px-6 py-4 border-b">
        <h3 class="font-semibold text-gray-800">Financial Overview</h3>
        <p class="text-xs text-gray-500 mt-0.5">Contributions, sponsors, and expenses summary</p>
    </div>
    <div class="p-5">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-blue-50 rounded-xl p-4 text-center">
                <p class="text-xs text-gray-500">Total Expected</p>
                <p class="text-xl font-bold text-blue-600">{{ number_format($stats['total_expected'] ?? 0) }} RWF</p>
            </div>
            <div class="bg-green-50 rounded-xl p-4 text-center">
                <p class="text-xs text-gray-500">Total Collected</p>
                <p class="text-xl font-bold text-green-600">{{ number_format($stats['total_collected'] ?? 0) }} RWF</p>
            </div>
            <div class="bg-yellow-50 rounded-xl p-4 text-center">
                <p class="text-xs text-gray-500">Collection Rate</p>
                <p class="text-xl font-bold text-yellow-600">{{ $stats['collection_rate'] ?? 0 }}%</p>
                <div class="w-full bg-gray-200 rounded-full h-1.5 mt-2">
                    <div class="bg-yellow-500 h-1.5 rounded-full" style="width: {{ $stats['collection_rate'] ?? 0 }}%"></div>
                </div>
            </div>
            <div class="bg-red-50 rounded-xl p-4 text-center">
                <p class="text-xs text-gray-500">Total Expenses</p>
                <p class="text-xl font-bold text-red-600">{{ number_format($stats['total_expenses'] ?? 0) }} RWF</p>
            </div>
        </div>
    </div>
</div>

<!-- Recent System Activities -->
<div class="bg-white rounded-2xl shadow-md overflow-hidden">
    <div class="bg-gray-50 px-6 py-4 border-b">
        <div class="flex justify-between items-center">
            <div>
                <h3 class="font-semibold text-gray-800">Recent System Activities</h3>
                <p class="text-xs text-gray-500 mt-0.5">Latest actions across the platform</p>
            </div>
            <button onclick="loadAllActivities()" class="text-xs text-blue-600 hover:text-blue-800">View All</button>
        </div>
    </div>
    <div class="divide-y divide-gray-100 max-h-96 overflow-y-auto" id="activitiesList">
        @forelse($recentActivities ?? [] as $activity)
        <div class="p-4 hover:bg-gray-50 transition">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $activity->icon_bg ?? 'bg-gray-100' }}">
                    <i class="{{ $activity->icon ?? 'fas fa-bell' }} {{ $activity->icon_color ?? 'text-gray-500' }} text-sm"></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-800">{{ $activity->description }}</p>
                    <p class="text-xs text-gray-400">{{ $activity->created_at ? \Carbon\Carbon::parse($activity->created_at)->diffForHumans() : 'Just now' }}</p>
                </div>
                @if($activity->module)
                <span class="text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-600">{{ $activity->module }}</span>
                @endif
            </div>
        </div>
        @empty
        <div class="p-8 text-center text-gray-500">
            <i class="fas fa-inbox text-3xl mb-2 text-gray-300"></i>
            <p>No recent activities found</p>
        </div>
        @endforelse
    </div>
</div>

<!-- Quick Admin Actions -->
<div class="bg-white rounded-2xl shadow-md overflow-hidden">
    <div class="bg-gray-50 px-6 py-4 border-b">
        <h3 class="font-semibold text-gray-800">Admin Quick Actions</h3>
        <p class="text-xs text-gray-500 mt-0.5">Common administrative tasks</p>
    </div>
    <div class="p-5">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <a href="{{ route('users.index') }}" class="flex flex-col items-center p-3 rounded-xl hover:bg-gray-50 transition group">
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mb-2 group-hover:bg-blue-200 transition">
                    <i class="fas fa-user-plus text-blue-600 text-lg"></i>
                </div>
                <p class="text-xs font-medium text-gray-700">Add User</p>
            </a>
            <a href="{{ route('roles.index') }}" class="flex flex-col items-center p-3 rounded-xl hover:bg-gray-50 transition group">
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center mb-2 group-hover:bg-purple-200 transition">
                    <i class="fas fa-tags text-purple-600 text-lg"></i>
                </div>
                <p class="text-xs font-medium text-gray-700">Manage Roles</p>
            </a>
            <a href="{{ route('permission-manager.index') }}" class="flex flex-col items-center p-3 rounded-xl hover:bg-gray-50 transition group">
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mb-2 group-hover:bg-green-200 transition">
                    <i class="fas fa-shield-alt text-green-600 text-lg"></i>
                </div>
                <p class="text-xs font-medium text-gray-700">Permissions</p>
            </a>
            <a href="{{ route('settings.index') }}" class="flex flex-col items-center p-3 rounded-xl hover:bg-gray-50 transition group">
                <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center mb-2 group-hover:bg-gray-200 transition">
                    <i class="fas fa-cog text-gray-600 text-lg"></i>
                </div>
                <p class="text-xs font-medium text-gray-700">System Settings</p>
            </a>
        </div>
    </div>
</div>

</div>

<script>
function loadAllActivities() {
    window.location.href = '/logs/activity';
}

// Auto-refresh dashboard every 60 seconds
setInterval(function() {
    location.reload();
}, 60000);
</script>

@endsection