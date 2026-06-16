<?php

use App\Http\Controllers\Intercession\IntercessionController;
use App\Http\Controllers\Intercession\FormController;

// ==================== INTERCESSION MAIN ROUTES ====================
Route::middleware('auth')->prefix('intercession')->name('intercession.')->group(function () {
    Route::get('/', [IntercessionController::class, 'index'])->name('index');
    Route::get('/devotion/{id}', [IntercessionController::class, 'showDevotion'])->name('devotion.show');
    Route::post('/devotion/{id}/complete', [IntercessionController::class, 'completeDevotion'])->name('devotion.complete');
    Route::get('/action-plans', [IntercessionController::class, 'actionPlans'])->name('action-plans');
    Route::post('/action-plans/store', [IntercessionController::class, 'storeActionPlan'])->name('action-plans.store');
    Route::put('/action-plans/{id}/status', [IntercessionController::class, 'updateActionPlanStatus'])->name('action-plans.status');
    Route::get('/archives', [IntercessionController::class, 'archives'])->name('archives');
    Route::post('/prayer/store', [IntercessionController::class, 'storePrayerRequest'])->name('prayer.store');
});

// ==================== ACTION PLANS ROUTES ====================
Route::prefix('intercession/action-plans')->middleware('auth')->group(function () {
    Route::post('/store', [IntercessionController::class, 'storeActionPlan'])->name('intercession.action-plans.store');
    Route::put('/{id}/status', [IntercessionController::class, 'updateActionPlanStatus'])->name('intercession.action-plans.status');
    Route::delete('/{id}', [IntercessionController::class, 'deleteActionPlan'])->name('intercession.action-plans.delete');
    Route::get('/{id}/edit', [IntercessionController::class, 'editActionPlan'])->name('intercession.action-plans.edit');
    Route::put('/{id}', [IntercessionController::class, 'updateActionPlan'])->name('intercession.action-plans.update');
});

// ==================== DEVOTIONS ROUTES ====================
Route::prefix('intercession/devotions')->middleware('auth')->group(function () {
    Route::post('/store', [IntercessionController::class, 'storeDevotion'])->name('intercession.devotions.store');
    Route::get('/{id}/edit', [IntercessionController::class, 'editDevotion'])->name('intercession.devotions.edit');
    Route::post('/{id}', [IntercessionController::class, 'updateDevotion'])->name('intercession.devotions.update');
    Route::delete('/{id}', [IntercessionController::class, 'deleteDevotion'])->name('intercession.devotions.delete');
});

Route::get('/intercession/devotion/show/{id}', [IntercessionController::class, 'showDevotion'])->name('intercession.devotion.show')->middleware('auth');

// ==================== ARCHIVES ROUTES ====================
Route::prefix('intercession/archives')->middleware('auth')->group(function () {
    Route::post('/sections/store', [IntercessionController::class, 'storeArchiveSection'])->name('intercession.archives.sections.store');
    Route::put('/sections/{id}', [IntercessionController::class, 'updateArchiveSection'])->name('intercession.archives.sections.update');
    Route::delete('/sections/{id}', [IntercessionController::class, 'deleteArchiveSection'])->name('intercession.archives.sections.delete');
    Route::get('/sections/{id}/pages', [IntercessionController::class, 'getSectionPages'])->name('intercession.archives.sections.pages');
    Route::post('/pages/store', [IntercessionController::class, 'storeArchivePage'])->name('intercession.archives.pages.store');
    Route::put('/pages/{id}', [IntercessionController::class, 'updateArchivePage'])->name('intercession.archives.pages.update');
    Route::delete('/pages/{id}', [IntercessionController::class, 'deleteArchivePage'])->name('intercession.archives.pages.delete');
    Route::get('/pages/{id}/edit', [IntercessionController::class, 'editArchivePage'])->name('intercession.archives.pages.edit');
    Route::get('/pages/{id}', [IntercessionController::class, 'showArchivePage'])->name('intercession.archives.pages.show');
});

// ==================== FORMS ROUTES ====================
Route::middleware('auth')->prefix('forms')->name('forms.')->group(function () {
    // Form management routes (admin)
    Route::get('/', [FormController::class, 'index'])->name('index');
    Route::get('/manage', [FormController::class, 'index'])->name('manage.index');
    Route::get('/manage/create', [FormController::class, 'create'])->name('manage.create');
    Route::post('/manage/store', [FormController::class, 'store'])->name('manage.store');
    Route::get('/manage/{id}/edit', [FormController::class, 'edit'])->name('manage.edit');
    Route::put('/manage/{id}', [FormController::class, 'update'])->name('manage.update');
    Route::delete('/manage/{id}', [FormController::class, 'destroy'])->name('manage.delete');
    Route::post('/manage/{id}/toggle-publish', [FormController::class, 'togglePublish'])->name('manage.toggle-publish');
    Route::get('/manage/{id}/submissions', [FormController::class, 'submissions'])->name('manage.submissions');
    Route::get('/manage/{id}/settings', [FormController::class, 'settings'])->name('manage.settings');
    Route::put('/manage/{id}/settings', [FormController::class, 'updateSettings'])->name('manage.settings.update');
    Route::get('/manage/{id}/submissions/export', [FormController::class, 'exportSubmissions'])->name('manage.submissions.export');
    
    // Form taking routes (users)
    Route::get('/{id}/take', [FormController::class, 'take'])->name('take');
    Route::post('/{id}/submit', [FormController::class, 'submit'])->name('submit');
    Route::get('/{id}/results', [FormController::class, 'results'])->name('results');
    
    // Alias for edit (without manage prefix for compatibility)
    Route::get('/{id}/edit', [FormController::class, 'edit'])->name('edit');
});

// ==================== BACKWARD COMPATIBILITY ALIASES ====================
Route::get('/forms/manage', [FormController::class, 'index'])->name('forms.manage')->middleware('auth');
Route::get('/forms/create', [FormController::class, 'create'])->name('forms.create')->middleware('auth');