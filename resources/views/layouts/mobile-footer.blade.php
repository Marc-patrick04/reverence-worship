@auth
    @if(auth()->user()->isSuperAdmin())
        <a href="{{ route('super-admin.dashboard') }}" class="mobile-footer-item {{ request()->routeIs('super-admin.dashboard') ? 'active' : '' }}">
            <i class="fas fa-tachometer-alt"></i>
            <span class="text-xs mt-1">Home</span>
        </a>
        <a href="{{ route('users.index') }}" class="mobile-footer-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
            <i class="fas fa-users"></i>
            <span class="text-xs mt-1">Users</span>
        </a>
        <a href="{{ route('music.index') }}" class="mobile-footer-item {{ request()->routeIs('music.*') ? 'active' : '' }}">
            <i class="fas fa-music"></i>
            <span class="text-xs mt-1">Music</span>
        </a>
        <a href="{{ route('intercession.index') }}" class="mobile-footer-item {{ request()->routeIs('intercession.*') ? 'active' : '' }}">
            <i class="fas fa-pray"></i>
            <span class="text-xs mt-1">Growth</span>
        </a>
        <a href="{{ route('settings.index') }}" class="mobile-footer-item {{ request()->routeIs('settings.*') ? 'active' : '' }}">
            <i class="fas fa-cog"></i>
            <span class="text-xs mt-1">Settings</span>
        </a>
    @else
        <a href="{{ route('admin.dashboard') }}" class="mobile-footer-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="fas fa-tachometer-alt"></i>
            <span class="text-xs mt-1">Home</span>
        </a>
        @if(auth()->user()->canAccess('user-management', 'view'))
        <a href="{{ route('users.index') }}" class="mobile-footer-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
            <i class="fas fa-users"></i>
            <span class="text-xs mt-1">Users</span>
        </a>
        @endif
        @if(auth()->user()->canAccess('music-ministry', 'view'))
        <a href="{{ route('music.index') }}" class="mobile-footer-item {{ request()->routeIs('music.*') ? 'active' : '' }}">
            <i class="fas fa-music"></i>
            <span class="text-xs mt-1">Music</span>
        </a>
        @endif
        @if(auth()->user()->canAccess('intercession', 'view'))
        <a href="{{ route('intercession.index') }}" class="mobile-footer-item {{ request()->routeIs('intercession.*') ? 'active' : '' }}">
            <i class="fas fa-pray"></i>
            <span class="text-xs mt-1">Growth</span>
        </a>
        @endif
    @endif
@else
    <!-- Guest/Not logged in - show login and register -->
    <a href="{{ route('login') }}" class="mobile-footer-item {{ request()->routeIs('login') ? 'active' : '' }}">
        <i class="fas fa-sign-in-alt"></i>
        <span class="text-xs mt-1">Login</span>
    </a>
    <a href="{{ route('register') }}" class="mobile-footer-item {{ request()->routeIs('register') ? 'active' : '' }}">
        <i class="fas fa-user-plus"></i>
        <span class="text-xs mt-1">Register</span>
    </a>
@endauth
