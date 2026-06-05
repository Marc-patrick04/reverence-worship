<?php

use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PageAssignmentController;
use App\Http\Controllers\Admin\PagesController;
use App\Http\Controllers\Admin\PermissionManagerController;
use App\Http\Controllers\Reports\LogController;
use App\Http\Controllers\Settings\SettingController;
use App\Http\Controllers\ModuleAssignmentController;

Route::middleware('auth')->group(function () {
    // Role Management
    Route::prefix('roles')->name('roles.')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->name('index');
        Route::get('/create', [RoleController::class, 'create'])->name('create');
        Route::post('/store', [RoleController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [RoleController::class, 'edit'])->name('edit');
        Route::put('/{id}', [RoleController::class, 'update'])->name('update');
        Route::delete('/{id}', [RoleController::class, 'destroy'])->name('destroy');
        Route::get('/{id}', [RoleController::class, 'show'])->name('show');
    });
    
    // Page Assignment
    Route::prefix('page-assignment')->name('page-assignment.')->group(function () {
        Route::get('/', [PageAssignmentController::class, 'index'])->name('index');
        Route::get('/role/{roleId}/pages', [PageAssignmentController::class, 'getRolePages']);
        Route::get('/role/{roleId}/page/{pageId}/features', [PageAssignmentController::class, 'getRolePageFeatures']);
        Route::get('/role/{roleId}/features', [PageAssignmentController::class, 'getAssignedFeatures']);
        Route::post('/save', [PageAssignmentController::class, 'saveAssignments']);
    });
    
    // Pages Management
    Route::prefix('pages')->name('pages.')->group(function () {
        Route::get('/', [PagesController::class, 'index'])->name('index');
        Route::get('/create', [PagesController::class, 'create'])->name('create');
        Route::post('/store', [PagesController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [PagesController::class, 'edit'])->name('edit');
        Route::put('/{id}', [PagesController::class, 'update'])->name('update');
        Route::delete('/{id}', [PagesController::class, 'destroy'])->name('destroy');
        Route::get('/{pageId}/features', [PagesController::class, 'features'])->name('features');
        Route::post('/{pageId}/features/store', [PagesController::class, 'storeFeature'])->name('features.store');
        Route::get('/{pageId}/features/{featureId}/edit', [PagesController::class, 'editFeature'])->name('features.edit');
        Route::put('/{pageId}/features/{featureId}', [PagesController::class, 'updateFeature'])->name('features.update');
        Route::delete('/{pageId}/features/{featureId}', [PagesController::class, 'destroyFeature'])->name('features.destroy');
    });
    
    // Permission Manager
    Route::prefix('permission-manager')->name('permission-manager.')->group(function () {
        Route::get('/', [PermissionManagerController::class, 'index'])->name('index');
        Route::post('/page/store', [PermissionManagerController::class, 'storePage'])->name('page.store');
        Route::put('/page/{id}', [PermissionManagerController::class, 'updatePage'])->name('page.update');
        Route::get('/page/{id}/delete', [PermissionManagerController::class, 'deletePage'])->name('page.delete');
        Route::post('/feature/store', [PermissionManagerController::class, 'storeFeature'])->name('feature.store');
        Route::put('/feature/{id}', [PermissionManagerController::class, 'updateFeature'])->name('feature.update');
        Route::get('/feature/{id}/delete', [PermissionManagerController::class, 'deleteFeature'])->name('feature.delete');
    });
    
    // Module Assignment
    Route::prefix('module-assignment')->name('module-assignment.')->group(function () {
        Route::get('/', [ModuleAssignmentController::class, 'index'])->name('index');
        Route::post('/assign', [ModuleAssignmentController::class, 'assignModules'])->name('assign');
        Route::get('/user/{id}/modules', [ModuleAssignmentController::class, 'getUserModules'])->name('user-modules');
        Route::post('/remove', [ModuleAssignmentController::class, 'removeModule'])->name('remove');
    });
    
    // System Logs
    Route::prefix('logs')->name('logs.')->group(function () {
        Route::get('/activity', [LogController::class, 'activityLogs'])->name('activity');
        Route::get('/errors', [LogController::class, 'errorLogs'])->name('errors');
        Route::get('/activity/{id}', [LogController::class, 'viewActivity'])->name('view-activity');
        Route::get('/error/{id}', [LogController::class, 'viewError'])->name('view-error');
        Route::get('/clear-activity', [LogController::class, 'clearActivityLogs'])->name('clear.activity');
        Route::get('/clear-errors', [LogController::class, 'clearErrorLogs'])->name('clear.errors');
        Route::get('/export-activity', [LogController::class, 'exportActivityLogs'])->name('export.activity');
    });
    
    // System Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingController::class, 'index'])->name('index');
        Route::post('/general', [SettingController::class, 'updateGeneral'])->name('update.general');
        Route::post('/email', [SettingController::class, 'updateEmail'])->name('update.email');
        Route::post('/security', [SettingController::class, 'updateSecurity'])->name('update.security');
        Route::post('/clear-cache', [SettingController::class, 'clearCache'])->name('clear-cache');
        Route::post('/backup', [SettingController::class, 'backupDatabase'])->name('backup');
    });
});