<?php

use App\Http\Controllers\UserController;

Route::middleware('auth')->prefix('users')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('users.index');
    Route::get('/export', [UserController::class, 'export'])->name('users.export');
    Route::get('/export-pdf', [UserController::class, 'exportPdf'])->name('users.export-pdf');
    Route::get('/create-form', [UserController::class, 'getCreateForm'])->name('users.create-form');
    Route::post('/', [UserController::class, 'store'])->name('users.store');
    Route::get('/{id}/json', [UserController::class, 'getUserJson'])->name('users.json');
    Route::get('/{id}/edit-form', [UserController::class, 'getEditForm'])->name('users.edit-form');
    Route::get('/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/{id}', [UserController::class, 'update'])->name('users.update');
    Route::get('/{id}/roles/edit', [UserController::class, 'getEditRolesForm'])->name('users.roles.edit');
    Route::put('/{id}/roles', [UserController::class, 'updateRoles'])->name('users.roles.update');
    Route::post('/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::delete('/{id}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::get('/{id}', [UserController::class, 'show'])->name('users.show');


    // Add these routes inside the auth middleware group
Route::post('/users/{id}/approve', [UserController::class, 'approve'])->name('users.approve');
Route::post('/users/{id}/activate', [UserController::class, 'activate'])->name('users.activate');
Route::post('/users/{id}/deactivate', [UserController::class, 'deactivate'])->name('users.deactivate');
Route::get('/{id}/export-pdf', [UserController::class, 'exportSingleUserPdf'])->name('users.export-pdf');
    
});

Route::put('/users/{user}/roles', [UserController::class, 'updateRoles'])->name('users.update-roles');