<div>
    <!-- Header -->
    <div class="mb-6">
        <h3 class="text-xl font-bold text-gray-800">Sponsor Management</h3>
        <p class="text-sm text-gray-500 mt-1">Manage sponsors, track commitments, and record payments</p>
    </div>
    
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl shadow-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm">Total Sponsors</p>
                    <p class="text-2xl font-bold" id="totalSponsors">0</p>
                </div>
                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                    <i class="fas fa-users text-white"></i>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl shadow-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm">Total Commitments</p>
                    <p class="text-2xl font-bold" id="totalCommitments">RWF 0</p>
                </div>
                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                    <i class="fas fa-hand-holding-usd text-white"></i>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-xl shadow-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-yellow-100 text-sm">Total Received</p>
                    <p class="text-2xl font-bold" id="totalReceived">RWF 0</p>
                </div>
                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                    <i class="fas fa-check-circle text-white"></i>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl shadow-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm">Active Sponsors</p>
                    <p class="text-2xl font-bold" id="activeSponsors">0</p>
                </div>
                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                    <i class="fas fa-star text-white"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Search and Filter Bar -->
    <div class="bg-gray-50 rounded-xl p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Search Sponsor</label>
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input type="text" id="searchSponsor" placeholder="Search by name or email..." 
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select id="sponsorStatus" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="all">All Sponsors</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="completed">Completed</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">&nbsp;</label>
                <button onclick="filterSponsors()" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
                    <i class="fas fa-search mr-2"></i> Apply Filters
                </button>
            </div>
        </div>
    </div>
    
    <!-- Add Sponsor Button -->
    <div class="flex justify-end mb-4">
        <button onclick="openSponsorModal()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm flex items-center gap-2">
            <i class="fas fa-plus-circle"></i> Add New Sponsor
        </button>
    </div>
    
    <!-- Sponsors Cards Grid -->
    <div id="sponsors-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="text-center py-12 col-span-full">
            <i class="fas fa-spinner fa-spin text-3xl text-gray-400 mb-3"></i>
            <p class="text-gray-500">Loading sponsors...</p>
        </div>
    </div>
</div>

<!-- Add/Edit Sponsor Modal -->
<div id="sponsorModal" class="modal hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-6 border w-full max-w-md shadow-xl rounded-2xl bg-white">
        <div class="flex justify-between items-center pb-4 border-b">
            <h3 id="sponsorModalTitle" class="text-xl font-bold text-gray-800">Add New Sponsor</h3>
            <button onclick="closeModal('sponsorModal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form id="sponsorForm" onsubmit="submitSponsor(event)">
            @csrf
            <input type="hidden" id="sponsorId" name="id">
            <div class="mt-4 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sponsor Name *</label>
                    <input type="text" id="sponsorName" name="name" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" id="sponsorEmail" name="email" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                    <input type="text" id="sponsorPhone" name="phone" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Commitment Amount (RWF) *</label>
                    <input type="number" id="sponsorCommitment" name="commitment_amount" step="0.01" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fund Type</label>
                    <select id="sponsorType" name="fund_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        <option value="one_time">One Time</option>
                        <option value="monthly">Monthly</option>
                        <option value="quarterly">Quarterly</option>
                        <option value="annual">Annual</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                    <textarea id="sponsorNotes" name="notes" rows="3" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg"></textarea>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6 pt-4 border-t">
                <button type="button" onclick="closeModal('sponsorModal')" class="px-4 py-2 border rounded-lg text-sm">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm">Save Sponsor</button>
            </div>
        </form>
    </div>
</div>

<!-- Record Payment Modal -->
<div id="recordPaymentModal" class="modal hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-6 border w-full max-w-md shadow-xl rounded-2xl bg-white">
        <div class="flex justify-between items-center pb-4 border-b">
            <h3 class="text-xl font-bold text-gray-800">Record Sponsor Payment</h3>
            <button onclick="closeModal('recordPaymentModal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form id="recordPaymentForm" onsubmit="submitSponsorPayment(event)">
            @csrf
            <input type="hidden" id="paymentSponsorId" name="sponsor_id">
            <div class="mt-4 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sponsor</label>
                    <p id="paymentSponsorName" class="text-sm font-medium text-gray-800 bg-gray-50 p-2 rounded"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Amount (RWF) *</label>
                    <input type="number" id="paymentAmount" name="amount" step="0.01" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Payment Date</label>
                    <input type="date" id="paymentDate" name="payment_date" value="{{ date('Y-m-d') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                    <select id="paymentMethod" name="payment_method" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        <option value="cash">Cash</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="mobile_money">Mobile Money</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                    <textarea name="notes" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg"></textarea>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6 pt-4 border-t">
                <button type="button" onclick="closeModal('recordPaymentModal')" class="px-4 py-2 border rounded-lg text-sm">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm">Record Payment</button>
            </div>
        </form>
    </div>
</div>

<script>
function filterSponsors() {
    const search = document.getElementById('searchSponsor')?.value || '';
    const status = document.getElementById('sponsorStatus')?.value || 'all';
    
    fetch(`/finance/sponsors/filter?search=${encodeURIComponent(search)}&status=${status}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateSponsorsList(data.sponsors);
            updateStats(data.sponsors);
        }
    })
    .catch(error => console.error('Error:', error));
}

function updateSponsorsList(sponsors) {
    const container = document.getElementById('sponsors-container');
    
    if (!sponsors || sponsors.length === 0) {
        container.innerHTML = `
            <div class="text-center py-12 col-span-full">
                <i class="fas fa-users fa-3x text-gray-300 mb-3"></i>
                <p class="text-gray-500">No sponsors found</p>
                <button onclick="openSponsorModal()" class="mt-3 text-green-600 hover:text-green-700 text-sm">
                    <i class="fas fa-plus"></i> Add your first sponsor
                </button>
            </div>
        `;
        return;
    }
    
    container.innerHTML = sponsors.map(sponsor => {
        const commitment = parseFloat(sponsor.commitment_amount || 0);
        const received = parseFloat(sponsor.received_amount || 0);
        const progress = commitment > 0 ? ((received / commitment) * 100).toFixed(1) : 0;
        const status = progress >= 100 ? 'completed' : (sponsor.status || 'active');
        const statusColor = status === 'completed' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700';
        const statusText = status === 'completed' ? 'completed' : 'active';
        
        return `
            <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden hover:shadow-lg transition">
                <div class="p-5">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <h4 class="font-bold text-gray-800 text-lg">${escapeHtml(sponsor.name)}</h4>
                            <p class="text-xs text-gray-500">${escapeHtml(sponsor.email || 'No email')}</p>
                        </div>
                        <span class="px-2 py-1 rounded-full text-xs ${statusColor}">${statusText}</span>
                    </div>
                    
                    <div class="mb-3">
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-600">Fund</span>
                            <span class="font-medium text-gray-800">${escapeHtml(sponsor.fund_type || 'One Time')}</span>
                        </div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-600">Progress</span>
                            <span class="font-medium ${progress >= 100 ? 'text-green-600' : 'text-blue-600'}">${progress}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                            <div class="${progress >= 100 ? 'bg-green-500' : 'bg-blue-500'} h-2 rounded-full" style="width: ${progress}%"></div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 rounded-lg p-3 mb-4">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Received</span>
                            <span class="font-semibold text-green-600">RWF ${received.toLocaleString()}</span>
                        </div>
                        <div class="flex justify-between text-sm mt-1">
                            <span class="text-gray-500">Commitment</span>
                            <span class="font-semibold text-gray-800">RWF ${commitment.toLocaleString()}</span>
                        </div>
                    </div>
                    
                    <div class="flex gap-2">
                        <button onclick="openRecordPaymentModal(${sponsor.id}, '${escapeHtml(sponsor.name)}')" 
                                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-lg text-sm flex items-center justify-center gap-1">
                            <i class="fas fa-hand-holding-usd"></i> Record Payment
                        </button>
                        <button onclick="editSponsor(${sponsor.id})" 
                                class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded-lg text-sm flex items-center gap-1">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                    </div>
                </div>
            </div>
        `;
    }).join('');
}

function updateStats(sponsors) {
    const total = sponsors.length;
    const totalCommitments = sponsors.reduce((sum, s) => sum + parseFloat(s.commitment_amount || 0), 0);
    const totalReceived = sponsors.reduce((sum, s) => sum + parseFloat(s.received_amount || 0), 0);
    const active = sponsors.filter(s => s.status === 'active' && (parseFloat(s.received_amount || 0) < parseFloat(s.commitment_amount || 0))).length;
    
    document.getElementById('totalSponsors').textContent = total;
    document.getElementById('totalCommitments').textContent = 'RWF ' + totalCommitments.toLocaleString();
    document.getElementById('totalReceived').textContent = 'RWF ' + totalReceived.toLocaleString();
    document.getElementById('activeSponsors').textContent = active;
}

function openSponsorModal() {
    document.getElementById('sponsorModalTitle').textContent = 'Add New Sponsor';
    document.getElementById('sponsorId').value = '';
    document.getElementById('sponsorName').value = '';
    document.getElementById('sponsorEmail').value = '';
    document.getElementById('sponsorPhone').value = '';
    document.getElementById('sponsorCommitment').value = '';
    document.getElementById('sponsorType').value = 'one_time';
    document.getElementById('sponsorNotes').value = '';
    document.getElementById('sponsorModal').classList.remove('hidden');
}

function editSponsor(id) {
    fetch(`/finance/sponsors/${id}/edit`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('sponsorModalTitle').textContent = 'Edit Sponsor';
            document.getElementById('sponsorId').value = data.sponsor.id;
            document.getElementById('sponsorName').value = data.sponsor.name;
            document.getElementById('sponsorEmail').value = data.sponsor.email || '';
            document.getElementById('sponsorPhone').value = data.sponsor.phone || '';
            document.getElementById('sponsorCommitment').value = data.sponsor.commitment_amount;
            document.getElementById('sponsorType').value = data.sponsor.fund_type || 'one_time';
            document.getElementById('sponsorNotes').value = data.sponsor.notes || '';
            document.getElementById('sponsorModal').classList.remove('hidden');
        }
    });
}

function openRecordPaymentModal(sponsorId, sponsorName) {
    document.getElementById('paymentSponsorId').value = sponsorId;
    document.getElementById('paymentSponsorName').innerHTML = sponsorName;
    document.getElementById('paymentAmount').value = '';
    document.getElementById('paymentDate').value = new Date().toISOString().split('T')[0];
    document.getElementById('recordPaymentModal').classList.remove('hidden');
}

function submitSponsor(event) {
    event.preventDefault();
    
    const id = document.getElementById('sponsorId').value;
    const formData = new FormData();
    formData.append('name', document.getElementById('sponsorName').value);
    formData.append('email', document.getElementById('sponsorEmail').value);
    formData.append('phone', document.getElementById('sponsorPhone').value);
    formData.append('commitment_amount', document.getElementById('sponsorCommitment').value);
    formData.append('fund_type', document.getElementById('sponsorType').value);
    formData.append('notes', document.getElementById('sponsorNotes').value);
    
    const url = id ? `/finance/sponsors/${id}` : '/finance/sponsors/store';
    const method = id ? 'PUT' : 'POST';
    
    fetch(url, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeModal('sponsorModal');
            filterSponsors();
            alert(id ? 'Sponsor updated successfully!' : 'Sponsor added successfully!');
        } else {
            alert('Error: ' + (data.message || 'Failed to save sponsor'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Network error: ' + error.message);
    });
}

function submitSponsorPayment(event) {
    event.preventDefault();
    
    const formData = new FormData();
    formData.append('sponsor_id', document.getElementById('paymentSponsorId').value);
    formData.append('amount', document.getElementById('paymentAmount').value);
    formData.append('payment_date', document.getElementById('paymentDate').value);
    formData.append('payment_method', document.getElementById('paymentMethod').value);
    formData.append('notes', document.querySelector('#recordPaymentForm textarea[name="notes"]').value);
    
    fetch('/finance/sponsors/record-payment', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeModal('recordPaymentModal');
            filterSponsors();
            alert('Payment recorded successfully!');
        } else {
            alert('Error: ' + (data.message || 'Failed to record payment'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Network error: ' + error.message);
    });
}

function deleteSponsor(id) {
    if (confirm('Are you sure you want to delete this sponsor? This action cannot be undone.')) {
        fetch(`/finance/sponsors/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                filterSponsors();
                alert('Sponsor deleted successfully');
            } else {
                alert('Error: ' + (data.message || 'Failed to delete sponsor'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Network error: ' + error.message);
        });
    }
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Event listeners
document.getElementById('searchSponsor')?.addEventListener('keyup', function() {
    filterSponsors();
});
document.getElementById('sponsorStatus')?.addEventListener('change', function() {
    filterSponsors();
});

// Load initial data
filterSponsors();
</script>