<?php

use App\Http\Controllers\Finance\ContributionController;
use App\Http\Controllers\Finance\MyContributionController;

// Existing financial routes
Route::prefix('financial')->name('financial.')->middleware('auth')->group(function () {
    Route::get('/my-contributions', [MyContributionController::class, 'myContributions'])->name('my-contributions');
    Route::post('/submit-payment', [MyContributionController::class, 'submitPayment'])->name('submit-payment');
    Route::post('/update-annual-amount', [MyContributionController::class, 'updateAnnualAmount'])->name('update-annual-amount');
    Route::get('/admin', [MyContributionController::class, 'adminIndex'])->name('admin.index');
    Route::post('/approve/{id}', [MyContributionController::class, 'approveContribution'])->name('approve');
});

// New Finance Management Module Routes
Route::prefix('finance')->name('finance.')->middleware('auth')->group(function () {
    
    // Main view
    Route::get('/', [ContributionController::class, 'index'])->name('index');
    
    // ==================== OVERVIEW / DASHBOARD ROUTES ====================
    Route::prefix('overview')->name('overview.')->group(function () {
        Route::get('/stats', [ContributionController::class, 'getOverviewStats'])->name('stats');
        Route::get('/monthly-trend', [ContributionController::class, 'getMonthlyTrend'])->name('monthly-trend');
        Route::get('/income-breakdown', [ContributionController::class, 'getIncomeBreakdown'])->name('income-breakdown');
        Route::get('/expense-breakdown', [ContributionController::class, 'getExpenseBreakdown'])->name('expense-breakdown');
    });
    
    // ==================== SETTINGS ROUTES ====================
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/get', [ContributionController::class, 'getSettings'])->name('get');
        Route::post('/update', [ContributionController::class, 'updateSettings'])->name('update');
        Route::get('/terms', [ContributionController::class, 'getTerms'])->name('terms.get');
        Route::post('/terms', [ContributionController::class, 'updateTerms'])->name('terms.update');
    });
    
    // ==================== CONTRIBUTIONS ROUTES ====================
    Route::prefix('contributions')->name('contributions.')->group(function () {
        Route::get('/filter', [ContributionController::class, 'filterMemberContributions'])->name('filter');
        Route::post('/set-annual', [ContributionController::class, 'setMemberAnnualContribution'])->name('set-annual');
        Route::post('/pay', [ContributionController::class, 'payMemberContribution'])->name('pay');
        Route::post('/update', [ContributionController::class, 'updateMemberContribution'])->name('update');
        Route::delete('/{userId}', [ContributionController::class, 'deleteMemberContribution'])->name('delete');
        Route::get('/{userId}/details', [ContributionController::class, 'getMemberContributionDetails'])->name('details');
        Route::post('/edit-annual', [ContributionController::class, 'editMemberAnnualContribution'])->name('edit-annual');
    });
    
    // ==================== PAYMENTS ROUTES ====================
    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/', [ContributionController::class, 'getPaymentsList'])->name('data');
        Route::get('/filter', [ContributionController::class, 'filterPaymentsList'])->name('filter');
        Route::post('/', [ContributionController::class, 'storePayment'])->name('store');
        Route::put('/{id}', [ContributionController::class, 'updatePayment'])->name('update');
        Route::delete('/{id}', [ContributionController::class, 'deletePayment'])->name('delete');
        Route::get('/{id}', [ContributionController::class, 'showPayment'])->name('show');
        Route::get('/{paymentId}/history', [ContributionController::class, 'getPaymentHistory'])->name('history');
    });
    
    // ==================== SPONSORS ROUTES ====================
    Route::prefix('sponsors')->name('sponsors.')->group(function () {
        Route::get('/', [ContributionController::class, 'getSponsors'])->name('data');
        Route::get('/filter', [ContributionController::class, 'filterSponsors'])->name('filter');
        Route::post('/', [ContributionController::class, 'storeSponsor'])->name('store');
        Route::put('/{id}', [ContributionController::class, 'updateSponsor'])->name('update');
        Route::delete('/{id}', [ContributionController::class, 'deleteSponsor'])->name('delete');
        Route::get('/{id}/edit', [ContributionController::class, 'editSponsor'])->name('edit');
        Route::get('/{id}', [ContributionController::class, 'showSponsor'])->name('show');
        Route::post('/payment', [ContributionController::class, 'recordSponsorPayment'])->name('record-payment');
        Route::get('/{id}/payments', [ContributionController::class, 'getSponsorPayments'])->name('payments');
    });
    
    // ==================== GIFTS ROUTES ====================
    Route::prefix('gifts')->name('gifts.')->group(function () {
        Route::get('/', [ContributionController::class, 'getGifts'])->name('data');
        Route::get('/filter', [ContributionController::class, 'filterGifts'])->name('filter');
        Route::post('/', [ContributionController::class, 'storeGift'])->name('store');
        Route::put('/{id}', [ContributionController::class, 'updateGift'])->name('update');
        Route::delete('/{id}', [ContributionController::class, 'deleteGift'])->name('delete');
        Route::get('/{id}', [ContributionController::class, 'showGift'])->name('show');
    });
    
    // ==================== EXPENSES ROUTES ====================
    Route::prefix('expenses')->name('expenses.')->group(function () {
        Route::get('/', [ContributionController::class, 'getExpenses'])->name('data');
        Route::get('/filter', [ContributionController::class, 'filterExpenses'])->name('filter');
        Route::post('/', [ContributionController::class, 'storeExpense'])->name('store');
        Route::put('/{id}', [ContributionController::class, 'updateExpense'])->name('update');
        Route::delete('/{id}', [ContributionController::class, 'deleteExpense'])->name('delete');
        Route::get('/{id}', [ContributionController::class, 'showExpense'])->name('show');
        Route::get('/{id}/details', [ContributionController::class, 'getExpenseDetails'])->name('details');
        Route::post('/{id}/approve', [ContributionController::class, 'approveExpense'])->name('approve');
    });
    
    // ==================== BUDGET ROUTES ====================
    Route::prefix('budget')->name('budget.')->group(function () {
        Route::get('/', [ContributionController::class, 'getBudget'])->name('data');
        Route::post('/', [ContributionController::class, 'storeBudget'])->name('store');
        Route::put('/{id}', [ContributionController::class, 'updateBudget'])->name('update');
        Route::delete('/{id}', [ContributionController::class, 'deleteBudget'])->name('delete');
    });
    
    // ==================== ACTION PLANS ROUTES ====================
    Route::prefix('action-plans')->name('action-plans.')->group(function () {
        Route::get('/filter', [ContributionController::class, 'filterActionPlans'])->name('filter');
        Route::post('/', [ContributionController::class, 'storeActionPlan'])->name('store');
        Route::put('/{id}', [ContributionController::class, 'updateActionPlan'])->name('update');
        Route::delete('/{id}', [ContributionController::class, 'deleteActionPlan'])->name('delete');
        Route::get('/{id}/edit', [ContributionController::class, 'editActionPlan'])->name('edit');
        Route::get('/{id}', [ContributionController::class, 'showActionPlan'])->name('show');
    });
    
    // ==================== REPORTS ROUTES ====================
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/contributions', [ContributionController::class, 'generateContributionsReport'])->name('contributions');
        Route::get('/payments', [ContributionController::class, 'generatePaymentsReport'])->name('payments');
        Route::get('/gifts', [ContributionController::class, 'generateGiftsReport'])->name('gifts');
        Route::get('/sponsors', [ContributionController::class, 'generateSponsorsReport'])->name('sponsors');
        Route::get('/sponsors-gifts', [ContributionController::class, 'generateSponsorsGiftsReport'])->name('sponsors-gifts');
        Route::get('/expenses', [ContributionController::class, 'generateExpensesReport'])->name('expenses');
        Route::get('/summary', [ContributionController::class, 'generateSummaryReport'])->name('summary');
        Route::get('/income', [ContributionController::class, 'generateIncomeReport'])->name('income');
        Route::get('/balance', [ContributionController::class, 'generateBalanceReport'])->name('balance');
        Route::get('/export', [ContributionController::class, 'exportReport'])->name('export');
    });
});