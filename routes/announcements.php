<?php

use App\Http\Controllers\Announcement\AnnouncementController;

Route::prefix('announcements')->name('announcements.')->middleware('auth')->group(function () {
    Route::get('/', [AnnouncementController::class, 'index'])->name('index');
    Route::get('/filter', [AnnouncementController::class, 'filter'])->name('filter');
    Route::post('/store', [AnnouncementController::class, 'store'])->name('store');
    Route::put('/{id}', [AnnouncementController::class, 'update'])->name('update');
    Route::get('/{id}/edit', [AnnouncementController::class, 'edit'])->name('edit');
    Route::delete('/{id}', [AnnouncementController::class, 'destroy'])->name('destroy');
    Route::post('/{id}/toggle-status', [AnnouncementController::class, 'toggleStatus'])->name('toggle-status');
    Route::get('/stats', [AnnouncementController::class, 'getStats'])->name('stats');
});