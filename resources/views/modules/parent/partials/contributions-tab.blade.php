<div>
    <!-- Header with Year Selection -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <h3 class="text-lg font-semibold text-gray-800">
            <i class="fas fa-hand-holding-usd text-green-600 mr-2"></i>
            Children's Contributions
        </h3>
        <div class="flex flex-wrap gap-3">
            <div class="flex items-center gap-2">
                <label class="text-sm text-gray-600">Year:</label>
                <select id="yearSelector" onchange="changeYear()" 
                        class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 bg-white">
                    <!-- Years will be populated -->
                </select>
                <span id="yearBadge" class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-600 hidden">
                    <i class="fas fa-history mr-1"></i> <span id="yearStatus">Current</span>
                </span>
            </div>
        </div>
    </div>

    <!-- Info Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-blue-50 rounded-lg p-4">
            <p class="text-sm text-gray-600">Total Expected</p>
            <p class="text-2xl font-bold text-blue-600" id="totalExpected">RWF 0</p>
        </div>
        <div class="bg-green-50 rounded-lg p-4">
            <p class="text-sm text-gray-600">Total Collected</p>
            <p class="text-2xl font-bold text-green-600" id="totalCollected">RWF 0</p>
        </div>
        <div class="bg-purple-50 rounded-lg p-4">
            <p class="text-sm text-gray-600">Collection Rate</p>
            <p class="text-2xl font-bold text-purple-600" id="collectionRate">0%</p>
        </div>
    </div>

    <!-- Search -->
    <div class="mb-4">
        <div class="relative">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input type="text" id="searchContributions" placeholder="Search by child's name..." 
                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
        </div>
        <p id="contributionsCount" class="text-xs text-gray-500 mt-1">0 contribution records found</p>
    </div>

    <!-- Contributions Table -->
    <div class="overflow-x-auto">
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
</div>

<script>
// ============================================
// CONTRIBUTIONS FUNCTIONS
// ============================================
let currentYear = new Date().getFullYear();

function populateYearSelector() {
    const currentYearNow = new Date().getFullYear();
    const startYear = currentYearNow - 5;
    const endYear = currentYearNow + 1;
    const selector = document.getElementById('yearSelector');
    
    if (!selector) return;
    
    selector.innerHTML = '';
    for (let year = endYear; year >= startYear; year--) {
        const option = document.createElement('option');
        option.value = year;
        option.textContent = year + (year === currentYearNow ? ' (Current)' : '');
        selector.appendChild(option);
    }
    
    const savedYear = localStorage.getItem('parentSelectedContributionYear');
    if (savedYear && savedYear >= startYear && savedYear <= endYear) {
        selector.value = savedYear;
    } else {
        selector.value = currentYearNow;
    }
    
    updateYearBadge();
}

function updateYearBadge() {
    const selectedYear = parseInt(document.getElementById('yearSelector')?.value || currentYear);
    const currentYearNow = new Date().getFullYear();
    const yearBadge = document.getElementById('yearBadge');
    const yearStatus = document.getElementById('yearStatus');
    
    if (!yearBadge) return;
    
    if (selectedYear === currentYearNow) {
        yearBadge.classList.add('hidden');
    } else if (selectedYear < currentYearNow) {
        yearBadge.classList.remove('hidden');
        yearStatus.innerHTML = 'Archived Year';
        yearBadge.className = 'px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-700';
    } else {
        yearBadge.classList.remove('hidden');
        yearStatus.innerHTML = 'Future Year';
        yearBadge.className = 'px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-700';
    }
}

function changeYear() {
    const selectedYear = document.getElementById('yearSelector').value;
    localStorage.setItem('parentSelectedContributionYear', selectedYear);
    updateYearBadge();
    loadContributions();
}

function loadContributions() {
    const search = document.getElementById('searchContributions')?.value || '';
    const year = document.getElementById('yearSelector')?.value || new Date().getFullYear();
    
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
            if (tbody) {
                tbody.innerHTML = `<tr><td colspan="5" class="text-center py-8 text-red-500">Error loading contributions: ${data.message}</td></tr>`;
            }
        }
    })
    .catch(error => {
        console.error('Error loading contributions:', error);
        const tbody = document.getElementById('contributions-table-body');
        if (tbody) {
            tbody.innerHTML = `<tr><td colspan="5" class="text-center py-8 text-red-500">Error loading contributions</td></tr>`;
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
    const numberOfTerms = 3;
    const currentYear = document.getElementById('yearSelector')?.value || new Date().getFullYear();
    
    if (!tbody) return;
    
    if (!contributions || contributions.length === 0) {
        tbody.innerHTML = `<tr><td colspan="${numberOfTerms + 2}" class="text-center py-8 text-gray-500">No contributions found for ${currentYear}</td></tr>`;
        return;
    }
    
    tbody.innerHTML = contributions.map(cont => {
        let termsHtml = '';
        const childTermTotals = termTotals && termTotals[cont.user_id] ? termTotals[cont.user_id] : {};
        
        for (let i = 1; i <= numberOfTerms; i++) {
            const termAmount = cont[`term${i}_paid`] || 0;
            const termTarget = childTermTotals[i] || 0;
            const termProgress = termTarget > 0 ? ((termAmount / termTarget) * 100).toFixed(1) : 0;
            
            termsHtml += `
                <td class="px-4 py-3 text-sm">
                    <div>
                        <span class="font-medium text-green-600">RWF ${parseFloat(termAmount).toLocaleString()}</span>
                        <span class="text-gray-400"> / RWF ${parseFloat(termTarget).toLocaleString()}</span>
                        <div class="w-full bg-gray-200 rounded-full h-1.5 mt-1">
                            <div class="bg-green-500 h-1.5 rounded-full" style="width: ${termProgress}%"></div>
                        </div>
                        <span class="text-xs text-gray-500">${termProgress}% complete</span>
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
                        <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                            <div class="bg-purple-600 h-2 rounded-full" style="width: ${overallProgress}%"></div>
                        </div>
                        <span class="text-xs text-gray-500">${overallProgress}% complete</span>
                    </div>
                </td>
            </tr>
        `;
    }).join('');
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