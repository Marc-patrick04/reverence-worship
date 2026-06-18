<div>
   
    
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Expenses</p>
                    <p class="text-2xl font-bold text-gray-800" id="totalExpenses">RWF 0</p>
                </div>
                <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-chart-line text-red-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Pending Approval</p>
                    <p class="text-2xl font-bold text-gray-800" id="pendingExpenses">RWF 0</p>
                </div>
                <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Approved</p>
                    <p class="text-2xl font-bold text-gray-800" id="approvedExpenses">RWF 0</p>
                </div>
                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">This Month</p>
                    <p class="text-2xl font-bold text-gray-800" id="monthlyExpenses">RWF 0</p>
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-calendar text-blue-600"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Add Expense Form -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-8">
        <div class="bg-gray-50 px-6 py-4 border-b">
            <h4 class="font-semibold text-gray-800 flex items-center gap-2">
                <i class="fas fa-plus-circle text-red-600"></i>
                Record New Expense
            </h4>
        </div>
        <div class="p-6">
            <form id="expenseForm" onsubmit="submitExpense(event)">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Amount <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 text-sm">RWF</span>
                            <input type="number" id="expenseAmount" name="amount" step="0.01" required 
                                   placeholder="0.00"
                                   class="w-full pl-12 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                        </div>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Reason / Description <span class="text-red-500">*</span></label>
                        <textarea id="expenseDescription" name="description" rows="3" required 
                                  placeholder="Reason for the expense..."
                                  class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Expense Date <span class="text-red-500">*</span></label>
                        <input type="date" id="expenseDate" name="date" value="{{ date('Y-m-d') }}" required
                               class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    </div>
                    <div class="flex items-center">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" id="requestApproval" name="request_approval" class="w-4 h-4 text-red-600 rounded focus:ring-red-500">
                            <span class="text-sm text-gray-700">Request Approval</span>
                            <span class="text-xs text-gray-400">(Pending approval from admin)</span>
                        </label>
                    </div>
                </div>
                <div class="flex justify-end mt-6 pt-4 border-t">
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2.5 rounded-lg text-sm font-medium transition flex items-center gap-2">
                        <i class="fas fa-save"></i> Save Expense
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Expense History -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 border-b">
            <h4 class="font-semibold text-gray-800 flex items-center gap-2">
                <i class="fas fa-history text-gray-600"></i>
                Expense History
            </h4>
        </div>
        
        <!-- Filters -->
        <div class="p-4 border-b bg-white">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Category</label>
                    <select id="filterCategory" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                        <option value="all">All Categories</option>
                        <option value="utilities">Utilities</option>
                        <option value="salaries">Salaries</option>
                        <option value="maintenance">Maintenance</option>
                        <option value="events">Events</option>
                        <option value="equipment">Equipment</option>
                        <option value="supplies">Supplies</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Status</label>
                    <select id="filterStatus" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                        <option value="all">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Date From</label>
                    <input type="date" id="filterStartDate" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Date To</label>
                    <input type="date" id="filterEndDate" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                </div>
            </div>
            <div class="flex justify-end mt-4">
                <button onclick="resetExpenseFilters()" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm transition flex items-center gap-2">
                    <i class="fas fa-undo"></i> Reset Filters
                </button>
            </div>
        </div>
        
        <!-- Expenses Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">DATE</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">REASON</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">CATEGORY</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">AMOUNT</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">STATUS</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ACTIONS</th>
                    </tr>
                </thead>
                <tbody id="expenses-table-body">
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                            <p>Loading expenses...</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- View Expense Modal -->
<div id="viewExpenseModal" class="modal hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-6 border w-full max-w-md shadow-xl rounded-2xl bg-white">
        <div class="flex justify-between items-center pb-4 border-b">
            <h3 class="text-xl font-bold text-gray-800">Expense Details</h3>
            <button onclick="closeModal('viewExpenseModal')" class="text-gray-400 hover:text-gray-600 transition">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div id="viewExpenseContent" class="mt-4 space-y-3"></div>
        <div class="flex justify-end mt-6 pt-4 border-t">
            <button onclick="closeModal('viewExpenseModal')" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm transition">
                Close
            </button>
        </div>
    </div>
</div>

<script>
function submitExpense(event) {
    event.preventDefault();
    
    const submitBtn = event.target.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Saving...';
    
    const formData = new FormData();
    formData.append('amount', document.getElementById('expenseAmount').value);
    formData.append('category', document.getElementById('expenseCategory').value);
    formData.append('description', document.getElementById('expenseDescription').value);
    formData.append('date', document.getElementById('expenseDate').value);
    formData.append('request_approval', document.getElementById('requestApproval').checked ? 1 : 0);
    
    fetch('/finance/expenses/store', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-save mr-2"></i> Save Expense';
        
        if (data.success) {
            document.getElementById('expenseForm').reset();
            document.getElementById('expenseDate').value = new Date().toISOString().split('T')[0];
            filterExpenses();
            showNotification('Expense recorded successfully!', 'success');
        } else {
            showNotification('Error: ' + (data.message || 'Failed to record expense'), 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-save mr-2"></i> Save Expense';
        showNotification('Network error: ' + error.message, 'error');
    });
}

function resetExpenseFilters() {
    document.getElementById('filterCategory').value = 'all';
    document.getElementById('filterStatus').value = 'all';
    document.getElementById('filterStartDate').value = '';
    document.getElementById('filterEndDate').value = '';
    filterExpenses();
}

function filterExpenses() {
    const category = document.getElementById('filterCategory')?.value || 'all';
    const status = document.getElementById('filterStatus')?.value || 'all';
    const startDate = document.getElementById('filterStartDate')?.value || '';
    const endDate = document.getElementById('filterEndDate')?.value || '';
    
    let url = `/finance/expenses/filter?category=${category}&status=${status}&start_date=${startDate}&end_date=${endDate}`;
    
    fetch(url, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateExpensesTable(data.expenses);
            updateStats(data.expenses);
        }
    })
    .catch(error => console.error('Error:', error));
}

function updateStats(expenses) {
    const total = expenses.reduce((sum, e) => sum + parseFloat(e.amount), 0);
    const pending = expenses.filter(e => e.status === 'pending').reduce((sum, e) => sum + parseFloat(e.amount), 0);
    const approved = expenses.filter(e => e.status === 'approved').reduce((sum, e) => sum + parseFloat(e.amount), 0);
    
    const currentMonth = new Date().getMonth();
    const currentYear = new Date().getFullYear();
    const monthly = expenses.filter(e => {
        const date = new Date(e.date);
        return date.getMonth() === currentMonth && date.getFullYear() === currentYear;
    }).reduce((sum, e) => sum + parseFloat(e.amount), 0);
    
    document.getElementById('totalExpenses').textContent = 'RWF ' + total.toLocaleString();
    document.getElementById('pendingExpenses').textContent = 'RWF ' + pending.toLocaleString();
    document.getElementById('approvedExpenses').textContent = 'RWF ' + approved.toLocaleString();
    document.getElementById('monthlyExpenses').textContent = 'RWF ' + monthly.toLocaleString();
}

function updateExpensesTable(expenses) {
    const tbody = document.getElementById('expenses-table-body');
    
    if (!expenses || expenses.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                    <i class="fas fa-inbox text-4xl mb-2 text-gray-300"></i>
                    <p>No expenses recorded yet.</p>
                    <p class="text-xs mt-1 text-gray-400">Click "Save Expense" to create your first expense record</p>
                </td>
            </tr>
        `;
        return;
    }
    
    tbody.innerHTML = expenses.map((expense, index) => `
        <tr class="border-b hover:bg-gray-50 transition">
            <td class="px-6 py-4 text-sm text-gray-400">${index + 1}</td>
            <td class="px-6 py-4 text-sm text-gray-600">${formatDate(expense.date)}</td>
            <td class="px-6 py-4 text-sm text-gray-800 max-w-xs truncate">${escapeHtml(expense.description || '-')}</td>
            <td class="px-6 py-4 text-sm capitalize">${expense.category || '-'}</td>
            <td class="px-6 py-4 text-sm font-semibold text-red-600">RWF ${parseFloat(expense.amount).toLocaleString()}</td>
            <td class="px-6 py-4">
                <span class="px-2 py-1 rounded-full text-xs font-medium ${getStatusBadge(expense.status)}">
                    ${expense.status === 'approved' ? 'Approved' : (expense.status === 'pending' ? 'Pending' : (expense.status || 'Pending'))}
                </span>
            </td>
            <td class="px-6 py-4">
                <div class="flex items-center gap-2">
                    <button onclick="viewExpense(${expense.id})" class="text-blue-500 hover:text-blue-700 transition" title="View Details">
                        <i class="fas fa-eye"></i>
                    </button>
                    ${expense.status === 'pending' ? `
                        <button onclick="approveExpense(${expense.id})" class="text-green-500 hover:text-green-700 transition" title="Approve">
                            <i class="fas fa-check-circle"></i>
                        </button>
                    ` : ''}
                    <button onclick="deleteExpense(${expense.id})" class="text-red-500 hover:text-red-700 transition" title="Delete">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
}

function viewExpense(id) {
    fetch(`/finance/expenses/${id}/details`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const e = data.expense;
            document.getElementById('viewExpenseContent').innerHTML = `
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-xs text-gray-500">Amount</p>
                    <p class="text-xl font-bold text-red-600">RWF ${parseFloat(e.amount).toLocaleString()}</p>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-gray-50 rounded-lg p-3">
                        <p class="text-xs text-gray-500">Category</p>
                        <p class="font-medium capitalize">${e.category || '-'}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3">
                        <p class="text-xs text-gray-500">Date</p>
                        <p class="font-medium">${formatDate(e.date)}</p>
                    </div>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-xs text-gray-500">Description</p>
                    <p class="text-sm">${escapeHtml(e.description || '-')}</p>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-gray-50 rounded-lg p-3">
                        <p class="text-xs text-gray-500">Status</p>
                        <p class="font-medium capitalize">${e.status || 'Pending'}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3 border-l-4 border-blue-500">
                        <p class="text-xs text-gray-500">Recorded By</p>
                        <p class="font-medium text-sm">${escapeHtml(e.created_by_name || 'System')}</p>
                    </div>
                </div>
                ${e.approved_by_name ? `
                <div class="bg-gray-50 rounded-lg p-3 border-l-4 border-green-500">
                    <p class="text-xs text-gray-500">Approved By</p>
                    <p class="font-medium text-sm">${escapeHtml(e.approved_by_name)}</p>
                </div>
                ` : ''}
            `;
            document.getElementById('viewExpenseModal').classList.remove('hidden');
        }
    });
}

function approveExpense(id) {
    if (confirm('Approve this expense?')) {
        fetch(`/finance/expenses/${id}/approve`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                filterExpenses();
                showNotification('Expense approved successfully!', 'success');
            } else {
                showNotification('Error: ' + (data.message || 'Failed to approve expense'), 'error');
            }
        });
    }
}

function deleteExpense(id) {
    if (confirm('Are you sure you want to delete this expense? This action cannot be undone.')) {
        fetch(`/finance/expenses/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                filterExpenses();
                showNotification('Expense deleted successfully', 'success');
            } else {
                showNotification('Error: ' + (data.message || 'Failed to delete expense'), 'error');
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

function formatDate(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-GB', { day: '2-digit', month: '2-digit', year: 'numeric' });
}

function getStatusBadge(status) {
    switch(status) {
        case 'approved': return 'bg-green-100 text-green-700';
        case 'pending': return 'bg-yellow-100 text-yellow-700';
        case 'rejected': return 'bg-red-100 text-red-700';
        default: return 'bg-gray-100 text-gray-700';
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

// Auto-filter with debounce
let filterTimeout = null;
document.getElementById('filterCategory')?.addEventListener('change', function() { 
    clearTimeout(filterTimeout);
    filterTimeout = setTimeout(() => filterExpenses(), 300);
});
document.getElementById('filterStatus')?.addEventListener('change', function() { 
    clearTimeout(filterTimeout);
    filterTimeout = setTimeout(() => filterExpenses(), 300);
});
document.getElementById('filterStartDate')?.addEventListener('change', function() { 
    clearTimeout(filterTimeout);
    filterTimeout = setTimeout(() => filterExpenses(), 300);
});
document.getElementById('filterEndDate')?.addEventListener('change', function() { 
    clearTimeout(filterTimeout);
    filterTimeout = setTimeout(() => filterExpenses(), 300);
});

// Load initial data
filterExpenses();

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
</script>