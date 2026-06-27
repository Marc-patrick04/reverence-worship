<div class="space-y-6">
    <!-- Header with Year Selection -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Financial Dashboard</h2>
            <p class="text-sm text-gray-500 mt-1">Overview of your financial performance</p>
        </div>
        <div class="flex items-center gap-2">
            <!-- Year Picker -->
            <div class="relative">
                <div onclick="toggleOverviewYearPicker()" 
                    class="flex items-center justify-between border border-gray-300 rounded-lg px-3 py-2 bg-white cursor-pointer hover:border-blue-400 transition-all min-w-[120px]">
                    <span id="overviewYearDisplay" class="text-sm font-semibold text-gray-800">{{ date('Y') }}</span>
                    <svg class="w-4 h-4 text-gray-400 transition-transform duration-200 ml-2" id="overviewYearArrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>
                <input type="hidden" id="overviewSelectedYear" value="{{ date('Y') }}">
                
                <!-- Year Picker Dropdown - 3x3 Grid -->
                <div id="overviewYearPickerDropdown" class="hidden absolute top-full left-0 right-0 mt-1 bg-white border border-gray-200 rounded-lg shadow-xl z-50 p-3 min-w-[200px]">
                    <div class="flex items-center justify-between mb-2">
                        <button type="button" onclick="changeOverviewYearPage(-1)" 
                            class="p-1 hover:bg-gray-100 rounded transition text-gray-500 hover:text-gray-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </button>
                        <span id="overviewYearPageTitle" class="text-xs font-medium text-gray-600">2018 - 2024</span>
                        <button type="button" onclick="changeOverviewYearPage(1)" 
                            class="p-1 hover:bg-gray-100 rounded transition text-gray-500 hover:text-gray-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="grid grid-cols-3 gap-1" id="overviewYearGrid">
                        <!-- Years populated by JavaScript -->
                    </div>
                </div>
            </div>
            <button onclick="refreshAllCharts()" 
                    class="px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
            </button>
        </div>
    </div>

    <!-- KPI Cards Row -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <!-- Total Revenue Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Revenue</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1" id="overviewTotalIncome">RWF 0</p>
                </div>
                <div class="w-11 h-11 bg-gray-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Expenses Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Expenses</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1" id="overviewTotalExpenses">RWF 0</p>
                </div>
                <div class="w-11 h-11 bg-gray-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Collection Rate Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Collection Rate</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1" id="overviewCollectionRate">0%</p>
                    <div class="w-full bg-gray-200 rounded-full h-1.5 mt-2">
                        <div id="collectionBar" class="bg-blue-600 h-1.5 rounded-full transition-all" style="width: 0%"></div>
                    </div>
                </div>
                <div class="w-11 h-11 bg-gray-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Stats Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Member Contributions Detail -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-5 py-3.5 bg-gray-50 border-b border-gray-200">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <h3 class="font-semibold text-gray-800 text-sm">Member Contributions</h3>
                </div>
            </div>
            <div class="p-5 space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500">Expected Amount</span>
                    <span class="font-semibold text-gray-800" id="overviewTotalExpected">RWF 0</span>
                </div>
               
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500">Collected Amount</span>
                    <span class="font-semibold text-green-600" id="overviewTotalCollected">RWF 0</span>
                </div>
                
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500">Outstanding</span>
                    <span class="font-semibold text-amber-600" id="overviewOutstanding">RWF 0</span>
                </div>
                <div class="mt-2 pt-3 border-t border-gray-100">
                    <div class="flex justify-between text-xs text-gray-500 mb-1">
                        <span>Progress</span>
                        <span id="collectionProgress">0%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-1.5">
                        <div id="collectionProgressBar" class="bg-blue-600 h-1.5 rounded-full transition-all" style="width: 0%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gift Summary Detail -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-5 py-3.5 bg-gray-50 border-b border-gray-200">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"></path>
                    </svg>
                    <h3 class="font-semibold text-gray-800 text-sm">Gift Summary</h3>
                </div>
            </div>
            <div class="p-5 space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500">Commitments</span>
                    <span class="font-semibold text-gray-800" id="overviewGiftCommitments">RWF 0</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500">Received</span>
                    <span class="font-semibold text-green-600" id="overviewGiftReceived">RWF 0</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500">Pending</span>
                    <span class="font-semibold text-amber-600" id="overviewGiftPending">RWF 0</span>
                </div>
                <div class="mt-2 pt-2 border-t border-gray-100">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                        Active Gifts: <span id="overviewActiveGifts" class="ml-1 font-bold">0</span>
                    </span>
                </div>
            </div>
        </div>

        <!-- Sponsor Summary Detail -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-5 py-3.5 bg-gray-50 border-b border-gray-200">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <h3 class="font-semibold text-gray-800 text-sm">Sponsor Summary</h3>
                </div>
            </div>
            <div class="p-5 space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500">Commitments</span>
                    <span class="font-semibold text-gray-800" id="overviewSponsorCommitments">RWF 0</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500">Received</span>
                    <span class="font-semibold text-green-600" id="overviewSponsorReceived">RWF 0</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500">Pending</span>
                    <span class="font-semibold text-amber-600" id="overviewSponsorPending">RWF 0</span>
                </div>
                <div class="mt-2 pt-2 border-t border-gray-100">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                        Active Funds: <span id="overviewActiveFunds" class="ml-1 font-bold">0</span>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// ============================================
// OVERVIEW YEAR PICKER
// ============================================

let overviewCurrentYear = new Date().getFullYear();
let overviewYearPageOffset = 0;

// Toggle Year Picker
function toggleOverviewYearPicker() {
    const dropdown = document.getElementById('overviewYearPickerDropdown');
    const arrow = document.getElementById('overviewYearArrow');
    
    if (dropdown.classList.contains('hidden')) {
        dropdown.classList.remove('hidden');
        arrow.classList.add('rotate-180');
        renderOverviewYearGrid();
    } else {
        dropdown.classList.add('hidden');
        arrow.classList.remove('rotate-180');
    }
}

// Close year picker
function closeOverviewYearPicker() {
    const dropdown = document.getElementById('overviewYearPickerDropdown');
    const arrow = document.getElementById('overviewYearArrow');
    
    if (dropdown && !dropdown.classList.contains('hidden')) {
        dropdown.classList.add('hidden');
        arrow.classList.remove('rotate-180');
    }
}

// Change year page
function changeOverviewYearPage(direction) {
    overviewYearPageOffset += direction;
    renderOverviewYearGrid();
}

// Render 3x3 Year Grid
function renderOverviewYearGrid() {
    const currentYear = new Date().getFullYear();
    const startYear = currentYear + (overviewYearPageOffset * 9) - 4;
    
    const grid = document.getElementById('overviewYearGrid');
    const title = document.getElementById('overviewYearPageTitle');
    
    if (!grid) return;
    
    const endYear = startYear + 8;
    title.textContent = `${startYear} - ${endYear}`;
    
    grid.innerHTML = '';
    
    for (let i = 0; i < 9; i++) {
        const year = startYear + i;
        const isSelected = year == overviewCurrentYear;
        const isCurrentYear = year == currentYear;
        const isDisabled = year < 2000 || year > 2100;
        
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.textContent = year;
        btn.className = 'year-grid-btn py-1.5 px-2 rounded text-xs transition-all text-center';
        
        if (isSelected) {
            btn.classList.add('bg-blue-600', 'text-white', 'font-semibold', 'shadow-sm');
        } else if (isCurrentYear) {
            btn.classList.add('bg-blue-50', 'text-blue-600', 'font-medium', 'border', 'border-blue-200');
        } else {
            btn.classList.add('text-gray-700', 'hover:bg-gray-100');
        }
        
        if (isDisabled) {
            btn.classList.add('text-gray-300', 'cursor-not-allowed');
            btn.disabled = true;
        } else {
            btn.onclick = function() {
                selectOverviewYear(year);
            };
        }
        
        grid.appendChild(btn);
    }
}

// Select a year
function selectOverviewYear(year) {
    overviewCurrentYear = year;
    document.getElementById('overviewSelectedYear').value = year;
    document.getElementById('overviewYearDisplay').textContent = year;
    
    closeOverviewYearPicker();
    renderOverviewYearGrid();
    
    // Reload data with new year
    loadFinanceOverview();
}

// ============================================
// CURRENCY FORMATTING
// ============================================

function formatCurrency(amount) {
    const num = Number(amount || 0);
    return 'RWF ' + num.toLocaleString('en-RW', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    });
}

function formatNumber(num) {
    return Number(num || 0).toLocaleString();
}

// ============================================
// LOAD FINANCE DATA
// ============================================

async function loadFinanceOverview() {
    const year = overviewCurrentYear || new Date().getFullYear();
    
    try {
        const response = await fetch(`/finance/overview/stats?year=${year}`, {
            headers: { 
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (data.success && data.stats) {
            const stats = data.stats;
            
            // Update KPI Cards (3 cards now)
            const totalIncome = parseFloat(stats.total_income) || 0;
            const totalExpenses = parseFloat(stats.total_expenses) || 0;
            
            document.getElementById('overviewTotalIncome').textContent = formatCurrency(totalIncome);
            document.getElementById('overviewTotalExpenses').textContent = formatCurrency(totalExpenses);
            
            const collectionRate = parseFloat(stats.collection_rate) || 0;
            document.getElementById('overviewCollectionRate').textContent = collectionRate + '%';
            document.getElementById('collectionBar').style.width = Math.min(collectionRate, 100) + '%';
            
            // Update detailed stats
            document.getElementById('overviewTotalExpected').textContent = formatCurrency(stats.total_expected);
            
            document.getElementById('overviewTotalCollected').textContent = formatCurrency(stats.total_collected);
           
            
            const outstanding = (parseFloat(stats.total_expected) || 0) - (parseFloat(stats.total_collected) || 0);
            document.getElementById('overviewOutstanding').textContent = formatCurrency(outstanding);
            document.getElementById('collectionProgress').textContent = collectionRate + '%';
            document.getElementById('collectionProgressBar').style.width = Math.min(collectionRate, 100) + '%';
            
            document.getElementById('overviewGiftCommitments').textContent = formatCurrency(stats.gift_commitments);
            document.getElementById('overviewGiftReceived').textContent = formatCurrency(stats.gift_received);
            const giftPending = (parseFloat(stats.gift_commitments) || 0) - (parseFloat(stats.gift_received) || 0);
            document.getElementById('overviewGiftPending').textContent = formatCurrency(giftPending);
            document.getElementById('overviewActiveGifts').textContent = formatNumber(stats.active_gifts);
            
            document.getElementById('overviewSponsorCommitments').textContent = formatCurrency(stats.sponsor_commitments);
            document.getElementById('overviewSponsorReceived').textContent = formatCurrency(stats.sponsor_received);
            const sponsorPending = (parseFloat(stats.sponsor_commitments) || 0) - (parseFloat(stats.sponsor_received) || 0);
            document.getElementById('overviewSponsorPending').textContent = formatCurrency(sponsorPending);
            document.getElementById('overviewActiveFunds').textContent = formatNumber(stats.active_funds);
            
            // Remove any $ signs that might have slipped through
            setTimeout(removeDollarSigns, 50);
            
        } else {
            console.error('Failed to load finance overview:', data.message);
        }
    } catch (error) {
        console.error('Failed to load finance overview:', error);
    }
}

// ============================================
// REMOVE $ SIGNS
// ============================================

function removeDollarSigns() {
    const elements = document.querySelectorAll('#overviewTotalIncome, #overviewTotalExpenses, #overviewTotalExpected, #overviewTotalCollected, #overviewOutstanding, #overviewGiftCommitments, #overviewGiftReceived, #overviewGiftPending, #overviewSponsorCommitments, #overviewSponsorReceived, #overviewSponsorPending');
    elements.forEach(el => {
        if (el && el.textContent) {
            let text = el.textContent;
            if (text.includes('$')) {
                text = text.replace(/\$/g, 'RWF');
                el.textContent = text;
            }
        }
    });
}

function refreshAllCharts() {
    loadFinanceOverview();
}

// ============================================
// INITIALIZE
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    const currentYear = new Date().getFullYear();
    overviewCurrentYear = currentYear;
    document.getElementById('overviewSelectedYear').value = currentYear;
    document.getElementById('overviewYearDisplay').textContent = currentYear;
    renderOverviewYearGrid();
    loadFinanceOverview();
    removeDollarSigns();
});

// Auto-refresh every 30 seconds
let autoRefreshInterval = setInterval(() => {
    const overviewTab = document.getElementById('overview-tab');
    if (overviewTab && !overviewTab.classList.contains('hidden')) {
        loadFinanceOverview();
        removeDollarSigns();
    }
}, 30000);

window.addEventListener('beforeunload', function() {
    if (autoRefreshInterval) {
        clearInterval(autoRefreshInterval);
    }
});

// Close year picker when clicking outside
document.addEventListener('click', function(event) {
    const picker = document.getElementById('overviewYearPickerDropdown');
    const display = document.querySelector('#overviewYearDisplay');
    
    if (picker && !picker.classList.contains('hidden') && display) {
        const parentDiv = display.closest('.relative');
        if (parentDiv && !parentDiv.contains(event.target)) {
            closeOverviewYearPicker();
        }
    }
});

// Expose functions globally
window.refreshFinanceOverview = loadFinanceOverview;
window.refreshAllCharts = refreshAllCharts;
window.removeDollarSigns = removeDollarSigns;
</script>

<style>
.year-grid-btn {
    transition: all 0.2s ease;
    cursor: pointer;
    min-height: 32px;
}

.year-grid-btn:hover:not(:disabled) {
    transform: translateY(-1px);
    box-shadow: 0 2px 6px rgba(0,0,0,0.08);
}

.year-grid-btn:disabled {
    cursor: not-allowed;
    opacity: 0.5;
}

#overviewYearPickerDropdown {
    animation: fade-in 0.15s ease-out;
}

.rotate-180 {
    transform: rotate(180deg);
}

@keyframes fade-in {
    from { opacity: 0; transform: translateY(-5px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>