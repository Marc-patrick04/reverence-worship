@extends('layouts.app')

@section('title', 'Reports & Analytics')
@section('page-title', 'Reports & Analytics')

@section('content')
<div class="container mx-auto px-4 py-8">
    
    <!-- Page Header -->
    <div class="mb-8">
        <p class="text-gray-600 text-sm">Overview of system metrics and activity</p>
    </div>
    
    <!-- Navigation Tabs -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden mb-6">
        <div class="border-b border-gray-200 overflow-x-auto">
            <nav class="flex flex-nowrap min-w-max">
                <button class="tab-btn active px-5 py-3 text-sm font-medium text-blue-600 border-b-2 border-blue-600" data-tab="overview">
                    <i class="fas fa-chart-line mr-2"></i> Overview
                </button>
                <button class="tab-btn px-5 py-3 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent" data-tab="action-plans">
                    <i class="fas fa-tasks mr-2"></i> Action Plans
                </button>
                <button class="tab-btn px-5 py-3 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent" data-tab="discipline">
                    <i class="fas fa-gavel mr-2"></i> Discipline Report
                </button>
                <button class="tab-btn px-5 py-3 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent" data-tab="permission">
                    <i class="fas fa-envelope-open-text mr-2"></i> Permission requests
                </button>
                <button class="tab-btn px-5 py-3 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent" data-tab="events">
                    <i class="fas fa-calendar-alt mr-2"></i> Events
                </button>
                <button class="tab-btn px-5 py-3 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent" data-tab="users">
                    <i class="fas fa-users mr-2"></i> Users
                </button>
                <button class="tab-btn px-5 py-3 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent" data-tab="attendance">
                    <i class="fas fa-calendar-check mr-2"></i> Attendance
                </button>
                <button class="tab-btn px-5 py-3 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent" data-tab="financial">
                    <i class="fas fa-chart-pie mr-2"></i> Financial
                </button>
                <button class="tab-btn px-5 py-3 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent" data-tab="forms">
                    <i class="fas fa-file-alt mr-2"></i> Form
                </button>
            </nav>
        </div>
    </div>
    
    <!-- Tab Content -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="p-6">
            <div id="overview-tab" class="tab-content">
                @include('modules.reports.partials.overview-tab')
            </div>
            <div id="action-plans-tab" class="tab-content hidden">
                @include('modules.reports.partials.action-plans-tab')
            </div>
            <div id="discipline-tab" class="tab-content hidden">
                @include('modules.reports.partials.discipline-tab')
            </div>
            <div id="permission-tab" class="tab-content hidden">
                @include('modules.reports.partials.permission-tab')
            </div>
            <div id="events-tab" class="tab-content hidden">
                @include('modules.reports.partials.events-tab')
            </div>
            <div id="users-tab" class="tab-content hidden">
                @include('modules.reports.partials.users-tab')
            </div>
            <div id="attendance-tab" class="tab-content hidden">
                @include('modules.reports.partials.attendance-tab')
            </div>
            <div id="financial-tab" class="tab-content hidden">
                @include('modules.reports.partials.financial-tab')
            </div>
            <div id="forms-tab" class="tab-content hidden">
                @include('modules.reports.partials.forms-tab')
            </div>
        </div>
    </div>
</div>

@include('modules.reports.partials.modals')

<script>
const STORAGE_KEY = 'reports_active_tab';

document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('.tab-btn');
    const savedTab = localStorage.getItem(STORAGE_KEY);
    const activeTab = savedTab && isValidTab(savedTab) ? savedTab : 'overview';
    
    function isValidTab(tabName) {
        const validTabs = ['overview', 'action-plans', 'discipline', 'permission', 'events', 'users', 'attendance', 'financial', 'forms'];
        return validTabs.includes(tabName);
    }
    
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
        
        // Load tab data
        if (tabName === 'action-plans' && typeof loadActionPlansReport === 'function') loadActionPlansReport();
        if (tabName === 'discipline' && typeof loadDisciplineReport === 'function') loadDisciplineReport();
        if (tabName === 'permission' && typeof loadPermissionReport === 'function') loadPermissionReport();
        if (tabName === 'events' && typeof loadEventsReport === 'function') loadEventsReport();
        if (tabName === 'users' && typeof loadUsersReport === 'function') loadUsersReport();
        if (tabName === 'attendance' && typeof loadAttendanceReport === 'function') loadAttendanceReport();
        if (tabName === 'financial' && typeof loadFinancialReport === 'function') loadFinancialReport();
        if (tabName === 'forms' && typeof loadFormsReport === 'function') loadFormsReport();
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