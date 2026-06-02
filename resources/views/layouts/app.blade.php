<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reverence Worship Team - @yield('title')</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: #f3f4f6;
        }
        
        /* Desktop Sidebar - White Background */
        .sidebar {
            background: white;
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            width: 280px;
            z-index: 1000;
            overflow-y: auto;
            transition: all 0.3s ease;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.05);
            border-right: 1px solid #e5e7eb;
        }
        
        /* Collapsed Sidebar - Only Icons */
        .sidebar.collapsed {
            width: 80px;
        }
        
        .sidebar.collapsed .sidebar-logo-text,
        .sidebar.collapsed .nav-item span,
        .sidebar.collapsed .user-info-text {
            display: none;
        }
        
        .sidebar.collapsed .nav-item {
            justify-content: center;
            padding: 12px;
        }
        
        .sidebar.collapsed .nav-item i {
            margin: 0;
            font-size: 20px;
        }
        
        .sidebar.collapsed .logo-section {
            padding: 16px 0;
        }
        
        .sidebar.collapsed .logo-section img {
            width: 40px;
            height: 40px;
        }
        
        .sidebar.collapsed .user-info-footer {
            justify-content: center;
            padding: 12px 0;
        }
        
        .sidebar.collapsed .user-info-footer .flex-1 {
            display: none;
        }
        
        .sidebar::-webkit-scrollbar {
            width: 5px;
        }
        
        .sidebar::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        .sidebar::-webkit-scrollbar-thumb {
            background: #3b82f6;
            border-radius: 5px;
        }
        
        .main-content {
            margin-left: 280px;
            min-height: 100vh;
            transition: all 0.3s ease;
        }
        
        .main-content.expanded {
            margin-left: 80px;
        }
        
        /* Top Header - White Background */
        .top-header {
            background: white;
            border-bottom: 1px solid #e5e7eb;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }
        
        /* Sidebar Toggle Button */
        .sidebar-toggle {
            cursor: pointer;
            padding: 8px 12px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .sidebar-toggle:hover {
            background: #f3f4f6;
        }
        
        /* User Dropdown Menu */
        .user-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            margin-top: 8px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
            min-width: 220px;
            z-index: 50;
            overflow: hidden;
            border: 1px solid #e5e7eb;
        }
        
        .user-dropdown-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 16px;
            transition: all 0.2s ease;
            color: #374151;
        }
        
        .user-dropdown-item:hover {
            background: #f3f4f6;
        }
        
        .user-dropdown-item i {
            width: 20px;
            font-size: 16px;
            color: #6b7280;
        }
        
        .user-dropdown-divider {
            height: 1px;
            background: #e5e7eb;
            margin: 4px 0;
        }
        
        /* Mobile Styles */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                width: 280px;
                z-index: 1001;
            }
            
            .sidebar.open {
                transform: translateX(0);
            }
            
            .sidebar.collapsed {
                transform: translateX(-100%);
            }
            
            .main-content {
                margin-left: 0;
                margin-bottom: 70px;
            }
            
            .mobile-menu-btn {
                display: block;
                position: fixed;
                top: 15px;
                left: 15px;
                z-index: 1002;
                background: #2563eb;
                color: white;
                border: none;
                border-radius: 10px;
                padding: 10px 15px;
                cursor: pointer;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            }
            
            .mobile-footer-nav {
                display: flex;
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                background: linear-gradient(90deg, #1e3a8a 0%, #2563eb 100%);
                z-index: 1000;
                padding: 10px;
                justify-content: space-around;
                box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
            }
            
            .mobile-footer-nav a {
                color: white;
                text-align: center;
                font-size: 12px;
                padding: 8px;
                border-radius: 8px;
                transition: all 0.3s ease;
                text-decoration: none;
            }
            
            .mobile-footer-nav a:hover {
                background: rgba(255, 255, 255, 0.2);
            }
            
            .mobile-footer-nav i {
                font-size: 20px;
                display: block;
                margin-bottom: 4px;
            }
            
            .overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.5);
                z-index: 1000;
            }
            
            .overlay.active {
                display: block;
            }
            
            .top-bar {
                padding-top: 60px;
            }
        }
        
        @media (min-width: 769px) {
            .mobile-menu-btn {
                display: none;
            }
            
            .mobile-footer-nav {
                display: none;
            }
            
            .overlay {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <!-- Mobile Menu Button -->
    <button class="mobile-menu-btn" onclick="toggleMobileMenu()">
        <i class="fas fa-bars text-xl"></i>
    </button>
    
    <!-- Overlay -->
    <div class="overlay" onclick="toggleMobileMenu()"></div>
    
    <!-- Sidebar (White Background) -->
    <div class="sidebar" id="sidebar">
        @include('layouts.sidebar')
    </div>
    
    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <!-- Top Navigation Bar - White Header -->
        <nav class="top-header shadow-sm sticky top-0 z-50">
            <div class="px-6 py-4 flex justify-between items-center">
                <!-- Left side - Sidebar Toggle Button + Page Title -->
                <div class="flex items-center gap-4">
                    <button onclick="toggleSidebar()" class="sidebar-toggle text-gray-600 hover:text-gray-800">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <h1 class="text-2xl font-bold text-gray-800">@yield('page-title', 'Dashboard')</h1>
                </div>
                
                <!-- Right side - User Profile with Dropdown -->
                @auth
                <div class="relative">
                    <button onclick="toggleUserDropdown()" class="flex items-center space-x-4 focus:outline-none">
                        <div class="text-right hidden sm:block">
                            <p class="text-sm font-semibold text-gray-800">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                        </div>
                        <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center">
                            <i class="fas fa-user text-gray-500 text-sm"></i>
                        </div>
                        <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                    </button>
                    
                    <!-- Dropdown Menu -->
                    <div id="userDropdown" class="user-dropdown hidden">
                        <a href="{{ route('profile.index') }}" class="user-dropdown-item">
                            <i class="fas fa-user-circle"></i>
                            <span>My Profile</span>
                        </a>
                        <a href="{{ route('settings.index') }}" class="user-dropdown-item">
                            <i class="fas fa-cog"></i>
                            <span>Settings</span>
                        </a>
                        <div class="user-dropdown-divider"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="user-dropdown-item w-full text-left">
                                <i class="fas fa-sign-out-alt text-red-500"></i>
                                <span class="text-red-600">Sign Out</span>
                            </button>
                        </form>
                    </div>
                </div>
                @else
                <div class="flex items-center space-x-3">
                    <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-800">Login</a>
                    <a href="{{ route('register') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">Register</a>
                </div>
                @endauth
            </div>
        </nav>
        
        <!-- Page Content -->
        <div class="p-6">
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 px-4 py-3 rounded mb-4">
                    <i class="fas fa-check-circle mr-2"></i>
                    {{ session('success') }}
                </div>
            @endif
            
            @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded mb-4">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    {{ session('error') }}
                </div>
            @endif
            
            @yield('content')
        </div>
    </div>
    
    <!-- Mobile Footer Navigation -->
    <div class="mobile-footer-nav">
        @include('layouts.mobile-footer')
    </div>
    
    <script>
        function toggleMobileMenu() {
            const sidebar = document.querySelector('.sidebar');
            const overlay = document.querySelector('.overlay');
            if (sidebar && overlay) {
                sidebar.classList.toggle('open');
                overlay.classList.toggle('active');
            }
        }
        
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            
            if (sidebar && mainContent) {
                sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('expanded');
                
                // Save state to localStorage
                const isCollapsed = sidebar.classList.contains('collapsed');
                localStorage.setItem('sidebarCollapsed', isCollapsed);
            }
        }
        
        function toggleUserDropdown() {
            const dropdown = document.getElementById('userDropdown');
            if (dropdown) {
                dropdown.classList.toggle('hidden');
            }
        }
        
        // Load sidebar state from localStorage
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
            
            if (sidebar && mainContent && isCollapsed) {
                sidebar.classList.add('collapsed');
                mainContent.classList.add('expanded');
            }
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('userDropdown');
            const button = event.target.closest('[onclick="toggleUserDropdown()"]');
            if (dropdown && !button && !dropdown.contains(event.target)) {
                dropdown.classList.add('hidden');
            }
        });
        
        // Close mobile menu when clicking a link
        if (document.querySelectorAll('.sidebar a').length > 0) {
            document.querySelectorAll('.sidebar a').forEach(link => {
                link.addEventListener('click', () => {
                    if (window.innerWidth <= 768) {
                        const sidebar = document.querySelector('.sidebar');
                        const overlay = document.querySelector('.overlay');
                        if (sidebar && overlay) {
                            sidebar.classList.remove('open');
                            overlay.classList.remove('active');
                        }
                    }
                });
            });
        }
    </script>
    @stack('scripts')
</body>
</html>