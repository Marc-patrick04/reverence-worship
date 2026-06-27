<div class="space-y-6">
    <!-- Header with Date Range Selection -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Financial Dashboard</h2>
            <p class="text-sm text-gray-500 mt-1">Overview of your financial performance</p>
        </div>
        <div class="flex flex-wrap items-end gap-2">
            <div>
                <label for="overviewFromDate" class="block text-xs font-medium text-gray-500 mb-1">From date</label>
                <input type="date" id="overviewFromDate" value="{{ date('Y-01-01') }}"
                    class="border border-gray-300 rounded-lg px-3 py-2 bg-white text-sm text-gray-800 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
            </div>
            <div>
                <label for="overviewToDate" class="block text-xs font-medium text-gray-500 mb-1">To date</label>
                <input type="date" id="overviewToDate" value="{{ date('Y-12-31') }}"
                    class="border border-gray-300 rounded-lg px-3 py-2 bg-white text-sm text-gray-800 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
            </div>
            <button onclick="refreshAllCharts()" title="Refresh overview"
                    class="px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
            </button>
        </div>
    </div>

    <!-- KPI Cards Row -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
        <!-- Total Revenue Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-semibold text-gray-500 uppercase">Total Revenue</p>
                    <p class="text-2xl sm:text-3xl font-bold text-gray-800 mt-1 break-words" id="overviewTotalIncome">RWF 0</p>
                </div>
                <div class="w-12 h-12 sm:w-14 sm:h-14 shrink-0 bg-gray-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 sm:w-7 sm:h-7 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Expenses Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-semibold text-gray-500 uppercase">Total Expenses</p>
                    <p class="text-2xl sm:text-3xl font-bold text-gray-800 mt-1 break-words" id="overviewTotalExpenses">RWF 0</p>
                </div>
                <div class="w-12 h-12 sm:w-14 sm:h-14 shrink-0 bg-gray-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 sm:w-7 sm:h-7 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Collection Rate Card -->
        <div class="sm:col-span-2 lg:col-span-1 bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-semibold text-gray-500 uppercase">Collection Rate</p>
                    <p class="text-2xl sm:text-3xl font-bold text-gray-800 mt-1" id="overviewCollectionRate">0%</p>
                    <div class="w-full max-w-xs bg-gray-200 rounded-full h-2 mt-2">
                        <div id="collectionBar" class="bg-blue-600 h-2 rounded-full transition-all" style="width: 0%"></div>
                    </div>
                </div>
                <div class="w-12 h-12 sm:w-14 sm:h-14 shrink-0 bg-gray-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 sm:w-7 sm:h-7 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
// OVERVIEW DATE RANGE
// ============================================

function handleOverviewDateChange() {
    const fromDate = document.getElementById('overviewFromDate');
    const toDate = document.getElementById('overviewToDate');

    fromDate.setCustomValidity('');
    toDate.setCustomValidity('');

    if (fromDate.value && toDate.value && fromDate.value > toDate.value) {
        toDate.setCustomValidity('To date must be on or after from date.');
        toDate.reportValidity();
        return;
    }

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
    const fromDate = document.getElementById('overviewFromDate').value;
    const toDate = document.getElementById('overviewToDate').value;

    if (!fromDate || !toDate || fromDate > toDate) return;
    
    try {
        const params = new URLSearchParams({
            from_date: fromDate,
            to_date: toDate
        });
        const response = await fetch(`/finance/overview/stats?${params.toString()}`, {
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
    document.getElementById('overviewFromDate').addEventListener('change', handleOverviewDateChange);
    document.getElementById('overviewToDate').addEventListener('change', handleOverviewDateChange);
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

// Expose functions globally
window.refreshFinanceOverview = loadFinanceOverview;
window.refreshAllCharts = refreshAllCharts;
window.removeDollarSigns = removeDollarSigns;
</script>
