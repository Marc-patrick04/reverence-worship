<?php

use App\Http\Controllers\Reports\UserReportController;
use App\Http\Controllers\Reports\AttendanceReportController;
use App\Http\Controllers\Reports\FinancialReportController;

Route::middleware(['auth'])->prefix('reports')->name('reports.')->group(function () {
    
    // ==========================================
    // USER REPORTS
    // ==========================================
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserReportController::class, 'index'])->name('index');
        Route::get('/data', [UserReportController::class, 'getReport'])->name('data');
        Route::get('/export', [UserReportController::class, 'exportReport'])->name('export');
        Route::get('/summary', [UserReportController::class, 'getSummary'])->name('summary');
        Route::get('/roles', [UserReportController::class, 'getRoles'])->name('roles');
    });
    
    // ==========================================
    // ATTENDANCE REPORTS
    // ==========================================
    Route::prefix('attendance')->name('attendance.')->group(function () {
        Route::get('/', [AttendanceReportController::class, 'index'])->name('index');
        Route::get('/data', [AttendanceReportController::class, 'getReport'])->name('data');
        Route::get('/export', [AttendanceReportController::class, 'exportReport'])->name('export');
        Route::get('/summary', [AttendanceReportController::class, 'getSummary'])->name('summary');
        Route::get('/sessions', [AttendanceReportController::class, 'getSessions'])->name('sessions');
        Route::get('/status-distribution', [AttendanceReportController::class, 'getStatusDistribution'])->name('status-distribution');
        Route::get('/trend', [AttendanceReportController::class, 'getTrend'])->name('trend');
    });
    
    // ==========================================
    // FINANCIAL REPORTS
    // ==========================================
    Route::prefix('finance')->name('finance.')->group(function () {
        Route::get('/', [FinancialReportController::class, 'index'])->name('index');
        Route::get('/data', [FinancialReportController::class, 'getReport'])->name('data');
        Route::get('/export', [FinancialReportController::class, 'exportReport'])->name('export');
        Route::get('/summary', [FinancialReportController::class, 'getSummary'])->name('summary');
        Route::get('/income-breakdown', [FinancialReportController::class, 'getIncomeBreakdown'])->name('income-breakdown');
        Route::get('/expense-breakdown', [FinancialReportController::class, 'getExpenseBreakdown'])->name('expense-breakdown');
        Route::get('/monthly-trend', [FinancialReportController::class, 'getMonthlyTrend'])->name('monthly-trend');
        Route::get('/years', [FinancialReportController::class, 'getYears'])->name('years');
    });
    
    // ==========================================
    // CONTRIBUTION REPORTS
    // ==========================================
    Route::prefix('contributions')->name('contributions.')->group(function () {
        Route::get('/', [FinancialReportController::class, 'getContributionsReport'])->name('index');
        Route::get('/data', [FinancialReportController::class, 'getContributionsData'])->name('data');
        Route::get('/export', [FinancialReportController::class, 'exportContributions'])->name('export');
        Route::get('/summary', [FinancialReportController::class, 'getContributionsSummary'])->name('summary');
    });
    
    // ==========================================
    // EXPENSE REPORTS
    // ==========================================
    Route::prefix('expenses')->name('expenses.')->group(function () {
        Route::get('/', [FinancialReportController::class, 'getExpensesReport'])->name('index');
        Route::get('/data', [FinancialReportController::class, 'getExpensesData'])->name('data');
        Route::get('/export', [FinancialReportController::class, 'exportExpenses'])->name('export');
        Route::get('/summary', [FinancialReportController::class, 'getExpensesSummary'])->name('summary');
        Route::get('/by-category', [FinancialReportController::class, 'getExpensesByCategory'])->name('by-category');
    });
    
    // ==========================================
    // SPONSOR REPORTS
    // ==========================================
    Route::prefix('sponsors')->name('sponsors.')->group(function () {
        Route::get('/', [FinancialReportController::class, 'getSponsorsReport'])->name('index');
        Route::get('/data', [FinancialReportController::class, 'getSponsorsData'])->name('data');
        Route::get('/export', [FinancialReportController::class, 'exportSponsors'])->name('export');
        Route::get('/summary', [FinancialReportController::class, 'getSponsorsSummary'])->name('summary');
    });
});