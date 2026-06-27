<div>
    <!-- Header with Year Selection -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <h3 class="text-lg font-semibold text-gray-800">Sponsors</h3>
        <div class="flex flex-wrap gap-3">
            <!-- Year Selector - Same as Contributions -->
            <div class="flex items-center gap-2">
                <label class="text-sm text-gray-600">Year:</label>
                <div class="relative">
                    <div onclick="toggleSponsorYearPicker()" 
                        class="flex items-center justify-between border border-gray-300 rounded-lg px-3 py-2 bg-white cursor-pointer hover:border-blue-400 transition-all min-w-[120px]">
                        <span id="sponsorYearDisplay" class="text-sm font-semibold text-gray-800">{{ date('Y') }}</span>
                        <svg class="w-4 h-4 text-gray-400 transition-transform duration-200 ml-2" id="sponsorYearArrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                    <input type="hidden" id="sponsorSelectedYear" value="{{ date('Y') }}">
                    
                    <div id="sponsorYearPickerDropdown" class="hidden absolute top-full left-0 right-0 mt-1 bg-white border border-gray-200 rounded-lg shadow-xl z-50 p-3 min-w-[200px]">
                        <div class="flex items-center justify-between mb-2">
                            <button type="button" onclick="changeSponsorYearPage(-1)" 
                                class="p-1 hover:bg-gray-100 rounded transition text-gray-500 hover:text-gray-700">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                            </button>
                            <span id="sponsorYearPageTitle" class="text-xs font-medium text-gray-600">2018 - 2024</span>
                            <button type="button" onclick="changeSponsorYearPage(1)" 
                                class="p-1 hover:bg-gray-100 rounded transition text-gray-500 hover:text-gray-700">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </button>
                        </div>
                        <div class="grid grid-cols-3 gap-1" id="sponsorYearGrid"></div>
                    </div>
                </div>
                <span id="sponsorYearBadge" class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-600 hidden">
                    <i class="fas fa-history mr-1"></i> <span id="sponsorYearStatus">Current</span>
                </span>
            </div>
            <button onclick="openSponsorModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm flex items-center gap-2">
                <i class="fas fa-plus-circle"></i> Add Sponsor
            </button>
        </div>
    </div>
    
    <!-- Info Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-blue-50 rounded-lg p-4">
            <p class="text-sm text-gray-600">Total Sponsors</p>
            <p class="text-2xl font-bold text-blue-600" id="totalSponsors">0</p>
        </div>
        <div class="bg-green-50 rounded-lg p-4">
            <p class="text-sm text-gray-600">Total Received</p>
            <p class="text-2xl font-bold text-green-600" id="totalReceived">RWF 0</p>
        </div>
        <div class="bg-purple-50 rounded-lg p-4">
            <p class="text-sm text-gray-600">Commitments</p>
            <p class="text-2xl font-bold text-purple-600" id="totalCommitments">RWF 0</p>
        </div>
    </div>
    
    <!-- Search -->
    <div class="mb-4">
        <div class="relative">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input type="text" id="searchSponsor" placeholder="Search by sponsor name or email..." 
                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
        </div>
        <p id="sponsorsCount" class="text-xs text-gray-500 mt-1">0 sponsors found</p>
    </div>
    
    <!-- Sponsors Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">SPONSOR</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">COMMITMENT</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">RECEIVED</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">REMAINING</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">STATUS</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">ACTIONS</th>
                </tr>
            </thead>
            <tbody id="sponsors-table-body">
                <tr>
                    <td colspan="6" class="text-center py-8 text-gray-500">Loading sponsors...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Add/Edit Sponsor Modal -->
<div id="sponsorModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-6 border w-full max-w-md shadow-xl rounded-2xl bg-white">
        <div class="flex justify-between items-center pb-4 border-b">
            <h3 id="modalTitle" class="text-xl font-bold text-gray-800">Add Sponsor</h3>
            <button onclick="closeFinanceModal('sponsorModal')" class="text-gray-400 hover:text-gray-600 transition">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form id="sponsorForm" onsubmit="saveSponsor(event)" class="mt-4">
            @csrf
            <input type="hidden" id="sponsorId">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sponsor Name <span class="text-red-500">*</span></label>
                    <input type="text" id="name" placeholder="Enter sponsor name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" id="email" placeholder="Enter email address" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                    <input type="text" id="phone" placeholder="Enter phone number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Commitment Amount (RWF) <span class="text-red-500">*</span></label>
                    <input type="number" id="commitment_amount" placeholder="Enter commitment amount" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                    <textarea id="notes" rows="2" placeholder="Additional notes..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6 pt-4 border-t">
                <button type="button" onclick="closeFinanceModal('sponsorModal')" class="px-5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm transition">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm transition flex items-center gap-2">
                    <i class="fas fa-save"></i> Save
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Record Payment Modal -->
<div id="paymentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-6 border w-full max-w-md shadow-xl rounded-2xl bg-white">
        <div class="flex justify-between items-center pb-4 border-b">
            <h3 class="text-xl font-bold text-gray-800">Record Payment</h3>
            <button onclick="closeFinanceModal('paymentModal')" class="text-gray-400 hover:text-gray-600 transition">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form id="paymentForm" onsubmit="savePayment(event)" class="mt-4">
            @csrf
            <input type="hidden" id="payment_sponsor_id">
            <div class="space-y-4">
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-xs text-gray-500">Sponsor</p>
                    <p id="payment_sponsor_name" class="text-sm font-medium text-gray-800"></p>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-xs text-gray-500">Year</p>
                    <p id="payment_year_display" class="text-sm font-medium text-gray-800"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Amount (RWF) <span class="text-red-500">*</span></label>
                    <input type="number" id="amount" placeholder="Enter amount" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                    <select id="payment_method" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="cash">Cash</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="mobile_money">Mobile Money</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                    <textarea id="payment_notes" rows="2" placeholder="Payment notes..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6 pt-4 border-t">
                <button type="button" onclick="closeFinanceModal('paymentModal')" class="px-5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm transition">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm transition flex items-center gap-2">
                    <i class="fas fa-check"></i> Record
                </button>
            </div>
        </form>
    </div>
</div>

<!-- View Payments Modal -->
<div id="viewModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-6 border w-full max-w-2xl shadow-xl rounded-2xl bg-white">
        <div class="flex justify-between items-center pb-4 border-b">
            <h3 class="text-xl font-bold text-gray-800">Payment History</h3>
            <button onclick="closeFinanceModal('viewModal')" class="text-gray-400 hover:text-gray-600 transition">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div id="paymentHistoryList" class="mt-4 space-y-2 max-h-96 overflow-y-auto">
            <p class="text-center text-gray-500 py-4">Loading payments...</p>
        </div>
        <div class="flex justify-end mt-6 pt-4 border-t">
            <button onclick="closeFinanceModal('viewModal')" class="px-5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm transition">
                Close
            </button>
        </div>
    </div>
</div>

<script>
let currentSponsorYear = new Date().getFullYear();
let sponsorYearPageOffset = 0;

// ============================================
// YEAR PICKER FUNCTIONS
// ============================================

function toggleSponsorYearPicker() {
    const dropdown = document.getElementById('sponsorYearPickerDropdown');
    const arrow = document.getElementById('sponsorYearArrow');
    
    if (dropdown.classList.contains('hidden')) {
        dropdown.classList.remove('hidden');
        arrow.classList.add('rotate-180');
        renderSponsorYearGrid();
    } else {
        dropdown.classList.add('hidden');
        arrow.classList.remove('rotate-180');
    }
}

function closeSponsorYearPicker() {
    const dropdown = document.getElementById('sponsorYearPickerDropdown');
    const arrow = document.getElementById('sponsorYearArrow');
    
    if (dropdown && !dropdown.classList.contains('hidden')) {
        dropdown.classList.add('hidden');
        arrow.classList.remove('rotate-180');
    }
}

function changeSponsorYearPage(direction) {
    sponsorYearPageOffset += direction;
    renderSponsorYearGrid();
}

function renderSponsorYearGrid() {
    const currentYear = new Date().getFullYear();
    const startYear = currentYear + (sponsorYearPageOffset * 9) - 4;
    
    const grid = document.getElementById('sponsorYearGrid');
    const title = document.getElementById('sponsorYearPageTitle');
    
    if (!grid) return;
    
    const endYear = startYear + 8;
    title.textContent = `${startYear} - ${endYear}`;
    
    grid.innerHTML = '';
    
    for (let i = 0; i < 9; i++) {
        const year = startYear + i;
        const isSelected = year == currentSponsorYear;
        const isCurrentYear = year == currentYear;
        const isDisabled = year < 2000 || year > 2100;
        
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.textContent = year;
        btn.className = 'py-1.5 px-2 rounded text-xs transition-all text-center';
        
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
                selectSponsorYear(year);
            };
        }
        
        grid.appendChild(btn);
    }
}

function selectSponsorYear(year) {
    currentSponsorYear = year;
    document.getElementById('sponsorSelectedYear').value = year;
    document.getElementById('sponsorYearDisplay').textContent = year;
    
    closeSponsorYearPicker();
    renderSponsorYearGrid();
    updateSponsorYearBadge();
    loadSponsors();
}

function updateSponsorYearBadge() {
    const currentYearNow = new Date().getFullYear();
    const yearBadge = document.getElementById('sponsorYearBadge');
    const yearStatus = document.getElementById('sponsorYearStatus');
    
    if (!yearBadge) return;
    
    if (currentSponsorYear === currentYearNow) {
        yearBadge.classList.add('hidden');
    } else if (currentSponsorYear < currentYearNow) {
        yearBadge.classList.remove('hidden');
        yearStatus.innerHTML = 'Archived Year';
        yearBadge.className = 'px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-700';
    } else {
        yearBadge.classList.remove('hidden');
        yearStatus.innerHTML = 'Future Year';
        yearBadge.className = 'px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-700';
    }
}

// ============================================
// MODAL FUNCTIONS
// ============================================

function openFinanceModal(modalId) {
    document.getElementById(modalId).classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeFinanceModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
    document.body.style.overflow = '';
}

// ============================================
// SPONSOR FUNCTIONS
// ============================================

function loadSponsors() {
    const search = document.getElementById('searchSponsor')?.value || '';
    const year = currentSponsorYear;
    
    fetch(`/finance/sponsors/filter?search=${encodeURIComponent(search)}&year=${year}&status=all`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displaySponsors(data.sponsors);
            updateStats(data.sponsors);
        }
    })
    .catch(error => {
        console.error('Error loading sponsors:', error);
        const tbody = document.getElementById('sponsors-table-body');
        if (tbody) {
            tbody.innerHTML = `<tr><td colspan="6" class="text-center py-8 text-red-500">Error loading sponsors. Please try again.</td></tr>`;
        }
    });
}

function displaySponsors(sponsors) {
    const tbody = document.getElementById('sponsors-table-body');
    
    if (!sponsors || sponsors.length === 0) {
        tbody.innerHTML = `<tr><td colspan="6" class="text-center py-8 text-gray-500">No sponsors found for ${currentSponsorYear}</td></tr>`;
        return;
    }
    
    tbody.innerHTML = sponsors.map(s => {
        const commitment = parseFloat(s.commitment_amount || 0);
        const received = parseFloat(s.received_amount || 0);
        const remaining = commitment - received;
        
        let status = 'Active';
        let statusClass = 'bg-blue-100 text-blue-700';
        if (received >= commitment && commitment > 0) {
            status = 'Completed';
            statusClass = 'bg-green-100 text-green-700';
        } else if (received > commitment) {
            status = 'Overpaid';
            statusClass = 'bg-orange-100 text-orange-700';
        }
        
        return `
            <tr class="border-b hover:bg-gray-50 transition">
                <td class="px-4 py-3">
                    <div>
                        <p class="font-medium text-gray-800">${escapeHtml(s.name)}</p>
                        <p class="text-xs text-gray-500">${escapeHtml(s.email || 'No email')}</p>
                    </div>
                </td>
                <td class="px-4 py-3 text-sm font-medium text-gray-700">
                    RWF ${commitment.toLocaleString()}
                </td>
                <td class="px-4 py-3 text-sm font-medium text-green-600">
                    RWF ${received.toLocaleString()}
                </td>
                <td class="px-4 py-3 text-sm font-medium text-gray-600">
                    RWF ${Math.max(remaining, 0).toLocaleString()}
                </td>
                <td class="px-4 py-3">
                    <span class="px-2 py-1 rounded-full text-xs font-medium ${statusClass}">
                        ${status}
                    </span>
                </td>
                <td class="px-4 py-3">
                    <div class="flex items-center gap-2">
                        <button onclick="openPaymentModal(${s.id}, '${escapeHtml(s.name)}')" 
                                class="text-green-500 hover:text-green-700 transition" title="Record Payment">
                            <i class="fas fa-plus-circle text-lg"></i>
                        </button>
                        <button onclick="viewPayments(${s.id})" 
                                class="text-yellow-500 hover:text-yellow-700 transition" title="View History">
                            <i class="fas fa-history text-lg"></i>
                        </button>
                        <button onclick="editSponsor(${s.id})" 
                                class="text-blue-500 hover:text-blue-700 transition" title="Edit Sponsor">
                            <i class="fas fa-edit text-lg"></i>
                        </button>
                        <button onclick="deleteSponsor(${s.id}, '${escapeHtml(s.name)}')" 
                                class="text-red-500 hover:text-red-700 transition" title="Delete Sponsor">
                            <i class="fas fa-trash text-lg"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }).join('');
}

function updateStats(sponsors) {
    const total = sponsors.length;
    const commitments = sponsors.reduce((s, p) => s + parseFloat(p.commitment_amount || 0), 0);
    const received = sponsors.reduce((s, p) => s + parseFloat(p.received_amount || 0), 0);
    
    document.getElementById('totalSponsors').innerText = total;
    document.getElementById('totalCommitments').innerHTML = 'RWF ' + commitments.toLocaleString();
    document.getElementById('totalReceived').innerHTML = 'RWF ' + received.toLocaleString();
    document.getElementById('sponsorsCount').innerText = total + ' sponsors found';
}

// ============================================
// SPONSOR CRUD OPERATIONS
// ============================================

function openSponsorModal() {
    document.getElementById('modalTitle').innerText = 'Add Sponsor';
    document.getElementById('sponsorId').value = '';
    document.getElementById('name').value = '';
    document.getElementById('email').value = '';
    document.getElementById('phone').value = '';
    document.getElementById('commitment_amount').value = '';
    document.getElementById('notes').value = '';
    openFinanceModal('sponsorModal');
}

function editSponsor(id) {
    fetch(`/finance/sponsors/${id}/edit`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                document.getElementById('modalTitle').innerText = 'Edit Sponsor';
                document.getElementById('sponsorId').value = data.sponsor.id;
                document.getElementById('name').value = data.sponsor.name;
                document.getElementById('email').value = data.sponsor.email || '';
                document.getElementById('phone').value = data.sponsor.phone || '';
                document.getElementById('commitment_amount').value = data.sponsor.commitment_amount;
                document.getElementById('notes').value = data.sponsor.notes || '';
                openFinanceModal('sponsorModal');
            }
        })
        .catch(error => console.error('Error:', error));
}

function saveSponsor(event) {
    event.preventDefault();
    const id = document.getElementById('sponsorId').value;
    const formData = new FormData();
    formData.append('name', document.getElementById('name').value);
    formData.append('email', document.getElementById('email').value);
    formData.append('phone', document.getElementById('phone').value);
    formData.append('commitment_amount', document.getElementById('commitment_amount').value);
    formData.append('notes', document.getElementById('notes').value);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
    
    const url = id ? `/finance/sponsors/${id}` : '/finance/sponsors';
    if (id) formData.append('_method', 'PUT');
    
    fetch(url, { 
        method: 'POST', 
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            closeFinanceModal('sponsorModal');
            loadSponsors();
            showNotification(id ? 'Sponsor updated successfully!' : 'Sponsor added successfully!', 'success');
        } else {
            showNotification('Error: ' + (data.message || 'Failed to save sponsor'), 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Network error: ' + error.message, 'error');
    });
}

function deleteSponsor(id, name) {
    if (confirm(`Are you sure you want to delete "${name}"? This will also delete all associated payments.`)) {
        fetch(`/finance/sponsors/${id}`, {
            method: 'DELETE',
            headers: { 
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                loadSponsors();
                showNotification('Sponsor deleted successfully', 'success');
            } else {
                showNotification('Error: ' + (data.message || 'Failed to delete sponsor'), 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Network error: ' + error.message, 'error');
        });
    }
}

// ============================================
// PAYMENT FUNCTIONS
// ============================================

function openPaymentModal(id, name) {
    document.getElementById('payment_sponsor_id').value = id;
    document.getElementById('payment_sponsor_name').innerText = name;
    document.getElementById('payment_year_display').innerText = currentSponsorYear;
    document.getElementById('amount').value = '';
    document.getElementById('payment_notes').value = '';
    openFinanceModal('paymentModal');
}

function savePayment(event) {
    event.preventDefault();
    
    const formData = new FormData();
    formData.append('sponsor_id', document.getElementById('payment_sponsor_id').value);
    formData.append('amount', document.getElementById('amount').value);
    formData.append('year', currentSponsorYear);
    formData.append('payment_method', document.getElementById('payment_method').value);
    formData.append('notes', document.getElementById('payment_notes').value);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
    
    fetch('/finance/sponsors/payment', { 
        method: 'POST', 
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            closeFinanceModal('paymentModal');
            loadSponsors();
            showNotification('Payment recorded successfully!', 'success');
        } else {
            showNotification('Error: ' + (data.message || 'Failed to record payment'), 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Network error: ' + error.message, 'error');
    });
}

function viewPayments(id) {
    fetch(`/finance/sponsors/${id}/payments?year=${currentSponsorYear}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        const container = document.getElementById('paymentHistoryList');
        if (data.success && data.payments && data.payments.length) {
            container.innerHTML = data.payments.map(p => `
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 mb-2 hover:shadow-sm transition">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm font-medium text-gray-800">RWF ${parseFloat(p.amount).toLocaleString()}</p>
                            <div class="flex flex-wrap gap-3 mt-1 text-xs text-gray-500">
                                <span><i class="far fa-calendar mr-1"></i> ${new Date(p.payment_date).toLocaleDateString()}</span>
                                <span><i class="fas fa-credit-card mr-1"></i> ${p.payment_method || 'Cash'}</span>
                            </div>
                        </div>
                        <span class="text-xs text-gray-400">${p.recorded_by || 'System'}</span>
                    </div>
                    ${p.notes ? `<p class="text-xs text-gray-500 mt-2">${escapeHtml(p.notes)}</p>` : ''}
                </div>
            `).join('');
        } else {
            container.innerHTML = `
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-inbox text-3xl text-gray-300 mb-2"></i>
                    <p>No payments recorded for ${currentSponsorYear}</p>
                </div>
            `;
        }
        openFinanceModal('viewModal');
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error loading payment history', 'error');
    });
}

// ============================================
// UTILITY FUNCTIONS
// ============================================

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg text-white z-50 transition-all transform ${
        type === 'success' ? 'bg-green-500' : 'bg-red-500'
    }`;
    notification.innerHTML = `<i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} mr-2"></i> ${message}`;
    document.body.appendChild(notification);
    setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transform = 'translateY(-10px)';
        setTimeout(() => notification.remove(), 300);
    }, 4000);
}

// Event listeners
document.getElementById('searchSponsor')?.addEventListener('keyup', function() {
    loadSponsors();
});

// Close year picker when clicking outside
document.addEventListener('click', function(event) {
    const picker = document.getElementById('sponsorYearPickerDropdown');
    const display = document.querySelector('#sponsorYearDisplay');
    
    if (picker && !picker.classList.contains('hidden') && display) {
        const parentDiv = display.closest('.relative');
        if (parentDiv && !parentDiv.contains(event.target)) {
            closeSponsorYearPicker();
        }
    }
});

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    const currentYear = new Date().getFullYear();
    currentSponsorYear = currentYear;
    document.getElementById('sponsorSelectedYear').value = currentYear;
    document.getElementById('sponsorYearDisplay').textContent = currentYear;
    
    loadSponsors();
});
</script>

<style>
.rotate-180 {
    transform: rotate(180deg);
}
</style>