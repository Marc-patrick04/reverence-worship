<!-- ============================================ -->
<!-- EXPENSE MODALS - Finance Module              -->
<!-- ============================================ -->

<!-- Add Expense Modal -->
<div id="expenseModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-[9999] hidden" 
     role="dialog" aria-modal="true" aria-labelledby="expenseModalTitle">
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="relative bg-white rounded-2xl shadow-2xl max-w-lg w-full mx-auto my-8 border">
            <div class="flex justify-between items-center p-5 pb-3 border-b">
                <h3 id="expenseModalTitle" class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-plus-circle text-blue-600"></i>
                    New Expense
                </h3>
                <button onclick="window.expensesManager.closeModal('expenseModal')" class="text-gray-400 hover:text-gray-600 transition text-2xl leading-none">
                    &times;
                </button>
            </div>
            
            <form id="expenseForm" onsubmit="window.expensesManager.submitExpense(event)" class="p-5">
                @csrf
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Amount <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 text-sm">RWF</span>
                            <input type="number" id="expenseAmount" name="amount" step="0.01" required 
                                   placeholder="0.00"
                                   class="w-full pl-12 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description <span class="text-red-500">*</span></label>
                        <textarea id="expenseDescription" name="description" rows="2" required 
                                  placeholder="Reason for the expense..."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                    </div>
                    
                    <!-- Date is auto-assigned - removed from user input -->
                    
                    <!-- Approver Selection - Can select 1 or 2 approvers -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Approvers <span class="text-gray-400 text-xs">(Select 1 or 2)</span></label>
                        
                        <!-- Approver 1 -->
                        <div class="mb-2">
                            <label class="text-xs text-gray-500">Approver 1</label>
                            <div class="relative">
                                <input type="text" id="approverSearch1" placeholder="Search user..."
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       oninput="window.expensesManager.searchApprovers(1)">
                                <div id="approverSearchResults1" class="hidden absolute z-20 w-full bg-white border border-gray-200 rounded-lg shadow-xl mt-1 max-h-40 overflow-y-auto"></div>
                            </div>
                            <input type="hidden" id="approverId1" name="approver_id_1">
                            <div id="selectedApproverDisplay1" class="hidden mt-1 p-2 bg-blue-50 rounded-lg flex items-center justify-between">
                                <span id="selectedApproverName1" class="text-sm font-medium text-gray-800"></span>
                                <button type="button" onclick="window.expensesManager.clearApprover(1)" class="text-red-500 hover:text-red-700 text-sm font-bold">×</button>
                            </div>
                        </div>
                        
                        <!-- Approver 2 (Optional) -->
                        <div>
                            <label class="text-xs text-gray-500">Approver 2 <span class="text-gray-400">(Optional)</span></label>
                            <div class="relative">
                                <input type="text" id="approverSearch2" placeholder="Search user..."
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       oninput="window.expensesManager.searchApprovers(2)">
                                <div id="approverSearchResults2" class="hidden absolute z-20 w-full bg-white border border-gray-200 rounded-lg shadow-xl mt-1 max-h-40 overflow-y-auto"></div>
                            </div>
                            <input type="hidden" id="approverId2" name="approver_id_2">
                            <div id="selectedApproverDisplay2" class="hidden mt-1 p-2 bg-blue-50 rounded-lg flex items-center justify-between">
                                <span id="selectedApproverName2" class="text-sm font-medium text-gray-800"></span>
                                <button type="button" onclick="window.expensesManager.clearApprover(2)" class="text-red-500 hover:text-red-700 text-sm font-bold">×</button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-end gap-3 mt-5 pt-3 border-t">
                    <button type="button" onclick="window.expensesManager.closeModal('expenseModal')" class="px-5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm transition">
                        Cancel
                    </button>
                    <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm transition flex items-center gap-2">
                        <i class="fas fa-save"></i> Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Expense Modal -->
<div id="viewExpenseModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-[9999] hidden" 
     role="dialog" aria-modal="true" aria-labelledby="viewExpenseModalTitle">
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full mx-auto my-8 border">
            <div class="flex justify-between items-center p-5 pb-3 border-b">
                <h3 id="viewExpenseModalTitle" class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-info-circle text-blue-600"></i>
                    Expense Details
                </h3>
                <button onclick="window.expensesManager.closeModal('viewExpenseModal')" class="text-gray-400 hover:text-gray-600 transition text-2xl leading-none">
                    &times;
                </button>
            </div>
            <div id="viewExpenseContent" class="p-5 space-y-3"></div>
            <div class="flex justify-end p-5 pt-3 border-t">
                <button onclick="window.expensesManager.closeModal('viewExpenseModal')" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm transition">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .modal-overlay {
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
    }
    .z-\[9999\] {
        z-index: 9999;
    }
</style>