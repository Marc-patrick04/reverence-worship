<?php

use App\Http\Controllers\Parent\ParentController;
use Illuminate\Support\Facades\Route;

// Redirect from /parent to /parent/dashboard
Route::get('/parent', function () {
    return redirect()->route('parent.dashboard');
})->middleware('auth')->name('parent.index');

// Parent Routes
Route::prefix('parent')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', [ParentController::class, 'index'])->name('parent.dashboard');
    
    // Child routes
    Route::get('/child/{id}/details', [ParentController::class, 'getChildDetails'])->name('parent.child.details');
    Route::get('/child/{id}/financial', [ParentController::class, 'getChildFinancialReport'])->name('parent.child.financial');
    
    // Task routes
    Route::get('/tasks', [ParentController::class, 'getTasks'])->name('parent.tasks');
    Route::post('/tasks', [ParentController::class, 'storeTask'])->name('parent.tasks.store');
    Route::get('/tasks/{id}/edit', [ParentController::class, 'editTask'])->name('parent.tasks.edit');
    Route::post('/tasks/{id}', [ParentController::class, 'updateTask'])->name('parent.tasks.update');
    Route::delete('/tasks/{id}', [ParentController::class, 'deleteTask'])->name('parent.tasks.delete');
    Route::post('/tasks/{id}/complete', [ParentController::class, 'completeTask'])->name('parent.tasks.complete');
    
    // Contribution routes
    Route::get('/contributions/children', [ParentController::class, 'getChildrenContributions'])->name('parent.contributions.children');
    Route::get('/contributions/{userId}/details', [ParentController::class, 'getChildContributionDetails'])->name('parent.contributions.details');
    
});
// Parent Routes
Route::prefix('parent')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', [ParentController::class, 'index'])->name('parent.dashboard');
    
    // Child routes
    Route::get('/child/{id}/details', [ParentController::class, 'getChildDetails'])->name('parent.child.details');
    Route::get('/child/{id}/financial', [ParentController::class, 'getChildFinancialReport'])->name('parent.child.financial');
    
    // Task routes
    Route::get('/tasks', [ParentController::class, 'getTasks'])->name('parent.tasks');
    Route::post('/tasks', [ParentController::class, 'storeTask'])->name('parent.tasks.store');
    Route::get('/tasks/{id}/edit', [ParentController::class, 'editTask'])->name('parent.tasks.edit');
    Route::post('/tasks/{id}', [ParentController::class, 'updateTask'])->name('parent.tasks.update');
    Route::delete('/tasks/{id}', [ParentController::class, 'deleteTask'])->name('parent.tasks.delete');
    
    // Subtask routes
    Route::post('/subtasks/{id}/toggle', [ParentController::class, 'toggleSubtask'])->name('parent.subtasks.toggle');
    
    // Contribution routes
    Route::get('/contributions/children', [ParentController::class, 'getChildrenContributions'])->name('parent.contributions.children');
    Route::get('/contributions/{userId}/details', [ParentController::class, 'getChildContributionDetails'])->name('parent.contributions.details');
});