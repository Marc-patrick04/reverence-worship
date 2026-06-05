<div>
    <!-- Header -->
    <div class="mb-6">
        <h3 class="text-xl font-bold text-gray-800">Expense Management</h3>
        <p class="text-sm text-gray-500 mt-1">Track and manage all department expenses</p>
    </div>
    
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-xl shadow-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-red-100 text-sm">Total Expenses</p>
                    <p class="text-2xl font-bold" id="totalExpenses">RWF 0</p>
                </div>
                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                    <i class="fas fa-chart-line text-white"></i>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-xl shadow-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-yellow-100 text-sm">Pending Approval</p>
                    <p class="text-2xl font-bold" id="pendingExpenses">RWF 0</p>
                </div>
                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                    <i class="fas fa-clock text-white"></i>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl shadow-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm">Approved</p>
                    <p class="text-2xl font-bold" id="approvedExpenses">RWF 0</p>
                </div>
                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                    <i class="fas fa-check-circle text-white"></i>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl shadow-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm">This Month</p>
                    <p class="text-2xl font-bold" id="monthlyExpenses">RWF 0</p>
                </div>
                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                    <i class="fas fa-calendar text-white"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Add Expense Form -->
    <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden mb-8">
        <div class="bg-gray-50 px-6 py-4 border-b">
            <h4 class="font-semibold text-gray-800">Record New Expense</h4>
        </div>
        <div class="p-6">
            <form id="expenseForm" onsubmit="submitExpense(event)">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Amount *</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">RWF</span>
                            <input type="number" id="expenseAmount" name="amount" step="0.01" required 
                                   placeholder="0.00"
                                   class="w-full pl-12 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                        <select id="expenseCategory" name="category" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500">
                            <option value="utilities">Utilities</option>
                            <option value="salaries">Salaries</option>
                            <option value="maintenance">Maintenance</option>
                            <option value="events">Events</option>
                            <option value="equipment">Equipment</option>
                            <option value="supplies">Supplies</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Reason / Description *</label>
                        <textarea id="expenseDescription" name="description" rows="3" required 
                                  placeholder="Reason for the expense..."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Expense Date *</label>
                        <input type="date" id="expenseDate" name="date" value="{{ date('Y-m-d') }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500">
                    </div>
                    <div class="flex items-end">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" id="requestApproval" name="request_approval" class="w-4 h-4 text-red-600 rounded">
                            <span class="text-sm text-gray-700">Request Approval</span>
                        </label>
                    </div>
                </div>
                <div class="flex justify-end mt-6 pt-4 border-t">
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg text-sm font-medium transition">
                        <i class="fas fa-save mr-2"></i> Save Expense
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Expense History -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 border-b">
            <h4 class="font-semibold text-gray-800">Expense History</h4>
        </div>
        
        <!-- Filters -->
        <div class="p-4 border-b bg-white">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                    <select id="filterCategory" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500">
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
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select id="filterStatus" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500">
                        <option value="all">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date From</label>
                    <input type="date" id="filterStartDate" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date To</label>
                    <input type="date" id="filterEndDate" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500">
                </div>
            </div>
            <div class="flex justify-end mt-4">
                <button onclick="filterExpenses()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm">
                    <i class="fas fa-search mr-2"></i> Apply Filters
                </button>
            </div>
        </div>
        
        <!-- Expenses Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
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
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
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
            <button onclick="closeModal('viewExpenseModal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div id="viewExpenseContent" class="mt-4 space-y-3"></div>
        <div class="flex justify-end mt-6 pt-4 border-t">
            <button onclick="closeModal('viewExpenseModal')" class="px-5 py-2 bg-gray-600 text-white rounded-lg text-sm hover:bg-gray-700 transition">
                Close
            </button>
        </div>
    </div>
</div>

<script>
function submitExpense(event) {
    event.preventDefault();
    
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
        if (data.success) {
            document.getElementById('expenseForm').reset();
            document.getElementById('expenseDate').value = new Date().toISOString().split('T')[0];
            filterExpenses();
            alert('Expense recorded successfully!');
        } else {
            alert('Error: ' + (data.message || 'Failed to record expense'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Network error: ' + error.message);
    });
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
                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                    <i class="fas fa-inbox text-4xl mb-2 text-gray-300"></i>
                    <p>No expenses recorded yet.</p>
                    <p class="text-xs mt-1">Click "Add Expense" to create your first expense record</p>
                </td>
            </tr>
        `;
        return;
    }
    
    tbody.innerHTML = expenses.map(expense => `
        <tr class="border-b hover:bg-gray-50 transition">
            <td class="px-6 py-4 text-sm text-gray-600">${formatDate(expense.date)}</td>
            <td class="px-6 py-4 text-sm text-gray-800 max-w-xs truncate">${escapeHtml(expense.description || '-')}</td>
            <td class="px-6 py-4 text-sm capitalize">${expense.category || '-'}</td>
            <td class="px-6 py-4 text-sm font-semibold text-red-600">RWF ${parseFloat(expense.amount).toLocaleString()}</td>
            <td class="px-6 py-4">
                <span class="px-2 py-1 rounded-full text-xs ${getStatusBadge(expense.status)}">
                    ${expense.status === 'approved' ? 'Approved' : (expense.status === 'pending' ? 'Pending' : (expense.status || 'Pending'))}
                </span>
            </td>
            <td class="px-6 py-4">
                <div class="flex items-center gap-2">
                    <button onclick="viewExpense(${expense.id})" class="text-blue-500 hover:text-blue-700 transition" title="View Details">
                        <i class="fas fa-eye text-lg"></i>
                    </button>
                    ${expense.status === 'pending' ? `
                        <button onclick="approveExpense(${expense.id})" class="text-green-500 hover:text-green-700 transition" title="Approve">
                            <i class="fas fa-check-circle text-lg"></i>
                        </button>
                    ` : ''}
                    <button onclick="deleteExpense(${expense.id})" class="text-red-500 hover:text-red-700 transition" title="Delete">
                        <i class="fas fa-trash-alt text-lg"></i>
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
            document.getElementById('viewExpenseContent').innerHTML = `
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-xs text-gray-500">Amount</p>
                    <p class="text-xl font-bold text-red-600">RWF ${parseFloat(data.expense.amount).toLocaleString()}</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-xs text-gray-500">Category</p>
                    <p class="font-medium capitalize">${data.expense.category || '-'}</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-xs text-gray-500">Date</p>
                    <p class="font-medium">${formatDate(data.expense.date)}</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-xs text-gray-500">Description</p>
                    <p class="text-sm">${escapeHtml(data.expense.description || '-')}</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-xs text-gray-500">Status</p>
                    <p class="font-medium capitalize">${data.expense.status || 'Pending'}</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-xs text-gray-500">Recorded By</p>
                    <p class="text-sm">${escapeHtml(data.expense.created_by_name || 'System')}</p>
                </div>
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
                alert('Expense approved successfully!');
            } else {
                alert('Error: ' + (data.message || 'Failed to approve expense'));
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
                alert('Expense deleted successfully');
            } else {
                alert('Error: ' + (data.message || 'Failed to delete expense'));
            }
        });
    }
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

// Event listeners for filters
document.getElementById('filterCategory')?.addEventListener('change', function() { filterExpenses(); });
document.getElementById('filterStatus')?.addEventListener('change', function() { filterExpenses(); });
document.getElementById('filterStartDate')?.addEventListener('change', function() { filterExpenses(); });
document.getElementById('filterEndDate')?.addEventListener('change', function() { filterExpenses(); });

// Load initial data
filterExpenses();
</script>