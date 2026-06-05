<?php

use App\Http\Controllers\Reports\ReportController;

Route::prefix('reports')->name('reports.')->middleware('auth')->group(function () {
    Route::get('/', [ReportController::class, 'index'])->name('index');
    
    // Report data endpoints
    Route::get('/action-plans', [ReportController::class, 'actionPlansReport'])->name('action-plans');
    Route::get('/discipline', [ReportController::class, 'disciplineReport'])->name('discipline');
    Route::get('/permission', [ReportController::class, 'permissionReport'])->name('permission');
    Route::get('/events', [ReportController::class, 'eventsReport'])->name('events');
    Route::get('/users', [ReportController::class, 'usersReport'])->name('users');
    Route::get('/attendance', [ReportController::class, 'attendanceReport'])->name('attendance');
    Route::get('/financial', [ReportController::class, 'financialReport'])->name('financial');
    Route::get('/forms', [ReportController::class, 'formsReport'])->name('forms');
    
    // Export
    Route::post('/export', [ReportController::class, 'export'])->name('export');
    
    // Event CRUD
    Route::post('/events/store', [ReportController::class, 'storeEvent'])->name('events.store');
    Route::get('/events/{id}', [ReportController::class, 'showEvent'])->name('events.show');
    Route::put('/events/{id}', [ReportController::class, 'updateEvent'])->name('events.update');
    Route::delete('/events/{id}', [ReportController::class, 'deleteEvent'])->name('events.delete');
});