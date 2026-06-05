<div>
    <!-- Header Buttons -->
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold text-gray-800">Member Contributions</h3>
        <div class="flex gap-3">
            <button onclick="window.openSetAnnualModal()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm flex items-center gap-2">
                <i class="fas fa-plus-circle"></i> Set Annual Contribution
            </button>
            <button onclick="window.openContributeModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm flex items-center gap-2">
                <i class="fas fa-hand-holding-usd"></i> Contribute for User
            </button>
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
            <input type="text" id="searchContributions" placeholder="Search by member name or email..." 
                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
        </div>
        <p id="contributionsCount" class="text-xs text-gray-500 mt-1">0 contribution records found</p>
    </div>
    
    <!-- Contributions Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">MEMBER</th>
                    @for($i = 1; $i <= ($numberOfTerms ?? 3); $i++)
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">TERM {{ $i }}</th>
                    @endfor
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">TOTAL PROGRESS</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">ACTIONS</th>
                </tr>
            </thead>
            <tbody id="contributions-table-body">
                <tr>
                    <td colspan="{{ ($numberOfTerms ?? 3) + 3 }}" class="text-center py-8 text-gray-500">Loading contributions...<\/td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- View Details Modal -->
<div id="viewDetailsModal" class="modal hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-6 border w-full max-w-2xl shadow-xl rounded-2xl bg-white">
        <div class="flex justify-between items-center pb-4 border-b">
            <h3 id="viewDetailsTitle" class="text-xl font-bold text-gray-800">Contribution Details</h3>
            <button onclick="window.closeModal('viewDetailsModal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div id="viewDetailsContent" class="mt-4 max-h-96 overflow-y-auto"></div>
        <div class="flex justify-end mt-6 pt-4 border-t">
            <button onclick="window.closeModal('viewDetailsModal')" class="px-5 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700 transition">
                Close
            </button>
        </div>
    </div>
</div>

<!-- Set Annual Contribution Modal -->
<div id="setAnnualModal" class="modal hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center pb-3 border-b">
            <h3 class="text-lg font-bold text-gray-800">Set Annual Contribution</h3>
            <button onclick="window.closeModal('setAnnualModal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form id="setAnnualForm" onsubmit="submitSetAnnual(event)">
            @csrf
            <div class="mt-4 space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Search Member *</label>
                    <input type="text" id="searchMemberInput" placeholder="Type member name or email..." 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           onkeyup="searchMembers('setAnnual')">
                    <input type="hidden" id="selectedUserId" name="user_id">
                    <div id="memberSearchResults" class="mt-2 border rounded-lg max-h-48 overflow-y-auto hidden">
                        <!-- Search results will appear here -->
                    </div>
                </div>
                <div id="selectedMemberDisplay" class="hidden bg-green-50 rounded-lg p-2">
                    <div class="flex justify-between items-center">
                        <span id="selectedMemberName" class="text-sm font-medium text-gray-800"></span>
                        <button type="button" onclick="clearSelectedMember()" class="text-red-500 hover:text-red-700">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Annual Amount (RWF) *</label>
                    <input type="number" name="annual_amount" id="annualAmount" step="0.01" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                    <textarea name="notes" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg"></textarea>
                </div>
            </div>
            <div class="flex justify-end gap-2 mt-5 pt-3 border-t">
                <button type="button" onclick="window.closeModal('setAnnualModal')" class="px-4 py-2 border rounded-lg text-sm">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm">Set Contribution</button>
            </div>
        </form>
    </div>
</div>

<!-- Contribute for User Modal -->
<div id="contributeModal" class="modal hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center pb-3 border-b">
            <h3 class="text-lg font-bold text-gray-800">Contribute for User</h3>
            <button onclick="window.closeModal('contributeModal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form id="contributeForm" onsubmit="submitContribute(event)">
            @csrf
            <input type="hidden" id="selectedContributeUserId" name="user_id">
            <div class="mt-4 space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Search Member</label>
                    <input type="text" id="searchContributeMemberInput" placeholder="Type member name or email to search..." 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           onkeyup="searchContributeMembers()">
                    <div id="contributeMemberSearchResults" class="mt-2 border rounded-lg max-h-48 overflow-y-auto hidden">
                        <!-- Search results will appear here -->
                    </div>
                </div>
                <div id="selectedContributeMemberDisplay" class="hidden bg-green-50 rounded-lg p-2 mb-3">
                    <div class="flex justify-between items-center">
                        <span id="selectedContributeMemberName" class="text-sm font-medium text-gray-800"></span>
                        <button type="button" onclick="clearSelectedContributeMember()" class="text-red-500 hover:text-red-700">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Term *</label>
                    <select name="term" id="contributeTerm" required class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        @for($i = 1; $i <= ($numberOfTerms ?? 3); $i++)
                            <option value="{{ $i }}">Term {{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Amount (RWF) *</label>
                    <input type="number" name="amount" id="contributeAmount" step="0.01" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                    <select name="payment_method" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
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
            <div class="flex justify-end gap-2 mt-5 pt-3 border-t">
                <button type="button" onclick="window.closeModal('contributeModal')" class="px-4 py-2 border rounded-lg text-sm">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm">Submit Payment</button>
            </div>
        </form>
    </div>
</div>

<script>
let allUsers = @json($users ?? []);
let currentContributeUserId = null;

// Make functions globally available
window.openSetAnnualModal = function() {
    document.getElementById('setAnnualModal').classList.remove('hidden');
    clearSelectedMember();
    document.getElementById('annualAmount').value = '';
}

window.openContributeModal = function() {
    document.getElementById('contributeModal').classList.remove('hidden');
    document.getElementById('selectedContributeUserId').value = '';
    document.getElementById('selectedContributeMemberDisplay').classList.add('hidden');
    document.getElementById('searchContributeMemberInput').value = '';
    document.getElementById('contributeAmount').value = '';
    currentContributeUserId = null;
}

window.closeModal = function(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

function searchMembers(type) {
    let searchTerm = '';
    let resultsDiv = '';
    
    if (type === 'setAnnual') {
        searchTerm = document.getElementById('searchMemberInput').value.toLowerCase();
        resultsDiv = document.getElementById('memberSearchResults');
    } else {
        return;
    }
    
    if (searchTerm.length < 1) {
        resultsDiv.classList.add('hidden');
        return;
    }
    
    const filteredUsers = allUsers.filter(user => 
        user.name.toLowerCase().includes(searchTerm) || 
        user.email.toLowerCase().includes(searchTerm)
    );
    
    if (filteredUsers.length === 0) {
        resultsDiv.innerHTML = '<div class="p-3 text-center text-gray-500">No members found</div>';
        resultsDiv.classList.remove('hidden');
        return;
    }
    
    resultsDiv.innerHTML = filteredUsers.map(user => `
        <div onclick="selectMemberForSetAnnual(${user.id}, '${escapeHtml(user.name)}')" 
             class="p-2 hover:bg-blue-50 cursor-pointer border-b">
            <div class="font-medium text-gray-800">${escapeHtml(user.name)}</div>
            <div class="text-xs text-gray-500">${escapeHtml(user.email)}</div>
        </div>
    `).join('');
    resultsDiv.classList.remove('hidden');
}

function searchContributeMembers() {
    const searchTerm = document.getElementById('searchContributeMemberInput').value.toLowerCase();
    const resultsDiv = document.getElementById('contributeMemberSearchResults');
    
    if (searchTerm.length < 1) {
        resultsDiv.classList.add('hidden');
        return;
    }
    
    const filteredUsers = allUsers.filter(user => 
        user.name.toLowerCase().includes(searchTerm) || 
        user.email.toLowerCase().includes(searchTerm)
    );
    
    if (filteredUsers.length === 0) {
        resultsDiv.innerHTML = '<div class="p-3 text-center text-gray-500">No members found</div>';
        resultsDiv.classList.remove('hidden');
        return;
    }
    
    resultsDiv.innerHTML = filteredUsers.map(user => `
        <div onclick="selectMemberForContribute(${user.id}, '${escapeHtml(user.name)}')" 
             class="p-2 hover:bg-blue-50 cursor-pointer border-b">
            <div class="font-medium text-gray-800">${escapeHtml(user.name)}</div>
            <div class="text-xs text-gray-500">${escapeHtml(user.email)}</div>
        </div>
    `).join('');
    resultsDiv.classList.remove('hidden');
}

function selectMemberForSetAnnual(userId, userName) {
    document.getElementById('selectedUserId').value = userId;
    document.getElementById('selectedMemberName').innerHTML = userName;
    document.getElementById('selectedMemberDisplay').classList.remove('hidden');
    document.getElementById('searchMemberInput').value = '';
    document.getElementById('memberSearchResults').classList.add('hidden');
}

function selectMemberForContribute(userId, userName) {
    currentContributeUserId = userId;
    document.getElementById('selectedContributeUserId').value = userId;
    document.getElementById('selectedContributeMemberName').innerHTML = userName;
    document.getElementById('selectedContributeMemberDisplay').classList.remove('hidden');
    document.getElementById('searchContributeMemberInput').value = '';
    document.getElementById('contributeMemberSearchResults').classList.add('hidden');
}

function clearSelectedMember() {
    document.getElementById('selectedUserId').value = '';
    document.getElementById('selectedMemberDisplay').classList.add('hidden');
    document.getElementById('searchMemberInput').value = '';
}

function clearSelectedContributeMember() {
    currentContributeUserId = null;
    document.getElementById('selectedContributeUserId').value = '';
    document.getElementById('selectedContributeMemberDisplay').classList.add('hidden');
    document.getElementById('searchContributeMemberInput').value = '';
}

function submitSetAnnual(event) {
    event.preventDefault();
    
    const userId = document.getElementById('selectedUserId').value;
    const annualAmount = document.getElementById('annualAmount').value;
    
    if (!userId) {
        alert('Please search and select a member');
        return;
    }
    
    if (!annualAmount) {
        alert('Please enter annual amount');
        return;
    }
    
    const formData = new FormData();
    formData.append('user_id', userId);
    formData.append('annual_amount', annualAmount);
    formData.append('notes', document.querySelector('#setAnnualForm textarea[name="notes"]').value);
    
    fetch('/finance/contributions/set-annual', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.closeModal('setAnnualModal');
            loadContributions();
            alert('Annual contribution set successfully!');
            clearSelectedMember();
            document.getElementById('annualAmount').value = '';
        } else {
            alert('Error: ' + (data.message || 'Failed to set contribution'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Network error: ' + error.message);
    });
}

function submitContribute(event) {
    event.preventDefault();
    
    const userId = document.getElementById('selectedContributeUserId').value;
    const term = document.getElementById('contributeTerm').value;
    const amount = document.getElementById('contributeAmount').value;
    
    if (!userId) {
        alert('Please search and select a member or click the Contribute icon on a user row');
        return;
    }
    
    if (!amount) {
        alert('Please enter amount');
        return;
    }
    
    const formData = new FormData();
    formData.append('user_id', userId);
    formData.append('term', term);
    formData.append('amount', amount);
    formData.append('payment_method', document.querySelector('#contributeForm select[name="payment_method"]').value);
    formData.append('notes', document.querySelector('#contributeForm textarea[name="notes"]').value);
    
    fetch('/finance/contributions/pay', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.closeModal('contributeModal');
            loadContributions();
            alert('Payment recorded successfully!');
            clearSelectedContributeMember();
            document.getElementById('contributeAmount').value = '';
        } else {
            alert('Error: ' + (data.message || 'Failed to record payment'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Network error: ' + error.message);
    });
}

function deleteContribution(userId, userName) {
    if (confirm(`Are you sure you want to delete all contributions for ${userName}? This action cannot be undone.`)) {
        fetch(`/finance/contributions/${userId}/delete`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadContributions();
                alert('Contributions deleted successfully!');
            } else {
                alert('Error: ' + (data.message || 'Failed to delete contributions'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Network error: ' + error.message);
        });
    }
}

function loadContributions() {
    const search = document.getElementById('searchContributions')?.value || '';
    fetch(`/finance/contributions/filter?search=${encodeURIComponent(search)}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateContributionsTable(data.contributions);
                updateStats(data.contributions);
            } else {
                console.error('Error loading contributions:', data.message);
            }
        })
        .catch(error => console.error('Error loading contributions:', error));
}

function updateStats(contributions) {
    let totalExpected = 0;
    let totalCollected = 0;
    
    contributions.forEach(cont => {
        totalExpected += parseFloat(cont.annual_amount || 0);
        totalCollected += parseFloat(cont.total_paid || 0);
    });
    
    const collectionRate = totalExpected > 0 ? ((totalCollected / totalExpected) * 100).toFixed(1) : 0;
    
    document.getElementById('totalExpected').textContent = 'RWF ' + totalExpected.toLocaleString();
    document.getElementById('totalCollected').textContent = 'RWF ' + totalCollected.toLocaleString();
    document.getElementById('collectionRate').textContent = collectionRate + '%';
    document.getElementById('contributionsCount').textContent = contributions.length + ' contribution records found';
}

function updateContributionsTable(contributions) {
    const tbody = document.getElementById('contributions-table-body');
    const numberOfTerms = {{ $numberOfTerms ?? 3 }};
    
    if (!contributions || contributions.length === 0) {
        tbody.innerHTML = `<tr><td colspan="${numberOfTerms + 3}" class="text-center py-8 text-gray-500">No contributions found<\/td><\/tr>`;
        return;
    }
    
    tbody.innerHTML = contributions.map(cont => {
        let termsHtml = '';
        
        for (let i = 1; i <= numberOfTerms; i++) {
            const termAmount = cont[`term${i}_paid`] || 0;
            const termTarget = cont.term_targets ? cont.term_targets[i] : 0;
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
                <\/td>
            `;
        }
        
        const annualAmount = cont.annual_amount || 0;
        const totalPaid = cont.total_paid || 0;
        const overallProgress = annualAmount > 0 ? ((totalPaid / annualAmount) * 100).toFixed(1) : 0;
        
        return `
            <tr class="border-b hover:bg-gray-50">
                <td class="px-4 py-3">
                    <div>
                        <p class="font-medium text-gray-800">${escapeHtml(cont.user_name)}</p>
                        <p class="text-xs text-gray-500">${escapeHtml(cont.email)}</p>
                    </div>
                <\/td>
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
                <\/td>
                <td class="px-4 py-3">
                    <div class="flex items-center gap-3">
                        <button onclick='openSetAnnualForUser(${cont.user_id}, "${escapeHtml(cont.user_name)}", ${annualAmount})' 
                                class="text-green-500 hover:text-green-700 transition" title="Set Annual Amount">
                            <i class="fas fa-plus-circle text-lg"></i>
                        </button>
                        <button onclick='openContributeForUser(${cont.user_id}, "${escapeHtml(cont.user_name)}")' 
                                class="text-blue-500 hover:text-blue-700 transition" title="Make Payment">
                            <i class="fas fa-hand-holding-usd text-lg"></i>
                        </button>
                        <button onclick='viewContributionDetails(${cont.user_id})' 
                                class="text-yellow-500 hover:text-yellow-700 transition" title="View Details">
                            <i class="fas fa-eye text-lg"></i>
                        </button>
                        <button onclick='deleteContribution(${cont.user_id}, "${escapeHtml(cont.user_name)}")' 
                                class="text-red-500 hover:text-red-700 transition" title="Delete All Contributions">
                            <i class="fas fa-trash-alt text-lg"></i>
                        </button>
                    </div>
                <\/td>
            <\/tr>
        `;
    }).join('');
}

function openSetAnnualForUser(userId, userName, currentAmount) {
    document.getElementById('selectedUserId').value = userId;
    document.getElementById('selectedMemberName').innerHTML = userName;
    document.getElementById('selectedMemberDisplay').classList.remove('hidden');
    document.getElementById('annualAmount').value = currentAmount;
    document.getElementById('setAnnualModal').classList.remove('hidden');
}

function openContributeForUser(userId, userName) {
    currentContributeUserId = userId;
    document.getElementById('selectedContributeUserId').value = userId;
    document.getElementById('selectedContributeMemberName').innerHTML = userName;
    document.getElementById('selectedContributeMemberDisplay').classList.remove('hidden');
    document.getElementById('searchContributeMemberInput').value = '';
    document.getElementById('contributeModal').classList.remove('hidden');
    document.getElementById('contributeAmount').value = '';
}

function viewContributionDetails(userId) {
    fetch(`/finance/contributions/${userId}/details`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('viewDetailsTitle').innerHTML = `${escapeHtml(data.user_name)} - Contribution Details`;
                
                let paymentsHtml = '';
                if (data.payments && data.payments.length > 0) {
                    paymentsHtml = `
                        <table class="min-w-full divide-y divide-gray-200 mt-4">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Term</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Method</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                ${data.payments.map(p => `
                                    <tr>
                                        <td class="px-4 py-2 text-sm">Term ${p.term}<\/td>
                                        <td class="px-4 py-2 text-sm font-semibold text-green-600">RWF ${parseFloat(p.amount).toLocaleString()}<\/td>
                                        <td class="px-4 py-2 text-sm">${p.payment_date}<\/td>
                                        <td class="px-4 py-2 text-sm capitalize">${p.payment_method || 'Cash'}<\/td>
                                    <\/tr>
                                `).join('')}
                            </tbody>
                        <\/table>
                    `;
                } else {
                    paymentsHtml = '<p class="text-gray-500 text-center py-4">No payment records found</p>';
                }
                
                document.getElementById('viewDetailsContent').innerHTML = `
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-blue-50 rounded-lg p-4 text-center">
                                <p class="text-xs text-gray-500">Annual Amount</p>
                                <p class="text-xl font-bold text-blue-600">RWF ${parseFloat(data.annual_amount).toLocaleString()}</p>
                            </div>
                            <div class="bg-green-50 rounded-lg p-4 text-center">
                                <p class="text-xs text-gray-500">Total Paid</p>
                                <p class="text-xl font-bold text-green-600">RWF ${parseFloat(data.total_paid).toLocaleString()}</p>
                            </div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex justify-between text-sm mb-1">
                                <span>Overall Progress</span>
                                <span class="font-semibold">${data.progress}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-purple-600 h-2 rounded-full" style="width: ${data.progress}%"></div>
                            </div>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-800 mb-2">Payment History</h4>
                            ${paymentsHtml}
                        </div>
                    </div>
                `;
                window.openModal('viewDetailsModal');
            }
        })
        .catch(error => console.error('Error:', error));
}

function openModal(modalId) {
    document.getElementById(modalId).classList.remove('hidden');
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Search functionality
document.getElementById('searchContributions')?.addEventListener('keyup', function() {
    loadContributions();
});

// Close search results when clicking outside
document.addEventListener('click', function(event) {
    const setAnnualSearch = document.getElementById('memberSearchResults');
    const setAnnualInput = document.getElementById('searchMemberInput');
    const contributeSearch = document.getElementById('contributeMemberSearchResults');
    const contributeInput = document.getElementById('searchContributeMemberInput');
    
    if (setAnnualSearch && !setAnnualSearch.contains(event.target) && event.target !== setAnnualInput) {
        setAnnualSearch.classList.add('hidden');
    }
    if (contributeSearch && !contributeSearch.contains(event.target) && event.target !== contributeInput) {
        contributeSearch.classList.add('hidden');
    }
});

// Load initial data
loadContributions();
</script>