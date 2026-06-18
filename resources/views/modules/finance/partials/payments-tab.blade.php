<div>
    <!-- Filters -->
    <div class="bg-gray-50 rounded-xl p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Search Member</label>
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input type="text" id="paymentSearchInput" placeholder="Search by member name or email..." 
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Term</label>
                <select id="paymentTermFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Terms</option>
                    @for($i = 1; $i <= ($numberOfTerms ?? 3); $i++)
                        <option value="{{ $i }}">Term {{ $i }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                <select id="paymentMethodFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Methods</option>
                    <option value="cash">Cash</option>
                    <option value="bank_transfer">Bank Transfer</option>
                    <option value="mobile_money">Mobile Money</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date Range</label>
                <input type="month" id="paymentMonthFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
        </div>
        <div class="flex justify-end mt-4">
            <button onclick="resetFilters()" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2 rounded-lg text-sm transition flex items-center gap-2">
                <i class="fas fa-undo"></i> Reset Filters
            </button>
        </div>
    </div>
    
    <!-- Stats Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-md p-4 border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total Payments</p>
                    <p class="text-2xl font-bold text-gray-800" id="totalPayments">RWF 0</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-chart-line text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-md p-4 border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total Transactions</p>
                    <p class="text-2xl font-bold text-gray-800" id="paymentCount">0</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-receipt text-green-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-md p-4 border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Payment Methods</p>
                    <p class="text-2xl font-bold text-gray-800" id="paymentMethodsCount">0</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-credit-card text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Payments Table -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" id="paymentsTable">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">DATE</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">MEMBER</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">TERM</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">AMOUNT</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">METHOD</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NOTES</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ACTIONS</th>
                    </tr>
                </thead>
                <tbody id="payments-table-body">
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-500">
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
            <button onclick="closeModal('viewPaymentModal')" class="text-gray-400 hover:text-gray-600 transition">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div id="viewPaymentContent" class="mt-4 space-y-3"></div>
        <div class="flex justify-end mt-6 pt-4 border-t">
            <button onclick="closeModal('viewPaymentModal')" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm transition">
                Close
            </button>
        </div>
    </div>
</div>

<!-- Edit Payment Modal -->
<div id="editPaymentModal" class="modal hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-6 border w-full max-w-md shadow-xl rounded-2xl bg-white">
        <div class="flex justify-between items-center pb-4 border-b">
            <h3 class="text-xl font-bold text-gray-800">Edit Payment</h3>
            <button onclick="closeModal('editPaymentModal')" class="text-gray-400 hover:text-gray-600 transition">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form id="editPaymentForm" class="mt-4 space-y-4">
            <input type="hidden" id="editPaymentId">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Amount (RWF)</label>
                <input type="number" id="editAmount" step="0.01" min="0" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                <select id="editPaymentMethod" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="cash">Cash</option>
                    <option value="bank_transfer">Bank Transfer</option>
                    <option value="mobile_money">Mobile Money</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Payment Date</label>
                <input type="date" id="editPaymentDate" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Term</label>
                <select id="editTerm" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @for($i = 1; $i <= ($numberOfTerms ?? 3); $i++)
                        <option value="{{ $i }}">Term {{ $i }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                <textarea id="editNotes" rows="3" 
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                          placeholder="Optional notes..."></textarea>
            </div>
            <div class="flex justify-end gap-3 pt-4 border-t">
                <button type="button" onclick="closeModal('editPaymentModal')" class="px-5 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-lg text-sm transition">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm transition flex items-center gap-2">
                    <i class="fas fa-save"></i> Update Payment
                </button>
            </div>
        </form>
    </div>
</div>

<script>
(function() {
    'use strict';
    
    // Local variables
    let searchTimeout = null;
    
    // Load payments on page load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            loadPayments();
        });
    } else {
        loadPayments();
    }

    function loadPayments() {
        filterPayments();
    }

    window.resetFilters = function() {
        document.getElementById('paymentSearchInput').value = '';
        document.getElementById('paymentTermFilter').value = '';
        document.getElementById('paymentMethodFilter').value = '';
        document.getElementById('paymentMonthFilter').value = '';
        filterPayments();
    };

    window.filterPayments = function() {
        const search = document.getElementById('paymentSearchInput')?.value || '';
        const term = document.getElementById('paymentTermFilter')?.value || '';
        const method = document.getElementById('paymentMethodFilter')?.value || '';
        const month = document.getElementById('paymentMonthFilter')?.value || '';
        
        let url = `/finance/payments/filter?search=${encodeURIComponent(search)}&term=${term}&method=${method}&month=${month}`;
        
        fetch(url, {
            headers: { 
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                updatePaymentsTable(data.payments);
                updatePaymentStats(data.payments);
            } else {
                console.error('Error loading payments:', data.message);
                showNotification('Error loading payments: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error loading payments. Please try again.', 'error');
        });
    };

    function updatePaymentsTable(payments) {
        const tbody = document.getElementById('payments-table-body');
        
        if (!payments || payments.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-2 text-gray-300"></i>
                        <p>No payment records found</p>
                        <p class="text-sm text-gray-400 mt-1">Try adjusting your filters</p>
                    </td>
                </tr>
            `;
            return;
        }
        
        tbody.innerHTML = payments.map((payment, index) => `
            <tr class="border-b hover:bg-gray-50 transition">
                <td class="px-6 py-4 text-sm text-gray-400">${index + 1}</td>
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
                        <button onclick="window.viewPaymentDetails(${payment.id})" class="text-blue-500 hover:text-blue-700 transition" title="View Details">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button onclick="window.openEditModal(${payment.id})" class="text-green-500 hover:text-green-700 transition" title="Edit Payment">
                            <i class="fas fa-edit"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');
    }

    function updatePaymentStats(payments) {
        const total = payments.reduce((sum, p) => sum + parseFloat(p.amount), 0);
        const count = payments.length;
        
        const methods = new Set(payments.map(p => p.payment_method));
        const methodCount = methods.size;
        
        document.getElementById('totalPayments').textContent = 'RWF ' + total.toLocaleString();
        document.getElementById('paymentCount').textContent = count;
        document.getElementById('paymentMethodsCount').textContent = methodCount;
    }

    window.viewPaymentDetails = function(id) {
        fetch(`/finance/payments/${id}/details`, {
            headers: { 
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Payment not found');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                const p = data.payment;
                document.getElementById('viewPaymentContent').innerHTML = `
                    <div class="bg-gray-50 rounded-lg p-3">
                        <p class="text-xs text-gray-500">Member</p>
                        <p class="font-medium text-gray-800">${escapeHtml(p.member_name)}</p>
                        <p class="text-sm text-gray-600">${escapeHtml(p.member_email || '')}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3">
                        <p class="text-xs text-gray-500">Amount</p>
                        <p class="text-xl font-bold text-green-600">RWF ${parseFloat(p.amount).toLocaleString()}</p>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-xs text-gray-500">Term</p>
                            <p class="font-medium">Term ${p.term}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-xs text-gray-500">Payment Date</p>
                            <p class="font-medium">${formatDate(p.payment_date)}</p>
                        </div>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3">
                        <p class="text-xs text-gray-500">Payment Method</p>
                        <p class="font-medium capitalize">${getMethodName(p.payment_method)}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3">
                        <p class="text-xs text-gray-500">Notes</p>
                        <p class="text-sm">${p.notes || 'No notes'}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3 border-l-4 border-blue-500">
                        <p class="text-xs text-gray-500">Recorded By</p>
                        <p class="font-medium text-gray-800">${escapeHtml(p.recorded_by_name || 'System')}</p>
                        <p class="text-xs text-gray-400">${p.created_at ? formatDateTime(p.created_at) : ''}</p>
                    </div>
                `;
                document.getElementById('viewPaymentModal').classList.remove('hidden');
            } else {
                showNotification(data.message || 'Error loading payment details', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Payment not found or error loading details', 'error');
        });
    };

    window.openEditModal = function(id) {
        fetch(`/finance/payments/${id}/details`, {
            headers: { 
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Payment not found');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                const p = data.payment;
                document.getElementById('editPaymentId').value = p.id;
                document.getElementById('editAmount').value = p.amount;
                document.getElementById('editPaymentMethod').value = p.payment_method;
                document.getElementById('editPaymentDate').value = p.payment_date;
                document.getElementById('editTerm').value = p.term;
                document.getElementById('editNotes').value = p.notes || '';
                document.getElementById('editPaymentModal').classList.remove('hidden');
            } else {
                showNotification(data.message || 'Error loading payment details', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Payment not found or error loading details', 'error');
        });
    };

    window.closeModal = function(modalId) {
        document.getElementById(modalId).classList.add('hidden');
    };

    // Edit payment form submission
    document.getElementById('editPaymentForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const id = document.getElementById('editPaymentId').value;
        const formData = {
            amount: document.getElementById('editAmount').value,
            payment_method: document.getElementById('editPaymentMethod').value,
            payment_date: document.getElementById('editPaymentDate').value,
            term: document.getElementById('editTerm').value,
            notes: document.getElementById('editNotes').value
        };
        
        fetch(`/finance/payments/${id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: JSON.stringify(formData)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showNotification('Payment updated successfully!', 'success');
                window.closeModal('editPaymentModal');
                window.filterPayments();
            } else {
                showNotification('Error: ' + (data.message || 'Failed to update payment'), 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Network error: ' + error.message, 'error');
        });
    });

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

    function formatDate(dateString) {
        if (!dateString) return '-';
        const date = new Date(dateString);
        return date.toLocaleDateString('en-GB', { day: '2-digit', month: '2-digit', year: 'numeric' });
    }

    function formatDateTime(dateString) {
        if (!dateString) return '';
        const date = new Date(dateString);
        return date.toLocaleString('en-GB', { 
            day: '2-digit', 
            month: '2-digit', 
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
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

    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Auto-filter with debounce
    document.getElementById('paymentSearchInput')?.addEventListener('keyup', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => window.filterPayments(), 400);
    });

    document.getElementById('paymentTermFilter')?.addEventListener('change', function() {
        window.filterPayments();
    });
    document.getElementById('paymentMethodFilter')?.addEventListener('change', function() {
        window.filterPayments();
    });
    document.getElementById('paymentMonthFilter')?.addEventListener('change', function() {
        window.filterPayments();
    });

    // Add animation styles (only if not already added)
    if (!document.getElementById('payments-tab-styles')) {
        const style = document.createElement('style');
        style.id = 'payments-tab-styles';
        style.textContent = `
            @keyframes slideIn {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
            .animate-slide-in {
                animation: slideIn 0.3s ease-out;
            }
        `;
        document.head.appendChild(style);
    }

})();
</script>

<style>
/* Table hover effect */
tbody tr {
    transition: background-color 0.2s ease;
}

/* Modal backdrop */
.modal {
    background-color: rgba(0, 0, 0, 0.5);
}

.modal .relative {
    animation: modalIn 0.3s ease-out;
}

@keyframes modalIn {
    from {
        transform: scale(0.9);
        opacity: 0;
    }
    to {
        transform: scale(1);
        opacity: 1;
    }
}

/* Card hover effect */
.bg-white.rounded-xl {
    transition: all 0.2s ease;
}
.bg-white.rounded-xl:hover {
    transform: translateY(-2px);
    shadow: 0 8px 15px -3px rgba(0, 0, 0, 0.1);
}
</style>