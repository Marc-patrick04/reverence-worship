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
    Route::post('/{id}/resend-emails', [AnnouncementController::class, 'resendEmails'])->name('resend-emails');
    Route::get('/{id}/recipients', [AnnouncementController::class, 'getRecipients'])->name('recipients');
Route::post('/recipients/batch', [AnnouncementController::class, 'getBatchRecipients'])->name('recipients.batch');
Route::post('/{id}/resend', [AnnouncementController::class, 'resend'])->name('resend');
Route::post('/{id}/duplicate', [AnnouncementController::class, 'duplicate'])->name('duplicate');
    // API routes for target selection
    Route::get('/roles', [AnnouncementController::class, 'getRoles'])->name('roles');
    Route::get('/users', [AnnouncementController::class, 'getUsers'])->name('users');
    Route::post('/roles/batch', [AnnouncementController::class, 'getRolesBatch'])->name('roles.batch');
});