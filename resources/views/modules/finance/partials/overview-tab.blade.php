<div class="space-y-6">
    <!-- Header with Period Filter -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Financial Dashboard</h2>
            <p class="text-sm text-gray-500 mt-1">Overview of your financial performance</p>
        </div>
        <div class="flex items-center gap-2">
            <select id="periodFilter" onchange="loadFinanceOverview()" 
                    class="px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                <option value="current">Current Year</option>
                <option value="last_year">Last Year</option>
                <option value="all">All Time</option>
            </select>
            <button onclick="refreshAllCharts()" 
                    class="px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
            </button>
        </div>
    </div>

    <!-- KPI Cards Row -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Revenue Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Revenue</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1" id="overviewTotalIncome">RWF 0</p>
                    <p class="text-xs text-green-600 mt-2 flex items-center gap-1" id="revenueTrend">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                        +0%
                    </p>
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
                    <p class="text-xs text-red-600 mt-2 flex items-center gap-1" id="expenseTrend">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                        </svg>
                        +0%
                    </p>
                </div>
                <div class="w-11 h-11 bg-gray-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Net Profit Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Net Profit</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1" id="overviewNetProfit">RWF 0</p>
                    <p class="text-xs text-blue-600 mt-2 flex items-center gap-1">
                        Margin: <span id="profitMargin">0%</span>
                    </p>
                </div>
                <div class="w-11 h-11 bg-gray-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
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

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Revenue vs Expenses Chart -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-5 py-3.5 bg-gray-50 border-b border-gray-200">
                <h3 class="font-semibold text-gray-800 text-sm">Revenue vs Expenses</h3>
            </div>
            <div class="p-5">
                <canvas id="revenueExpenseChart" height="200"></canvas>
            </div>
        </div>

        <!-- Monthly Trend Chart -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-5 py-3.5 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                <h3 class="font-semibold text-gray-800 text-sm">Monthly Trend</h3>
                <select id="trendYearFilter" onchange="loadMonthlyTrend()" class="text-xs border border-gray-200 rounded px-2 py-1">
                    @for($i = date('Y'); $i >= date('Y')-5; $i--)
                        <option value="{{ $i }}" {{ $i == date('Y') ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
            </div>
            <div class="p-5">
                <canvas id="monthlyTrendChart" height="200"></canvas>
            </div>
        </div>
    </div>

    <!-- Income Sources & Expense Categories -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Income Sources Chart -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-5 py-3.5 bg-gray-50 border-b border-gray-200">
                <h3 class="font-semibold text-gray-800 text-sm">Income Sources</h3>
            </div>
            <div class="p-5">
                <canvas id="incomeSourcesChart" height="200"></canvas>
            </div>
        </div>

        <!-- Expense Categories Chart -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-5 py-3.5 bg-gray-50 border-b border-gray-200">
                <h3 class="font-semibold text-gray-800 text-sm">Expense Categories</h3>
            </div>
            <div class="p-5">
                <canvas id="expenseCategoriesChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
// Chart instances
let revenueExpenseChart = null;
let incomeSourcesChart = null;
let monthlyTrendChart = null;
let expenseCategoriesChart = null;

// Format currency in RWF
function formatCurrency(amount) {
    return 'RWF ' + Number(amount || 0).toLocaleString('en-RW');
}

// Format number
function formatNumber(num) {
    return Number(num || 0).toLocaleString();
}

// Load all data and update charts
async function loadFinanceOverview() {
    const period = document.getElementById('periodFilter')?.value || 'current';
    
    try {
        const response = await fetch(`/finance/overview/stats?period=${period}`, {
            headers: { 
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (data.success && data.stats) {
            const stats = data.stats;
            
            // Update KPI Cards
            const totalIncome = stats.total_income || 0;
            const totalExpenses = stats.total_expenses || 0;
            const netProfit = totalIncome - totalExpenses;
            const profitMargin = totalIncome > 0 ? ((netProfit / totalIncome) * 100).toFixed(1) : 0;
            
            document.getElementById('overviewTotalIncome').innerHTML = formatCurrency(totalIncome);
            document.getElementById('overviewTotalExpenses').innerHTML = formatCurrency(totalExpenses);
            document.getElementById('overviewNetProfit').innerHTML = formatCurrency(netProfit);
            document.getElementById('profitMargin').innerHTML = profitMargin + '%';
            
            const collectionRate = stats.collection_rate || 0;
            document.getElementById('overviewCollectionRate').innerHTML = collectionRate + '%';
            document.getElementById('collectionBar').style.width = collectionRate + '%';
            
            // Update detailed stats
            document.getElementById('overviewTotalExpected').innerHTML = formatCurrency(stats.total_expected);
            document.getElementById('overviewTotalCollected').innerHTML = formatCurrency(stats.total_collected);
            const outstanding = (stats.total_expected || 0) - (stats.total_collected || 0);
            document.getElementById('overviewOutstanding').innerHTML = formatCurrency(outstanding);
            document.getElementById('collectionProgress').innerHTML = collectionRate + '%';
            document.getElementById('collectionProgressBar').style.width = collectionRate + '%';
            
            document.getElementById('overviewGiftCommitments').innerHTML = formatCurrency(stats.gift_commitments);
            document.getElementById('overviewGiftReceived').innerHTML = formatCurrency(stats.gift_received);
            document.getElementById('overviewGiftPending').innerHTML = formatCurrency((stats.gift_commitments || 0) - (stats.gift_received || 0));
            document.getElementById('overviewActiveGifts').innerHTML = formatNumber(stats.active_gifts);
            
            document.getElementById('overviewSponsorCommitments').innerHTML = formatCurrency(stats.sponsor_commitments);
            document.getElementById('overviewSponsorReceived').innerHTML = formatCurrency(stats.sponsor_received);
            document.getElementById('overviewSponsorPending').innerHTML = formatCurrency((stats.sponsor_commitments || 0) - (stats.sponsor_received || 0));
            document.getElementById('overviewActiveFunds').innerHTML = formatNumber(stats.active_funds);
            
            // Update trends if available
            if (stats.revenue_trend) document.getElementById('revenueTrend').innerHTML = stats.revenue_trend;
            if (stats.expense_trend) document.getElementById('expenseTrend').innerHTML = stats.expense_trend;
            
            // Update charts
            updateRevenueExpenseChart(totalIncome, totalExpenses);
            updateIncomeSourcesChart(stats.income_breakdown || {});
            updateExpenseCategoriesChart(stats.expense_breakdown || {});
            
            // Load monthly trends
            await loadMonthlyTrend();
        }
    } catch (error) {
        console.error('Failed to load finance overview:', error);
    }
}

// Revenue vs Expenses Pie Chart
function updateRevenueExpenseChart(income, expenses) {
    const ctx = document.getElementById('revenueExpenseChart')?.getContext('2d');
    if (!ctx) return;
    
    if (revenueExpenseChart) {
        revenueExpenseChart.destroy();
    }
    
    revenueExpenseChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Income', 'Expenses'],
            datasets: [{
                data: [income, expenses],
                backgroundColor: ['#10b981', '#f43f5e'],
                borderWidth: 0,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { usePointStyle: true, padding: 15 }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.raw / total) * 100).toFixed(1);
                            return `${context.label}: ${formatCurrency(context.raw)} (${percentage}%)`;
                        }
                    }
                }
            },
            cutout: '60%'
        }
    });
}

// Income Sources Chart
function updateIncomeSourcesChart(incomeBreakdown) {
    const ctx = document.getElementById('incomeSourcesChart')?.getContext('2d');
    if (!ctx) return;
    
    const labels = Object.keys(incomeBreakdown);
    const data = Object.values(incomeBreakdown);
    const colors = ['#3b82f6', '#8b5cf6', '#06b6d4', '#10b981', '#f59e0b', '#ef4444'];
    
    if (incomeSourcesChart) {
        incomeSourcesChart.destroy();
    }
    
    if (data.length === 0) {
        ctx.clearRect(0, 0, ctx.canvas.width, ctx.canvas.height);
        ctx.font = '14px sans-serif';
        ctx.fillStyle = '#9ca3af';
        ctx.textAlign = 'center';
        ctx.fillText('No data available', ctx.canvas.width / 2, ctx.canvas.height / 2);
        return;
    }
    
    incomeSourcesChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: labels.map(l => l.replace(/_/g, ' ').toUpperCase()),
            datasets: [{
                data: data,
                backgroundColor: colors.slice(0, data.length),
                borderWidth: 0,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                    labels: { usePointStyle: true, padding: 10, font: { size: 11 } }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.raw / total) * 100).toFixed(1);
                            return `${context.label}: ${formatCurrency(context.raw)} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
}

// Expense Categories Chart
function updateExpenseCategoriesChart(expenseBreakdown) {
    const ctx = document.getElementById('expenseCategoriesChart')?.getContext('2d');
    if (!ctx) return;
    
    const labels = Object.keys(expenseBreakdown);
    const data = Object.values(expenseBreakdown);
    const colors = ['#f43f5e', '#f97316', '#eab308', '#22c55e', '#06b6d4', '#8b5cf6'];
    
    if (expenseCategoriesChart) {
        expenseCategoriesChart.destroy();
    }
    
    if (data.length === 0) {
        ctx.clearRect(0, 0, ctx.canvas.width, ctx.canvas.height);
        ctx.font = '14px sans-serif';
        ctx.fillStyle = '#9ca3af';
        ctx.textAlign = 'center';
        ctx.fillText('No data available', ctx.canvas.width / 2, ctx.canvas.height / 2);
        return;
    }
    
    expenseCategoriesChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels.map(l => l.replace(/_/g, ' ').toUpperCase()),
            datasets: [{
                data: data,
                backgroundColor: colors.slice(0, data.length),
                borderWidth: 0,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { usePointStyle: true, padding: 10, font: { size: 11 } }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.raw / total) * 100).toFixed(1);
                            return `${context.label}: ${formatCurrency(context.raw)} (${percentage}%)`;
                        }
                    }
                }
            },
            cutout: '55%'
        }
    });
}

// Monthly Trend Line Chart
async function loadMonthlyTrend() {
    const year = document.getElementById('trendYearFilter')?.value || new Date().getFullYear();
    
    try {
        const response = await fetch(`/finance/overview/monthly-trend?year=${year}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await response.json();
        
        const ctx = document.getElementById('monthlyTrendChart')?.getContext('2d');
        if (!ctx) return;
        
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        const incomeData = data.income || new Array(12).fill(0);
        const expenseData = data.expenses || new Array(12).fill(0);
        
        if (monthlyTrendChart) {
            monthlyTrendChart.destroy();
        }
        
        monthlyTrendChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: months,
                datasets: [
                    {
                        label: 'Income',
                        data: incomeData,
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        fill: true,
                        tension: 0.4,
                        pointRadius: 3,
                        pointHoverRadius: 6
                    },
                    {
                        label: 'Expenses',
                        data: expenseData,
                        borderColor: '#f43f5e',
                        backgroundColor: 'rgba(244, 63, 94, 0.1)',
                        fill: true,
                        tension: 0.4,
                        pointRadius: 3,
                        pointHoverRadius: 6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `${context.dataset.label}: ${formatCurrency(context.raw)}`;
                            }
                        }
                    },
                    legend: { position: 'top' }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return formatCurrency(value);
                            }
                        }
                    }
                }
            }
        });
    } catch (error) {
        console.error('Failed to load monthly trend:', error);
    }
}

// Refresh all charts
function refreshAllCharts() {
    loadFinanceOverview();
}

// Auto-refresh every 30 seconds
let autoRefreshInterval = setInterval(() => {
    const overviewTab = document.getElementById('overview-tab');
    if (overviewTab && !overviewTab.classList.contains('hidden')) {
        loadFinanceOverview();
    }
}, 30000);

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    loadFinanceOverview();
    
    const overviewTab = document.getElementById('overview-tab');
    if (overviewTab) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    loadFinanceOverview();
                }
            });
        }, { threshold: 0.1 });
        observer.observe(overviewTab);
    }
});

// Cleanup interval on page unload
window.addEventListener('beforeunload', function() {
    if (autoRefreshInterval) {
        clearInterval(autoRefreshInterval);
    }
});

// Expose refresh function globally
window.refreshFinanceOverview = loadFinanceOverview;
window.refreshAllCharts = refreshAllCharts;
</script>