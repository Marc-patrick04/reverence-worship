<?php

use App\Http\Controllers\Family\FamilyController;

Route::prefix('family')->group(function () {
    Route::get('/', [FamilyController::class, 'index'])->name('family.index');
    Route::put('/task/{id}/status', [FamilyController::class, 'updateTaskStatus'])->name('family.task.status');
    Route::get('/member/{id}/details', [FamilyController::class, 'getMemberDetails'])->name('family.member.details');
});

Route::get('/my-family/member/{userId}/details', [FamilyController::class, 'getMemberDetails']);
Route::put('/my-family/task/{taskId}/status', [FamilyController::class, 'updateTaskStatus']);