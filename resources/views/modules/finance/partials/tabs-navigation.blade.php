<div class="bg-white rounded-xl shadow-md overflow-hidden mb-4 sm:mb-6">
    <!-- Desktop Tabs (hidden on mobile) -->
    <div class="hidden md:block border-b border-gray-200">
        <nav class="flex flex-wrap">
            <button class="tab-btn px-4 lg:px-6 py-3 text-sm font-medium transition whitespace-nowrap" data-tab="overview">
                <i class="fas fa-chart-line mr-2"></i> Overview
            </button>
            <button class="tab-btn px-4 lg:px-6 py-3 text-sm font-medium transition whitespace-nowrap" data-tab="contributions">
                <i class="fas fa-hand-holding-usd mr-2"></i> Contributions
            </button>
            <button class="tab-btn px-4 lg:px-6 py-3 text-sm font-medium transition whitespace-nowrap" data-tab="gifts">
                <i class="fas fa-gift mr-2"></i> Gifts
            </button>
            <button class="tab-btn px-4 lg:px-6 py-3 text-sm font-medium transition whitespace-nowrap" data-tab="sponsors">
                <i class="fas fa-users mr-2"></i> Sponsors
            </button>
            <button class="tab-btn px-4 lg:px-6 py-3 text-sm font-medium transition whitespace-nowrap" data-tab="expenses">
                <i class="fas fa-receipt mr-2"></i> Expenses
            </button>
            <button class="tab-btn px-4 lg:px-6 py-3 text-sm font-medium transition whitespace-nowrap" data-tab="budget">
                <i class="fas fa-chart-pie mr-2"></i> Budget
            </button>
            <button class="tab-btn px-4 lg:px-6 py-3 text-sm font-medium transition whitespace-nowrap" data-tab="reports">
                <i class="fas fa-file-alt mr-2"></i> Reports
            </button>
            <button class="tab-btn px-4 lg:px-6 py-3 text-sm font-medium transition whitespace-nowrap" data-tab="settings">
                <i class="fas fa-cog mr-2"></i> Settings
            </button>
        </nav>
    </div>

    <!-- Tablet Tabs (Scrollable) -->
    <div class="hidden sm:block md:hidden border-b border-gray-200 overflow-x-auto">
        <nav class="flex flex-nowrap min-w-max">
            <button class="tab-btn px-4 py-3 text-sm font-medium transition whitespace-nowrap" data-tab="overview">
                <i class="fas fa-chart-line mr-1"></i> Overview
            </button>
            <button class="tab-btn px-4 py-3 text-sm font-medium transition whitespace-nowrap" data-tab="contributions">
                <i class="fas fa-hand-holding-usd mr-1"></i> Contributions
            </button>
            <button class="tab-btn px-4 py-3 text-sm font-medium transition whitespace-nowrap" data-tab="gifts">
                <i class="fas fa-gift mr-1"></i> Gifts
            </button>
            <button class="tab-btn px-4 py-3 text-sm font-medium transition whitespace-nowrap" data-tab="sponsors">
                <i class="fas fa-users mr-1"></i> Sponsors
            </button>
            <button class="tab-btn px-4 py-3 text-sm font-medium transition whitespace-nowrap" data-tab="expenses">
                <i class="fas fa-receipt mr-1"></i> Expenses
            </button>
            <button class="tab-btn px-4 py-3 text-sm font-medium transition whitespace-nowrap" data-tab="budget">
                <i class="fas fa-chart-pie mr-1"></i> Budget
            </button>
            <button class="tab-btn px-4 py-3 text-sm font-medium transition whitespace-nowrap" data-tab="reports">
                <i class="fas fa-file-alt mr-1"></i> Reports
            </button>
            <button class="tab-btn px-4 py-3 text-sm font-medium transition whitespace-nowrap" data-tab="settings">
                <i class="fas fa-cog mr-1"></i> Settings
            </button>
        </nav>
    </div>

    <!-- Mobile Tabs (Dropdown Menu) -->
    <div class="sm:hidden">
        <!-- Selected Tab Display -->
        <div class="flex items-center justify-between border-b border-gray-200 px-4 py-3">
            <div class="flex items-center gap-2">
                <i id="mobileActiveIcon" class="fas fa-chart-line text-blue-600 text-sm"></i>
                <span id="mobileActiveTab" class="text-sm font-medium text-gray-800">Overview</span>
            </div>
            <button onclick="toggleMobileTabsMenu()" class="p-2 hover:bg-gray-100 rounded-lg transition active:bg-gray-200">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
        </div>
        
        <!-- Mobile Dropdown Menu -->
        <div id="mobileTabsMenu" class="hidden border-b border-gray-100 bg-gray-50">
            <div class="grid grid-cols-2 gap-1 p-2">
                <button class="mobile-tab-btn flex items-center gap-2 px-3 py-2.5 text-sm rounded-lg transition" data-tab="overview" data-icon="chart-line">
                    <i class="fas fa-chart-line text-blue-500 w-4"></i>
                    <span>Overview</span>
                </button>
                <button class="mobile-tab-btn flex items-center gap-2 px-3 py-2.5 text-sm rounded-lg transition" data-tab="contributions" data-icon="hand-holding-usd">
                    <i class="fas fa-hand-holding-usd text-green-500 w-4"></i>
                    <span>Contributions</span>
                </button>
                <button class="mobile-tab-btn flex items-center gap-2 px-3 py-2.5 text-sm rounded-lg transition" data-tab="gifts" data-icon="gift">
                    <i class="fas fa-gift text-purple-500 w-4"></i>
                    <span>Gifts</span>
                </button>
                <button class="mobile-tab-btn flex items-center gap-2 px-3 py-2.5 text-sm rounded-lg transition" data-tab="sponsors" data-icon="users">
                    <i class="fas fa-users text-teal-500 w-4"></i>
                    <span>Sponsors</span>
                </button>
                <button class="mobile-tab-btn flex items-center gap-2 px-3 py-2.5 text-sm rounded-lg transition" data-tab="expenses" data-icon="receipt">
                    <i class="fas fa-receipt text-red-500 w-4"></i>
                    <span>Expenses</span>
                </button>
                <button class="mobile-tab-btn flex items-center gap-2 px-3 py-2.5 text-sm rounded-lg transition" data-tab="budget" data-icon="chart-pie">
                    <i class="fas fa-chart-pie text-yellow-500 w-4"></i>
                    <span>Budget</span>
                </button>
                <button class="mobile-tab-btn flex items-center gap-2 px-3 py-2.5 text-sm rounded-lg transition" data-tab="reports" data-icon="file-alt">
                    <i class="fas fa-file-alt text-indigo-500 w-4"></i>
                    <span>Reports</span>
                </button>
                <button class="mobile-tab-btn flex items-center gap-2 px-3 py-2.5 text-sm rounded-lg transition" data-tab="settings" data-icon="cog">
                    <i class="fas fa-cog text-gray-500 w-4"></i>
                    <span>Settings</span>
                </button>
            </div>
        </div>
    </div>
</div>

<style>
/* Smooth transitions */
#mobileTabsMenu {
    transition: all 0.3s ease;
    max-height: 0;
    overflow: hidden;
}

#mobileTabsMenu.show {
    max-height: 500px;
}

/* Touch-friendly button sizing */
.tab-btn, .mobile-tab-btn {
    cursor: pointer;
    -webkit-tap-highlight-color: transparent;
    touch-action: manipulation;
}

.tab-btn:active, .mobile-tab-btn:active {
    transform: scale(0.97);
}

/* Active tab styles */
.tab-btn.active {
    color: #2563eb;
    border-bottom-color: #2563eb;
    border-bottom-width: 2px;
}

.tab-btn:not(.active) {
    color: #6b7280;
    border-bottom-color: transparent;
}

.tab-btn:not(.active):hover {
    color: #374151;
    border-bottom-color: #e5e7eb;
}

/* Mobile active state */
.mobile-tab-btn.active-mobile {
    background-color: #e0e7ff;
    color: #1e40af;
}

/* Better touch targets on mobile */
@media (max-width: 640px) {
    .tab-btn, .mobile-tab-btn {
        min-height: 44px;
    }
}

/* Scrollbar styling for tablet view */
.overflow-x-auto::-webkit-scrollbar {
    height: 3px;
}

.overflow-x-auto::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>

<script>
// Tab configuration for mobile display
const mobileTabConfig = {
    overview: { icon: 'chart-line', label: 'Overview' },
    contributions: { icon: 'hand-holding-usd', label: 'Contributions' },
    gifts: { icon: 'gift', label: 'Gifts' },
    sponsors: { icon: 'users', label: 'Sponsors' },
    expenses: { icon: 'receipt', label: 'Expenses' },
    budget: { icon: 'chart-pie', label: 'Budget' },
    reports: { icon: 'file-alt', label: 'Reports' },
    settings: { icon: 'cog', label: 'Settings' }
};

// Toggle mobile menu
function toggleMobileTabsMenu() {
    const menu = document.getElementById('mobileTabsMenu');
    if (menu) {
        menu.classList.toggle('hidden');
        menu.classList.toggle('show');
    }
}

// Close mobile menu
function closeMobileTabsMenu() {
    const menu = document.getElementById('mobileTabsMenu');
    if (menu && !menu.classList.contains('hidden')) {
        menu.classList.add('hidden');
        menu.classList.remove('show');
    }
}

// Update mobile active display
function updateMobileActiveDisplay(tabName) {
    if (window.innerWidth >= 640) return;
    
    const config = mobileTabConfig[tabName] || mobileTabConfig.overview;
    const mobileActiveIcon = document.getElementById('mobileActiveIcon');
    const mobileActiveTab = document.getElementById('mobileActiveTab');
    
    if (mobileActiveIcon) {
        mobileActiveIcon.className = `fas fa-${config.icon} text-blue-600 text-sm`;
    }
    if (mobileActiveTab) {
        mobileActiveTab.textContent = config.label;
    }
}

// Set active tab (to be called from main index)
function setActiveTab(tabName) {
    // Update desktop tab buttons
    const tabs = document.querySelectorAll('.tab-btn');
    tabs.forEach(tab => {
        const tabBtnName = tab.getAttribute('data-tab');
        if (tabBtnName === tabName) {
            tab.classList.add('active');
        } else {
            tab.classList.remove('active');
        }
    });
    
    // Update mobile active display
    updateMobileActiveDisplay(tabName);
    
    // Update mobile menu items active state
    const mobileTabs = document.querySelectorAll('.mobile-tab-btn');
    mobileTabs.forEach(tab => {
        const tabBtnName = tab.getAttribute('data-tab');
        if (tabBtnName === tabName) {
            tab.classList.add('active-mobile');
        } else {
            tab.classList.remove('active-mobile');
        }
    });
}

// Setup mobile tab click handlers
function setupMobileTabHandlers() {
    const mobileTabs = document.querySelectorAll('.mobile-tab-btn');
    mobileTabs.forEach(tab => {
        tab.removeEventListener('click', handleMobileTabClick);
        tab.addEventListener('click', handleMobileTabClick);
    });
}

function handleMobileTabClick(event) {
    const tab = event.currentTarget;
    const tabName = tab.getAttribute('data-tab');
    const icon = tab.getAttribute('data-icon');
    const label = tab.querySelector('span')?.innerText || mobileTabConfig[tabName]?.label || tabName;
    
    // Update mobile header display
    const mobileActiveIcon = document.getElementById('mobileActiveIcon');
    const mobileActiveTab = document.getElementById('mobileActiveTab');
    
    if (mobileActiveIcon) {
        mobileActiveIcon.className = `fas fa-${icon} text-blue-600 text-sm`;
    }
    if (mobileActiveTab) {
        mobileActiveTab.textContent = label;
    }
    
    // Close mobile menu
    closeMobileTabsMenu();
    
    // Dispatch custom event for tab change
    const tabChangeEvent = new CustomEvent('tabChange', { detail: { tab: tabName } });
    document.dispatchEvent(tabChangeEvent);
}

// Close menu when clicking outside
function setupOutsideClickClose() {
    document.addEventListener('click', function(event) {
        const menu = document.getElementById('mobileTabsMenu');
        const headerDiv = document.querySelector('.sm\\:hidden .flex.items-center');
        
        if (window.innerWidth < 640 && menu && !menu.classList.contains('hidden')) {
            if (headerDiv && !headerDiv.contains(event.target)) {
                closeMobileTabsMenu();
            }
        }
    });
}

// Handle window resize
function setupResizeHandler() {
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 640) {
            closeMobileTabsMenu();
        }
    });
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    setupMobileTabHandlers();
    setupOutsideClickClose();
    setupResizeHandler();
});

// Expose functions globally
window.toggleMobileTabsMenu = toggleMobileTabsMenu;
window.closeMobileTabsMenu = closeMobileTabsMenu;
window.setActiveTab = setActiveTab;
window.updateMobileActiveDisplay = updateMobileActiveDisplay;
</script>