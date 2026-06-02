@extends('layouts.app')

@section('title', 'Intercession & Spiritual Growth')
@section('page-title', 'Intercession & Spiritual Growth')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    {{-- HEADER --}}
    

    {{-- TABS --}}
    <div class="border-b border-gray-200">
        <nav class="flex space-x-8 overflow-x-auto">
            <button onclick="showTab('forms')" id="tab-forms" class="tab-btn py-2 px-1 border-b-2 font-medium text-sm transition">
                <i class="fas fa-file-alt mr-2"></i>Forms
            </button>
            <button onclick="showTab('devotions')" id="tab-devotions" class="tab-btn py-2 px-1 border-b-2 font-medium text-sm transition">
                <i class="fas fa-praying-hands mr-2"></i>Devotions
            </button>
            <button onclick="showTab('actions')" id="tab-actions" class="tab-btn py-2 px-1 border-b-2 font-medium text-sm transition">
                <i class="fas fa-tasks mr-2"></i>Action Plans
            </button>
            <button onclick="showTab('archives')" id="tab-archives" class="tab-btn py-2 px-1 border-b-2 font-medium text-sm transition">
                <i class="fas fa-archive mr-2"></i>Archives
            </button>
        </nav>
    </div>

    {{-- FORMS TAB --}}
    <div id="forms-tab" class="tab-content">
        @include('modules.intercession.partials.forms')
    </div>

    {{-- DEVOTIONS TAB --}}
    <div id="devotions-tab" class="tab-content hidden">
        @include('modules.intercession.partials.devotions')
    </div>

    {{-- ACTION PLANS TAB --}}
    <div id="actions-tab" class="tab-content hidden">
        @include('modules.intercession.partials.actions')
    </div>

    {{-- ARCHIVES TAB --}}
    <div id="archives-tab" class="tab-content hidden">
        @include('modules.intercession.partials.archives-tab')
    </div>

</div>

{{-- MODALS - Include at the bottom of the page --}}
@include('modules.intercession.partials.modals')

<script>
// Function to switch tabs with persistence
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
    const selectedBtn = document.getElementById(`tab-${tabName}`);
    if (selectedBtn) {
        selectedBtn.classList.remove('border-transparent', 'text-gray-500');
        selectedBtn.classList.add('border-blue-600', 'text-blue-600');
    }
    
    // Save current tab to localStorage
    localStorage.setItem('activeIntercessionTab', tabName);
}

// On page load, restore the last active tab
document.addEventListener('DOMContentLoaded', function() {
    const savedTab = localStorage.getItem('activeIntercessionTab');
    const validTabs = ['forms', 'devotions', 'actions', 'archives'];
    
    if (savedTab && validTabs.includes(savedTab)) {
        showTab(savedTab);
    } else {
        // Default to forms tab
        showTab('forms');
    }
});
</script>

<style>
.tab-btn { 
    transition: all 0.3s ease; 
}
.tab-btn:hover { 
    opacity: 0.8; 
}
</style>
@endsection