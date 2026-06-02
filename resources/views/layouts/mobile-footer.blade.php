@auth
    @if(auth()->user()->isSuperAdmin())
        <a href="{{ route('super-admin.dashboard') }}" class="flex flex-col items-center {{ request()->routeIs('super-admin.dashboard') ? 'text-blue-300' : 'text-white' }}">
            <i class="fas fa-tachometer-alt"></i>
            <span class="text-xs mt-1">Home</span>
        </a>
        <a href="{{ route('users.index') }}" class="flex flex-col items-center {{ request()->routeIs('users.*') ? 'text-blue-300' : 'text-white' }}">
            <i class="fas fa-users"></i>
            <span class="text-xs mt-1">Users</span>
        </a>
        <a href="{{ route('music.index') }}" class="flex flex-col items-center {{ request()->routeIs('music.*') ? 'text-blue-300' : 'text-white' }}">
            <i class="fas fa-music"></i>
            <span class="text-xs mt-1">Music</span>
        </a>
        <a href="{{ route('intercession.index') }}" class="flex flex-col items-center {{ request()->routeIs('intercession.*') ? 'text-blue-300' : 'text-white' }}">
            <i class="fas fa-pray"></i>
            <span class="text-xs mt-1">Growth</span>
        </a>
        <a href="{{ route('settings.index') }}" class="flex flex-col items-center {{ request()->routeIs('settings.*') ? 'text-blue-300' : 'text-white' }}">
            <i class="fas fa-cog"></i>
            <span class="text-xs mt-1">Settings</span>
        </a>
    @else
        <a href="{{ route('admin.dashboard') }}" class="flex flex-col items-center {{ request()->routeIs('admin.dashboard') ? 'text-blue-300' : 'text-white' }}">
            <i class="fas fa-tachometer-alt"></i>
            <span class="text-xs mt-1">Home</span>
        </a>
        @if(auth()->user()->canAccess('user-management', 'view'))
        <a href="{{ route('users.index') }}" class="flex flex-col items-center {{ request()->routeIs('users.*') ? 'text-blue-300' : 'text-white' }}">
            <i class="fas fa-users"></i>
            <span class="text-xs mt-1">Users</span>
        </a>
        @endif
        @if(auth()->user()->canAccess('music-ministry', 'view'))
        <a href="{{ route('music.index') }}" class="flex flex-col items-center {{ request()->routeIs('music.*') ? 'text-blue-300' : 'text-white' }}">
            <i class="fas fa-music"></i>
            <span class="text-xs mt-1">Music</span>
        </a>
        @endif
        @if(auth()->user()->canAccess('intercession', 'view'))
        <a href="{{ route('intercession.index') }}" class="flex flex-col items-center {{ request()->routeIs('intercession.*') ? 'text-blue-300' : 'text-white' }}">
            <i class="fas fa-pray"></i>
            <span class="text-xs mt-1">Growth</span>
        </a>
        @endif
    @endif
@else
    <!-- Guest/Not logged in - show login and register -->
    <a href="{{ route('login') }}" class="flex flex-col items-center text-white">
        <i class="fas fa-sign-in-alt"></i>
        <span class="text-xs mt-1">Login</span>
    </a>
    <a href="{{ route('register') }}" class="flex flex-col items-center text-white">
        <i class="fas fa-user-plus"></i>
        <span class="text-xs mt-1">Register</span>
    </a>
@endauth