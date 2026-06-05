@extends('layouts.app')

@section('title', 'Announcement Management')
@section('page-title', 'Announcement Management')

@section('content')
<div class="container mx-auto px-4 py-8">
    
    <!-- Page Header -->
    <div class="mb-8">
        <p class="text-gray-600 text-sm">Create and manage system announcements</p>
    </div>
    
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
document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('.tab-btn');
    const STORAGE_KEY = 'announcements_active_tab';
    
    const savedTab = localStorage.getItem(STORAGE_KEY);
    const activeTab = savedTab && ['overview', 'announcements'].includes(savedTab) ? savedTab : 'overview';
    
    function switchTab(tabName) {
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
        
        if (tabName === 'overview' && typeof loadOverviewStats === 'function') {
            loadOverviewStats();
        }
        if (tabName === 'announcements' && typeof loadAnnouncements === 'function') {
            loadAnnouncements();
        }
    }
    
    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            switchTab(this.getAttribute('data-tab'));
        });
    });
    
    switchTab(activeTab);
});
</script>
@endsection