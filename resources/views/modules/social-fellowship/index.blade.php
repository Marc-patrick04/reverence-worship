@extends('layouts.app')

@section('title', 'Social Fellowship')
@section('page-title', 'Social Fellowship')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    
   
    <!-- Navigation Tabs -->
    <div class="border-b border-gray-200">
        <nav class="flex space-x-8 overflow-x-auto">
            <button onclick="showTab('families')" id="tab-families" class="tab-btn py-2 px-1 border-b-2 font-medium text-sm transition border-gray-900 text-gray-900">
                <i class="fas fa-users mr-2"></i>Families
            </button>
            <button onclick="showTab('users')" id="tab-users" class="tab-btn py-2 px-1 border-b-2 font-medium text-sm transition border-transparent text-gray-500">
                <i class="fas fa-user-friends mr-2"></i>Users
            </button>
            <button onclick="showTab('tasks')" id="tab-tasks" class="tab-btn py-2 px-1 border-b-2 font-medium text-sm transition border-transparent text-gray-500">
                <i class="fas fa-tasks mr-2"></i>Tasks
            </button>
            <button onclick="showTab('actionPlans')" id="tab-actionPlans" class="tab-btn py-2 px-1 border-b-2 font-medium text-sm transition border-transparent text-gray-500">
                <i class="fas fa-clipboard-list mr-2"></i>Action Plans
            </button>
            <button onclick="showTab('archives')" id="tab-archives" class="tab-btn py-2 px-1 border-b-2 font-medium text-sm transition border-transparent text-gray-500">
                <i class="fas fa-archive mr-2"></i>Archives
            </button>
        </nav>
    </div>
    
    <!-- Families Tab -->
    <div id="families-tab" class="tab-content">
        @include('modules.social-fellowship.partials.families-list', [
            'families' => $families ?? [],
            'availableUsers' => $availableUsers ?? [],
            'users' => $users ?? []
        ])
    </div>
    
    <!-- Users Tab -->
    <div id="users-tab" class="tab-content hidden">
        @include('modules.social-fellowship.partials.users-list', [
            'allUsers' => $allUsers ?? [],
            'families' => $families ?? [],
            'availableUsers' => $availableUsers ?? []
        ])
    </div>
    
    <!-- Tasks Tab -->
    <div id="tasks-tab" class="tab-content hidden">
        @include('modules.social-fellowship.partials.tasks-list', [
            'tasks' => $tasks ?? [],
            'families' => $families ?? []
        ])
    </div>
    
    <!-- Action Plans Tab -->
    <div id="actionPlans-tab" class="tab-content hidden">
        @include('modules.social-fellowship.partials.action-plans-list', [
            'actionPlans' => $actionPlans ?? [],
            'totalActionPlans' => $totalActionPlans ?? 0,
            'completedPlans' => $completedPlans ?? 0,
            'inProgressPlans' => $inProgressPlans ?? 0,
            'pendingPlans' => $pendingPlans ?? 0,
            'overallProgress' => $overallProgress ?? 0,
            'families' => $families ?? []
        ])
    </div>
    
    <!-- Archives Tab -->
    <div id="archives-tab" class="tab-content hidden">
        @include('modules.social-fellowship.partials.archives-list', [
            'archiveSections' => $archiveSections ?? []
        ])
    </div>
    
</div>

<script>
// Function to show tab with persistence
window.showTab = function(tabName) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.add('hidden');
    });
    
    // Remove active class from all tab buttons
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('border-gray-900', 'text-gray-900');
        btn.classList.add('border-transparent', 'text-gray-500');
    });
    
    // Show selected tab
    const selectedTab = document.getElementById(`${tabName}-tab`);
    if (selectedTab) {
        selectedTab.classList.remove('hidden');
    }
    
    // Activate selected button (black border and black text)
    const activeBtn = document.getElementById(`tab-${tabName}`);
    if (activeBtn) {
        activeBtn.classList.remove('border-transparent', 'text-gray-500');
        activeBtn.classList.add('border-gray-900', 'text-gray-900');
    }
    
    // Save current tab to localStorage
    localStorage.setItem('activeSocialFellowshipTab', tabName);
}

// On page load, restore the last active tab
document.addEventListener('DOMContentLoaded', function() {
    const savedTab = localStorage.getItem('activeSocialFellowshipTab');
    const validTabs = ['families', 'users', 'tasks', 'actionPlans', 'archives'];
    
    if (savedTab && validTabs.includes(savedTab)) {
        const tabButton = document.getElementById(`tab-${savedTab}`);
        if (tabButton) {
            window.showTab(savedTab);
        } else {
            window.showTab('families');
        }
    } else {
        window.showTab('families');
    }
});
</script>

<style>
.tab-btn { 
    transition: all 0.3s ease; 
    background: transparent;
    cursor: pointer;
}
.tab-btn:hover { 
    color: #374151;
    border-bottom-color: #9ca3af;
}
</style>
@endsection