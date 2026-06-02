@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

<div class="max-w-7xl mx-auto space-y-5">

<!-- Welcome -->
<div class="bg-white rounded-xl shadow p-5">
    <h2 class="text-xl font-semibold text-gray-900">
        Welcome back, {{ Auth::user()->name }}!
    </h2>
    <p class="mt-1 text-sm text-gray-600">
        You're logged in as a <span class="text-blue-600 font-medium">admin</span>.
        Here's your account overview.
    </p>
</div>

<!-- Overview Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4">
    <div class="bg-white rounded-xl shadow p-4">
        <div class="flex items-center gap-3">
            <div class="bg-blue-500 text-white p-3 rounded-xl">
                <i class="fas fa-user text-sm"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500">Account Status</p>
                <p class="text-base font-semibold">{{ Auth::user()->is_active ? 'Active' : 'Inactive' }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow p-4">
        <div class="flex items-center gap-3">
            <div class="bg-green-500 text-white p-3 rounded-xl">
                <i class="fas fa-calendar text-sm"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500">Member Since</p>
                <p class="text-base font-semibold">{{ Auth::user()->created_at ? date('d/m/Y', strtotime(Auth::user()->created_at)) : 'N/A' }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow p-4">
        <div class="flex items-center gap-3">
            <div class="bg-purple-500 text-white p-3 rounded-xl">
                <i class="fas fa-clock text-sm"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500">Last Login</p>
                <p class="text-base font-semibold">{{ Auth::user()->last_login_at ? date('d/m/Y', strtotime(Auth::user()->last_login_at)) : 'N/A' }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow p-4">
        <div class="flex items-center gap-3">
            <div class="bg-indigo-500 text-white p-3 rounded-xl">
                <i class="fas fa-shield-alt text-sm"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500">Role</p>
                <p class="text-base font-semibold">{{ Auth::user()->roles->first()->display_name ?? 'Admin' }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Performance Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4">
    <div class="bg-white rounded-xl shadow p-4">
        <h3 class="text-sm font-semibold mb-3">Discipline Performance</h3>
        <div class="flex justify-center">
            <div class="w-20 h-20 rounded-full border-[4px] border-green-500 flex items-center justify-center">
                <span class="text-xl font-bold">{{ $stats['discipline_rate'] ?? 100 }}%</span>
            </div>
        </div>
        <p class="text-center text-xs text-gray-500 mt-2">{{ $stats['good_records'] ?? 0 }} good / {{ $stats['total_records'] ?? 0 }} total records</p>
    </div>

    <div class="bg-white rounded-xl shadow p-4">
        <h3 class="text-sm font-semibold mb-3">Attendance Performance</h3>
        <div class="flex justify-center">
            <div class="w-20 h-20 rounded-full border-[4px] border-blue-500 flex items-center justify-center">
                <span class="text-xl font-bold">{{ $stats['attendance_rate'] ?? 97 }}%</span>
            </div>
        </div>
        <p class="text-center text-xs text-gray-500 mt-2">Attendance Rate</p>
        <p class="text-center text-xs text-gray-400">Feb 2026 – May 2026</p>
    </div>

    <div class="bg-white rounded-xl shadow p-4">
        <h3 class="text-sm font-semibold mb-3">Communication Performance</h3>
        <div class="flex justify-center">
            <div class="w-20 h-20 rounded-full border-[4px] border-purple-500 flex items-center justify-center">
                <span class="text-xl font-bold">{{ $stats['communication_rate'] ?? 94 }}%</span>
            </div>
        </div>
        <p class="text-center text-xs text-gray-500 mt-2">Communication Rate</p>
        <p class="text-center text-xs text-gray-400">Excellent – Feb 2026 – May 2026</p>
    </div>

    <div class="bg-white rounded-xl shadow p-4">
        <h3 class="text-sm font-semibold mb-3">Contribution Progress</h3>
        <div class="flex justify-center">
            <div class="w-20 h-20 rounded-full border-[4px] border-yellow-500 flex items-center justify-center">
                <span class="text-xl font-bold">{{ $stats['contribution_rate'] ?? 42 }}%</span>
            </div>
        </div>
        <p class="text-center text-xs text-gray-500 mt-2">Contribution Rate</p>
        <p class="text-center text-xs font-medium text-gray-700 mt-1">{{ $stats['contributed_amount'] ?? 50000 }} / {{ $stats['target_amount'] ?? 120000 }} FRw</p>
        <div class="mt-2 w-full bg-gray-200 rounded-full h-1">
            <div class="bg-yellow-500 h-1 rounded-full" style="width: {{ $stats['contribution_rate'] ?? 42 }}%"></div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="bg-white rounded-xl shadow p-5">
    <h2 class="text-base font-semibold mb-4">Quick Actions</h2>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
        <a href="{{ route('social-fellowship.index') }}" class="border rounded-lg p-3 hover:bg-gray-50 transition text-center">
            <i class="fas fa-users text-xl mb-2 text-gray-500"></i>
            <h3 class="text-sm font-medium">My Family</h3>
            <p class="text-xs text-gray-400">Manage family members</p>
        </a>

        <a href="{{ route('financial.my-contributions') }}" class="border rounded-lg p-3 hover:bg-gray-50 transition text-center">
            <i class="fas fa-hand-holding-usd text-xl mb-2 text-gray-500"></i>
            <h3 class="text-sm font-medium">My Contributions</h3>
            <p class="text-xs text-gray-400">Track your contributions</p>
        </a>

        <a href="{{ route('users.index') }}" class="border rounded-lg p-3 hover:bg-gray-50 transition text-center">
            <i class="fas fa-users-cog text-xl mb-2 text-gray-500"></i>
            <h3 class="text-sm font-medium">User Management</h3>
            <p class="text-xs text-gray-400">Manage team members</p>
        </a>

        <a href="{{ route('music.index') }}" class="border rounded-lg p-3 hover:bg-gray-50 transition text-center">
            <i class="fas fa-music text-xl mb-2 text-gray-500"></i>
            <h3 class="text-sm font-medium">Music & Evangelism</h3>
            <p class="text-xs text-gray-400">Department management</p>
        </a>

        <a href="{{ route('intercession.index') }}" class="border rounded-lg p-3 hover:bg-gray-50 transition text-center">
            <i class="fas fa-pray text-xl mb-2 text-gray-500"></i>
            <h3 class="text-sm font-medium">Intercession & Growth</h3>
            <p class="text-xs text-gray-400">Spiritual development</p>
        </a>

        <a href="{{ route('social-fellowship.index') }}" class="border rounded-lg p-3 hover:bg-gray-50 transition text-center">
            <i class="fas fa-hand-holding-heart text-xl mb-2 text-gray-500"></i>
            <h3 class="text-sm font-medium">Social Fellowship</h3>
            <p class="text-xs text-gray-400">Community events</p>
        </a>

        <a href="{{ route('discipline.index') }}" class="border rounded-lg p-3 hover:bg-gray-50 transition text-center">
            <i class="fas fa-gavel text-xl mb-2 text-gray-500"></i>
            <h3 class="text-sm font-medium">Discipline Management</h3>
            <p class="text-xs text-gray-400">Track member discipline</p>
        </a>

        <a href="#" class="border rounded-lg p-3 hover:bg-gray-50 transition text-center">
            <i class="fas fa-user-clock text-xl mb-2 text-gray-500"></i>
            <h3 class="text-sm font-medium">Probation Management</h3>
            <p class="text-xs text-gray-400">Manage probation members</p>
        </a>

        <a href="{{ route('finance.index') }}" class="border rounded-lg p-3 hover:bg-gray-50 transition text-center">
            <i class="fas fa-chart-line text-xl mb-2 text-gray-500"></i>
            <h3 class="text-sm font-medium">Financial Management</h3>
            <p class="text-xs text-gray-400">Manage finances</p>
        </a>

        <a href="{{ route('announcements.index') }}" class="border rounded-lg p-3 hover:bg-gray-50 transition text-center">
            <i class="fas fa-bullhorn text-xl mb-2 text-gray-500"></i>
            <h3 class="text-sm font-medium">Admin Announcements</h3>
            <p class="text-xs text-gray-400">Create announcements</p>
        </a>

        <a href="{{ route('settings.index') }}" class="border rounded-lg p-3 hover:bg-gray-50 transition text-center">
            <i class="fas fa-cog text-xl mb-2 text-gray-500"></i>
            <h3 class="text-sm font-medium">Settings</h3>
            <p class="text-xs text-gray-400">System configuration</p>
        </a>

        <a href="{{ route('reports.index') }}" class="border rounded-lg p-3 hover:bg-gray-50 transition text-center">
            <i class="fas fa-chart-bar text-xl mb-2 text-gray-500"></i>
            <h3 class="text-sm font-medium">Reports</h3>
            <p class="text-xs text-gray-400">View analytics</p>
        </a>
    </div>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4">
    <div class="bg-white rounded-xl shadow p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wide">Total Users</p>
                <p class="text-xl font-bold text-gray-800">{{ $stats['total_users'] ?? 0 }}</p>
            </div>
            <i class="fas fa-users text-lg text-gray-400"></i>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wide">Active Users</p>
                <p class="text-xl font-bold text-gray-800">{{ $stats['active_users'] ?? 0 }}</p>
            </div>
            <i class="fas fa-user-check text-lg text-gray-400"></i>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wide">Total Roles</p>
                <p class="text-xl font-bold text-gray-800">{{ $stats['total_roles'] ?? 0 }}</p>
            </div>
            <i class="fas fa-tags text-lg text-gray-400"></i>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wide">Today's Logins</p>
                <p class="text-xl font-bold text-gray-800">{{ $stats['today_logins'] ?? 0 }}</p>
            </div>
            <i class="fas fa-sign-in-alt text-lg text-gray-400"></i>
        </div>
    </div>
</div>

</div>

@endsection