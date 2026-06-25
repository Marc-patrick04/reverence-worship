<div>
    <!-- Header with Year Selection -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <h3 class="text-lg font-semibold text-gray-800">Member Contributions</h3>
        <div class="flex flex-wrap gap-3">
            <!-- Year Selector -->
            <div class="flex items-center gap-2">
                <label class="text-sm text-gray-600">Year:</label>
                <div class="relative">
                    <div onclick="toggleContributionYearPicker()" 
                        class="flex items-center justify-between border border-gray-300 rounded-lg px-3 py-2 bg-white cursor-pointer hover:border-blue-400 transition-all min-w-[120px]">
                        <span id="contributionYearDisplay" class="text-sm font-semibold text-gray-800">{{ date('Y') }}</span>
                        <svg class="w-4 h-4 text-gray-400 transition-transform duration-200 ml-2" id="contributionYearArrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                    <input type="hidden" id="contributionSelectedYear" value="{{ date('Y') }}">
                    
                    <div id="contributionYearPickerDropdown" class="hidden absolute top-full left-0 right-0 mt-1 bg-white border border-gray-200 rounded-lg shadow-xl z-50 p-3 min-w-[200px]">
                        <div class="flex items-center justify-between mb-2">
                            <button type="button" onclick="changeContributionYearPage(-1)" 
                                class="p-1 hover:bg-gray-100 rounded transition text-gray-500 hover:text-gray-700">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                            </button>
                            <span id="contributionYearPageTitle" class="text-xs font-medium text-gray-600">2018 - 2024</span>
                            <button type="button" onclick="changeContributionYearPage(1)" 
                                class="p-1 hover:bg-gray-100 rounded transition text-gray-500 hover:text-gray-700">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </button>
                        </div>
                        <div class="grid grid-cols-3 gap-1" id="contributionYearGrid"></div>
                    </div>
                </div>
                <span id="yearBadge" class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-600 hidden">
                    <i class="fas fa-history mr-1"></i> <span id="yearStatus">Current</span>
                </span>
            </div>
            <button onclick="openSetAnnualModal()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm flex items-center gap-2">
                <i class="fas fa-plus-circle"></i> Set Annual Contribution
            </button>
            <button onclick="openContributeModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm flex items-center gap-2">
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
    
    <!-- Filters -->
    <div class="mb-4 flex flex-col sm:flex-row gap-3">
        <div class="relative flex-1 min-w-[200px]">
            <i class="fas fa-users absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <select id="filterFamily" onchange="loadContributions()" 
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 appearance-none bg-white">
                <option value="all">All Families</option>
                @foreach($families ?? [] as $family)
                    <option value="{{ $family->id }}">{{ $family->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="relative flex-[2]">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input type="text" id="searchContributions" placeholder="Search by member name or email..." 
                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
        </div>
    </div>
    <p id="contributionsCount" class="text-xs text-gray-500 mt-1 mb-3">0 contribution records found</p>
    
    <!-- Contributions Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">MEMBER</th>
                    <th id="termsHeaderContainer"></th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">TOTAL PROGRESS</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">ACTIONS</th>
                </tr>
            </thead>
            <tbody id="contributions-table-body">
                <tr>
                    <td colspan="10" class="text-center py-8 text-gray-500">Loading contributions...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- ==================== MODALS - Tailwind Only ==================== -->

<!-- Edit Annual Contribution Modal -->
<div id="editContributionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 backdrop-blur-sm overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-6 border w-full max-w-md shadow-2xl rounded-2xl bg-white">
        <div class="flex justify-between items-center pb-4 border-b">
            <h3 class="text-xl font-bold text-gray-800">Edit Annual Contribution</h3>
            <button onclick="closeFinanceModal('editContributionModal')" class="text-gray-400 hover:text-gray-600 transition">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form id="editContributionForm" onsubmit="submitEditContribution(event)" class="mt-4">
            @csrf
            <input type="hidden" id="editContributionId" name="contribution_id">
            <input type="hidden" id="editContributionUserId" name="user_id">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Member</label>
                    <p id="editContributionMemberName" class="w-full px-3 py-2 bg-gray-50 rounded-lg text-sm text-gray-800">angelique</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Year</label>
                    <p id="editContributionYear" class="w-full px-3 py-2 bg-gray-50 rounded-lg text-sm text-gray-800">2026</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Annual Amount (RWF) *</label>
                    <input type="number" id="editAnnualAmount" name="annual_amount" step="0.01" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Reason for Change</label>
                    <textarea id="editContributionNotes" name="notes" rows="2" placeholder="Why is this amount being changed?" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 text-xs text-yellow-700">
                    <i class="fas fa-info-circle mr-1"></i> Your name (<strong>{{ auth()->user()->name }}</strong>) will be recorded as the person making this change.
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6 pt-4 border-t">
                <button type="button" onclick="closeFinanceModal('editContributionModal')" class="px-5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm transition">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm transition flex items-center gap-2">
                    <i class="fas fa-save"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Payment Modal -->
<div id="editPaymentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 backdrop-blur-sm overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-6 border w-full max-w-md shadow-2xl rounded-2xl bg-white">
        <div class="flex justify-between items-center pb-4 border-b">
            <h3 class="text-xl font-bold text-gray-800">Edit Payment</h3>
            <button onclick="closeFinanceModal('editPaymentModal')" class="text-gray-400 hover:text-gray-600 transition">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form id="editPaymentForm" onsubmit="submitEditPayment(event)" class="mt-4">
            @csrf
            <input type="hidden" id="editPaymentId" name="payment_id">
            <input type="hidden" id="editPaymentUserId" name="user_id">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Member</label>
                    <p id="editPaymentMemberName" class="w-full px-3 py-2 bg-gray-50 rounded-lg text-sm text-gray-800"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Year</label>
                    <p id="editPaymentYear" class="w-full px-3 py-2 bg-gray-50 rounded-lg text-sm text-gray-800"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Term *</label>
                    <select id="editPaymentTerm" name="term" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Amount (RWF) *</label>
                    <input type="number" id="editPaymentAmount" name="amount" step="0.01" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                    <select id="editPaymentMethod" name="payment_method" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="cash">Cash</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="mobile_money">Mobile Money</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Payment Date</label>
                    <input type="date" id="editPaymentDate" name="payment_date" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Reason for Change</label>
                    <textarea id="editPaymentNotes" name="notes" rows="2" placeholder="Why is this payment being changed?" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 text-xs text-yellow-700">
                    <i class="fas fa-info-circle mr-1"></i> Your name (<strong>{{ auth()->user()->name }}</strong>) will be recorded as the person making this change.
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6 pt-4 border-t">
                <button type="button" onclick="closeFinanceModal('editPaymentModal')" class="px-5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm transition">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm transition flex items-center gap-2">
                    <i class="fas fa-save"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<!-- View Details Modal -->
<div id="viewDetailsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 backdrop-blur-sm overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-6 border w-full max-w-4xl shadow-2xl rounded-2xl bg-white">
        <div class="flex justify-between items-center pb-4 border-b">
            <h3 id="viewDetailsTitle" class="text-xl font-bold text-gray-800">Contribution Details</h3>
            <button onclick="closeFinanceModal('viewDetailsModal')" class="text-gray-400 hover:text-gray-600 transition">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div id="viewDetailsContent" class="mt-4 max-h-[500px] overflow-y-auto"></div>
        <div class="flex justify-end mt-6 pt-4 border-t">
            <button onclick="closeFinanceModal('viewDetailsModal')" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm transition">
                Close
            </button>
        </div>
    </div>
</div>

<!-- Set Annual Contribution Modal -->
<div id="setAnnualModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 backdrop-blur-sm overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-6 border w-full max-w-md shadow-2xl rounded-2xl bg-white">
        <div class="flex justify-between items-center pb-4 border-b">
            <h3 class="text-xl font-bold text-gray-800">Set Annual Contribution</h3>
            <button onclick="closeFinanceModal('setAnnualModal')" class="text-gray-400 hover:text-gray-600 transition">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form id="setAnnualForm" onsubmit="submitSetAnnual(event)" class="mt-4">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Search Member *</label>
                    <input type="text" id="searchMemberInput" placeholder="Type member name or email..." 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" onkeyup="searchMembers()">
                    <input type="hidden" id="selectedUserId" name="user_id">
                    <div id="memberSearchResults" class="mt-1 border border-gray-200 rounded-lg max-h-48 overflow-y-auto hidden"></div>
                </div>
                <div id="selectedMemberDisplay" class="bg-green-50 border border-green-200 rounded-lg p-3 hidden">
                    <div class="flex justify-between items-center">
                        <span id="selectedMemberName" class="text-sm font-medium text-gray-800"></span>
                        <button type="button" onclick="clearSelectedMember()" class="text-red-500 hover:text-red-700 transition">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Year</label>
                    <p id="setAnnualYear" class="w-full px-3 py-2 bg-gray-50 rounded-lg text-sm text-gray-800"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Annual Amount (RWF) *</label>
                    <input type="number" name="annual_amount" id="annualAmount" step="0.01" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                    <textarea name="notes" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6 pt-4 border-t">
                <button type="button" onclick="closeFinanceModal('setAnnualModal')" class="px-5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm transition">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm transition flex items-center gap-2">
                    <i class="fas fa-check"></i> Set Contribution
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Record Payment Modal -->
<div id="contributeModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 backdrop-blur-sm overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-6 border w-full max-w-md shadow-2xl rounded-2xl bg-white">
        <div class="flex justify-between items-center pb-4 border-b">
            <h3 class="text-xl font-bold text-gray-800">Record Payment</h3>
            <button onclick="closeFinanceModal('contributeModal')" class="text-gray-400 hover:text-gray-600 transition">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form id="contributeForm" onsubmit="submitContribute(event)" class="mt-4">
            @csrf
            <input type="hidden" id="selectedContributeUserId" name="user_id">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Select Member *</label>
                    <div class="relative">
                        <input type="text" id="searchContributeMemberInput" placeholder="Search members..." 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" onkeyup="searchContributeMembers()">
                        <div id="contributeMemberList" class="absolute top-full left-0 right-0 mt-1 bg-white border border-gray-200 rounded-lg max-h-48 overflow-y-auto shadow-lg hidden"></div>
                    </div>
                </div>
                <div id="selectedContributeMemberDisplay" class="bg-green-50 border border-green-200 rounded-lg p-3 hidden">
                    <div class="flex justify-between items-center">
                        <span id="selectedContributeMemberName" class="text-sm font-medium text-gray-800"></span>
                        <button type="button" onclick="clearSelectedContributeMember()" class="text-red-500 hover:text-red-700 transition">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Year</label>
                    <p id="contributeYear" class="w-full px-3 py-2 bg-gray-50 rounded-lg text-sm text-gray-800"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Term *</label>
                    <select name="term" id="contributeTerm" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Amount (RWF) *</label>
                    <input type="number" name="amount" id="contributeAmount" step="0.01" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                    <select name="payment_method" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="cash">Cash</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="mobile_money">Mobile Money</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                    <textarea name="notes" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6 pt-4 border-t">
                <button type="button" onclick="closeFinanceModal('contributeModal')" class="px-5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm transition">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm transition flex items-center gap-2">
                    <i class="fas fa-check"></i> Submit Payment
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// ============================================
// All JavaScript functions remain the same
// ============================================

let allUsers = @json($users ?? []);
let allFamilies = @json($families ?? []);
let currentContributionYear = new Date().getFullYear();
let contributionYearPageOffset = 0;
let currentNumberOfTerms = 4;
let contributionYearSettings = null;
let currentContributeUserId = null;

// ============================================
// YEAR PICKER FUNCTIONS
// ============================================

function toggleContributionYearPicker() {
    const dropdown = document.getElementById('contributionYearPickerDropdown');
    const arrow = document.getElementById('contributionYearArrow');
    
    if (dropdown.classList.contains('hidden')) {
        dropdown.classList.remove('hidden');
        arrow.classList.add('rotate-180');
        renderContributionYearGrid();
    } else {
        dropdown.classList.add('hidden');
        arrow.classList.remove('rotate-180');
    }
}

function closeContributionYearPicker() {
    const dropdown = document.getElementById('contributionYearPickerDropdown');
    const arrow = document.getElementById('contributionYearArrow');
    
    if (dropdown && !dropdown.classList.contains('hidden')) {
        dropdown.classList.add('hidden');
        arrow.classList.remove('rotate-180');
    }
}

function changeContributionYearPage(direction) {
    contributionYearPageOffset += direction;
    renderContributionYearGrid();
}

function renderContributionYearGrid() {
    const currentYear = new Date().getFullYear();
    const startYear = currentYear + (contributionYearPageOffset * 9) - 4;
    
    const grid = document.getElementById('contributionYearGrid');
    const title = document.getElementById('contributionYearPageTitle');
    
    if (!grid) return;
    
    const endYear = startYear + 8;
    title.textContent = `${startYear} - ${endYear}`;
    
    grid.innerHTML = '';
    
    for (let i = 0; i < 9; i++) {
        const year = startYear + i;
        const isSelected = year == currentContributionYear;
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
                selectContributionYear(year);
            };
        }
        
        grid.appendChild(btn);
    }
}

function selectContributionYear(year) {
    currentContributionYear = year;
    document.getElementById('contributionSelectedYear').value = year;
    document.getElementById('contributionYearDisplay').textContent = year;
    
    closeContributionYearPicker();
    renderContributionYearGrid();
    
    loadTermSettingsForYear(year);
    updateYearBadge();
    loadContributions();
}

function updateYearBadge() {
    const currentYearNow = new Date().getFullYear();
    const yearBadge = document.getElementById('yearBadge');
    const yearStatus = document.getElementById('yearStatus');
    
    if (!yearBadge) return;
    
    if (currentContributionYear === currentYearNow) {
        yearBadge.classList.add('hidden');
    } else if (currentContributionYear < currentYearNow) {
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
// TERM SETTINGS FUNCTIONS
// ============================================

function loadTermSettingsForYear(year) {
    fetch(`/finance/settings/get?year=${year}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.settings) {
            contributionYearSettings = data.settings;
            currentNumberOfTerms = data.settings.number_of_terms || 4;
        } else {
            contributionYearSettings = null;
            currentNumberOfTerms = 4;
        }
        
        updateTermsHeader();
        updateTermSelectors();
    })
    .catch(error => {
        console.error('Error loading term settings:', error);
        currentNumberOfTerms = 4;
        updateTermsHeader();
        updateTermSelectors();
    });
}

function updateTermsHeader() {
    const container = document.getElementById('termsHeaderContainer');
    if (!container) return;
    
    let html = '';
    for (let i = 1; i <= currentNumberOfTerms; i++) {
        html += `<th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap min-w-[140px]">TERM ${i}</th>`;
    }
    container.innerHTML = html;
}

function updateTermSelectors() {
    const editTermSelect = document.getElementById('editPaymentTerm');
    if (editTermSelect) {
        editTermSelect.innerHTML = '';
        for (let i = 1; i <= currentNumberOfTerms; i++) {
            const option = document.createElement('option');
            option.value = i;
            option.textContent = `Term ${i}`;
            editTermSelect.appendChild(option);
        }
    }
    
    const contributeTermSelect = document.getElementById('contributeTerm');
    if (contributeTermSelect) {
        contributeTermSelect.innerHTML = '';
        for (let i = 1; i <= currentNumberOfTerms; i++) {
            const option = document.createElement('option');
            option.value = i;
            option.textContent = `Term ${i}`;
            contributeTermSelect.appendChild(option);
        }
    }
}

// ============================================
// MODAL FUNCTIONS
// ============================================

function openFinanceModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
}

function closeFinanceModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }
}

function openSetAnnualModal() {
    document.getElementById('setAnnualModal').classList.remove('hidden');
    document.getElementById('setAnnualYear').innerHTML = currentContributionYear;
    clearSelectedMember();
    document.getElementById('annualAmount').value = '';
    document.body.style.overflow = 'hidden';
}

function openContributeModal() {
    document.getElementById('contributeModal').classList.remove('hidden');
    document.getElementById('contributeYear').innerHTML = currentContributionYear;
    document.getElementById('selectedContributeUserId').value = '';
    document.getElementById('selectedContributeMemberDisplay').classList.add('hidden');
    document.getElementById('searchContributeMemberInput').value = '';
    document.getElementById('contributeAmount').value = '';
    document.getElementById('contributeMemberList').classList.add('hidden');
    currentContributeUserId = null;
    document.body.style.overflow = 'hidden';
    
    populateMemberList();
}

function populateMemberList() {
    const list = document.getElementById('contributeMemberList');
    if (!list) return;
    
    list.innerHTML = allUsers.map(user => `
        <div onclick="selectContributeMember(${user.id}, '${escapeHtml(user.name)}')" 
             class="px-3 py-2 hover:bg-blue-50 cursor-pointer border-b border-gray-100 last:border-0">
            <div class="font-medium text-gray-800">${escapeHtml(user.name)}</div>
            <div class="text-xs text-gray-500">${escapeHtml(user.email)}</div>
        </div>
    `).join('');
    list.classList.remove('hidden');
}

// ============================================
// SEARCH FUNCTIONS
// ============================================

function searchMembers() {
    const searchTerm = document.getElementById('searchMemberInput').value.toLowerCase();
    const resultsDiv = document.getElementById('memberSearchResults');
    
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
             class="px-3 py-2 hover:bg-blue-50 cursor-pointer border-b border-gray-100 last:border-0">
            <div class="font-medium text-gray-800">${escapeHtml(user.name)}</div>
            <div class="text-xs text-gray-500">${escapeHtml(user.email)}</div>
        </div>
    `).join('');
    resultsDiv.classList.remove('hidden');
}

function searchContributeMembers() {
    const searchTerm = document.getElementById('searchContributeMemberInput').value.toLowerCase();
    const list = document.getElementById('contributeMemberList');
    
    if (searchTerm.length < 1) {
        populateMemberList();
        return;
    }
    
    const filteredUsers = allUsers.filter(user => 
        user.name.toLowerCase().includes(searchTerm) || 
        user.email.toLowerCase().includes(searchTerm)
    );
    
    if (filteredUsers.length === 0) {
        list.innerHTML = '<div class="p-3 text-center text-gray-500">No members found</div>';
        list.classList.remove('hidden');
        return;
    }
    
    list.innerHTML = filteredUsers.map(user => `
        <div onclick="selectContributeMember(${user.id}, '${escapeHtml(user.name)}')" 
             class="px-3 py-2 hover:bg-blue-50 cursor-pointer border-b border-gray-100 last:border-0">
            <div class="font-medium text-gray-800">${escapeHtml(user.name)}</div>
            <div class="text-xs text-gray-500">${escapeHtml(user.email)}</div>
        </div>
    `).join('');
    list.classList.remove('hidden');
}

function selectContributeMember(userId, userName) {
    currentContributeUserId = userId;
    document.getElementById('selectedContributeUserId').value = userId;
    document.getElementById('selectedContributeMemberName').innerHTML = userName;
    document.getElementById('selectedContributeMemberDisplay').classList.remove('hidden');
    document.getElementById('searchContributeMemberInput').value = '';
    document.getElementById('contributeMemberList').classList.add('hidden');
}

function selectMemberForSetAnnual(userId, userName) {
    document.getElementById('selectedUserId').value = userId;
    document.getElementById('selectedMemberName').innerHTML = userName;
    document.getElementById('selectedMemberDisplay').classList.remove('hidden');
    document.getElementById('searchMemberInput').value = '';
    document.getElementById('memberSearchResults').classList.add('hidden');
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
    populateMemberList();
}

// ============================================
// EDIT FUNCTIONS
// ============================================

function editContribution(contributionId, userId, userName, year, annualAmount, notes) {
    document.getElementById('editContributionId').value = contributionId || '';
    document.getElementById('editContributionUserId').value = userId;
    document.getElementById('editContributionMemberName').innerHTML = userName;
    document.getElementById('editContributionYear').innerHTML = year;
    document.getElementById('editAnnualAmount').value = annualAmount || 0;
    document.getElementById('editContributionNotes').value = notes || '';
    openFinanceModal('editContributionModal');
}

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
    openFinanceModal('editPaymentModal');
}

// ============================================
// FORM SUBMISSIONS
// ============================================

function submitEditContribution(event) {
    event.preventDefault();
    
    const contributionId = document.getElementById('editContributionId').value;
    const userId = document.getElementById('editContributionUserId').value;
    const annualAmount = document.getElementById('editAnnualAmount').value;
    const notes = document.getElementById('editContributionNotes').value;
    const year = currentContributionYear;
    
    const formData = new FormData();
    formData.append('contribution_id', contributionId);
    formData.append('user_id', userId);
    formData.append('annual_amount', annualAmount);
    formData.append('notes', notes);
    formData.append('year', year);
    
    fetch('/finance/contributions/update', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeFinanceModal('editContributionModal');
            loadContributions();
            showNotification('Contribution updated successfully!', 'success');
        } else {
            showNotification('Error: ' + (data.message || 'Failed to update contribution'), 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Network error: ' + error.message, 'error');
    });
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
    const year = currentContributionYear;
    
    const formData = new FormData();
    formData.append('payment_id', paymentId);
    formData.append('user_id', userId);
    formData.append('term', term);
    formData.append('amount', amount);
    formData.append('payment_method', paymentMethod);
    formData.append('payment_date', paymentDate);
    formData.append('notes', notes);
    formData.append('year', year);
    
    fetch('/finance/payments/update', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeFinanceModal('editPaymentModal');
            loadContributions();
            showNotification('Payment updated successfully!', 'success');
        } else {
            showNotification('Error: ' + (data.message || 'Failed to update payment'), 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Network error: ' + error.message, 'error');
    });
}

function submitSetAnnual(event) {
    event.preventDefault();
    
    const userId = document.getElementById('selectedUserId').value;
    const annualAmount = document.getElementById('annualAmount').value;
    const year = currentContributionYear;
    
    if (!userId) {
        showNotification('Please search and select a member', 'error');
        return;
    }
    
    if (!annualAmount) {
        showNotification('Please enter annual amount', 'error');
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
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeFinanceModal('setAnnualModal');
            loadContributions();
            showNotification('Annual contribution set successfully!', 'success');
            clearSelectedMember();
            document.getElementById('annualAmount').value = '';
        } else {
            showNotification('Error: ' + (data.message || 'Failed to set contribution'), 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Network error: ' + error.message, 'error');
    });
}

function submitContribute(event) {
    event.preventDefault();
    
    const userId = document.getElementById('selectedContributeUserId').value;
    const term = document.getElementById('contributeTerm').value;
    const amount = document.getElementById('contributeAmount').value;
    const year = currentContributionYear;
    
    if (!userId) {
        showNotification('Please select a member', 'error');
        return;
    }
    
    if (!amount) {
        showNotification('Please enter amount', 'error');
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
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeFinanceModal('contributeModal');
            loadContributions();
            showNotification('Payment recorded successfully!', 'success');
            clearSelectedContributeMember();
            document.getElementById('contributeAmount').value = '';
        } else {
            showNotification('Error: ' + (data.message || 'Failed to record payment'), 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Network error: ' + error.message, 'error');
    });
}

// ============================================
// CONTRIBUTION TABLE FUNCTIONS
// ============================================

function loadContributions() {
    const search = document.getElementById('searchContributions')?.value || '';
    const year = currentContributionYear;
    const familyId = document.getElementById('filterFamily')?.value || 'all';
    
    fetch(`/finance/contributions/filter?search=${encodeURIComponent(search)}&year=${year}&family_id=${encodeURIComponent(familyId)}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            updateContributionsTable(data.contributions);
            updateStats(data.contributions);
        } else {
            console.error('Error loading contributions:', data.message);
            showNotification('Error loading contributions: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error loading contributions:', error);
        const tbody = document.getElementById('contributions-table-body');
        if (tbody) {
            tbody.innerHTML = `<tr><td colspan="${currentNumberOfTerms + 3}" class="text-center py-8 text-red-500">Error loading contributions. Please try again.</td></tr>`;
        }
    });
}

function updateStats(contributions) {
    let totalExpected = 0;
    let totalCollected = 0;
    
    contributions.forEach(cont => {
        const annualAmount = parseFloat(cont.annual_amount) || 0;
        const totalPaid = parseFloat(cont.total_paid) || 0;
        
        totalExpected += annualAmount;
        totalCollected += totalPaid;
    });
    
    const collectionRate = totalExpected > 0 ? ((totalCollected / totalExpected) * 100).toFixed(1) : 0;
    
    document.getElementById('totalExpected').textContent = 'RWF ' + totalExpected.toLocaleString();
    document.getElementById('totalCollected').textContent = 'RWF ' + totalCollected.toLocaleString();
    document.getElementById('collectionRate').textContent = collectionRate + '%';
    document.getElementById('contributionsCount').textContent = contributions.length + ' contribution records found';
}

function updateContributionsTable(contributions) {
    const tbody = document.getElementById('contributions-table-body');
    const numberOfTerms = currentNumberOfTerms;
    const currentYear = currentContributionYear;
    
    if (!contributions || contributions.length === 0) {
        tbody.innerHTML = `<tr><td colspan="${numberOfTerms + 3}" class="text-center py-8 text-gray-500">No contributions found for ${currentYear}</td></tr>`;
        return;
    }
    
    tbody.innerHTML = contributions.map(cont => {
        let termsHtml = '';
        const annualAmount = parseFloat(cont.annual_amount) || 0;
        
        for (let i = 1; i <= numberOfTerms; i++) {
            const termAmount = parseFloat(cont[`term${i}_paid`]) || 0;
            const termTarget = parseFloat(cont[`term${i}_target`]) || 0;
            
            let termProgress = 0;
            if (termTarget > 0) {
                termProgress = Math.min((termAmount / termTarget) * 100, 100);
            } else if (termAmount > 0) {
                termProgress = 100;
            }
            
            const progressColor = termProgress >= 100 ? 'bg-green-500' : (termProgress >= 50 ? 'bg-blue-500' : 'bg-yellow-500');
            
            termsHtml += `
                <td class="px-4 py-3 text-sm min-w-[140px]">
                    <div class="flex flex-col gap-1">
                        <div class="flex justify-between items-center">
                            <span class="font-medium text-green-600">RWF ${termAmount.toLocaleString()}</span>
                            <span class="text-gray-400 text-xs">/ RWF ${termTarget.toLocaleString()}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                            <div class="${progressColor} h-2 rounded-full transition-all duration-300" 
                                 style="width: ${termProgress}%"></div>
                        </div>
                    </div>
                </td>
            `;
        }
        
        const totalPaid = parseFloat(cont.total_paid) || 0;
        const overallProgress = annualAmount > 0 ? Math.min((totalPaid / annualAmount) * 100, 100) : 0;
        const progressColor = overallProgress >= 100 ? 'bg-green-600' : (overallProgress >= 50 ? 'bg-blue-600' : 'bg-purple-600');
        
        const familyInfo = cont.family_name ? `<span class="text-xs text-gray-400">🏠 ${escapeHtml(cont.family_name)}</span>` : '';
        
        return `
            <tr class="border-b hover:bg-gray-50 transition">
                <td class="px-4 py-3">
                    <div class="flex flex-col">
                        <p class="font-medium text-gray-800">${escapeHtml(cont.user_name)}</p>
                        <p class="text-xs text-gray-500">${escapeHtml(cont.email)}</p>
                        ${familyInfo}
                    </div>
                </td>
                ${termsHtml}
                <td class="px-4 py-3 min-w-[140px]">
                    <div class="flex flex-col gap-1">
                        <div class="flex justify-between items-center">
                            <span class="font-bold text-purple-600">${overallProgress.toFixed(1)}%</span>
                            <span class="text-gray-400 text-xs">RWF ${totalPaid.toLocaleString()} / RWF ${annualAmount.toLocaleString()}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                            <div class="${progressColor} h-2 rounded-full transition-all duration-300" 
                                 style="width: ${overallProgress}%"></div>
                        </div>
                    </div>
                </td>
                <td class="px-4 py-3">
                    <div class="flex items-center gap-3">
                        <button onclick='editContribution(${cont.id || 'null'}, ${cont.user_id}, "${escapeHtml(cont.user_name)}", ${currentYear}, ${annualAmount}, "${escapeHtml(cont.contribution_notes || '')}")' 
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
                </td>
            </tr>
        `;
    }).join('');
}

function openContributeForUser(userId, userName) {
    currentContributeUserId = userId;
    document.getElementById('selectedContributeUserId').value = userId;
    document.getElementById('selectedContributeMemberName').innerHTML = userName;
    document.getElementById('selectedContributeMemberDisplay').classList.remove('hidden');
    document.getElementById('searchContributeMemberInput').value = '';
    document.getElementById('contributeMemberList').classList.add('hidden');
    document.getElementById('contributeYear').innerHTML = currentContributionYear;
    openFinanceModal('contributeModal');
    document.getElementById('contributeAmount').value = '';
}

// ============================================
// VIEW DETAILS
// ============================================

function viewContributionDetails(userId) {
    const year = currentContributionYear;
    
    fetch(`/finance/contributions/${userId}/details?year=${year}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('viewDetailsTitle').innerHTML = `${escapeHtml(data.user_name)} - Contribution Details (${year})`;
            
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
            openFinanceModal('viewDetailsModal');
        }
    })
    .catch(error => console.error('Error:', error));
}

function viewPaymentHistory(paymentId, userId, userName, year) {
    fetch(`/finance/payments/${paymentId}/history`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const historyModal = document.createElement('div');
            historyModal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center';
            historyModal.innerHTML = `
                <div class="relative mx-auto p-6 border w-full max-w-3xl shadow-2xl rounded-2xl bg-white">
                    <div class="flex justify-between items-center pb-4 border-b">
                        <h3 class="text-xl font-bold text-gray-800">Payment Edit History</h3>
                        <button onclick="this.closest('.fixed').remove()" class="text-gray-400 hover:text-gray-600 transition">
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
                        <button onclick="this.closest('.fixed').remove()" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm transition">
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

// Search functionality
document.getElementById('searchContributions')?.addEventListener('keyup', function() {
    loadContributions();
});

// Close search results when clicking outside
document.addEventListener('click', function(event) {
    const setAnnualSearch = document.getElementById('memberSearchResults');
    const setAnnualInput = document.getElementById('searchMemberInput');
    const contributeList = document.getElementById('contributeMemberList');
    const contributeInput = document.getElementById('searchContributeMemberInput');
    
    if (setAnnualSearch && !setAnnualSearch.contains(event.target) && event.target !== setAnnualInput) {
        setAnnualSearch.classList.add('hidden');
    }
    if (contributeList && !contributeList.contains(event.target) && event.target !== contributeInput) {
        contributeList.classList.add('hidden');
    }
});

// Close year picker when clicking outside
document.addEventListener('click', function(event) {
    const picker = document.getElementById('contributionYearPickerDropdown');
    const display = document.querySelector('#contributionYearDisplay');
    
    if (picker && !picker.classList.contains('hidden') && display) {
        const parentDiv = display.closest('.relative');
        if (parentDiv && !parentDiv.contains(event.target)) {
            closeContributionYearPicker();
        }
    }
});

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    const currentYear = new Date().getFullYear();
    currentContributionYear = currentYear;
    document.getElementById('contributionSelectedYear').value = currentYear;
    document.getElementById('contributionYearDisplay').textContent = currentYear;
    
    loadTermSettingsForYear(currentYear);
    loadContributions();
});
</script>

<style>
.rotate-180 {
    transform: rotate(180deg);
}
</style>