<?php

use App\Http\Controllers\Intercession\IntercessionController;
use App\Http\Controllers\Intercession\FormController;

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

// Action Plans
Route::prefix('intercession/action-plans')->group(function () {
    Route::post('/store', [IntercessionController::class, 'storeActionPlan'])->name('intercession.action-plans.store');
    Route::put('/{id}/status', [IntercessionController::class, 'updateActionPlanStatus'])->name('intercession.action-plans.status');
    Route::delete('/{id}', [IntercessionController::class, 'deleteActionPlan'])->name('intercession.action-plans.delete');
    Route::get('/{id}/edit', [IntercessionController::class, 'editActionPlan'])->name('intercession.action-plans.edit');
    Route::put('/{id}', [IntercessionController::class, 'updateActionPlan'])->name('intercession.action-plans.update');
});

// Devotions
Route::prefix('intercession/devotions')->group(function () {
    Route::post('/store', [IntercessionController::class, 'storeDevotion'])->name('intercession.devotions.store');
    Route::get('/{id}/edit', [IntercessionController::class, 'editDevotion'])->name('intercession.devotions.edit');
    Route::post('/{id}', [IntercessionController::class, 'updateDevotion'])->name('intercession.devotions.update');
    Route::delete('/{id}', [IntercessionController::class, 'deleteDevotion'])->name('intercession.devotions.delete');
});

Route::get('/intercession/devotion/show/{id}', [IntercessionController::class, 'showDevotion'])->name('intercession.devotion.show');

// Archives
Route::prefix('intercession/archives')->group(function () {
    Route::post('/sections/store', [IntercessionController::class, 'storeArchiveSection'])->name('intercession.archives.sections.store');
    Route::put('/sections/{id}', [IntercessionController::class, 'updateArchiveSection']);
    Route::delete('/sections/{id}', [IntercessionController::class, 'deleteArchiveSection']);
    Route::get('/sections/{id}/pages', [IntercessionController::class, 'getSectionPages']);
    Route::post('/pages/store', [IntercessionController::class, 'storeArchivePage'])->name('intercession.archives.pages.store');
    Route::put('/pages/{id}', [IntercessionController::class, 'updateArchivePage']);
    Route::delete('/pages/{id}', [IntercessionController::class, 'deleteArchivePage']);
    Route::get('/pages/{id}/edit', [IntercessionController::class, 'editArchivePage']);
    Route::get('/pages/{id}', [IntercessionController::class, 'showArchivePage']);
});

// Forms
Route::prefix('forms')->group(function () {
    Route::get('/', [FormController::class, 'index'])->name('forms.manage.index');
    Route::get('/manage', [FormController::class, 'manageForms'])->name('forms.index');
    Route::get('/manage/create', [FormController::class, 'createForm'])->name('forms.manage.create');
    Route::post('/manage/store', [FormController::class, 'storeForm'])->name('forms.store');
    Route::get('/{id}/take', [FormController::class, 'takeForm'])->name('forms.take');
    Route::post('/{id}/submit', [FormController::class, 'submitForm'])->name('forms.submit');
    Route::get('/manage/{id}/edit', [FormController::class, 'editForm'])->name('forms.edit');
    Route::put('/manage/{id}', [FormController::class, 'updateForm'])->name('forms.update');
    Route::delete('/manage/{id}', [FormController::class, 'deleteForm'])->name('forms.delete');
    Route::get('/manage/{id}/settings', [FormController::class, 'settings'])->name('forms.manage.settings');
    Route::put('/manage/{id}/settings', [FormController::class, 'updateSettings'])->name('forms.manage.settings.update');
    Route::post('/manage/{id}/toggle-publish', [FormController::class, 'togglePublish'])->name('forms.manage.toggle-publish');
    Route::get('/manage/{id}/submissions', [FormController::class, 'viewSubmissions'])->name('forms.submissions');
    Route::get('/manage/{id}/submissions/export', [FormController::class, 'exportSubmissions'])->name('forms.manage.submissions.export');
    Route::get('/{id}/results', [FormController::class, 'results'])->name('forms.results');
});