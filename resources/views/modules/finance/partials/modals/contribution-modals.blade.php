<!-- ============================================ -->
<!-- CONTRIBUTION MODALS - Finance Module        -->
<!-- ============================================ -->

<!-- Set Annual Contribution Modal -->
<div id="setAnnualModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center modal-overlay" role="dialog" aria-modal="true" aria-labelledby="setAnnualModalTitle">
    <div class="relative mx-auto p-4 border w-full max-w-md shadow-2xl rounded-lg bg-white">
        <div class="flex justify-between items-center pb-3 border-b">
            <h3 id="setAnnualModalTitle" class="text-lg font-bold text-gray-800">Set Annual Contribution</h3>
            <button onclick="window.contributionsManager.closeFinanceModal('setAnnualModal')" class="text-gray-400 hover:text-gray-600 transition" aria-label="Close modal">
                <i class="fas fa-times text-xl" aria-hidden="true"></i>
            </button>
        </div>
        <form id="setAnnualForm" onsubmit="window.contributionsManager.submitSetAnnual(event)" class="mt-3">
            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Select Member</label>
                    <div class="relative">
                        <input type="text" id="searchMemberInput" placeholder="Search member by name or email..."
                            class="w-full h-8 px-3 py-0 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                            oninput="window.contributionsManager.searchMembers()"
                            aria-label="Search members">
                        <div id="memberSearchResults" class="hidden absolute z-10 w-full bg-white border border-gray-200 rounded-lg shadow-xl mt-1 max-h-60 overflow-y-auto"></div>
                    </div>
                    <input type="hidden" id="selectedUserId">
                    <div id="selectedMemberDisplay" class="hidden mt-2 p-2 bg-blue-50 rounded-lg flex items-center justify-between">
                        <span id="selectedMemberName" class="text-sm font-medium text-gray-800"></span>
                        <button type="button" onclick="window.contributionsManager.clearSelectedMember()" class="text-red-500 hover:text-red-700 text-sm">Remove</button>
                    </div>
                </div>
                <div>
                    <label for="annualAmount" class="block text-sm font-medium text-gray-700 mb-1">Annual Amount (RWF)</label>
                    <input type="number" id="annualAmount" step="0.01" min="0"
                        class="w-full h-8 px-3 py-0 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        required aria-required="true">
                </div>
                <div>
                    <label for="setAnnualYearDisplay" class="block text-sm font-medium text-gray-700 mb-1">Year</label>
                    <span id="setAnnualYearDisplay" class="block h-8 px-3 py-1.5 bg-gray-100 rounded-lg text-sm text-gray-800 font-medium">{{ date('Y') }}</span>
                    <input type="hidden" id="setAnnualYear" value="{{ date('Y') }}">
                </div>
                <div>
                    <label for="setAnnualNotes" class="block text-sm font-medium text-gray-700 mb-1">Notes (Optional)</label>
                    <textarea id="setAnnualNotes" rows="2" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Add any notes about this contribution..."></textarea>
                </div>
            </div>
            <div class="flex justify-end gap-2 mt-4 pt-3 border-t">
                <button type="button" onclick="window.contributionsManager.closeFinanceModal('setAnnualModal')" class="h-8 px-3 py-0 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg text-xs transition">
                    Cancel
                </button>
                <button type="submit" class="h-8 px-3 py-0 bg-green-600 hover:bg-green-700 text-white rounded-lg text-xs transition flex items-center gap-1.5">
                    <i class="fas fa-save" aria-hidden="true"></i> Set Contribution
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Record Payment Modal -->
<div id="contributeModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center modal-overlay" role="dialog" aria-modal="true" aria-labelledby="contributeModalTitle">
    <div class="relative mx-auto p-4 border w-full max-w-md shadow-2xl rounded-lg bg-white max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center pb-3 border-b">
            <h3 id="contributeModalTitle" class="text-lg font-bold text-gray-800">Record Payment</h3>
            <button onclick="window.contributionsManager.closeFinanceModal('contributeModal')" class="text-gray-400 hover:text-gray-600 transition" aria-label="Close modal">
                <i class="fas fa-times text-xl" aria-hidden="true"></i>
            </button>
        </div>
        <form id="contributeForm" onsubmit="window.contributionsManager.submitContribute(event)" class="mt-3">
            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Select Member</label>
                    <div class="relative">
                        <input type="text" id="searchContributeMemberInput" placeholder="Search member by name or email..."
                            class="w-full h-8 px-3 py-0 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                            oninput="window.contributionsManager.searchContributeMembers()"
                            aria-label="Search members for payment">
                        <div id="contributeMemberList" class="hidden absolute z-10 w-full bg-white border border-gray-200 rounded-lg shadow-xl mt-1 max-h-60 overflow-y-auto"></div>
                    </div>
                    <input type="hidden" id="selectedContributeUserId">
                    <div id="selectedContributeMemberDisplay" class="hidden mt-2 p-2 bg-blue-50 rounded-lg flex items-center justify-between">
                        <span id="selectedContributeMemberName" class="text-sm font-medium text-gray-800"></span>
                        <button type="button" onclick="window.contributionsManager.clearSelectedContributeMember()" class="text-red-500 hover:text-red-700 text-sm">Change</button>
                    </div>
                </div>
                <div>
                    <label for="contributeTerm" class="block text-sm font-medium text-gray-700 mb-1">Term</label>
                    <select id="contributeTerm" class="w-full h-8 px-3 py-0 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required aria-required="true">
                        <!-- Options populated by JavaScript -->
                    </select>
                </div>
                <div>
                    <label for="contributeAmount" class="block text-sm font-medium text-gray-700 mb-1">Amount (RWF)</label>
                    <input type="number" id="contributeAmount" step="0.01" min="0.01"
                        class="w-full h-8 px-3 py-0 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        required aria-required="true">
                </div>
                <div>
                    <label for="contributeYearDisplay" class="block text-sm font-medium text-gray-700 mb-1">Year</label>
                    <span id="contributeYearDisplay" class="block h-8 px-3 py-1.5 bg-gray-100 rounded-lg text-sm text-gray-800 font-medium">{{ date('Y') }}</span>
                    <input type="hidden" id="contributeYear" value="{{ date('Y') }}">
                </div>
                <div>
                    <label for="contributePaymentMethod" class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                    <select id="contributePaymentMethod" class="w-full h-8 px-3 py-0 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="cash">Cash</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="mobile_money">Mobile Money</option>
                        <option value="cheque">Cheque</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div>
                    <label for="contributeNotes" class="block text-sm font-medium text-gray-700 mb-1">Notes (Optional)</label>
                    <textarea id="contributeNotes" rows="2" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Add any notes about this payment..."></textarea>
                </div>
            </div>
            <div class="flex justify-end gap-2 mt-4 pt-3 border-t">
                <button type="button" onclick="window.contributionsManager.closeFinanceModal('contributeModal')" class="h-8 px-3 py-0 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg text-xs transition">
                    Cancel
                </button>
                <button type="submit" class="h-8 px-3 py-0 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs transition flex items-center gap-1.5">
                    <i class="fas fa-hand-holding-usd" aria-hidden="true"></i> Submit Payment
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Contribution Modal -->
<div id="editContributionModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center modal-overlay" role="dialog" aria-modal="true" aria-labelledby="editContributionModalTitle">
    <div class="relative mx-auto p-4 border w-full max-w-md shadow-2xl rounded-lg bg-white">
        <div class="flex justify-between items-center pb-3 border-b">
            <h3 id="editContributionModalTitle" class="text-lg font-bold text-gray-800">Edit Annual Contribution</h3>
            <button onclick="window.contributionsManager.closeFinanceModal('editContributionModal')" class="text-gray-400 hover:text-gray-600 transition" aria-label="Close modal">
                <i class="fas fa-times text-xl" aria-hidden="true"></i>
            </button>
        </div>
        <form id="editContributionForm" onsubmit="window.contributionsManager.submitEditContribution(event)" class="mt-3">
            <input type="hidden" id="editContributionId">
            <input type="hidden" id="editContributionUserId">
            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Member</label>
                    <span id="editContributionMemberName" class="block h-8 px-3 py-1.5 bg-gray-100 rounded-lg text-sm text-gray-800 font-medium"></span>
                </div>
                <div>
                    <label for="editContributionYearDisplay" class="block text-sm font-medium text-gray-700 mb-1">Year</label>
                    <span id="editContributionYearDisplay" class="block h-8 px-3 py-1.5 bg-gray-100 rounded-lg text-sm text-gray-800 font-medium"></span>
                    <input type="hidden" id="editContributionYear">
                </div>
                <div>
                    <label for="editAnnualAmount" class="block text-sm font-medium text-gray-700 mb-1">Annual Amount (RWF)</label>
                    <input type="number" id="editAnnualAmount" step="0.01" min="0"
                        class="w-full h-8 px-3 py-0 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        required aria-required="true">
                </div>
                <div>
                    <label for="editContributionNotes" class="block text-sm font-medium text-gray-700 mb-1">Notes (Optional)</label>
                    <textarea id="editContributionNotes" rows="2" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Add notes about this change..."></textarea>
                </div>
            </div>
            <div class="flex justify-end gap-2 mt-4 pt-3 border-t">
                <button type="button" onclick="window.contributionsManager.closeFinanceModal('editContributionModal')" class="h-8 px-3 py-0 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg text-xs transition">
                    Cancel
                </button>
                <button type="submit" id="submitEditContributionBtn" class="h-8 px-3 py-0 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs transition flex items-center gap-1.5">
                    <i class="fas fa-save" aria-hidden="true"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Payment Modal -->
<div id="editPaymentModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center modal-overlay" role="dialog" aria-modal="true" aria-labelledby="editPaymentModalTitle">
    <div class="relative mx-auto p-6 border w-full max-w-2xl shadow-2xl rounded-2xl bg-white">
        <div class="flex justify-between items-center pb-4 border-b">
            <h3 id="editPaymentModalTitle" class="text-xl font-bold text-gray-800">Edit Payment</h3>
            <button onclick="window.contributionsManager.closeFinanceModal('editPaymentModal')" class="text-gray-400 hover:text-gray-600 transition" aria-label="Close modal">
                <i class="fas fa-times text-xl" aria-hidden="true"></i>
            </button>
        </div>
        <form id="editPaymentForm" onsubmit="window.contributionsManager.submitEditPayment(event)" class="mt-4">
            <input type="hidden" id="editPaymentId">
            <input type="hidden" id="editPaymentUserId">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Member</label>
                    <span id="editPaymentMemberName" class="block px-4 py-2 bg-gray-100 rounded-lg text-gray-800 font-medium"></span>
                </div>
                <div>
                    <label for="editPaymentYearDisplay" class="block text-sm font-medium text-gray-700 mb-1">Year</label>
                    <span id="editPaymentYearDisplay" class="block px-4 py-2 bg-gray-100 rounded-lg text-gray-800 font-medium"></span>
                    <input type="hidden" id="editPaymentYear">
                </div>
                <div>
                    <label for="editPaymentTerm" class="block text-sm font-medium text-gray-700 mb-1">Term</label>
                    <select id="editPaymentTerm" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required aria-required="true">
                        <!-- Options populated by JavaScript -->
                    </select>
                </div>
                <div>
                    <label for="editPaymentAmount" class="block text-sm font-medium text-gray-700 mb-1">Amount (RWF)</label>
                    <input type="number" id="editPaymentAmount" step="0.01" min="0.01"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        required aria-required="true">
                </div>
                <div>
                    <label for="editPaymentMethod" class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                    <select id="editPaymentMethod" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="cash">Cash</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="mobile_money">Mobile Money</option>
                        <option value="cheque">Cheque</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div>
                    <label for="editPaymentDate" class="block text-sm font-medium text-gray-700 mb-1">Payment Date</label>
                    <input type="date" id="editPaymentDate"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label for="editPaymentNotes" class="block text-sm font-medium text-gray-700 mb-1">Notes (Optional)</label>
                    <textarea id="editPaymentNotes" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Add notes about this change..."></textarea>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6 pt-4 border-t">
                <button type="button" onclick="window.contributionsManager.closeFinanceModal('editPaymentModal')" class="px-5 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg text-sm transition">
                    Cancel
                </button>
                <button type="submit" id="submitEditPaymentBtn" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm transition flex items-center gap-2">
                    <i class="fas fa-save" aria-hidden="true"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<!-- View Details Modal -->
<div id="viewDetailsModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center modal-overlay" role="dialog" aria-modal="true" aria-labelledby="viewDetailsModalTitle">
    <div class="relative mx-auto p-4 border w-full max-w-3xl shadow-2xl rounded-lg bg-white max-h-[90vh] flex flex-col">
        <div class="flex justify-between items-center pb-3 border-b">
            <h3 id="viewDetailsModalTitle" class="text-lg font-bold text-gray-800">Contribution Details</h3>
            <button onclick="window.contributionsManager.closeFinanceModal('viewDetailsModal')" class="text-gray-400 hover:text-gray-600 transition" aria-label="Close modal">
                <i class="fas fa-times text-xl" aria-hidden="true"></i>
            </button>
        </div>
        <div id="viewDetailsContent" class="mt-3 overflow-y-auto flex-1">
            <!-- Content loaded dynamically -->
        </div>
        <div class="flex justify-end mt-4 pt-3 border-t">
            <button onclick="window.contributionsManager.closeFinanceModal('viewDetailsModal')" class="h-8 px-3 py-0 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs transition">
                Close
            </button>
        </div>
    </div>
</div>

<style>
    .modal-overlay {
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
    }
</style>
