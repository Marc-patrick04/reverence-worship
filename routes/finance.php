<?php

use App\Http\Controllers\Finance\ContributionController;
use App\Http\Controllers\Finance\FinanceController;

// Existing financial routes
Route::prefix('financial')->name('financial.')->middleware('auth')->group(function () {
    Route::get('/my-contributions', [ContributionController::class, 'myContributions'])->name('my-contributions');
    Route::post('/submit-payment', [ContributionController::class, 'submitPayment'])->name('submit-payment');
    Route::post('/update-annual-amount', [ContributionController::class, 'updateAnnualAmount'])->name('update-annual-amount');
    Route::get('/admin', [ContributionController::class, 'adminIndex'])->name('admin.index');
    Route::post('/approve/{id}', [ContributionController::class, 'approveContribution'])->name('approve');
});

// New Finance Management Module Routes
Route::prefix('finance')->name('finance.')->middleware('auth')->group(function () {
    
    // Main view
    Route::get('/', [FinanceController::class, 'index'])->name('index');
    
    // Overview / Dashboard
    Route::get('/overview/stats', [FinanceController::class, 'getOverviewStats'])->name('overview.stats');
    
    // Settings routes
    Route::get('/settings/get', [FinanceController::class, 'getSettings'])->name('settings.get');
    Route::post('/settings/update', [FinanceController::class, 'updateSettings'])->name('settings.update');
    
    // Term Structure Settings
    Route::get('/terms/get', [FinanceController::class, 'getTerms'])->name('terms.get');
    Route::post('/terms/update', [FinanceController::class, 'updateTerms'])->name('terms.update');
    
    // Contributions routes - ADD THESE
    Route::delete('/contributions/{userId}/delete', [FinanceController::class, 'deleteContribution'])->name('contributions.delete');
    Route::get('/contributions/filter', [FinanceController::class, 'filterContributions'])->name('contributions.filter');
    Route::post('/contributions/set-annual', [FinanceController::class, 'setAnnualContribution'])->name('contributions.set-annual');
    Route::post('/contributions/pay', [FinanceController::class, 'payContribution'])->name('contributions.pay');
    Route::post('/contributions/edit-annual', [FinanceController::class, 'editAnnualContribution'])->name('contributions.edit-annual');
    Route::get('/contributions/{userId}/details', [FinanceController::class, 'getContributionDetails'])->name('contributions.details');
    
    // Payments routes
    Route::get('/payments/filter', [FinanceController::class, 'filterPayments'])->name('payments.filter');
    Route::get('/payments/data', [FinanceController::class, 'getPayments'])->name('payments.data');
    Route::post('/payments/store', [FinanceController::class, 'storePayment'])->name('payments.store');
    Route::put('/payments/{id}', [FinanceController::class, 'updatePayment'])->name('payments.update');
    Route::delete('/payments/{id}', [FinanceController::class, 'deletePayment'])->name('payments.delete');
    Route::get('/payments/{id}', [FinanceController::class, 'showPayment'])->name('payments.show');
    

    // Sponsors routes
Route::get('/sponsors/filter', [FinanceController::class, 'filterSponsors'])->name('sponsors.filter');
Route::post('/sponsors/store', [FinanceController::class, 'storeSponsor'])->name('sponsors.store');
Route::put('/sponsors/{id}', [FinanceController::class, 'updateSponsor'])->name('sponsors.update');
Route::get('/sponsors/{id}/edit', [FinanceController::class, 'editSponsor'])->name('sponsors.edit');
Route::delete('/sponsors/{id}', [FinanceController::class, 'deleteSponsor'])->name('sponsors.delete');
Route::post('/sponsors/record-payment', [FinanceController::class, 'recordSponsorPayment'])->name('sponsors.record-payment');
    // Gifts routes
    Route::get('/gifts/data', [FinanceController::class, 'getGifts'])->name('gifts.data');
    Route::get('/gifts/filter', [FinanceController::class, 'filterGifts'])->name('gifts.filter');
    Route::post('/gifts/store', [FinanceController::class, 'storeGift'])->name('gifts.store');
    Route::put('/gifts/{id}', [FinanceController::class, 'updateGift'])->name('gifts.update');
    Route::delete('/gifts/{id}', [FinanceController::class, 'deleteGift'])->name('gifts.delete');
    Route::get('/gifts/{id}', [FinanceController::class, 'showGift'])->name('gifts.show');
    
    // Sponsors routes
    Route::get('/sponsors/data', [FinanceController::class, 'getSponsors'])->name('sponsors.data');
    Route::get('/sponsors/filter', [FinanceController::class, 'filterSponsors'])->name('sponsors.filter');
    Route::post('/sponsors/store', [FinanceController::class, 'storeSponsor'])->name('sponsors.store');
    Route::put('/sponsors/{id}', [FinanceController::class, 'updateSponsor'])->name('sponsors.update');
    Route::delete('/sponsors/{id}', [FinanceController::class, 'deleteSponsor'])->name('sponsors.delete');
    Route::get('/sponsors/{id}', [FinanceController::class, 'showSponsor'])->name('sponsors.show');
    
    // Expenses routes
    Route::get('/expenses/data', [FinanceController::class, 'getExpenses'])->name('expenses.data');
    Route::get('/expenses/filter', [FinanceController::class, 'filterExpenses'])->name('expenses.filter');
    Route::post('/expenses/store', [FinanceController::class, 'storeExpense'])->name('expenses.store');
    Route::put('/expenses/{id}', [FinanceController::class, 'updateExpense'])->name('expenses.update');
    Route::delete('/expenses/{id}', [FinanceController::class, 'deleteExpense'])->name('expenses.delete');
    Route::get('/expenses/{id}', [FinanceController::class, 'showExpense'])->name('expenses.show');
    
    // Budget routes
    Route::get('/budget/data', [FinanceController::class, 'getBudget'])->name('budget.data');
    Route::post('/budget/store', [FinanceController::class, 'storeBudget'])->name('budget.store');
    Route::put('/budget/{id}', [FinanceController::class, 'updateBudget'])->name('budget.update');
    Route::delete('/budget/{id}', [FinanceController::class, 'deleteBudget'])->name('budget.delete');
    
    // Reports routes
    Route::get('/reports/income', [FinanceController::class, 'generateIncomeReport'])->name('reports.income');
    Route::get('/reports/balance', [FinanceController::class, 'generateBalanceReport'])->name('reports.balance');
    Route::get('/reports/contributions', [FinanceController::class, 'generateContributionsReport'])->name('reports.contributions');
    Route::get('/reports/expenses', [FinanceController::class, 'generateExpensesReport'])->name('reports.expenses');
    Route::get('/reports/export', [FinanceController::class, 'exportReport'])->name('reports.export');
    // Expenses routes
Route::get('/expenses/filter', [FinanceController::class, 'filterExpenses'])->name('expenses.filter');
Route::post('/expenses/store', [FinanceController::class, 'storeExpense'])->name('expenses.store');
Route::put('/expenses/{id}', [FinanceController::class, 'updateExpense'])->name('expenses.update');
Route::delete('/expenses/{id}', [FinanceController::class, 'deleteExpense'])->name('expenses.delete');
Route::get('/expenses/{id}', [FinanceController::class, 'showExpense'])->name('expenses.show');
Route::get('/expenses/{id}/details', [FinanceController::class, 'getExpenseDetails'])->name('expenses.details');
Route::post('/expenses/{id}/approve', [FinanceController::class, 'approveExpense'])->name('expenses.approve');

    // Reports routes
Route::get('/reports/contributions', [FinanceController::class, 'generateContributionsReport'])->name('reports.contributions');
Route::get('/reports/payments', [FinanceController::class, 'generatePaymentsReport'])->name('reports.payments');
Route::get('/reports/gifts', [FinanceController::class, 'generateGiftsReport'])->name('reports.gifts');
Route::get('/reports/sponsors', [FinanceController::class, 'generateSponsorsReport'])->name('reports.sponsors');
Route::get('/reports/sponsors-gifts', [FinanceController::class, 'generateSponsorsGiftsReport'])->name('reports.sponsors-gifts');
Route::get('/reports/expenses', [FinanceController::class, 'generateExpensesReport'])->name('reports.expenses');
Route::get('/reports/summary', [FinanceController::class, 'generateSummaryReport'])->name('reports.summary');
    // Action Plans routes
    Route::get('/action-plans/filter', [FinanceController::class, 'filterActionPlans'])->name('action-plans.filter');
    Route::post('/action-plans/store', [FinanceController::class, 'storeActionPlan'])->name('action-plans.store');
    Route::put('/action-plans/{id}', [FinanceController::class, 'updateActionPlan'])->name('action-plans.update');
    Route::delete('/action-plans/{id}', [FinanceController::class, 'deleteActionPlan'])->name('action-plans.delete');
    Route::get('/action-plans/{id}/edit', [FinanceController::class, 'editActionPlan'])->name('action-plans.edit');
    Route::get('/action-plans/{id}', [FinanceController::class, 'showActionPlan'])->name('action-plans.show');
});