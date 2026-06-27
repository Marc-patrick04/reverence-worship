<!-- ============================================ -->
<!-- SPONSOR MODALS - Finance Module              -->
<!-- ============================================ -->

<!-- Add/Edit Sponsor Modal -->
<div id="sponsorModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center modal-overlay" role="dialog" aria-modal="true" aria-labelledby="sponsorModalTitle">
    <div class="relative mx-auto p-4 border w-full max-w-md shadow-2xl rounded-lg bg-white max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center pb-3 border-b">
            <h3 id="sponsorModalTitle" class="text-lg font-bold text-gray-800">Add Sponsor</h3>
            <button onclick="window.sponsorsManager.closeFinanceModal('sponsorModal')" class="text-gray-400 hover:text-gray-600 transition" aria-label="Close modal">
                <i class="fas fa-times text-xl" aria-hidden="true"></i>
            </button>
        </div>
        <form id="sponsorForm" onsubmit="window.sponsorsManager.saveSponsor(event)" class="mt-3">
            @csrf
            <input type="hidden" id="sponsorId" name="id">
            
            <div class="space-y-3">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                        Sponsor Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" required
                        class="w-full h-8 px-3 py-0 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        placeholder="Enter sponsor name">
                </div>
                
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" id="email" name="email"
                        class="w-full h-8 px-3 py-0 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        placeholder="">
                </div>
                
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                    <input type="text" id="phone" name="phone"
                        class="w-full h-8 px-3 py-0 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        placeholder="Enter phone number">
                </div>
                
                <div>
                    <label for="commitment_amount" class="block text-sm font-medium text-gray-700 mb-1">
                        Commitment Amount (RWF) <span class="text-gray-400 text-xs">(Optional)</span>
                    </label>
                    <input type="number" id="commitment_amount" name="commitment_amount" step="0.01" min="0"
                        class="w-full h-8 px-3 py-0 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        placeholder="Optional - Leave empty if no commitment">
                    <p class="text-xs text-gray-400 mt-1">Leave empty if the sponsor gives directly without a commitment</p>
                </div>
                
                <div>
                    <label for="sponsorYearDisplay" class="block text-sm font-medium text-gray-700 mb-1">Year</label>
                    <span id="sponsorYearDisplay" class="block h-8 px-3 py-1.5 bg-gray-100 rounded-lg text-sm text-gray-800 font-medium">--</span>
                    <input type="hidden" id="sponsorYear" name="year" value="">
                </div>
                
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes (Optional)</label>
                    <textarea id="notes" name="notes" rows="2"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        placeholder="Add any additional notes..."></textarea>
                </div>
            </div>
            
            <div class="flex justify-end gap-2 mt-4 pt-3 border-t">
                <button type="button" onclick="window.sponsorsManager.closeFinanceModal('sponsorModal')" class="h-8 px-3 py-0 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg text-xs transition">
                    Cancel
                </button>
                <button type="submit" id="submitSponsorBtn" class="h-8 px-3 py-0 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs transition flex items-center gap-1.5">
                    <i class="fas fa-save" aria-hidden="true"></i> <span id="sponsorSubmitBtnText">Add Sponsor</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Record Sponsor Payment Modal -->
<div id="paymentModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center modal-overlay" role="dialog" aria-modal="true" aria-labelledby="paymentModalTitle">
    <div class="relative mx-auto p-4 border w-full max-w-md shadow-2xl rounded-lg bg-white">
        <div class="flex justify-between items-center pb-3 border-b">
            <h3 id="paymentModalTitle" class="text-lg font-bold text-gray-800">Record Sponsor Payment</h3>
            <button onclick="window.sponsorsManager.closeFinanceModal('paymentModal')" class="text-gray-400 hover:text-gray-600 transition" aria-label="Close modal">
                <i class="fas fa-times text-xl" aria-hidden="true"></i>
            </button>
        </div>
        <form id="paymentForm" onsubmit="window.sponsorsManager.savePayment(event)" class="mt-3">
            @csrf
            <input type="hidden" id="payment_sponsor_id" name="sponsor_id">
            
            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sponsor</label>
                    <div class="bg-gray-100 rounded-lg px-3 py-2">
                        <p class="text-sm font-medium text-gray-800" id="payment_sponsor_name"></p>
                    </div>
                </div>
                
                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Amount (RWF) <span class="text-red-500">*</span></label>
                    <input type="number" id="amount" name="amount" required step="0.01" min="0.01"
                        class="w-full h-8 px-3 py-0 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        placeholder="0.00">
                </div>
                
                <div>
                    <label for="paymentYearDisplay" class="block text-sm font-medium text-gray-700 mb-1">Year</label>
                    <span id="payment_year_display" class="block h-8 px-3 py-1.5 bg-gray-100 rounded-lg text-sm text-gray-800 font-medium">--</span>
                    <input type="hidden" id="payment_year" name="year" value="">
                </div>
                
                <div>
                    <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                    <select id="payment_method" name="payment_method" class="w-full h-8 px-3 py-0 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="cash">Cash</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="mobile_money">Mobile Money</option>
                        <option value="cheque">Cheque</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                
                <div>
                    <label for="payment_notes" class="block text-sm font-medium text-gray-700 mb-1">Notes (Optional)</label>
                    <textarea id="payment_notes" name="notes" rows="2"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        placeholder="Add any notes about this payment..."></textarea>
                </div>
            </div>
            
            <div class="flex justify-end gap-2 mt-4 pt-3 border-t">
                <button type="button" onclick="window.sponsorsManager.closeFinanceModal('paymentModal')" class="h-8 px-3 py-0 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg text-xs transition">
                    Cancel
                </button>
                <button type="submit" class="h-8 px-3 py-0 bg-green-600 hover:bg-green-700 text-white rounded-lg text-xs transition flex items-center gap-1.5">
                    <i class="fas fa-hand-holding-usd" aria-hidden="true"></i> Record Payment
                </button>
            </div>
        </form>
    </div>
</div>

<!-- View Sponsor Payment History Modal -->
<div id="viewModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center modal-overlay" role="dialog" aria-modal="true" aria-labelledby="viewModalTitle">
    <div class="relative mx-auto p-6 border w-full max-w-3xl shadow-2xl rounded-2xl bg-white max-h-[90vh] flex flex-col">
        <div class="flex justify-between items-center pb-4 border-b">
            <h3 id="viewModalTitle" class="text-xl font-bold text-gray-800">Payment History</h3>
            <button onclick="window.sponsorsManager.closeFinanceModal('viewModal')" class="text-gray-400 hover:text-gray-600 transition" aria-label="Close modal">
                <i class="fas fa-times text-xl" aria-hidden="true"></i>
            </button>
        </div>
        <div id="paymentHistoryList" class="mt-4 overflow-y-auto flex-1 space-y-2">
            <!-- Payment history will be loaded here -->
        </div>
        <div class="flex justify-end mt-6 pt-4 border-t">
            <button onclick="window.sponsorsManager.closeFinanceModal('viewModal')" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm transition">
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
