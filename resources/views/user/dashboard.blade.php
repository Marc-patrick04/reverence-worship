@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">

<!-- Welcome Card -->
<div class="bg-gradient-to-r from-blue-600 to-indigo-800 rounded-2xl shadow-lg p-6 mb-6 text-white">
    <h2 class="text-2xl font-bold">Welcome back, {{ Auth::user()->name }}!</h2>
    <p class="text-blue-100 mt-1">{{ date('l, F j, Y') }}</p>
    <p class="text-blue-100 text-sm mt-2">You have access to {{ count($accessiblePages) }} modules</p>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-5 mb-6">
    <div class="bg-white rounded-xl shadow p-5 border-l-4 border-blue-500">
        <p class="text-gray-500 text-sm">Member Since</p>
        <p class="text-2xl font-bold">{{ $personalStats['member_since'] ?? 'N/A' }}</p>
    </div>
    <div class="bg-white rounded-xl shadow p-5 border-l-4 border-green-500">
        <p class="text-gray-500 text-sm">Total Logins</p>
        <p class="text-2xl font-bold">{{ $personalStats['total_logins'] ?? 0 }}</p>
    </div>
    <div class="bg-white rounded-xl shadow p-5 border-l-4 border-purple-500">
        <p class="text-gray-500 text-sm">Your Role</p>
        <p class="text-2xl font-bold">{{ $personalStats['roles'] ?? 'Member' }}</p>
    </div>
    <div class="bg-white rounded-xl shadow p-5 border-l-4 border-orange-500">
        <p class="text-gray-500 text-sm">Modules Access</p>
        <p class="text-2xl font-bold">{{ count($accessiblePages) }}</p>
    </div>
</div>

<!-- My Modules Section - Show all pages user has access to -->
<div class="bg-white rounded-xl shadow mb-6">
    <div class="border-b px-5 py-3">
        <h3 class="font-semibold text-gray-800">My Modules</h3>
        <p class="text-xs text-gray-500">Modules you have permission to access</p>
    </div>
    <div class="p-5">
        @if(count($accessiblePages) > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($accessiblePages as $page)
            @php
                $route = $page->route ?? '#';
                $icon = $page->icon ?? 'fa-folder';
                $color = $page->name == 'user-management' ? 'blue' : 
                         ($page->name == 'finance' ? 'green' : 
                         ($page->name == 'music-ministry' ? 'purple' : 
                         ($page->name == 'family' ? 'pink' : 
                         ($page->name == 'announcements' ? 'orange' : 
                         ($page->name == 'reports' ? 'indigo' : 'gray')))));
            @endphp
            
            @if($page->name == 'user-management')
            <a href="{{ route('users.index') }}" class="block p-4 border rounded-lg hover:shadow-md transition hover:border-blue-300">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-{{ $color }}-100 rounded-lg flex items-center justify-center">
                        <i class="fas {{ $icon }} text-{{ $color }}-600 text-lg"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-800 text-sm">{{ $page->display_name }}</h4>
                        <p class="text-xs text-gray-400">Click to access</p>
                    </div>
                </div>
            </a>
            @elseif($page->name == 'music-ministry')
            <a href="{{ route('music.index') }}" class="block p-4 border rounded-lg hover:shadow-md transition hover:border-purple-300">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-{{ $color }}-100 rounded-lg flex items-center justify-center">
                        <i class="fas {{ $icon }} text-{{ $color }}-600 text-lg"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-800 text-sm">{{ $page->display_name }}</h4>
                        <p class="text-xs text-gray-400">Click to access</p>
                    </div>
                </div>
            </a>
            @elseif($page->name == 'intercession')
            <a href="{{ route('intercession.index') }}" class="block p-4 border rounded-lg hover:shadow-md transition hover:border-blue-300">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-{{ $color }}-100 rounded-lg flex items-center justify-center">
                        <i class="fas {{ $icon }} text-{{ $color }}-600 text-lg"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-800 text-sm">{{ $page->display_name }}</h4>
                        <p class="text-xs text-gray-400">Click to access</p>
                    </div>
                </div>
            </a>
            @elseif($page->name == 'social-fellowship')
            <a href="{{ route('social-fellowship.index') }}" class="block p-4 border rounded-lg hover:shadow-md transition hover:border-green-300">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-{{ $color }}-100 rounded-lg flex items-center justify-center">
                        <i class="fas {{ $icon }} text-{{ $color }}-600 text-lg"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-800 text-sm">{{ $page->display_name }}</h4>
                        <p class="text-xs text-gray-400">Click to access</p>
                    </div>
                </div>
            </a>
            @elseif($page->name == 'discipline')
            <a href="{{ route('discipline.index') }}" class="block p-4 border rounded-lg hover:shadow-md transition hover:border-red-300">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-{{ $color }}-100 rounded-lg flex items-center justify-center">
                        <i class="fas {{ $icon }} text-{{ $color }}-600 text-lg"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-800 text-sm">{{ $page->display_name }}</h4>
                        <p class="text-xs text-gray-400">Click to access</p>
                    </div>
                </div>
            </a>
            @elseif($page->name == 'finance')
            <a href="{{ route('finance.index') }}" class="block p-4 border rounded-lg hover:shadow-md transition hover:border-green-300">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-{{ $color }}-100 rounded-lg flex items-center justify-center">
                        <i class="fas {{ $icon }} text-{{ $color }}-600 text-lg"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-800 text-sm">{{ $page->display_name }}</h4>
                        <p class="text-xs text-gray-400">Click to access</p>
                    </div>
                </div>
            </a>
            @elseif($page->name == 'family')
            <a href="{{ route('family.index') }}" class="block p-4 border rounded-lg hover:shadow-md transition hover:border-pink-300">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-{{ $color }}-100 rounded-lg flex items-center justify-center">
                        <i class="fas {{ $icon }} text-{{ $color }}-600 text-lg"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-800 text-sm">{{ $page->display_name }}</h4>
                        <p class="text-xs text-gray-400">Click to access</p>
                    </div>
                </div>
            </a>
            @elseif($page->name == 'announcements')
            <a href="{{ route('announcements.index') }}" class="block p-4 border rounded-lg hover:shadow-md transition hover:border-orange-300">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-{{ $color }}-100 rounded-lg flex items-center justify-center">
                        <i class="fas {{ $icon }} text-{{ $color }}-600 text-lg"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-800 text-sm">{{ $page->display_name }}</h4>
                        <p class="text-xs text-gray-400">Click to access</p>
                    </div>
                </div>
            </a>
            @elseif($page->name == 'reports')
            <a href="{{ route('reports.index') }}" class="block p-4 border rounded-lg hover:shadow-md transition hover:border-indigo-300">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-{{ $color }}-100 rounded-lg flex items-center justify-center">
                        <i class="fas {{ $icon }} text-{{ $color }}-600 text-lg"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-800 text-sm">{{ $page->display_name }}</h4>
                        <p class="text-xs text-gray-400">Click to access</p>
                    </div>
                </div>
            </a>
            @else
            <a href="{{ $route }}" class="block p-4 border rounded-lg hover:shadow-md transition">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                        <i class="fas {{ $icon }} text-gray-600 text-lg"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-800 text-sm">{{ $page->display_name }}</h4>
                        <p class="text-xs text-gray-400">Click to access</p>
                    </div>
                </div>
            </a>
            @endif
            @endforeach
        </div>
        @else
        <div class="text-center py-8">
            <i class="fas fa-lock text-4xl text-gray-300 mb-3"></i>
            <p class="text-gray-500">You don't have access to any modules yet.</p>
            <p class="text-sm text-gray-400 mt-1">Contact your administrator to grant permissions.</p>
        </div>
        @endif
    </div>
</div>

<!-- Quick Access Modules (Same as My Modules, but limited to 4) -->
@if(count($quickLinks) > 0)
<div class="bg-white rounded-xl shadow mb-6">
    <div class="border-b px-5 py-3">
        <h3 class="font-semibold text-gray-800">Quick Access</h3>
        <p class="text-xs text-gray-500">Frequently used modules</p>
    </div>
    <div class="p-5">
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            @foreach(array_slice($quickLinks, 0, 8) as $link)
            <a href="{{ $link['route'] }}" class="flex flex-col items-center p-3 rounded-lg hover:bg-gray-50 transition">
                <div class="w-12 h-12 bg-{{ $link['color'] }}-100 rounded-xl flex items-center justify-center">
                    <i class="fas {{ $link['icon'] }} text-{{ $link['color'] }}-600 text-xl"></i>
                </div>
                <span class="text-xs font-medium text-gray-700 mt-2 text-center">{{ $link['name'] }}</span>
            </a>
            @endforeach
        </div>
    </div>
</div>
@endif

<!-- My Contribution Card (if user has finance access) -->
@if(isset($stats['my_contributions']) && $stats['my_contributions'] > 0)
<div class="bg-white rounded-xl shadow mb-6">
    <div class="border-b px-5 py-3">
        <h3 class="font-semibold text-gray-800">My Contributions</h3>
    </div>
    <div class="p-5">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <p class="text-gray-500 text-sm">Expected</p>
                <p class="text-2xl font-bold text-blue-600">{{ number_format($stats['my_contributions']) }} RWF</p>
            </div>
            <div>
                <p class="text-gray-500 text-sm">Paid</p>
                <p class="text-2xl font-bold text-green-600">{{ number_format($stats['my_payments']) }} RWF</p>
            </div>
        </div>
        <div class="mt-3">
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $stats['payment_progress'] }}%"></div>
            </div>
            <p class="text-right text-sm text-gray-600 mt-1">{{ $stats['payment_progress'] }}% completed</p>
        </div>
    </div>
</div>
@endif

<!-- Recent Activities -->
<div class="bg-white rounded-xl shadow">
    <div class="border-b px-5 py-3">
        <h3 class="font-semibold text-gray-800">Recent Activities</h3>
    </div>
    <div class="divide-y max-h-80 overflow-y-auto">
        @forelse($recentActivities as $activity)
        <div class="p-4 flex items-center gap-3">
            <div class="w-8 h-8 rounded-full {{ $activity->icon_bg ?? 'bg-gray-100' }} flex items-center justify-center">
                <i class="{{ $activity->icon ?? 'fas fa-bell' }} {{ $activity->icon_color ?? 'text-gray-500' }} text-sm"></i>
            </div>
            <div class="flex-1">
                <p class="text-sm text-gray-800">{{ $activity->description }}</p>
                <p class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($activity->created_at)->diffForHumans() }}</p>
            </div>
        </div>
        @empty
        <div class="p-8 text-center text-gray-400">
            <i class="fas fa-inbox text-3xl mb-2"></i>
            <p>No recent activities</p>
        </div>
        @endforelse
    </div>
</div>

</div>
@endsection