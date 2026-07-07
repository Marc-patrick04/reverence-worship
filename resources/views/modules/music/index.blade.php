@extends('layouts.app')

@section('title', 'Music & Evangelism')
@section('page-title', 'Music & Evangelism')

@section('content')
<div class="max-w-7xl mx-auto px-2 sm:px-4">
    @if(!auth()->user()->canAccess('music-ministry', 'access'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg shadow-md">
            <div class="flex items-center">
                <i class="fas fa-lock text-red-500 text-2xl mr-3"></i>
                <div>
                    <h3 class="font-bold">Access Denied</h3>
                    <p>You do not have permission to access the Music & Evangelism module.</p>
                </div>
            </div>
        </div>
    @else
       

        <!-- Tab Navigation - Finance-style responsive selector -->
        <div class="relative z-40 bg-white rounded-lg shadow-sm border border-gray-200 overflow-visible mb-4">
            <!-- Mobile section selector -->
            <div class="md:hidden p-2">
                <div class="relative w-full max-w-[280px]" id="musicMobileTabPicker">
                    <button type="button" id="musicMobileTabButton"
                        onclick="toggleMusicMobileTabs()"
                        class="h-10 w-full flex items-center justify-between rounded-lg border border-gray-300 bg-white px-3 text-sm font-medium text-gray-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                        aria-haspopup="true" aria-expanded="false">
                        <span class="flex items-center gap-2">
                            <i id="musicMobileTabIcon" class="fas fa-list text-blue-600" aria-hidden="true"></i>
                            <span id="musicMobileTabLabel">Playlist</span>
                        </span>
                        <i class="fas fa-chevron-down text-gray-400 text-[10px]" aria-hidden="true"></i>
                    </button>
                    <div id="musicMobileTabMenu"
                        class="hidden absolute left-0 top-full z-50 mt-1 w-full rounded-lg border border-gray-200 bg-white p-1.5 shadow-lg">
                        <div class="grid grid-cols-2 gap-1">
                            @if(auth()->user()->canAccess('music-ministry', 'view-playlist-tab'))
                            <button type="button" onclick="selectMusicMobileTab('playlist')" class="music-mobile-tab-option h-10 rounded-md px-3 text-left text-sm text-gray-700 hover:bg-gray-100" data-tab="playlist" data-icon="list">Playlist</button>
                            @endif
                            @if(auth()->user()->canAccess('music-ministry', 'view-gallery-tab'))
                            <button type="button" onclick="selectMusicMobileTab('gallery')" class="music-mobile-tab-option h-10 rounded-md px-3 text-left text-sm text-gray-700 hover:bg-gray-100" data-tab="gallery" data-icon="images">Gallery</button>
                            @endif
                            @if(auth()->user()->canAccess('music-ministry', 'view-groups-tab'))
                            <button type="button" onclick="selectMusicMobileTab('groups')" class="music-mobile-tab-option h-10 rounded-md px-3 text-left text-sm text-gray-700 hover:bg-gray-100" data-tab="groups" data-icon="users">Groups</button>
                            @endif
                            @if(auth()->user()->canAccess('music-ministry', 'view-board-tab'))
                            <button type="button" onclick="selectMusicMobileTab('board')" class="music-mobile-tab-option h-10 rounded-md px-3 text-left text-sm text-gray-700 hover:bg-gray-100" data-tab="board" data-icon="bullhorn">Public Board</button>
                            @endif
                            @if(auth()->user()->canAccess('music-ministry', 'view-actionplan'))
                            <button type="button" onclick="selectMusicMobileTab('actionPlan')" class="music-mobile-tab-option col-span-2 h-10 rounded-md px-3 text-left text-sm text-gray-700 hover:bg-gray-100" data-tab="actionPlan" data-icon="tasks">Action Plans</button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tablet and desktop tabs -->
            <div class="hidden md:block border-b border-gray-200">
                <nav class="flex flex-wrap">
                    @if(auth()->user()->canAccess('music-ministry', 'view-playlist-tab'))
                    <button onclick="showTab('playlist')" id="tab-playlist" data-tab="playlist"
                            class="tab-btn px-3 sm:px-4 py-2 text-xs sm:text-sm font-medium border-b-2 transition whitespace-nowrap">
                        <i class="fas fa-list mr-1.5"></i> Playlist
                    </button>
                    @endif
                    
                    @if(auth()->user()->canAccess('music-ministry', 'view-gallery-tab'))
                    <button onclick="showTab('gallery')" id="tab-gallery" data-tab="gallery"
                            class="tab-btn px-3 sm:px-4 py-2 text-xs sm:text-sm font-medium border-b-2 transition whitespace-nowrap">
                        <i class="fas fa-images mr-1.5"></i> Photo Gallery
                    </button>
                    @endif
                    
                    @if(auth()->user()->canAccess('music-ministry', 'view-groups-tab'))
                    <button onclick="showTab('groups')" id="tab-groups" data-tab="groups"
                            class="tab-btn px-3 sm:px-4 py-2 text-xs sm:text-sm font-medium border-b-2 transition whitespace-nowrap">
                        <i class="fas fa-users mr-1.5"></i> Groups
                    </button>
                    @endif
                    
                    @if(auth()->user()->canAccess('music-ministry', 'view-board-tab'))
                    <button onclick="showTab('board')" id="tab-board" data-tab="board"
                            class="tab-btn px-3 sm:px-4 py-2 text-xs sm:text-sm font-medium border-b-2 transition whitespace-nowrap">
                        <i class="fas fa-bullhorn mr-1.5"></i> Public Board
                    </button>
                    @endif

                    @if(auth()->user()->canAccess('music-ministry', 'view-actionplan'))
                    <button onclick="showTab('actionPlan')" id="tab-actionPlan" data-tab="actionPlan"
                            class="tab-btn px-3 sm:px-4 py-2 text-xs sm:text-sm font-medium border-b-2 transition whitespace-nowrap">
                        <i class="fas fa-tasks mr-1.5"></i> Action Plans
                    </button>
                    @endif
                </nav>
            </div>
        </div>

        <!-- Tab Contents -->
        @if(auth()->user()->canAccess('music-ministry', 'view-playlist-tab'))
        <div id="playlist-tab" class="tab-content">
            @include('modules.music.playlist', [
                'canViewSongs' => auth()->user()->canAccess('music-ministry', 'view-songs'),
                'canAddSongs' => auth()->user()->canAccess('music-ministry', 'add-songs'),
                'canEditSongs' => auth()->user()->canAccess('music-ministry', 'edit-songs'),
                'canDeleteSongs' => auth()->user()->canAccess('music-ministry', 'delete-songs'),
                'canViewPlaylists' => auth()->user()->canAccess('music-ministry', 'view-playlists'),
                'canAddPlaylists' => auth()->user()->canAccess('music-ministry', 'add-playlists'),
                'canEditPlaylists' => auth()->user()->canAccess('music-ministry', 'edit-playlists'),
                'canDeletePlaylists' => auth()->user()->canAccess('music-ministry', 'delete-playlists')
            ])
        </div>
        @endif

        @if(auth()->user()->canAccess('music-ministry', 'view-gallery-tab'))
        <div id="gallery-tab" class="tab-content hidden">
            @include('modules.music.gallery', [
                'canView' => auth()->user()->canAccess('music-ministry', 'view-gallery'),
                'canAdd' => auth()->user()->canAccess('music-ministry', 'add-gallery'),
                'canEdit' => auth()->user()->canAccess('music-ministry', 'edit-gallery'),
                'canDelete' => auth()->user()->canAccess('music-ministry', 'delete-gallery')
            ])
        </div>
        @endif

        @if(auth()->user()->canAccess('music-ministry', 'view-groups-tab'))
        <div id="groups-tab" class="tab-content hidden">
            @include('modules.music.groups', [
                'canView' => auth()->user()->canAccess('music-ministry', 'view-groups'),
                'canAdd' => auth()->user()->canAccess('music-ministry', 'add-groups'),
                'canEdit' => auth()->user()->canAccess('music-ministry', 'edit-groups'),
                'canDelete' => auth()->user()->canAccess('music-ministry', 'delete-groups')
            ])
        </div>
        @endif

        @if(auth()->user()->canAccess('music-ministry', 'view-board-tab'))
        <div id="board-tab" class="tab-content hidden">
            @include('modules.music.board', [
                'canView' => auth()->user()->canAccess('music-ministry', 'view-board'),
                'canAdd' => auth()->user()->canAccess('music-ministry', 'add-board'),
                'canEdit' => auth()->user()->canAccess('music-ministry', 'edit-board'),
                'canDelete' => auth()->user()->canAccess('music-ministry', 'delete-board')
            ])
        </div>
        @endif

        @if(auth()->user()->canAccess('music-ministry', 'view-actionplan'))
        <div id="actionPlan-tab" class="tab-content hidden">
            @include('modules.music.actionplan')
        </div>
        @endif
    @endif
</div>

<script>
// Function to show tab with persistence
function showTab(tabName) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.add('hidden');
    });
    
    // Remove active class from all tab buttons
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('border-blue-600', 'text-blue-600');
        btn.classList.add('border-transparent', 'text-gray-500');
    });
    
    // Show selected tab
    const selectedTab = document.getElementById(`${tabName}-tab`);
    if (selectedTab) {
        selectedTab.classList.remove('hidden');
    }
    
    // Activate selected button
    const activeBtn = document.getElementById(`tab-${tabName}`);
    if (activeBtn) {
        activeBtn.classList.remove('border-transparent', 'text-gray-500');
        activeBtn.classList.add('border-blue-600', 'text-blue-600');
    }

    updateMusicMobileTabDisplay(tabName);
    
    // Save current tab to localStorage
    localStorage.setItem('activeMusicTab', tabName);
}

function toggleMusicMobileTabs() {
    const menu = document.getElementById('musicMobileTabMenu');
    const button = document.getElementById('musicMobileTabButton');
    if (!menu || !button) return;

    const isOpening = menu.classList.contains('hidden');
    menu.classList.toggle('hidden');
    button.setAttribute('aria-expanded', isOpening ? 'true' : 'false');
}

function selectMusicMobileTab(tabName) {
    showTab(tabName);
    document.getElementById('musicMobileTabMenu')?.classList.add('hidden');
    document.getElementById('musicMobileTabButton')?.setAttribute('aria-expanded', 'false');
}

function updateMusicMobileTabDisplay(tabName) {
    const activeMobileOption = document.querySelector(`.music-mobile-tab-option[data-tab="${tabName}"]`);
    const mobileLabel = document.getElementById('musicMobileTabLabel');
    const mobileIcon = document.getElementById('musicMobileTabIcon');

    if (activeMobileOption && mobileLabel && mobileIcon) {
        mobileLabel.textContent = activeMobileOption.textContent.trim();
        mobileIcon.className = `fas fa-${activeMobileOption.dataset.icon} text-blue-600`;
    }

    document.querySelectorAll('.music-mobile-tab-option').forEach(option => {
        const isActive = option.dataset.tab === tabName;
        option.classList.toggle('bg-blue-50', isActive);
        option.classList.toggle('text-blue-700', isActive);
        option.classList.toggle('font-semibold', isActive);
    });
}

// On page load, restore the last active tab
document.addEventListener('DOMContentLoaded', function() {
    const savedTab = localStorage.getItem('activeMusicTab');
    const validTabs = ['playlist', 'gallery', 'groups', 'board', 'actionPlan'];
    
    // Check if saved tab exists and user has permission
    if (savedTab && validTabs.includes(savedTab)) {
        // Check if the tab button exists (user has permission)
        const tabButton = document.getElementById(`tab-${savedTab}`);
        if (tabButton) {
            showTab(savedTab);
        } else {
            // Default to first available tab
            const firstTab = document.querySelector('.tab-btn');
            if (firstTab) {
                const firstTabId = firstTab.id.replace('tab-', '');
                showTab(firstTabId);
            }
        }
    } else {
        // Default to first available tab
        const firstTab = document.querySelector('.tab-btn');
        if (firstTab) {
            const firstTabId = firstTab.id.replace('tab-', '');
            showTab(firstTabId);
        }
    }

    document.addEventListener('click', function(event) {
        const picker = document.getElementById('musicMobileTabPicker');
        const menu = document.getElementById('musicMobileTabMenu');
        const button = document.getElementById('musicMobileTabButton');

        if (picker && menu && button && !picker.contains(event.target)) {
            menu.classList.add('hidden');
            button.setAttribute('aria-expanded', 'false');
        }
    });
});
</script>

<style>
.tab-btn {
    transition: all 0.3s ease;
}
.tab-btn:hover {
    color: #2563eb;
}
.tab-btn {
    color: #6b7280;
    border-bottom-color: transparent;
}
.music-mobile-tab-option {
    -webkit-tap-highlight-color: transparent;
    touch-action: manipulation;
}
.modal { display: none; }
.modal:not(.hidden) { display: flex !important; }
</style>
@endsection
