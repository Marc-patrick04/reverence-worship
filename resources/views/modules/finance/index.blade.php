@extends('layouts.app')

@section('title', 'Financial Management')
@section('page-title', 'Financial Management')

@section('content')
<div class="container mx-auto px-4 py-8">
    
    <!-- Tabs Navigation -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden mb-6">
        <div class="border-b border-gray-200 overflow-x-auto">
            <nav class="flex flex-nowrap min-w-max">
                <button class="tab-btn px-6 py-3 text-sm font-medium border-b-2 transition" data-tab="overview">
                    <i class="fas fa-chart-line mr-2"></i> Overview
                </button>
                <button class="tab-btn px-6 py-3 text-sm font-medium border-b-2 transition" data-tab="settings">
                    <i class="fas fa-cog mr-2"></i> Settings
                </button>
                <button class="tab-btn px-6 py-3 text-sm font-medium border-b-2 transition" data-tab="contributions">
                    <i class="fas fa-hand-holding-usd mr-2"></i> Contributions
                </button>
                <button class="tab-btn px-6 py-3 text-sm font-medium border-b-2 transition" data-tab="payments">
                    <i class="fas fa-credit-card mr-2"></i> Payments
                </button>
                <button class="tab-btn px-6 py-3 text-sm font-medium border-b-2 transition" data-tab="sponsors">
                    <i class="fas fa-users mr-2"></i> Sponsors
                </button>
                <button class="tab-btn px-6 py-3 text-sm font-medium border-b-2 transition" data-tab="expenses">
                    <i class="fas fa-receipt mr-2"></i> Expenses
                </button>
                <button class="tab-btn px-6 py-3 text-sm font-medium border-b-2 transition" data-tab="reports">
                    <i class="fas fa-chart-bar mr-2"></i> Reports
                </button>
                <button class="tab-btn px-6 py-3 text-sm font-medium border-b-2 transition" data-tab="action-plans">
                    <i class="fas fa-tasks mr-2"></i> Action Plans
                </button>
            </nav>
        </div>
    </div>
    
    <!-- Tab Content -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="p-6">
            <!-- Overview Tab -->
            <div id="overview-tab" class="tab-content">
                @include('modules.finance.partials.overview-tab')
            </div>
            
            <!-- Settings Tab -->
            <div id="settings-tab" class="tab-content hidden">
                @include('modules.finance.partials.settings-tab')
            </div>
            
            <!-- Contributions Tab -->
            <div id="contributions-tab" class="tab-content hidden">
                @include('modules.finance.partials.contributions-tab')
            </div>
            
            <!-- Payments Tab -->
            <div id="payments-tab" class="tab-content hidden">
                @include('modules.finance.partials.payments-tab')
            </div>
            
            <!-- Sponsors Tab -->
            <div id="sponsors-tab" class="tab-content hidden">
                @include('modules.finance.partials.sponsors-tab')
            </div>
            
            <!-- Expenses Tab -->
            <div id="expenses-tab" class="tab-content hidden">
                @include('modules.finance.partials.expenses-tab')
            </div>
            
            <!-- Reports Tab -->
            <div id="reports-tab" class="tab-content hidden">
                @include('modules.finance.partials.reports-tab')
            </div>
            
            <!-- Action Plans Tab -->
            <div id="action-plans-tab" class="tab-content hidden">
                @include('modules.finance.partials.action-plans-tab')
            </div>
        </div>
    </div>
</div>

@include('modules.finance.partials.modals')

<script>
// Tab Management with localStorage persistence
const STORAGE_KEY = 'finance_active_tab';

document.addEventListener('DOMContentLoaded', function() {
    // Get saved tab from localStorage
    const savedTab = localStorage.getItem(STORAGE_KEY);
    const defaultTab = 'overview';
    const activeTab = savedTab && isValidTab(savedTab) ? savedTab : defaultTab;
    
    // Initialize tabs - hide all first
    initializeTabs();
    
    // Activate the saved or default tab
    activateTab(activeTab);
    
    // Set up tab click handlers
    setupTabClickHandlers();
});

function isValidTab(tabName) {
    const validTabs = ['overview', 'settings', 'contributions', 'payments', 'sponsors', 'expenses', 'reports', 'action-plans'];
    return validTabs.includes(tabName);
}

function initializeTabs() {
    // Hide all tab contents initially
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });
}

function setupTabClickHandlers() {
    const tabs = document.querySelectorAll('.tab-btn');
    
    tabs.forEach(tab => {
        tab.removeEventListener('click', handleTabClick);
        tab.addEventListener('click', handleTabClick);
    });
}

function handleTabClick(event) {
    const tab = event.currentTarget;
    const tabName = tab.getAttribute('data-tab');
    
    // Save to localStorage
    localStorage.setItem(STORAGE_KEY, tabName);
    
    // Activate the tab
    activateTab(tabName);
}

function activateTab(tabName) {
    // Update tab buttons styles
    const tabs = document.querySelectorAll('.tab-btn');
    const inactiveClasses = 'text-gray-500 border-transparent hover:text-gray-700 hover:border-gray-300';
    const activeClasses = 'text-blue-600 border-blue-600';
    
    tabs.forEach(tab => {
        const tabBtnName = tab.getAttribute('data-tab');
        // Remove all classes and add base classes
        tab.classList.remove('text-blue-600', 'border-blue-600', 'text-gray-500', 'border-transparent');
        tab.classList.add('text-gray-500', 'border-transparent');
        
        if (tabBtnName === tabName) {
            tab.classList.remove('text-gray-500', 'border-transparent');
            tab.classList.add('text-blue-600', 'border-blue-600');
        }
    });
    
    // Update tab content visibility
    const tabContents = document.querySelectorAll('.tab-content');
    tabContents.forEach(content => {
        content.classList.add('hidden');
    });
    
    const activeContent = document.getElementById(`${tabName}-tab`);
    if (activeContent) {
        activeContent.classList.remove('hidden');
    }
    
    // Load tab-specific data when tab is activated
    setTimeout(() => {
        loadTabData(tabName);
    }, 100);
}

function loadTabData(tabName) {
    switch(tabName) {
        case 'contributions':
            if (typeof loadContributions === 'function') {
                loadContributions();
            }
            break;
        case 'payments':
            if (typeof loadPayments === 'function') {
                loadPayments();
            }
            break;
        case 'sponsors':
            if (typeof loadSponsors === 'function') {
                loadSponsors();
            }
            break;
        case 'expenses':
            if (typeof loadExpenses === 'function') {
                loadExpenses();
            }
            break;
        case 'action-plans':
            if (typeof loadActionPlans === 'function') {
                loadActionPlans();
            }
            break;
        case 'settings':
            if (typeof loadSettings === 'function') {
                loadSettings();
            }
            break;
        case 'reports':
            if (typeof loadReports === 'function') {
                loadReports();
            }
            break;
        default:
            // overview tab - no additional data needed
            if (tabName === 'overview' && typeof loadOverviewStats === 'function') {
                loadOverviewStats();
            }
            break;
    }
}

// Define load functions for each tab
window.loadOverviewStats = function() {
    fetch('/finance/overview/stats', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateOverviewStats(data.stats);
        }
    })
    .catch(error => console.error('Error loading overview stats:', error));
};

function updateOverviewStats(stats) {
    // Update overview stats if elements exist
    const totalIncomeEl = document.getElementById('totalIncome');
    const totalExpensesEl = document.getElementById('totalExpenses');
    const totalExpectedEl = document.getElementById('totalExpected');
    const totalCollectedEl = document.getElementById('totalCollected');
    const collectionRateEl = document.getElementById('collectionRate');
    
    if (totalIncomeEl) totalIncomeEl.textContent = formatCurrency(stats.total_income);
    if (totalExpensesEl) totalExpensesEl.textContent = formatCurrency(stats.total_expenses);
    if (totalExpectedEl) totalExpectedEl.textContent = formatCurrency(stats.total_expected);
    if (totalCollectedEl) totalCollectedEl.textContent = formatCurrency(stats.total_collected);
    if (collectionRateEl) collectionRateEl.textContent = stats.collection_rate + '%';
    
    // Update progress bar if exists
    const progressBar = document.getElementById('collectionProgressBar');
    if (progressBar) {
        progressBar.style.width = stats.collection_rate + '%';
    }
}

function formatCurrency(amount) {
    return '$' + parseFloat(amount || 0).toLocaleString(undefined, {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

// Close modal function (needed by modals)
window.closeModal = function(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('hidden');
    }
};
</script>
@endsection