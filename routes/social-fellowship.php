<?php

use App\Http\Controllers\SocialFellowship\SocialFellowshipController;

Route::prefix('social-fellowship')->group(function () {
    Route::get('/', [SocialFellowshipController::class, 'index'])->name('social-fellowship.index');
    Route::get('/debug', [SocialFellowshipController::class, 'debugData'])->name('social-fellowship.debug');
    
    // Family
    Route::post('/families/store', [SocialFellowshipController::class, 'storeFamily'])->name('social-fellowship.families.store');
    Route::get('/family/{id}/details', [SocialFellowshipController::class, 'getFamilyDetails'])->name('social-fellowship.family.details');
    Route::get('/family/{id}', [SocialFellowshipController::class, 'getFamily'])->name('social-fellowship.family.get');
    Route::delete('/family/{id}', [SocialFellowshipController::class, 'deleteFamily'])->name('social-fellowship.family.delete');
    Route::post('/family/{id}/member', [SocialFellowshipController::class, 'addMember'])->name('social-fellowship.family.add-member');
    Route::delete('/family/{familyId}/member/{userId}', [SocialFellowshipController::class, 'removeMember'])->name('social-fellowship.family.remove-member');
    
    // Tasks
    Route::post('/tasks/store', [SocialFellowshipController::class, 'storeTask'])->name('social-fellowship.tasks.store');
    Route::get('/tasks/{id}', [SocialFellowshipController::class, 'getTask'])->name('social-fellowship.tasks.get');
    Route::get('/tasks/{id}/edit', [SocialFellowshipController::class, 'editTask'])->name('social-fellowship.tasks.edit');
    Route::put('/tasks/{id}', [SocialFellowshipController::class, 'updateTask'])->name('social-fellowship.tasks.update');
    Route::delete('/tasks/{id}', [SocialFellowshipController::class, 'deleteTask'])->name('social-fellowship.tasks.delete');
    
    // Action Plans
    Route::post('/action-plans/store', [SocialFellowshipController::class, 'storeActionPlan'])->name('social-fellowship.action-plans.store');
    Route::get('/action-plans/{id}/edit', [SocialFellowshipController::class, 'editActionPlan'])->name('social-fellowship.action-plans.edit');
    Route::put('/action-plans/{id}', [SocialFellowshipController::class, 'updateActionPlan'])->name('social-fellowship.action-plans.update');
    Route::delete('/action-plans/{id}', [SocialFellowshipController::class, 'deleteActionPlan'])->name('social-fellowship.action-plans.delete');
    
    // Archives
    Route::prefix('archives')->group(function () {
        Route::post('/sections/store', [SocialFellowshipController::class, 'storeArchiveSection'])->name('social-fellowship.archives.sections.store');
        Route::put('/sections/{id}', [SocialFellowshipController::class, 'updateArchiveSection']);
        Route::delete('/sections/{id}', [SocialFellowshipController::class, 'deleteArchiveSection']);
        Route::get('/sections/{id}/pages', [SocialFellowshipController::class, 'getSectionPages']);
        Route::post('/pages/store', [SocialFellowshipController::class, 'storeArchivePage'])->name('social-fellowship.archives.pages.store');
        Route::put('/pages/{id}', [SocialFellowshipController::class, 'updateArchivePage']);
        Route::delete('/pages/{id}', [SocialFellowshipController::class, 'deleteArchivePage']);
        Route::get('/pages/{id}/edit', [SocialFellowshipController::class, 'editArchivePage']);
        Route::get('/pages/{id}', [SocialFellowshipController::class, 'showArchivePage']);
    });

    // Archives routes for Social Fellowship
Route::prefix('archives')->group(function () {
    // Sections
    Route::post('/sections', [SocialFellowshipController::class, 'storeArchiveSection'])->name('social-fellowship.archives.sections.store');
    Route::put('/sections/{id}', [SocialFellowshipController::class, 'updateArchiveSection']);
    Route::delete('/sections/{id}', [SocialFellowshipController::class, 'deleteArchiveSection']);
    Route::get('/sections/{id}/pages', [SocialFellowshipController::class, 'getSectionPages']);
    
    // Pages
    Route::post('/pages', [SocialFellowshipController::class, 'storeArchivePage'])->name('social-fellowship.archives.pages.store');
    Route::put('/pages/{id}', [SocialFellowshipController::class, 'updateArchivePage']);
    Route::delete('/pages/{id}', [SocialFellowshipController::class, 'deleteArchivePage']);
    Route::get('/pages/{id}/edit', [SocialFellowshipController::class, 'editArchivePage']);
    Route::get('/pages/{id}', [SocialFellowshipController::class, 'showArchivePage']);
});
});