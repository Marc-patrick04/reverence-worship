<div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm">Total Income</p>
                    <p class="text-3xl font-bold" id="overviewTotalIncome">RWF 0</p>
                </div>
                <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                    <i class="fas fa-arrow-up text-white text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-red-100 text-sm">Total Expenses</p>
                    <p class="text-3xl font-bold" id="overviewTotalExpenses">RWF 0</p>
                </div>
                <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                    <i class="fas fa-arrow-down text-white text-xl"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Member Contributions -->
        <div class="bg-white rounded-xl shadow-md p-6 border">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-users text-blue-600"></i> Member Contributions
            </h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Total Expected:</span>
                    <span class="font-bold text-gray-800" id="overviewTotalExpected">RWF 0</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Total Collected:</span>
                    <span class="font-bold text-green-600" id="overviewTotalCollected">RWF 0</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Collection Rate:</span>
                    <span class="font-bold text-yellow-600" id="overviewCollectionRate">0%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                    <div id="overviewCollectionBar" class="bg-blue-600 h-2 rounded-full" style="width: 0%"></div>
                </div>
            </div>
        </div>
        
        <!-- Gift Summary -->
        <div class="bg-white rounded-xl shadow-md p-6 border">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-gift text-purple-600"></i> Gift Summary
            </h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Total Commitments:</span>
                    <span class="font-bold text-gray-800" id="overviewGiftCommitments">RWF 0</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Total Received:</span>
                    <span class="font-bold text-green-600" id="overviewGiftReceived">RWF 0</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Total Pending:</span>
                    <span class="font-bold text-yellow-600" id="overviewGiftPending">RWF 0</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Active Gifts:</span>
                    <span class="font-bold text-purple-600" id="overviewActiveGifts">0</span>
                </div>
            </div>
        </div>
        
        <!-- Sponsor Summary -->
        <div class="bg-white rounded-xl shadow-md p-6 border">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-handshake text-green-600"></i> Sponsor Summary
            </h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Total Commitments:</span>
                    <span class="font-bold text-gray-800" id="overviewSponsorCommitments">RWF 0</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Total Received:</span>
                    <span class="font-bold text-green-600" id="overviewSponsorReceived">RWF 0</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Total Pending:</span>
                    <span class="font-bold text-yellow-600" id="overviewSponsorPending">RWF 0</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Active Funds:</span>
                    <span class="font-bold text-purple-600" id="overviewActiveFunds">0</span>
                </div>
            </div>
        </div>
        
        <!-- Expenses Summary -->
        <div class="bg-white rounded-xl shadow-md p-6 border">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-receipt text-red-600"></i> Expenses
            </h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Total Expenses:</span>
                    <span class="font-bold text-red-600" id="overviewExpensesTotal">RWF 0</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Pending Approval:</span>
                    <span class="font-bold text-yellow-600" id="overviewExpensesPending">RWF 0</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Number of Transactions:</span>
                    <span class="font-bold text-blue-600" id="overviewExpensesCount">0</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Load overview stats when page loads
document.addEventListener('DOMContentLoaded', function() {
    loadFinanceOverview();
    
    // Listen for tab switching
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.attributeName === 'class') {
                const overviewTab = document.getElementById('overview-tab');
                if (overviewTab && !overviewTab.classList.contains('hidden')) {
                    loadFinanceOverview();
                }
            }
        });
    });
    
    const overviewTab = document.getElementById('overview-tab');
    if (overviewTab) {
        observer.observe(overviewTab, { attributes: true });
    }
});

function loadFinanceOverview() {
    fetch('/finance/overview/stats', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update UI with data
            const stats = data.stats;
            
            // Total Income & Expenses
            document.getElementById('overviewTotalIncome').innerHTML = 'RWF ' + Number(stats.total_income || 0).toLocaleString();
            document.getElementById('overviewTotalExpenses').innerHTML = 'RWF ' + Number(stats.total_expenses || 0).toLocaleString();
            
            // Member Contributions
            document.getElementById('overviewTotalExpected').innerHTML = 'RWF ' + Number(stats.total_expected || 0).toLocaleString();
            document.getElementById('overviewTotalCollected').innerHTML = 'RWF ' + Number(stats.total_collected || 0).toLocaleString();
            document.getElementById('overviewCollectionRate').innerHTML = Number(stats.collection_rate || 0) + '%';
            document.getElementById('overviewCollectionBar').style.width = (stats.collection_rate || 0) + '%';
            
            // Gift Summary
            document.getElementById('overviewGiftCommitments').innerHTML = 'RWF ' + Number(stats.gift_commitments || 0).toLocaleString();
            document.getElementById('overviewGiftReceived').innerHTML = 'RWF ' + Number(stats.gift_received || 0).toLocaleString();
            document.getElementById('overviewGiftPending').innerHTML = 'RWF ' + Number((stats.gift_commitments || 0) - (stats.gift_received || 0)).toLocaleString();
            document.getElementById('overviewActiveGifts').innerHTML = stats.active_gifts || 0;
            
            // Sponsor Summary
            document.getElementById('overviewSponsorCommitments').innerHTML = 'RWF ' + Number(stats.sponsor_commitments || 0).toLocaleString();
            document.getElementById('overviewSponsorReceived').innerHTML = 'RWF ' + Number(stats.sponsor_received || 0).toLocaleString();
            document.getElementById('overviewSponsorPending').innerHTML = 'RWF ' + Number((stats.sponsor_commitments || 0) - (stats.sponsor_received || 0)).toLocaleString();
            document.getElementById('overviewActiveFunds').innerHTML = stats.active_funds || 0;
            
            // Expenses Summary
            document.getElementById('overviewExpensesTotal').innerHTML = 'RWF ' + Number(stats.total_expenses || 0).toLocaleString();
            document.getElementById('overviewExpensesPending').innerHTML = 'RWF ' + Number(stats.pending_approval || 0).toLocaleString();
            document.getElementById('overviewExpensesCount').innerHTML = stats.transaction_count || 0;
            
            // Debug log
            console.log('Stats loaded:', stats);
        }
    })
    .catch(error => console.error('Error loading stats:', error));
}
</script>