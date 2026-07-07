@extends('layouts.app')

@section('title', 'Social Fellowship')
@section('page-title', 'Social Fellowship')

@section('content')
<div class="max-w-7xl mx-auto px-2 sm:px-4 space-y-4 sm:space-y-6">
    
    @php
        $canViewFamilies = auth()->check() && auth()->user()->canAccess('social-fellowship', 'view-families');
        $canViewUsers = auth()->check() && auth()->user()->canAccess('social-fellowship', 'view-users');
        $canViewTasks = auth()->check() && auth()->user()->canAccess('social-fellowship', 'view-tasks');
        $canViewActionPlans = auth()->check() && auth()->user()->canAccess('social-fellowship', 'view-action-plans');
        $canViewArchives = auth()->check() && auth()->user()->canAccess('social-fellowship', 'view-archives');
    @endphp

   
   
    <!-- Navigation Tabs - Only show tabs user has permission for -->
    @if($canViewFamilies || $canViewUsers || $canViewTasks || $canViewActionPlans || $canViewArchives)
    <div class="relative z-40 bg-white rounded-lg shadow-sm border border-gray-200 overflow-visible mb-4">
        <div class="md:hidden p-2">
            <div class="relative w-full max-w-[280px]" id="socialMobileTabPicker">
                <button type="button" id="socialMobileTabButton"
                    onclick="toggleSocialMobileTabs()"
                    class="h-10 w-full flex items-center justify-between rounded-lg border border-gray-300 bg-white px-3 text-sm font-medium text-gray-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                    aria-haspopup="true" aria-expanded="false">
                    <span class="flex items-center gap-2">
                        <i id="socialMobileTabIcon" class="fas fa-users text-blue-600" aria-hidden="true"></i>
                        <span id="socialMobileTabLabel">Families</span>
                    </span>
                    <i class="fas fa-chevron-down text-gray-400 text-[10px]" aria-hidden="true"></i>
                </button>
                <div id="socialMobileTabMenu" class="hidden absolute left-0 top-full z-50 mt-1 w-full rounded-lg border border-gray-200 bg-white p-1.5 shadow-lg">
                    <div class="grid grid-cols-2 gap-1">
                        @if($canViewFamilies)
                        <button type="button" onclick="selectSocialMobileTab('families')" class="social-mobile-tab-option h-10 rounded-md px-3 text-left text-sm text-gray-700 hover:bg-gray-100" data-tab="families" data-icon="users">Families</button>
                        @endif
                        @if($canViewUsers)
                        <button type="button" onclick="selectSocialMobileTab('users')" class="social-mobile-tab-option h-10 rounded-md px-3 text-left text-sm text-gray-700 hover:bg-gray-100" data-tab="users" data-icon="user-friends">Users</button>
                        @endif
                        @if($canViewTasks)
                        <button type="button" onclick="selectSocialMobileTab('tasks')" class="social-mobile-tab-option h-10 rounded-md px-3 text-left text-sm text-gray-700 hover:bg-gray-100" data-tab="tasks" data-icon="tasks">Tasks</button>
                        @endif
                        @if($canViewActionPlans)
                        <button type="button" onclick="selectSocialMobileTab('actionPlans')" class="social-mobile-tab-option h-10 rounded-md px-3 text-left text-sm text-gray-700 hover:bg-gray-100" data-tab="actionPlans" data-icon="clipboard-list">Action Plans</button>
                        @endif
                        @if($canViewArchives)
                        <button type="button" onclick="selectSocialMobileTab('archives')" class="social-mobile-tab-option col-span-2 h-10 rounded-md px-3 text-left text-sm text-gray-700 hover:bg-gray-100" data-tab="archives" data-icon="archive">Archives</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <nav class="hidden md:flex flex-wrap border-b border-gray-200">
            @if($canViewFamilies)
            <button onclick="showTab('families')" id="tab-families" class="tab-btn px-3 sm:px-4 py-2 text-xs sm:text-sm border-b-2 font-medium transition border-gray-900 text-gray-900">
                <i class="fas fa-users mr-2"></i>Families
            </button>
            @endif
            
            @if($canViewUsers)
            <button onclick="showTab('users')" id="tab-users" class="tab-btn px-3 sm:px-4 py-2 text-xs sm:text-sm border-b-2 font-medium transition border-transparent text-gray-500">
                <i class="fas fa-user-friends mr-2"></i>Users
            </button>
            @endif
            
            @if($canViewTasks)
            <button onclick="showTab('tasks')" id="tab-tasks" class="tab-btn px-3 sm:px-4 py-2 text-xs sm:text-sm border-b-2 font-medium transition border-transparent text-gray-500">
                <i class="fas fa-tasks mr-2"></i>Tasks
            </button>
            @endif
            
            @if($canViewActionPlans)
            <button onclick="showTab('actionPlans')" id="tab-actionPlans" class="tab-btn px-3 sm:px-4 py-2 text-xs sm:text-sm border-b-2 font-medium transition border-transparent text-gray-500">
                <i class="fas fa-clipboard-list mr-2"></i>Action Plans
            </button>
            @endif
            
            @if($canViewArchives)
            <button onclick="showTab('archives')" id="tab-archives" class="tab-btn px-3 sm:px-4 py-2 text-xs sm:text-sm border-b-2 font-medium transition border-transparent text-gray-500">
                <i class="fas fa-archive mr-2"></i>Archives
            </button>
            @endif
        </nav>
    </div>
    @endif

    <!-- No Permission Message -->
    @if(!$canViewFamilies && !$canViewUsers && !$canViewTasks && !$canViewActionPlans && !$canViewArchives)
    <div class="bg-white rounded-xl shadow-sm p-12 text-center border border-gray-100">
        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-lock text-gray-400 text-3xl"></i>
        </div>
        <h3 class="text-lg font-semibold text-gray-800 mb-2">No Access</h3>
        <p class="text-gray-500 text-sm">You don't have permission to view this page.</p>
        <p class="text-gray-400 text-xs mt-2">Contact your administrator to grant access.</p>
    </div>
    @endif

    <!-- Families Tab -->
    @if($canViewFamilies)
    <div id="families-tab" class="tab-content">
        @include('modules.social-fellowship.partials.families-list', [
            'families' => $families ?? [],
            'availableUsers' => $availableUsers ?? [],
            'users' => $users ?? []
        ])
    </div>
    @endif
    
    <!-- Users Tab -->
    @if($canViewUsers)
    <div id="users-tab" class="tab-content hidden">
        @include('modules.social-fellowship.partials.users-list', [
            'allUsers' => $allUsers ?? [],
            'families' => $families ?? [],
            'availableUsers' => $availableUsers ?? []
        ])
    </div>
    @endif
    
    <!-- Tasks Tab -->
    @if($canViewTasks)
    <div id="tasks-tab" class="tab-content hidden">
        @include('modules.social-fellowship.partials.tasks-list', [
            'tasks' => $tasks ?? [],
            'families' => $families ?? []
        ])
    </div>
    @endif
    
    <!-- Action Plans Tab -->
    @if($canViewActionPlans)
    <div id="actionPlans-tab" class="tab-content hidden">
        @include('modules.social-fellowship.partials.action-plans-list', [
            'families' => $families ?? []
        ])
    </div>
    @endif
    
    <!-- Archives Tab -->
    @if($canViewArchives)
    <div id="archives-tab" class="tab-content hidden">
        @include('modules.social-fellowship.partials.archives-list', [
            'archiveSections' => $archiveSections ?? []
        ])
    </div>
    @endif
    
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
    
    // Activate selected button
    const activeBtn = document.getElementById(`tab-${tabName}`);
    if (activeBtn) {
        activeBtn.classList.remove('border-transparent', 'text-gray-500');
        activeBtn.classList.add('border-gray-900', 'text-gray-900');
    }

    updateSocialMobileTabDisplay(tabName);
    
    // Save current tab to localStorage
    localStorage.setItem('activeSocialFellowshipTab', tabName);
}

function toggleSocialMobileTabs() {
    const menu = document.getElementById('socialMobileTabMenu');
    const button = document.getElementById('socialMobileTabButton');
    if (!menu || !button) return;

    const isOpening = menu.classList.contains('hidden');
    menu.classList.toggle('hidden');
    button.setAttribute('aria-expanded', isOpening ? 'true' : 'false');
}

function selectSocialMobileTab(tabName) {
    window.showTab(tabName);
    document.getElementById('socialMobileTabMenu')?.classList.add('hidden');
    document.getElementById('socialMobileTabButton')?.setAttribute('aria-expanded', 'false');
}

function updateSocialMobileTabDisplay(tabName) {
    const activeOption = document.querySelector(`.social-mobile-tab-option[data-tab="${tabName}"]`);
    const label = document.getElementById('socialMobileTabLabel');
    const icon = document.getElementById('socialMobileTabIcon');

    if (activeOption && label && icon) {
        label.textContent = activeOption.textContent.trim();
        icon.className = `fas fa-${activeOption.dataset.icon} text-blue-600`;
    }

    document.querySelectorAll('.social-mobile-tab-option').forEach(option => {
        const isActive = option.dataset.tab === tabName;
        option.classList.toggle('bg-blue-50', isActive);
        option.classList.toggle('text-blue-700', isActive);
        option.classList.toggle('font-semibold', isActive);
    });
}

function openCreateFamilyModal() {
    @if(!(auth()->check() && auth()->user()->canAccess('social-fellowship', 'create-family')))
        appAlert('You do not have permission to create families.');
        return;
    @endif
    
    // Your modal opening logic here
    const modal = document.getElementById('createFamilyModal');
    if (modal) {
        modal.classList.remove('hidden');
    } else {
        appAlert('Create family modal not available');
    }
}

// On page load, restore the last active tab
document.addEventListener('DOMContentLoaded', function() {
    const savedTab = localStorage.getItem('activeSocialFellowshipTab');
    const validTabs = [];
    
    @if($canViewFamilies) validTabs.push('families'); @endif
    @if($canViewUsers) validTabs.push('users'); @endif
    @if($canViewTasks) validTabs.push('tasks'); @endif
    @if($canViewActionPlans) validTabs.push('actionPlans'); @endif
    @if($canViewArchives) validTabs.push('archives'); @endif
    
    if (savedTab && validTabs.includes(savedTab)) {
        const tabButton = document.getElementById(`tab-${savedTab}`);
        if (tabButton) {
            window.showTab(savedTab);
        } else if (validTabs.length > 0) {
            window.showTab(validTabs[0]);
        }
    } else if (validTabs.length > 0) {
        window.showTab(validTabs[0]);
    }

    document.addEventListener('click', function(event) {
        const picker = document.getElementById('socialMobileTabPicker');
        const menu = document.getElementById('socialMobileTabMenu');
        const button = document.getElementById('socialMobileTabButton');

        if (picker && menu && button && !picker.contains(event.target)) {
            menu.classList.add('hidden');
            button.setAttribute('aria-expanded', 'false');
        }
    });
});
function assignToFamily(userId, userName, currentFamilyId = null) {
    // Check if user has permission
    @if(!(auth()->check() && auth()->user()->canAccess('social-fellowship', 'assign-family-members')))
        appAlert('You do not have permission to assign family members.');
        return;
    @endif
    
    // Store the user info globally
    window.selectedUserId = userId;
    window.selectedUserName = userName;
    window.selectedCurrentFamilyId = currentFamilyId;
    
    // Update modal content
    const modalTitle = document.getElementById('assignFamilyModalLabel');
    if (modalTitle) {
        modalTitle.textContent = `Assign ${userName} to Family`;
    }
    
    const userInfoSpan = document.getElementById('selectedUserInfo');
    if (userInfoSpan) {
        userInfoSpan.textContent = userName;
    }
    
    // Load families into select dropdown
    loadFamiliesIntoSelect(currentFamilyId);
    
    // Show the modal
    const modal = document.getElementById('assignFamilyModal');
    if (modal) {
        modal.classList.remove('hidden');
    } else {
        // If modal doesn't exist, create a simple prompt
        const familySelect = prompt(`Assign ${userName} to a family:\n\nEnter family ID or select from available families.`);
        if (familySelect) {
            assignUserToFamily(userId, familySelect);
        }
    }
}

function loadFamiliesIntoSelect(currentFamilyId) {
    const familySelect = document.getElementById('familySelect');
    if (!familySelect) {
        console.error('Family select element not found');
        return;
    }
    
    // Clear existing options
    familySelect.innerHTML = '<option value="">-- Select a family --</option>';
    
    // Fetch families via AJAX
    fetch('/social-fellowship/families/list', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.families) {
            data.families.forEach(family => {
                const option = document.createElement('option');
                option.value = family.id;
                option.textContent = family.name;
                if (currentFamilyId && currentFamilyId == family.id) {
                    option.selected = true;
                }
                familySelect.appendChild(option);
            });
        }
    })
    .catch(error => {
        console.error('Error loading families:', error);
    });
}

function confirmAssignToFamily() {
    const userId = window.selectedUserId;
    const familyId = document.getElementById('familySelect')?.value;
    
    if (!familyId) {
        appAlert('Please select a family');
        return;
    }
    
    assignUserToFamily(userId, familyId);
}

function assignUserToFamily(userId, familyId) {
    fetch('/social-fellowship/users/assign-to-family', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            user_id: userId,
            family_id: familyId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            appAlert('User assigned to family successfully!');
            location.reload();
        } else {
            appAlert('Error: ' + (data.message || 'Failed to assign user'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        appAlert('Error assigning user to family');
    });
    
    // Close modal
    const modal = document.getElementById('assignFamilyModal');
    if (modal) {
        modal.classList.add('hidden');
    }
}
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
