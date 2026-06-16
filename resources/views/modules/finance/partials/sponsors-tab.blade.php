<div>
    <!-- Header -->
    <div class="mb-4">
        <h3 class="text-lg font-bold text-gray-800">Sponsor Management</h3>
        <p class="text-xs text-gray-500">Manage sponsors, track commitments, and record payments</p>
    </div>
    
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 mb-4">
        <!-- Year Filter -->
        <div class="bg-white rounded-lg shadow p-3">
            <label class="block text-xs font-medium text-gray-500 mb-1">Year</label>
            <select id="yearFilter" onchange="filterSponsors()" class="w-full px-2 py-1.5 text-sm border rounded-lg">
                <option value="all">All Years</option>
            </select>
        </div>
        
        <div class="bg-blue-600 rounded-lg shadow p-3 text-white">
            <p class="text-xs">Total Sponsors</p>
            <p class="text-xl font-bold" id="totalSponsors">0</p>
        </div>
        
        <div class="bg-purple-600 rounded-lg shadow p-3 text-white">
            <p class="text-xs">Total Commitments</p>
            <p class="text-xl font-bold" id="totalCommitments">RWF 0</p>
        </div>
        
        <div class="bg-green-600 rounded-lg shadow p-3 text-white">
            <p class="text-xs">Total Received</p>
            <p class="text-xl font-bold" id="totalReceived">RWF 0</p>
        </div>
    </div>
    
    <!-- Search -->
    <div class="bg-gray-50 rounded-lg p-3 mb-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <input type="text" id="searchSponsor" placeholder="Search by name or email..." 
                   class="px-3 py-2 text-sm border rounded-lg">
            <select id="sponsorStatus" class="px-3 py-2 text-sm border rounded-lg">
                <option value="all">All Status</option>
                <option value="active">Active</option>
                <option value="completed">Completed</option>
                <option value="overpaid">Overpaid</option>
            </select>
        </div>
    </div>
    
    <!-- Add Button -->
    <div class="flex justify-end mb-4">
        <button onclick="openSponsorModal()" class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm">
            + Add Sponsor
        </button>
    </div>
    
    <!-- Sponsors List -->
    <div id="sponsors-container" class="space-y-3">
        <div class="text-center py-8">Loading...</div>
    </div>
</div>

<!-- Add/Edit Modal -->
<div id="sponsorModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50">
    <div class="bg-white rounded-lg max-w-md mx-auto mt-20 p-5">
        <div class="flex justify-between mb-4">
            <h3 id="modalTitle" class="text-lg font-bold">Add Sponsor</h3>
            <button onclick="closeModal('sponsorModal')" class="text-gray-500">&times;</button>
        </div>
        <form id="sponsorForm" onsubmit="saveSponsor(event)">
            <input type="hidden" id="sponsorId">
            <div class="space-y-3">
                <input type="text" id="name" placeholder="Sponsor Name *" class="w-full px-3 py-2 border rounded-lg" required>
                <input type="email" id="email" placeholder="Email" class="w-full px-3 py-2 border rounded-lg">
                <input type="text" id="phone" placeholder="Phone" class="w-full px-3 py-2 border rounded-lg">
                <input type="number" id="commitment_amount" placeholder="Commitment Amount (RWF) *" class="w-full px-3 py-2 border rounded-lg" required>
                <textarea id="notes" placeholder="Notes" rows="2" class="w-full px-3 py-2 border rounded-lg"></textarea>
            </div>
            <div class="flex justify-end gap-2 mt-4">
                <button type="button" onclick="closeModal('sponsorModal')" class="px-4 py-2 border rounded-lg">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Save</button>
            </div>
        </form>
    </div>
</div>

<!-- Payment Modal -->
<div id="paymentModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50">
    <div class="bg-white rounded-lg max-w-md mx-auto mt-20 p-5">
        <div class="flex justify-between mb-4">
            <h3 class="text-lg font-bold">Record Payment</h3>
            <button onclick="closeModal('paymentModal')" class="text-gray-500">&times;</button>
        </div>
        <form id="paymentForm" onsubmit="savePayment(event)">
            <input type="hidden" id="payment_sponsor_id">
            <div class="space-y-3">
                <p id="payment_sponsor_name" class="text-sm font-medium"></p>
                <input type="number" id="amount" placeholder="Amount (RWF) *" class="w-full px-3 py-2 border rounded-lg" required>
                <select id="payment_method" class="w-full px-3 py-2 border rounded-lg">
                    <option value="cash">Cash</option>
                    <option value="bank_transfer">Bank Transfer</option>
                    <option value="mobile_money">Mobile Money</option>
                </select>
                <textarea id="payment_notes" placeholder="Notes" rows="2" class="w-full px-3 py-2 border rounded-lg"></textarea>
            </div>
            <div class="flex justify-end gap-2 mt-4">
                <button type="button" onclick="closeModal('paymentModal')" class="px-4 py-2 border rounded-lg">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Record</button>
            </div>
        </form>
    </div>
</div>

<!-- View Payments Modal -->
<div id="viewModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 overflow-y-auto">
    <div class="bg-white rounded-lg max-w-2xl mx-auto mt-10 p-5">
        <div class="flex justify-between mb-4">
            <h3 class="text-lg font-bold">Payment History</h3>
            <button onclick="closeModal('viewModal')" class="text-gray-500">&times;</button>
        </div>
        <div id="paymentHistoryList" class="space-y-2 max-h-96 overflow-y-auto">
            <p class="text-center text-gray-500">No payments recorded</p>
        </div>
        <div class="flex justify-end mt-4">
            <button onclick="closeModal('viewModal')" class="px-4 py-2 bg-gray-100 rounded-lg">Close</button>
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

// Load sponsors
function filterSponsors() {
    const search = document.getElementById('searchSponsor').value;
    const status = document.getElementById('sponsorStatus').value;
    const year = document.getElementById('yearFilter').value;
    
    fetch(`/finance/sponsors/filter?search=${search}&status=${status}&year=${year}`)
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
        container.innerHTML = '<div class="text-center py-8 text-gray-500">No sponsors found</div>';
        return;
    }
    
    container.innerHTML = sponsors.map(s => {
        const commitment = parseFloat(s.commitment_amount || 0);
        const received = parseFloat(s.received_amount || 0);
        const percent = commitment > 0 ? (received / commitment * 100).toFixed(1) : 0;
        let status = 'Active';
        let statusColor = 'bg-blue-100 text-blue-700';
        if (received > commitment) {
            status = 'Overpaid';
            statusColor = 'bg-orange-100 text-orange-700';
        } else if (received >= commitment && commitment > 0) {
            status = 'Completed';
            statusColor = 'bg-green-100 text-green-700';
        }
        
        return `
            <div class="bg-white rounded-lg shadow p-4 border">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <h4 class="font-bold">${escapeHtml(s.name)}</h4>
                        <p class="text-xs text-gray-500">${escapeHtml(s.email || 'No email')}</p>
                    </div>
                    <span class="px-2 py-1 rounded-full text-xs ${statusColor}">${status}</span>
                </div>
                <div class="bg-gray-50 rounded p-2 mb-3">
                    <div class="flex justify-between text-sm">
                        <span>Received:</span>
                        <span class="font-semibold text-green-600">RWF ${received.toLocaleString()}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span>Commitment:</span>
                        <span class="font-semibold">RWF ${commitment.toLocaleString()}</span>
                    </div>
                    <div class="mt-1">
                        <div class="w-full bg-gray-200 rounded-full h-1.5">
                            <div class="bg-blue-500 h-1.5 rounded-full" style="width: ${Math.min(percent, 100)}%"></div>
                        </div>
                        <p class="text-xs text-center mt-1">${percent}%</p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button onclick="openPaymentModal(${s.id}, '${escapeHtml(s.name)}')" class="flex-1 bg-blue-600 text-white px-2 py-1 rounded text-sm">Pay</button>
                    <button onclick="viewPayments(${s.id})" class="bg-gray-100 px-2 py-1 rounded text-sm">History</button>
                    <button onclick="editSponsor(${s.id})" class="bg-gray-100 px-2 py-1 rounded text-sm">Edit</button>
                    <button onclick="deleteSponsor(${s.id}, '${escapeHtml(s.name)}')" class="bg-red-50 text-red-600 px-2 py-1 rounded text-sm">Del</button>
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
                alert(id ? 'Sponsor updated!' : 'Sponsor added!');
            } else {
                alert('Error: ' + data.message);
            }
        });
}

function openPaymentModal(id, name) {
    document.getElementById('payment_sponsor_id').value = id;
    document.getElementById('payment_sponsor_name').innerText = name;
    document.getElementById('amount').value = '';
    document.getElementById('payment_notes').value = '';
    document.getElementById('paymentModal').classList.remove('hidden');
}

function savePayment(event) {
    event.preventDefault();
    const year = document.getElementById('yearFilter').value;
    
    if (year === 'all') {
        alert('Please select a specific year first');
        return;
    }
    
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
                alert('Payment recorded!');
            } else {
                alert('Error: ' + data.message);
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
                    <div class="bg-gray-50 rounded p-3">
                        <div class="flex justify-between">
                            <span>${new Date(p.payment_date).toLocaleDateString()}</span>
                            <span class="font-bold text-green-600">RWF ${parseFloat(p.amount).toLocaleString()}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span>Year: ${p.year}</span>
                            <span>Method: ${p.payment_method}</span>
                            <span>By: ${p.recorded_by || 'System'}</span>
                        </div>
                    </div>
                `).join('');
            } else {
                container.innerHTML = '<p class="text-center text-gray-500">No payments recorded</p>';
            }
            document.getElementById('viewModal').classList.remove('hidden');
        });
}

function deleteSponsor(id, name) {
    if (confirm(`Delete "${name}"? This will delete all payments too.`)) {
        fetch(`/finance/sponsors/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                filterSponsors();
                alert('Sponsor deleted');
            } else {
                alert('Error: ' + data.message);
            }
        });
    }
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

// Event listeners
document.getElementById('searchSponsor').addEventListener('input', () => filterSponsors());
document.getElementById('sponsorStatus').addEventListener('change', () => filterSponsors());

// Initialize
populateYears();
filterSponsors();
</script>