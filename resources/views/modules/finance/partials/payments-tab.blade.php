<div>
    <!-- Header -->
    <div class="mb-6">
        <h3 class="text-xl font-bold text-gray-800">All Payment Records</h3>
        <p class="text-sm text-gray-500 mt-1">Complete list of all payments made in the finance department</p>
    </div>
    
    <!-- Filters -->
    <div class="bg-gray-50 rounded-xl p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Search Member</label>
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input type="text" id="paymentSearchInput" placeholder="Search by member name or email..." 
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Term</label>
                <select id="paymentTermFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="">All Terms</option>
                    @for($i = 1; $i <= ($numberOfTerms ?? 3); $i++)
                        <option value="{{ $i }}">Term {{ $i }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                <select id="paymentMethodFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="">All Methods</option>
                    <option value="cash">Cash</option>
                    <option value="bank_transfer">Bank Transfer</option>
                    <option value="mobile_money">Mobile Money</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date Range</label>
                <input type="month" id="paymentMonthFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
            </div>
        </div>
        <div class="flex justify-end mt-4">
            <button onclick="filterPayments()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg text-sm">
                <i class="fas fa-search mr-2"></i> Apply Filters
            </button>
        </div>
    </div>
    
    <!-- Stats Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl shadow-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm">Total Payments</p>
                    <p class="text-2xl font-bold" id="totalPayments">RWF 0</p>
                </div>
                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                    <i class="fas fa-chart-line text-white"></i>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl shadow-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm">Completed</p>
                    <p class="text-2xl font-bold" id="completedPayments">RWF 0</p>
                </div>
                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                    <i class="fas fa-check-circle text-white"></i>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-xl shadow-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-yellow-100 text-sm">Pending</p>
                    <p class="text-2xl font-bold" id="pendingPayments">RWF 0</p>
                </div>
                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                    <i class="fas fa-clock text-white"></i>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl shadow-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm">Transactions</p>
                    <p class="text-2xl font-bold" id="paymentCount">0</p>
                </div>
                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                    <i class="fas fa-receipt text-white"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Payments Table -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">DATE</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">MEMBER</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">TERM</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">AMOUNT</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PAYMENT METHOD</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NOTES</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ACTIONS</th>
                    </tr>
                </thead>
                <tbody id="payments-table-body">
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                            <p>Loading payments...</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- View Payment Details Modal -->
<div id="viewPaymentModal" class="modal hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-6 border w-full max-w-md shadow-xl rounded-2xl bg-white">
        <div class="flex justify-between items-center pb-4 border-b">
            <h3 class="text-xl font-bold text-gray-800">Payment Details</h3>
            <button onclick="closeModal('viewPaymentModal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div id="viewPaymentContent" class="mt-4 space-y-3"></div>
        <div class="flex justify-end mt-6 pt-4 border-t">
            <button onclick="closeModal('viewPaymentModal')" class="px-5 py-2 bg-gray-600 text-white rounded-lg text-sm hover:bg-gray-700 transition">
                Close
            </button>
        </div>
    </div>
</div>

<script>
// Load payments on page load
function loadPayments() {
    filterPayments();
}

function filterPayments() {
    const search = document.getElementById('paymentSearchInput')?.value || '';
    const term = document.getElementById('paymentTermFilter')?.value || '';
    const method = document.getElementById('paymentMethodFilter')?.value || '';
    const month = document.getElementById('paymentMonthFilter')?.value || '';
    
    let url = `/finance/payments/filter?search=${encodeURIComponent(search)}&term=${term}&method=${method}&month=${month}`;
    
    fetch(url, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updatePaymentsTable(data.payments);
            updatePaymentStats(data.payments);
        } else {
            console.error('Error loading payments:', data.message);
        }
    })
    .catch(error => console.error('Error:', error));
}

function updatePaymentsTable(payments) {
    const tbody = document.getElementById('payments-table-body');
    
    if (!payments || payments.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                    <i class="fas fa-inbox text-4xl mb-2 text-gray-300"></i>
                    <p>No payment records found</p>
                </td>
            </tr>
        `;
        return;
    }
    
    tbody.innerHTML = payments.map(payment => `
        <tr class="border-b hover:bg-gray-50 transition">
            <td class="px-6 py-4 text-sm text-gray-600">${formatDate(payment.payment_date)}</td>
            <td class="px-6 py-4">
                <div>
                    <p class="font-medium text-gray-800">${escapeHtml(payment.member_name)}</p>
                    <p class="text-xs text-gray-500">${escapeHtml(payment.member_email || '')}</p>
                </div>
            </td>
            <td class="px-6 py-4 text-sm text-gray-600">Term ${payment.term}</td>
            <td class="px-6 py-4">
                <span class="font-semibold text-green-600">RWF ${parseFloat(payment.amount).toLocaleString()}</span>
            </td>
            <td class="px-6 py-4">
                <span class="px-2 py-1 rounded-full text-xs ${getMethodBadge(payment.payment_method)}">
                    ${getMethodName(payment.payment_method)}
                </span>
            </td>
            <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">
                ${payment.notes ? escapeHtml(payment.notes) : '-'}
            </td>
            <td class="px-6 py-4">
                <div class="flex items-center gap-2">
                    <button onclick="viewPaymentDetails(${payment.id})" class="text-blue-500 hover:text-blue-700 transition" title="View Details">
                        <i class="fas fa-eye text-lg"></i>
                    </button>
                    <button onclick="deletePayment(${payment.id})" class="text-red-500 hover:text-red-700 transition" title="Delete Payment">
                        <i class="fas fa-trash-alt text-lg"></i>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
}

function updatePaymentStats(payments) {
    const total = payments.reduce((sum, p) => sum + parseFloat(p.amount), 0);
    const completed = payments.filter(p => p.status === 'completed').reduce((sum, p) => sum + parseFloat(p.amount), 0);
    const pending = payments.filter(p => p.status === 'pending').reduce((sum, p) => sum + parseFloat(p.amount), 0);
    const count = payments.length;
    
    document.getElementById('totalPayments').textContent = 'RWF ' + total.toLocaleString();
    document.getElementById('completedPayments').textContent = 'RWF ' + completed.toLocaleString();
    document.getElementById('pendingPayments').textContent = 'RWF ' + pending.toLocaleString();
    document.getElementById('paymentCount').textContent = count;
}

function viewPaymentDetails(id) {
    fetch(`/finance/payments/${id}/details`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('viewPaymentContent').innerHTML = `
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-xs text-gray-500">Member</p>
                    <p class="font-medium text-gray-800">${escapeHtml(data.payment.member_name)}</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-xs text-gray-500">Amount</p>
                    <p class="text-xl font-bold text-green-600">RWF ${parseFloat(data.payment.amount).toLocaleString()}</p>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-gray-50 rounded-lg p-3">
                        <p class="text-xs text-gray-500">Term</p>
                        <p class="font-medium">Term ${data.payment.term}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3">
                        <p class="text-xs text-gray-500">Payment Date</p>
                        <p class="font-medium">${formatDate(data.payment.payment_date)}</p>
                    </div>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-xs text-gray-500">Payment Method</p>
                    <p class="font-medium capitalize">${getMethodName(data.payment.payment_method)}</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-xs text-gray-500">Notes</p>
                    <p class="text-sm">${data.payment.notes || 'No notes'}</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-xs text-gray-500">Recorded By</p>
                    <p class="text-sm">${escapeHtml(data.payment.recorded_by_name || 'System')}</p>
                </div>
            `;
            document.getElementById('viewPaymentModal').classList.remove('hidden');
        }
    })
    .catch(error => console.error('Error:', error));
}

function deletePayment(id) {
    if (confirm('Are you sure you want to delete this payment record? This action cannot be undone.')) {
        fetch(`/finance/payments/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                filterPayments();
                alert('Payment deleted successfully');
            } else {
                alert('Error: ' + (data.message || 'Failed to delete payment'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Network error: ' + error.message);
        });
    }
}

function formatDate(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-GB', { day: '2-digit', month: '2-digit', year: 'numeric' });
}

function getMethodBadge(method) {
    switch(method) {
        case 'cash': return 'bg-green-100 text-green-700';
        case 'bank_transfer': return 'bg-blue-100 text-blue-700';
        case 'mobile_money': return 'bg-purple-100 text-purple-700';
        default: return 'bg-gray-100 text-gray-700';
    }
}

function getMethodName(method) {
    switch(method) {
        case 'cash': return 'Cash';
        case 'bank_transfer': return 'Bank Transfer';
        case 'mobile_money': return 'Mobile Money';
        default: return method || '-';
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

// Add event listeners for filters
document.getElementById('paymentSearchInput')?.addEventListener('keyup', function() {
    filterPayments();
});
document.getElementById('paymentTermFilter')?.addEventListener('change', function() {
    filterPayments();
});
document.getElementById('paymentMethodFilter')?.addEventListener('change', function() {
    filterPayments();
});
document.getElementById('paymentMonthFilter')?.addEventListener('change', function() {
    filterPayments();
});

// Load initial data
loadPayments();
</script>