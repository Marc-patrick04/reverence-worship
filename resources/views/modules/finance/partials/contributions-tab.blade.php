<div>
    <!-- Header with Year Selection -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <h3 class="text-lg font-semibold text-gray-800">Member Contributions</h3>
        <div class="flex flex-wrap gap-3">
            <!-- Year Selector -->
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
            <button onclick="window.openSetAnnualModal()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm flex items-center gap-2">
                <i class="fas fa-plus-circle"></i> Set Annual Contribution
            </button>
            <button onclick="window.openContributeModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm flex items-center gap-2">
                <i class="fas fa-hand-holding-usd"></i> Record Payment
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

<!-- Edit Contribution Modal -->
<div id="editContributionModal" class="modal hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center pb-3 border-b">
            <h3 class="text-lg font-bold text-gray-800">Edit Annual Contribution</h3>
            <button onclick="window.closeModal('editContributionModal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form id="editContributionForm" onsubmit="submitEditContribution(event)">
            @csrf
            @method('PUT')
            <input type="hidden" id="editContributionId" name="contribution_id">
            <input type="hidden" id="editContributionUserId" name="user_id">
            <div class="mt-4 space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Member</label>
                    <p id="editContributionMemberName" class="text-sm font-semibold text-gray-800 bg-gray-50 p-2 rounded-lg"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Year</label>
                    <p id="editContributionYear" class="text-sm text-gray-600 bg-gray-50 p-2 rounded-lg"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Annual Amount (RWF) *</label>
                    <input type="number" id="editAnnualAmount" name="annual_amount" step="0.01" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Reason for Change</label>
                    <textarea id="editContributionNotes" name="notes" rows="2" placeholder="Why is this amount being changed?" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
                <div class="bg-yellow-50 rounded-lg p-3 text-xs text-yellow-700">
                    <i class="fas fa-info-circle mr-1"></i> Your name (<strong>{{ auth()->user()->name }}</strong>) and email (<strong>{{ auth()->user()->email }}</strong>) will be recorded as the person making this change.
                </div>
            </div>
            <div class="flex justify-end gap-2 mt-5 pt-3 border-t">
                <button type="button" onclick="window.closeModal('editContributionModal')" class="px-4 py-2 border rounded-lg text-sm">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Payment Modal -->
<div id="editPaymentModal" class="modal hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center pb-3 border-b">
            <h3 class="text-lg font-bold text-gray-800">Edit Payment</h3>
            <button onclick="window.closeModal('editPaymentModal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form id="editPaymentForm" onsubmit="submitEditPayment(event)">
            @csrf
            @method('PUT')
            <input type="hidden" id="editPaymentId" name="payment_id">
            <input type="hidden" id="editPaymentUserId" name="user_id">
            <div class="mt-4 space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Member</label>
                    <p id="editPaymentMemberName" class="text-sm font-semibold text-gray-800 bg-gray-50 p-2 rounded-lg"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Year</label>
                    <p id="editPaymentYear" class="text-sm text-gray-600 bg-gray-50 p-2 rounded-lg"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Term *</label>
                    <select id="editPaymentTerm" name="term" required class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        @for($i = 1; $i <= ($numberOfTerms ?? 3); $i++)
                            <option value="{{ $i }}">Term {{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Amount (RWF) *</label>
                    <input type="number" id="editPaymentAmount" name="amount" step="0.01" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                    <select id="editPaymentMethod" name="payment_method" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        <option value="cash">Cash</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="mobile_money">Mobile Money</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Payment Date</label>
                    <input type="date" id="editPaymentDate" name="payment_date" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Reason for Change</label>
                    <textarea id="editPaymentNotes" name="notes" rows="2" placeholder="Why is this payment being changed?" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg"></textarea>
                </div>
                <div class="bg-yellow-50 rounded-lg p-3 text-xs text-yellow-700">
                    <i class="fas fa-info-circle mr-1"></i> Your name (<strong>{{ auth()->user()->name }}</strong>) and email (<strong>{{ auth()->user()->email }}</strong>) will be recorded as the person making this change.
                </div>
            </div>
            <div class="flex justify-end gap-2 mt-5 pt-3 border-t">
                <button type="button" onclick="window.closeModal('editPaymentModal')" class="px-4 py-2 border rounded-lg text-sm">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<!-- View Details Modal with Full History -->
<div id="viewDetailsModal" class="modal hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-6 border w-full max-w-5xl shadow-xl rounded-2xl bg-white">
        <div class="flex justify-between items-center pb-4 border-b">
            <h3 id="viewDetailsTitle" class="text-xl font-bold text-gray-800">Contribution Details</h3>
            <button onclick="window.closeModal('viewDetailsModal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div id="viewDetailsContent" class="mt-4 max-h-[500px] overflow-y-auto"></div>
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
                    <label class="block text-sm font-medium text-gray-700 mb-1">Year</label>
                    <p id="setAnnualYear" class="text-sm text-gray-600 bg-gray-50 p-2 rounded-lg"></p>
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
            <h3 class="text-lg font-bold text-gray-800">Record Payment</h3>
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
                    <label class="block text-sm font-medium text-gray-700 mb-1">Year</label>
                    <p id="contributeYear" class="text-sm text-gray-600 bg-gray-50 p-2 rounded-lg"></p>
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
let currentYear = new Date().getFullYear();
let availableYears = [];

// Populate year selector (from 5 years ago to 5 years ahead)
function populateYearSelector() {
    const currentYearNow = new Date().getFullYear();
    const startYear = currentYearNow - 5;
    const endYear = currentYearNow + 5;
    const selector = document.getElementById('yearSelector');
    
    selector.innerHTML = '';
    for (let year = endYear; year >= startYear; year--) {
        const option = document.createElement('option');
        option.value = year;
        option.textContent = year + (year === currentYearNow ? ' (Current)' : '');
        selector.appendChild(option);
    }
    
    // Load saved year from localStorage or default to current
    const savedYear = localStorage.getItem('selectedContributionYear');
    if (savedYear && savedYear >= startYear && savedYear <= endYear) {
        selector.value = savedYear;
    } else {
        selector.value = currentYearNow;
    }
    
    updateYearBadge();
}

function updateYearBadge() {
    const selectedYear = parseInt(document.getElementById('yearSelector').value);
    const currentYearNow = new Date().getFullYear();
    const yearBadge = document.getElementById('yearBadge');
    const yearStatus = document.getElementById('yearStatus');
    
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
    localStorage.setItem('selectedContributionYear', selectedYear);
    updateYearBadge();
    loadContributions();
}

// Make functions globally available
window.openSetAnnualModal = function() {
    document.getElementById('setAnnualModal').classList.remove('hidden');
    document.getElementById('setAnnualYear').innerHTML = document.getElementById('yearSelector').value;
    clearSelectedMember();
    document.getElementById('annualAmount').value = '';
}

window.openContributeModal = function() {
    document.getElementById('contributeModal').classList.remove('hidden');
    document.getElementById('contributeYear').innerHTML = document.getElementById('yearSelector').value;
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

// Edit Contribution Function
function editContribution(contributionId, userId, userName, year, annualAmount, notes) {
    document.getElementById('editContributionId').value = contributionId;
    document.getElementById('editContributionUserId').value = userId;
    document.getElementById('editContributionMemberName').innerHTML = userName;
    document.getElementById('editContributionYear').innerHTML = year;
    document.getElementById('editAnnualAmount').value = annualAmount;
    document.getElementById('editContributionNotes').value = notes || '';
    document.getElementById('editContributionModal').classList.remove('hidden');
}

function submitEditContribution(event) {
    event.preventDefault();
    
    const contributionId = document.getElementById('editContributionId').value;
    const userId = document.getElementById('editContributionUserId').value;
    const annualAmount = document.getElementById('editAnnualAmount').value;
    const notes = document.getElementById('editContributionNotes').value;
    const year = document.getElementById('yearSelector').value;
    
    const formData = new FormData();
    formData.append('contribution_id', contributionId);
    formData.append('user_id', userId);
    formData.append('annual_amount', annualAmount);
    formData.append('notes', notes);
    formData.append('year', year);
    formData.append('_method', 'PUT');
    
    fetch('/finance/contributions/update', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.closeModal('editContributionModal');
            loadContributions();
            alert('Contribution updated successfully!');
        } else {
            alert('Error: ' + (data.message || 'Failed to update contribution'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Network error: ' + error.message);
    });
}

// Edit Payment Function
function editPayment(paymentId, userId, userName, year, term, amount, paymentMethod, paymentDate, notes) {
    document.getElementById('editPaymentId').value = paymentId;
    document.getElementById('editPaymentUserId').value = userId;
    document.getElementById('editPaymentMemberName').innerHTML = userName;
    document.getElementById('editPaymentYear').innerHTML = year;
    document.getElementById('editPaymentTerm').value = term;
    document.getElementById('editPaymentAmount').value = amount;
    document.getElementById('editPaymentMethod').value = paymentMethod || 'cash';
    document.getElementById('editPaymentDate').value = paymentDate || '';
    document.getElementById('editPaymentNotes').value = notes || '';
    document.getElementById('editPaymentModal').classList.remove('hidden');
}

function submitEditPayment(event) {
    event.preventDefault();
    
    const paymentId = document.getElementById('editPaymentId').value;
    const userId = document.getElementById('editPaymentUserId').value;
    const term = document.getElementById('editPaymentTerm').value;
    const amount = document.getElementById('editPaymentAmount').value;
    const paymentMethod = document.getElementById('editPaymentMethod').value;
    const paymentDate = document.getElementById('editPaymentDate').value;
    const notes = document.getElementById('editPaymentNotes').value;
    const year = document.getElementById('yearSelector').value;
    
    const formData = new FormData();
    formData.append('payment_id', paymentId);
    formData.append('user_id', userId);
    formData.append('term', term);
    formData.append('amount', amount);
    formData.append('payment_method', paymentMethod);
    formData.append('payment_date', paymentDate);
    formData.append('notes', notes);
    formData.append('year', year);
    formData.append('_method', 'PUT');
    
    fetch('/finance/payments/update', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.closeModal('editPaymentModal');
            loadContributions();
            alert('Payment updated successfully!');
        } else {
            alert('Error: ' + (data.message || 'Failed to update payment'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Network error: ' + error.message);
    });
}

function submitSetAnnual(event) {
    event.preventDefault();
    
    const userId = document.getElementById('selectedUserId').value;
    const annualAmount = document.getElementById('annualAmount').value;
    const year = document.getElementById('yearSelector').value;
    
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
    formData.append('year', year);
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
    const year = document.getElementById('yearSelector').value;
    
    if (!userId) {
        alert('Please search and select a member');
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
    formData.append('year', year);
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

function loadContributions() {
    const search = document.getElementById('searchContributions')?.value || '';
    const year = document.getElementById('yearSelector')?.value || new Date().getFullYear();
    
    fetch(`/finance/contributions/filter?search=${encodeURIComponent(search)}&year=${year}`)
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
    const currentYear = document.getElementById('yearSelector')?.value || new Date().getFullYear();
    
    if (!contributions || contributions.length === 0) {
        tbody.innerHTML = `<tr><td colspan="${numberOfTerms + 3}" class="text-center py-8 text-gray-500">No contributions found for ${currentYear}</td></tr>`;
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
                        <button onclick='editContribution(${cont.id || 'null'}, ${cont.user_id}, "${escapeHtml(cont.user_name)}", ${currentYear}, ${annualAmount}, "${escapeHtml(cont.notes || '')}")' 
                                class="text-blue-500 hover:text-blue-700 transition" title="Edit Annual Amount">
                            <i class="fas fa-edit text-lg"></i>
                        </button>
                        <button onclick='openContributeForUser(${cont.user_id}, "${escapeHtml(cont.user_name)}")' 
                                class="text-green-500 hover:text-green-700 transition" title="Record Payment">
                            <i class="fas fa-hand-holding-usd text-lg"></i>
                        </button>
                        <button onclick='viewContributionDetails(${cont.user_id})' 
                                class="text-yellow-500 hover:text-yellow-700 transition" title="View Details & History">
                            <i class="fas fa-eye text-lg"></i>
                        </button>
                    </div>
                <\/td>
            <\/tr>
        `;
    }).join('');
}

function openContributeForUser(userId, userName) {
    currentContributeUserId = userId;
    document.getElementById('selectedContributeUserId').value = userId;
    document.getElementById('selectedContributeMemberName').innerHTML = userName;
    document.getElementById('selectedContributeMemberDisplay').classList.remove('hidden');
    document.getElementById('searchContributeMemberInput').value = '';
    document.getElementById('contributeYear').innerHTML = document.getElementById('yearSelector').value;
    document.getElementById('contributeModal').classList.remove('hidden');
    document.getElementById('contributeAmount').value = '';
}

function viewContributionDetails(userId) {
    const year = document.getElementById('yearSelector').value;
    
    fetch(`/finance/contributions/${userId}/details?year=${year}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('viewDetailsTitle').innerHTML = `${escapeHtml(data.user_name)} - Contribution Details (${year})`;
                
                // Contribution Edit History
                let contributionHistoryHtml = '';
                if (data.contribution_history && data.contribution_history.length > 0) {
                    contributionHistoryHtml = `
                        <div class="mb-6">
                            <h4 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                                <i class="fas fa-chart-line text-blue-500"></i> Annual Amount Edit History
                            </h4>
                            <div class="space-y-2">
                                ${data.contribution_history.map(history => `
                                    <div class="border-l-4 border-blue-400 bg-blue-50 rounded-r-lg p-3">
                                        <div class="flex justify-between items-start mb-2">
                                            <div>
                                                <span class="text-xs font-semibold text-blue-700">${escapeHtml(history.edited_by_name || 'Unknown')}</span>
                                                <span class="text-xs text-gray-500">(${escapeHtml(history.edited_by_email || 'No email')})</span>
                                            </div>
                                            <span class="text-xs text-gray-500">${new Date(history.created_at).toLocaleString()}</span>
                                        </div>
                                        <div class="text-sm">
                                            <div class="grid grid-cols-2 gap-2 mb-2">
                                                <div><span class="text-gray-500">Amount:</span> <span class="line-through text-red-500">RWF ${parseFloat(history.old_amount || 0).toLocaleString()}</span> → <span class="text-green-600 font-medium">RWF ${parseFloat(history.new_amount || 0).toLocaleString()}</span></div>
                                            </div>
                                            ${history.notes ? `<div class="text-xs text-gray-500 mt-1"><span class="text-gray-400">Reason:</span> ${escapeHtml(history.notes)}</div>` : ''}
                                        </div>
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                    `;
                }
                
                // Payment History
                let paymentsHtml = '';
                if (data.payments && data.payments.length > 0) {
                    paymentsHtml = `
                        <div>
                            <h4 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                                <i class="fas fa-money-bill-wave text-green-500"></i> Payment Records
                            </h4>
                            <table class="min-w-full divide-y divide-gray-200 border rounded-lg">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Term</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Method</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    ${data.payments.map(p => `
                                        <tr>
                                            <td class="px-4 py-2 text-sm">Term ${p.term}</td>
                                            <td class="px-4 py-2 text-sm font-semibold text-green-600">RWF ${parseFloat(p.amount).toLocaleString()}</td>
                                            <td class="px-4 py-2 text-sm">${p.payment_date}</td>
                                            <td class="px-4 py-2 text-sm capitalize">${p.payment_method || 'Cash'}</td>
                                            <td class="px-4 py-2 text-sm">
                                                <div class="flex gap-2">
                                                    <button onclick='editPayment(${p.id}, ${data.user_id}, "${escapeHtml(data.user_name)}", ${year}, ${p.term}, ${p.amount}, "${p.payment_method || 'cash'}", "${p.payment_date || ''}", "${escapeHtml(p.notes || '')}")' 
                                                            class="text-blue-500 hover:text-blue-700 transition" title="Edit Payment">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button onclick='viewPaymentHistory(${p.id}, ${data.user_id}, "${escapeHtml(data.user_name)}", ${year})' 
                                                            class="text-gray-500 hover:text-gray-700 transition" title="View Payment History">
                                                        <i class="fas fa-history"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
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
                                <button onclick='editContribution(${data.contribution_id || 'null'}, ${data.user_id}, "${escapeHtml(data.user_name)}", ${year}, ${data.annual_amount}, "")' 
                                        class="mt-2 text-xs text-blue-500 hover:text-blue-700">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
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
                        ${contributionHistoryHtml}
                        ${paymentsHtml}
                    </div>
                `;
                window.openModal('viewDetailsModal');
            }
        })
        .catch(error => console.error('Error:', error));
}

function viewPaymentHistory(paymentId, userId, userName, year) {
    fetch(`/finance/payments/${paymentId}/history`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const historyModal = document.createElement('div');
                historyModal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center';
                historyModal.innerHTML = `
                    <div class="relative mx-auto p-6 border w-full max-w-3xl shadow-xl rounded-2xl bg-white">
                        <div class="flex justify-between items-center pb-4 border-b">
                            <h3 class="text-xl font-bold text-gray-800">Payment Edit History</h3>
                            <button onclick="this.closest('.fixed').remove()" class="text-gray-400 hover:text-gray-600">
                                <i class="fas fa-times text-xl"></i>
                            </button>
                        </div>
                        <div class="mt-4 max-h-96 overflow-y-auto">
                            <div class="bg-gray-50 rounded-lg p-4 mb-4">
                                <h4 class="font-semibold text-gray-800 mb-2">Current Payment Details</h4>
                                <div class="grid grid-cols-2 gap-3 text-sm">
                                    <div><span class="text-gray-500">Term:</span> <span class="font-medium">${data.payment.term}</span></div>
                                    <div><span class="text-gray-500">Amount:</span> <span class="font-medium text-green-600">RWF ${parseFloat(data.payment.amount).toLocaleString()}</span></div>
                                    <div><span class="text-gray-500">Method:</span> <span class="font-medium capitalize">${data.payment.payment_method || 'Cash'}</span></div>
                                    <div><span class="text-gray-500">Date:</span> <span class="font-medium">${data.payment.payment_date || '-'}</span></div>
                                </div>
                            </div>
                            ${data.history && data.history.length > 0 ? `
                                <div>
                                    <h4 class="font-semibold text-gray-800 mb-3">Edit History</h4>
                                    <div class="space-y-3">
                                        ${data.history.map(history => `
                                            <div class="border-l-4 border-blue-400 bg-blue-50 rounded-r-lg p-3">
                                                <div class="flex justify-between items-start mb-2">
                                                    <div>
                                                        <span class="text-xs font-semibold text-blue-700">${escapeHtml(history.edited_by_name || 'Unknown')}</span>
                                                        <span class="text-xs text-gray-500">(${escapeHtml(history.edited_by_email || 'No email')})</span>
                                                    </div>
                                                    <span class="text-xs text-gray-500">${new Date(history.created_at).toLocaleString()}</span>
                                                </div>
                                                <div class="text-sm">
                                                    <div class="grid grid-cols-2 gap-2 mb-2">
                                                        <div><span class="text-gray-500">Term:</span> <span class="line-through text-red-500">${history.old_term || '-'}</span> → <span class="text-green-600 font-medium">${history.new_term || '-'}</span></div>
                                                        <div><span class="text-gray-500">Amount:</span> <span class="line-through text-red-500">RWF ${parseFloat(history.old_amount || 0).toLocaleString()}</span> → <span class="text-green-600 font-medium">RWF ${parseFloat(history.new_amount || 0).toLocaleString()}</span></div>
                                                        <div><span class="text-gray-500">Method:</span> <span class="line-through text-red-500 capitalize">${history.old_payment_method || '-'}</span> → <span class="text-green-600 font-medium capitalize">${history.new_payment_method || '-'}</span></div>
                                                        <div><span class="text-gray-500">Date:</span> <span class="line-through text-red-500">${history.old_payment_date || '-'}</span> → <span class="text-green-600 font-medium">${history.new_payment_date || '-'}</span></div>
                                                    </div>
                                                    ${history.notes ? `<div class="text-xs text-gray-500 mt-1"><span class="text-gray-400">Reason:</span> ${escapeHtml(history.notes)}</div>` : ''}
                                                </div>
                                            </div>
                                        `).join('')}
                                    </div>
                                </div>
                            ` : '<div class="text-center py-8 text-gray-500">No edit history found for this payment</div>'}
                        </div>
                        <div class="flex justify-end mt-6 pt-4 border-t">
                            <button onclick="this.closest('.fixed').remove()" class="px-5 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700 transition">
                                Close
                            </button>
                        </div>
                    </div>
                `;
                document.body.appendChild(historyModal);
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

// Initialize
populateYearSelector();
loadContributions();
</script>