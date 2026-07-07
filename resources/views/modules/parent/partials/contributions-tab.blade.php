<div>
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-4 sm:mb-6">
        <h3 class="text-base sm:text-lg font-semibold text-gray-800">
            <i class="fas fa-hand-holding-usd text-green-600 mr-2"></i>
            Children's Contributions
        </h3>
    </div>

    <!-- Info Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 sm:gap-4 mb-4 sm:mb-6">
        <div class="bg-blue-50 rounded-xl sm:rounded-2xl p-3 sm:p-4 flex items-center justify-between sm:block">
            <p class="text-xs sm:text-sm text-gray-600">Total Expected</p>
            <p class="text-sm sm:text-2xl font-bold text-blue-600 text-right sm:text-left" id="totalExpected">RWF 0</p>
        </div>
        <div class="bg-green-50 rounded-xl sm:rounded-2xl p-3 sm:p-4 flex items-center justify-between sm:block">
            <p class="text-xs sm:text-sm text-gray-600">Total Collected</p>
            <p class="text-sm sm:text-2xl font-bold text-green-600 text-right sm:text-left" id="totalCollected">RWF 0</p>
        </div>
        <div class="bg-purple-50 rounded-xl sm:rounded-2xl p-3 sm:p-4 flex items-center justify-between sm:block">
            <p class="text-xs sm:text-sm text-gray-600">Collection Rate</p>
            <p class="text-sm sm:text-2xl font-bold text-purple-600 text-right sm:text-left" id="collectionRate">0%</p>
        </div>
    </div>

    <!-- Search -->
    <div class="mb-4">
        <div class="relative">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input type="text" id="searchContributions" placeholder="Search by child's name..." 
                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
        </div>
        <p id="contributionsCount" class="text-xs text-gray-500 mt-1">0 contribution records found</p>
    </div>

    <!-- Contributions Table -->
    <div class="hidden md:block overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">CHILD</th>
                    @for($i = 1; $i <= 3; $i++)
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">TERM {{ $i }}</th>
                    @endfor
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">TOTAL PROGRESS</th>
                </tr>
            </thead>
            <tbody id="contributions-table-body">
                <tr>
                    <td colspan="5" class="text-center py-8 text-gray-500">Loading contributions...</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div id="contributions-mobile-list" class="md:hidden space-y-3">
        <div class="text-center py-8 text-gray-500 bg-white border border-gray-100 rounded-2xl">Loading contributions...</div>
    </div>
</div>

<script>
// ============================================
// CONTRIBUTIONS FUNCTIONS
// ============================================
let currentYear = new Date().getFullYear();

function populateYearSelector() {
    currentYear = new Date().getFullYear();
}

function loadContributions() {
    const search = document.getElementById('searchContributions')?.value || '';
    const year = new Date().getFullYear();
    
    fetch(`/parent/contributions/children?year=${year}&search=${encodeURIComponent(search)}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateContributionsTable(data.contributions, data.term_totals);
            updateStats(data.contributions);
        } else {
            console.error('Error loading contributions:', data.message);
            const tbody = document.getElementById('contributions-table-body');
            const mobileList = document.getElementById('contributions-mobile-list');
            if (tbody) {
                tbody.innerHTML = `<tr><td colspan="5" class="text-center py-8 text-red-500">Error loading contributions: ${data.message}</td></tr>`;
            }
            if (mobileList) {
                mobileList.innerHTML = `<div class="text-center py-8 text-red-500 bg-white border border-red-100 rounded-2xl">Error loading contributions: ${data.message}</div>`;
            }
        }
    })
    .catch(error => {
        console.error('Error loading contributions:', error);
        const tbody = document.getElementById('contributions-table-body');
        const mobileList = document.getElementById('contributions-mobile-list');
        if (tbody) {
            tbody.innerHTML = `<tr><td colspan="5" class="text-center py-8 text-red-500">Error loading contributions</td></tr>`;
        }
        if (mobileList) {
            mobileList.innerHTML = `<div class="text-center py-8 text-red-500 bg-white border border-red-100 rounded-2xl">Error loading contributions</div>`;
        }
    });
}

function updateStats(contributions) {
    let totalExpected = 0;
    let totalCollected = 0;
    
    contributions.forEach(cont => {
        totalExpected += parseFloat(cont.annual_amount || 0);
        totalCollected += parseFloat(cont.total_paid || 0);
    });
    
    const collectionRate = totalExpected > 0 ? ((totalCollected / totalExpected) * 100).toFixed(1) : 0;
    
    const totalExpectedEl = document.getElementById('totalExpected');
    const totalCollectedEl = document.getElementById('totalCollected');
    const collectionRateEl = document.getElementById('collectionRate');
    const contributionsCountEl = document.getElementById('contributionsCount');
    
    if (totalExpectedEl) totalExpectedEl.textContent = 'RWF ' + totalExpected.toLocaleString();
    if (totalCollectedEl) totalCollectedEl.textContent = 'RWF ' + totalCollected.toLocaleString();
    if (collectionRateEl) collectionRateEl.textContent = collectionRate + '%';
    if (contributionsCountEl) contributionsCountEl.textContent = contributions.length + ' contribution records found';
}

function updateContributionsTable(contributions, termTotals) {
    const tbody = document.getElementById('contributions-table-body');
    const mobileList = document.getElementById('contributions-mobile-list');
    const numberOfTerms = 3;
    const currentYear = new Date().getFullYear();
    
    if (!tbody && !mobileList) return;
    
    if (!contributions || contributions.length === 0) {
        if (tbody) tbody.innerHTML = `<tr><td colspan="${numberOfTerms + 2}" class="text-center py-8 text-gray-500">No contributions found for ${currentYear}</td></tr>`;
        if (mobileList) mobileList.innerHTML = `<div class="text-center py-8 text-gray-500 bg-white border border-gray-100 rounded-2xl">No contributions found for ${currentYear}</div>`;
        return;
    }
    
    const tableRows = contributions.map(cont => {
        let termsHtml = '';
        const childTermTotals = termTotals && termTotals[cont.user_id] ? termTotals[cont.user_id] : {};
        
        for (let i = 1; i <= numberOfTerms; i++) {
            const termAmount = cont[`term${i}_paid`] || 0;
            const termTarget = childTermTotals[i] || 0;
            const termProgress = termTarget > 0 ? ((termAmount / termTarget) * 100).toFixed(1) : 0;
            
            termsHtml += `
                <td class="px-4 py-3 text-sm">
                    <div class="space-y-1">
                        <p><span class="text-gray-500">To pay:</span> <span class="font-medium text-gray-800">RWF ${parseFloat(termTarget).toLocaleString()}</span></p>
                        <p><span class="text-gray-500">Paid:</span> <span class="font-semibold text-green-700">RWF ${parseFloat(termAmount).toLocaleString()}</span></p>
                    </div>
                </td>
            `;
        }
        
        const annualAmount = cont.annual_amount || 0;
        const totalPaid = cont.total_paid || 0;
        const overallProgress = annualAmount > 0 ? ((totalPaid / annualAmount) * 100).toFixed(1) : 0;
        
        let statusBadge = '';
        if (overallProgress >= 100) {
            statusBadge = '<span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">Completed</span>';
        } else if (overallProgress >= 75) {
            statusBadge = '<span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">Almost Done</span>';
        } else if (overallProgress >= 50) {
            statusBadge = '<span class="text-xs bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full">In Progress</span>';
        } else if (overallProgress > 0) {
            statusBadge = '<span class="text-xs bg-orange-100 text-orange-700 px-2 py-0.5 rounded-full">Started</span>';
        } else {
            statusBadge = '<span class="text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full">Not Started</span>';
        }
        
        return `
            <tr class="border-b hover:bg-gray-50">
                <td class="px-4 py-3">
                    <div>
                        <p class="font-medium text-gray-800">${escapeHtml(cont.user_name)}</p>
                        <p class="text-xs text-gray-500">${escapeHtml(cont.email)}</p>
                        ${statusBadge}
                    </div>
                </td>
                ${termsHtml}
                <td class="px-4 py-3">
                    <div>
                        <span class="font-bold text-purple-600">RWF ${parseFloat(totalPaid).toLocaleString()}</span>
                        <span class="text-gray-400"> / RWF ${parseFloat(annualAmount).toLocaleString()}</span>
                        <span class="text-xs text-gray-500">${overallProgress}% complete</span>
                    </div>
                </td>
            </tr>
        `;
    }).join('');

    const mobileCards = contributions.map(cont => {
        const childTermTotals = termTotals && termTotals[cont.user_id] ? termTotals[cont.user_id] : {};
        const annualAmount = cont.annual_amount || 0;
        const totalPaid = cont.total_paid || 0;
        const overallProgress = annualAmount > 0 ? ((totalPaid / annualAmount) * 100).toFixed(1) : 0;

        let statusBadge = '';
        if (overallProgress >= 100) {
            statusBadge = '<span class="text-xs bg-green-100 text-green-700 px-2.5 py-1 rounded-full">Completed</span>';
        } else if (overallProgress >= 75) {
            statusBadge = '<span class="text-xs bg-blue-100 text-blue-700 px-2.5 py-1 rounded-full">Almost Done</span>';
        } else if (overallProgress >= 50) {
            statusBadge = '<span class="text-xs bg-yellow-100 text-yellow-700 px-2.5 py-1 rounded-full">In Progress</span>';
        } else if (overallProgress > 0) {
            statusBadge = '<span class="text-xs bg-orange-100 text-orange-700 px-2.5 py-1 rounded-full">Started</span>';
        } else {
            statusBadge = '<span class="text-xs bg-gray-100 text-gray-500 px-2.5 py-1 rounded-full">Not Started</span>';
        }

        let termsCards = '';
        for (let i = 1; i <= numberOfTerms; i++) {
            const termAmount = cont[`term${i}_paid`] || 0;
            const termTarget = childTermTotals[i] || 0;
            const termProgress = termTarget > 0 ? ((termAmount / termTarget) * 100).toFixed(1) : 0;
            termsCards += `
                <div class="grid grid-cols-[4.5rem_1fr_1fr] gap-2 items-center py-2 border-t border-gray-100 first:border-t-0">
                    <span class="text-sm font-semibold text-gray-800">Term ${i}</span>
                    <div>
                        <p class="text-[11px] text-gray-500">To pay</p>
                        <p class="text-sm font-medium text-gray-900">RWF ${parseFloat(termTarget).toLocaleString()}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-[11px] text-gray-500">Paid</p>
                        <p class="text-sm font-semibold text-green-700">RWF ${parseFloat(termAmount).toLocaleString()}</p>
                    </div>
                </div>
            `;
        }

        return `
            <div class="bg-white border border-gray-200 rounded-2xl p-3 shadow-sm">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="font-semibold text-gray-900 break-words">${escapeHtml(cont.user_name)}</p>
                        <p class="text-xs text-gray-500 break-all">${escapeHtml(cont.email || '')}</p>
                    </div>
                    ${statusBadge}
                </div>

                <div class="mt-3 rounded-xl bg-purple-50 px-3 py-2">
                    <div class="flex items-center justify-between text-xs text-gray-500 mb-1">
                        <span>Total</span>
                        <span>${overallProgress}%</span>
                    </div>
                    <p class="text-sm leading-tight">
                        <span class="font-bold text-purple-700">Paid RWF ${parseFloat(totalPaid).toLocaleString()}</span>
                        <span class="text-gray-400"> / RWF ${parseFloat(annualAmount).toLocaleString()}</span>
                    </p>
                </div>

                <div class="mt-3 rounded-xl border border-gray-100 overflow-hidden px-2">
                    ${termsCards}
                </div>
            </div>
        `;
    }).join('');

    if (tbody) tbody.innerHTML = tableRows;
    if (mobileList) mobileList.innerHTML = mobileCards;
}

// Search contributions
document.getElementById('searchContributions')?.addEventListener('keyup', function() {
    loadContributions();
});

// Initialize contributions if tab is active
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('panel-contributions') && !document.getElementById('panel-contributions').classList.contains('hidden')) {
        populateYearSelector();
        loadContributions();
    }
});
</script>
