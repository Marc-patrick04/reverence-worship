<div>
    
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-6">
        <!-- Year Filter -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Filter by Year</label>
            <select id="yearFilter" onchange="filterSponsors()" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="all">All Years</option>
            </select>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Sponsors</p>
                    <p class="text-2xl font-bold text-gray-800" id="totalSponsors">0</p>
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-users text-blue-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Commitments</p>
                    <p class="text-2xl font-bold text-gray-800" id="totalCommitments">RWF 0</p>
                </div>
                <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-handshake text-purple-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Received</p>
                    <p class="text-2xl font-bold text-gray-800" id="totalReceived">RWF 0</p>
                </div>
                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Search</label>
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input type="text" id="searchSponsor" placeholder="Search by name or email..." 
                           class="w-full pl-10 pr-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Status</label>
                <select id="sponsorStatus" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="all">All Status</option>
                    <option value="active">Active</option>
                    <option value="completed">Completed</option>
                    <option value="overpaid">Overpaid</option>
                </select>
            </div>
            <div class="flex items-end justify-end gap-2">
                <button onclick="resetFilters()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm transition flex items-center gap-2">
                    <i class="fas fa-undo"></i> Reset
                </button>
                <button onclick="openSponsorModal()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm transition flex items-center gap-2">
                    <i class="fas fa-plus"></i> Add Sponsor
                </button>
            </div>
        </div>
    </div>
    
    <!-- Sponsors List -->
    <div id="sponsors-container" class="space-y-4">
        <div class="text-center py-8 text-gray-500">
            <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
            <p>Loading sponsors...</p>
        </div>
    </div>
</div>

<!-- Add/Edit Modal -->
<div id="sponsorModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-6 border w-full max-w-md shadow-xl rounded-2xl bg-white">
        <div class="flex justify-between items-center pb-4 border-b">
            <h3 id="modalTitle" class="text-xl font-bold text-gray-800">Add Sponsor</h3>
            <button onclick="closeModal('sponsorModal')" class="text-gray-400 hover:text-gray-600 transition">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form id="sponsorForm" onsubmit="saveSponsor(event)" class="mt-4">
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
                <button type="button" onclick="closeModal('sponsorModal')" class="px-5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm transition">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm transition flex items-center gap-2">
                    <i class="fas fa-save"></i> Save
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Payment Modal -->
<div id="paymentModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-6 border w-full max-w-md shadow-xl rounded-2xl bg-white">
        <div class="flex justify-between items-center pb-4 border-b">
            <h3 class="text-xl font-bold text-gray-800">Record Payment</h3>
            <button onclick="closeModal('paymentModal')" class="text-gray-400 hover:text-gray-600 transition">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form id="paymentForm" onsubmit="savePayment(event)" class="mt-4">
            <input type="hidden" id="payment_sponsor_id">
            <div class="space-y-4">
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-xs text-gray-500">Sponsor</p>
                    <p id="payment_sponsor_name" class="text-sm font-medium text-gray-800"></p>
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
                <button type="button" onclick="closeModal('paymentModal')" class="px-5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm transition">
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
<div id="viewModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-6 border w-full max-w-2xl shadow-xl rounded-2xl bg-white">
        <div class="flex justify-between items-center pb-4 border-b">
            <h3 class="text-xl font-bold text-gray-800">Payment History</h3>
            <button onclick="closeModal('viewModal')" class="text-gray-400 hover:text-gray-600 transition">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div id="paymentHistoryList" class="mt-4 space-y-2 max-h-96 overflow-y-auto">
            <p class="text-center text-gray-500 py-4">Loading payments...</p>
        </div>
        <div class="flex justify-end mt-6 pt-4 border-t">
            <button onclick="closeModal('viewModal')" class="px-5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm transition">
                Close
            </button>
        </div>
    </div>
</div>

<script>
let currentYear = new Date().getFullYear();

// Populate years
function populateYears() {
    const select = document.getElementById('yearFilter');
    select.innerHTML = '<option value="all">All Years</option>';
    for (let y = currentYear + 5; y >= 2020; y--) {
        const option = document.createElement('option');
        option.value = y;
        option.textContent = y;
        if (y === currentYear) option.selected = true;
        select.appendChild(option);
    }
}

// Reset filters
function resetFilters() {
    document.getElementById('searchSponsor').value = '';
    document.getElementById('sponsorStatus').value = 'all';
    document.getElementById('yearFilter').value = currentYear;
    filterSponsors();
}

// Load sponsors
function filterSponsors() {
    const search = document.getElementById('searchSponsor').value;
    const status = document.getElementById('sponsorStatus').value;
    const year = document.getElementById('yearFilter').value;
    
    fetch(`/finance/sponsors/filter?search=${encodeURIComponent(search)}&status=${status}&year=${year}`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                displaySponsors(data.sponsors);
                updateStats(data.sponsors);
            }
        })
        .catch(err => console.error(err));
}

// Display sponsors
function displaySponsors(sponsors) {
    const container = document.getElementById('sponsors-container');
    if (!sponsors.length) {
        container.innerHTML = `
            <div class="text-center py-12 bg-white rounded-xl border border-gray-200">
                <i class="fas fa-inbox text-4xl text-gray-300 mb-3"></i>
                <p class="text-gray-500">No sponsors found</p>
                <p class="text-sm text-gray-400 mt-1">Try adjusting your filters</p>
            </div>
        `;
        return;
    }
    
    container.innerHTML = sponsors.map(s => {
        const commitment = parseFloat(s.commitment_amount || 0);
        const received = parseFloat(s.received_amount || 0);
        const percent = commitment > 0 ? (received / commitment * 100).toFixed(1) : 0;
        let status = 'Active';
        let statusColor = 'bg-blue-100 text-blue-700';
        let statusIcon = 'fa-circle';
        if (received > commitment) {
            status = 'Overpaid';
            statusColor = 'bg-orange-100 text-orange-700';
            statusIcon = 'fa-exclamation-triangle';
        } else if (received >= commitment && commitment > 0) {
            status = 'Completed';
            statusColor = 'bg-green-100 text-green-700';
            statusIcon = 'fa-check-circle';
        }
        
        return `
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                    <div class="flex-1">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-500 flex items-center justify-center text-white font-medium text-sm">
                                ${s.name.charAt(0).toUpperCase()}
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-800">${escapeHtml(s.name)}</h4>
                                <p class="text-xs text-gray-500">${escapeHtml(s.email || 'No email')}</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 flex-wrap">
                        <span class="px-3 py-1 rounded-full text-xs font-medium ${statusColor} flex items-center gap-1">
                            <i class="fas ${statusIcon} text-xs"></i>
                            ${status}
                        </span>
                        <span class="text-xs text-gray-400">|</span>
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-700">RWF ${received.toLocaleString()}</p>
                            <p class="text-xs text-gray-400">of RWF ${commitment.toLocaleString()}</p>
                        </div>
                    </div>
                </div>
                
                <div class="mt-3">
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-500 h-2 rounded-full transition-all duration-500" style="width: ${Math.min(percent, 100)}%"></div>
                    </div>
                    <div class="flex justify-between text-xs text-gray-500 mt-1">
                        <span>${percent}%</span>
                        <span>${commitment > 0 ? 'RWF ' + (commitment - received).toLocaleString() + ' remaining' : 'No commitment'}</span>
                    </div>
                </div>
                
                <div class="flex flex-wrap gap-2 mt-4 pt-3 border-t border-gray-100">
                    <button onclick="openPaymentModal(${s.id}, '${escapeHtml(s.name)}')" class="flex-1 sm:flex-none bg-blue-600 hover:bg-blue-700 text-white px-4 py-1.5 rounded-lg text-sm transition flex items-center justify-center gap-1">
                        <i class="fas fa-plus-circle"></i> Pay
                    </button>
                    <button onclick="viewPayments(${s.id})" class="flex-1 sm:flex-none bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-1.5 rounded-lg text-sm transition flex items-center justify-center gap-1">
                        <i class="fas fa-history"></i> History
                    </button>
                    <button onclick="editSponsor(${s.id})" class="flex-1 sm:flex-none bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-1.5 rounded-lg text-sm transition flex items-center justify-center gap-1">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button onclick="deleteSponsor(${s.id}, '${escapeHtml(s.name)}')" class="flex-1 sm:flex-none bg-red-50 hover:bg-red-100 text-red-600 px-4 py-1.5 rounded-lg text-sm transition flex items-center justify-center gap-1">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </div>
            </div>
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
}

function openSponsorModal() {
    document.getElementById('modalTitle').innerText = 'Add Sponsor';
    document.getElementById('sponsorId').value = '';
    document.getElementById('name').value = '';
    document.getElementById('email').value = '';
    document.getElementById('phone').value = '';
    document.getElementById('commitment_amount').value = '';
    document.getElementById('notes').value = '';
    document.getElementById('sponsorModal').classList.remove('hidden');
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
                document.getElementById('sponsorModal').classList.remove('hidden');
            }
        });
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
    
    const url = id ? `/finance/sponsors/${id}` : '/finance/sponsors/store';
    if (id) formData.append('_method', 'PUT');
    
    fetch(url, { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                closeModal('sponsorModal');
                filterSponsors();
                showNotification(id ? 'Sponsor updated successfully!' : 'Sponsor added successfully!', 'success');
            } else {
                showNotification('Error: ' + data.message, 'error');
            }
        });
}

function openPaymentModal(id, name) {
    const year = document.getElementById('yearFilter').value;
    if (year === 'all') {
        showNotification('Please select a specific year first', 'error');
        return;
    }
    
    document.getElementById('payment_sponsor_id').value = id;
    document.getElementById('payment_sponsor_name').innerText = name + ' (Year: ' + year + ')';
    document.getElementById('amount').value = '';
    document.getElementById('payment_notes').value = '';
    document.getElementById('paymentModal').classList.remove('hidden');
}

function savePayment(event) {
    event.preventDefault();
    const year = document.getElementById('yearFilter').value;
    
    const formData = new FormData();
    formData.append('sponsor_id', document.getElementById('payment_sponsor_id').value);
    formData.append('amount', document.getElementById('amount').value);
    formData.append('payment_year', year);
    formData.append('payment_method', document.getElementById('payment_method').value);
    formData.append('notes', document.getElementById('payment_notes').value);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
    
    fetch('/finance/sponsors/record-payment', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                closeModal('paymentModal');
                filterSponsors();
                showNotification('Payment recorded successfully!', 'success');
            } else {
                showNotification('Error: ' + data.message, 'error');
            }
        });
}

function viewPayments(id) {
    fetch(`/finance/sponsors/${id}/payments/list`)
        .then(res => res.json())
        .then(data => {
            const container = document.getElementById('paymentHistoryList');
            if (data.success && data.payments.length) {
                container.innerHTML = data.payments.map(p => `
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 hover:shadow-sm transition">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-gray-800">RWF ${parseFloat(p.amount).toLocaleString()}</p>
                                <div class="flex flex-wrap gap-3 mt-1 text-xs text-gray-500">
                                    <span><i class="far fa-calendar mr-1"></i> ${new Date(p.payment_date).toLocaleDateString()}</span>
                                    <span><i class="far fa-calendar-alt mr-1"></i> Year: ${p.year}</span>
                                    <span><i class="fas fa-credit-card mr-1"></i> ${p.payment_method}</span>
                                </div>
                            </div>
                            <span class="text-xs text-gray-400">By: ${p.recorded_by || 'System'}</span>
                        </div>
                        ${p.notes ? `<p class="text-xs text-gray-500 mt-2">${escapeHtml(p.notes)}</p>` : ''}
                    </div>
                `).join('');
            } else {
                container.innerHTML = `
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-inbox text-3xl text-gray-300 mb-2"></i>
                        <p>No payments recorded</p>
                    </div>
                `;
            }
            document.getElementById('viewModal').classList.remove('hidden');
        });
}

function deleteSponsor(id, name) {
    if (confirm(`Are you sure you want to delete "${name}"? This action cannot be undone and will delete all associated payments.`)) {
        fetch(`/finance/sponsors/${id}`, {
            method: 'DELETE',
            headers: { 
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                filterSponsors();
                showNotification('Sponsor deleted successfully', 'success');
            } else {
                showNotification('Error: ' + data.message, 'error');
            }
        });
    }
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `fixed top-20 right-4 z-50 px-6 py-3 rounded-lg shadow-lg flex items-center gap-3 animate-slide-in max-w-md`;
    notification.style.backgroundColor = type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#3b82f6';
    notification.innerHTML = `
        <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} text-white"></i>
        <span class="text-white text-sm">${message}</span>
        <button onclick="this.parentElement.remove()" class="text-white/70 hover:text-white transition">
            <i class="fas fa-times"></i>
        </button>
    `;
    document.body.appendChild(notification);
    setTimeout(() => {
        if (notification.parentElement) {
            notification.style.opacity = '0';
            notification.style.transform = 'translateX(100px)';
            setTimeout(() => notification.remove(), 300);
        }
    }, 3000);
}

function closeModal(id) {
    document.getElementById(id).classList.add('hidden');
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Event listeners with debounce
let searchTimeout = null;
document.getElementById('searchSponsor').addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => filterSponsors(), 400);
});
document.getElementById('sponsorStatus').addEventListener('change', filterSponsors);

// Add animation styles
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    .animate-slide-in {
        animation: slideIn 0.3s ease-out;
    }
    .modal {
        background-color: rgba(0, 0, 0, 0.5);
    }
    .modal .relative {
        animation: modalIn 0.3s ease-out;
    }
    @keyframes modalIn {
        from { transform: scale(0.9); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }
`;
document.head.appendChild(style);

// Initialize
populateYears();
filterSponsors();
</script>