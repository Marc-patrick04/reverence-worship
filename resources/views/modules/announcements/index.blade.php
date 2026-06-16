@extends('layouts.app')

@section('title', 'Announcement Management')
@section('page-title', 'Announcement Management')

@section('content')
<div class="container mx-auto px-4 py-8">
    
 
    <!-- Tabs Navigation -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden mb-6">
        <div class="border-b border-gray-200 overflow-x-auto">
            <nav class="flex flex-nowrap min-w-max">
                <button class="tab-btn active px-6 py-3 text-sm font-medium text-blue-600 border-b-2 border-blue-600" data-tab="overview">
                    <i class="fas fa-chart-line mr-2"></i> Overview
                </button>
                <button class="tab-btn px-6 py-3 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent" data-tab="announcements">
                    <i class="fas fa-bullhorn mr-2"></i> Announcements
                </button>
            </nav>
        </div>
    </div>
    
    <!-- Tab Content -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="p-6">
            <div id="overview-tab" class="tab-content">
                @include('modules.announcements.partials.overview-tab')
            </div>
            <div id="announcements-tab" class="tab-content hidden">
                @include('modules.announcements.partials.announcements-tab')
            </div>
        </div>
    </div>
</div>

@include('modules.announcements.partials.create-modal')
@include('modules.announcements.partials.edit-modal')

<script>
// Global functions that need to be available everywhere
window.openCreateModal = function() {
    console.log('Opening create modal');
    const modal = document.getElementById('createAnnouncementModal');
    if (modal) {
        const form = document.getElementById('createAnnouncementForm');
        if (form) form.reset();
        modal.classList.remove('hidden');
    } else {
        console.error('Create modal not found');
    }
};

window.closeModal = function(modalId) {
    console.log('Closing modal:', modalId);
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('hidden');
    }
};

window.refreshAnnouncementsList = function() {
    if (typeof window.loadAnnouncements === 'function') {
        window.loadAnnouncements();
    }
};

window.refreshOverviewStats = function() {
    if (typeof window.loadOverviewStats === 'function') {
        window.loadOverviewStats();
    }
};

// Tab switching
document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('.tab-btn');
    const STORAGE_KEY = 'announcements_active_tab';
    
    const savedTab = localStorage.getItem(STORAGE_KEY);
    const activeTab = savedTab && ['overview', 'announcements'].includes(savedTab) ? savedTab : 'overview';
    
    function switchTab(tabName) {
        console.log('Switching to tab:', tabName);
        
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });
        
        const selectedTab = document.getElementById(`${tabName}-tab`);
        if (selectedTab) {
            selectedTab.classList.remove('hidden');
        }
        
        tabs.forEach(tab => {
            tab.classList.remove('text-blue-600', 'border-blue-600');
            tab.classList.add('text-gray-500', 'border-transparent');
            if (tab.getAttribute('data-tab') === tabName) {
                tab.classList.remove('text-gray-500', 'border-transparent');
                tab.classList.add('text-blue-600', 'border-blue-600');
            }
        });
        
        localStorage.setItem(STORAGE_KEY, tabName);
        
        // Refresh data when switching tabs
        if (tabName === 'overview') {
            if (typeof window.loadOverviewStats === 'function') {
                window.loadOverviewStats();
            }
        }
        if (tabName === 'announcements') {
            if (typeof window.loadAnnouncements === 'function') {
                window.loadAnnouncements();
            }
        }
    }
    
    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            switchTab(this.getAttribute('data-tab'));
        });
    });
    
    // Initial load
    switchTab(activeTab);
});
</script>
@endsection