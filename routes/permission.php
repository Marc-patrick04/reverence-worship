<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\PermissionManagerController;

// ==================== PERMISSION MANAGER ROUTES ====================
Route::prefix('permission-manager')->name('permission-manager.')->middleware('auth')->group(function () {
    // Main view
    Route::get('/', [PermissionManagerController::class, 'index'])->name('index');
    
    // ==================== PAGE CRUD ROUTES ====================
    Route::post('/page/store', [PermissionManagerController::class, 'storePage'])->name('page.store');
    Route::get('/page/{id}/edit', [PermissionManagerController::class, 'editPage'])->name('page.edit');
    Route::put('/page/{id}', [PermissionManagerController::class, 'updatePage'])->name('page.update');
    Route::delete('/page/{id}', [PermissionManagerController::class, 'deletePage'])->name('page.delete');
    
    // ==================== FEATURE CRUD ROUTES ====================
    Route::post('/feature/store', [PermissionManagerController::class, 'storeFeature'])->name('feature.store');
    Route::get('/feature/{id}/edit', [PermissionManagerController::class, 'editFeature'])->name('feature.edit');
    Route::put('/feature/{id}', [PermissionManagerController::class, 'updateFeature'])->name('feature.update');
    Route::delete('/feature/{id}', [PermissionManagerController::class, 'deleteFeature'])->name('feature.delete');
    
    // ==================== ROLE CRUD ROUTES ====================
    Route::post('/role/store', [PermissionManagerController::class, 'storeRole'])->name('role.store');
    Route::get('/role/{id}/edit', [PermissionManagerController::class, 'editRole'])->name('role.edit');
    Route::put('/role/{id}', [PermissionManagerController::class, 'updateRole'])->name('role.update');
    Route::delete('/role/{id}', [PermissionManagerController::class, 'deleteRole'])->name('role.delete');
    
    // ==================== MODULE ASSIGNMENT (USER MODULES) ====================
    Route::get('/user/{userId}/modules', [PermissionManagerController::class, 'getUserModules'])->name('user.modules');
    Route::post('/assign-modules', [PermissionManagerController::class, 'assignModules'])->name('assign.modules');
    
    // ==================== PAGE ASSIGNMENT (ROLE PERMISSIONS) ====================
    Route::post('/save-assignments', [PermissionManagerController::class, 'savePageAssignments'])->name('save.assignments');
});