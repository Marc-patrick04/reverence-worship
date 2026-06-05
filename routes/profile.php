<?php

Route::get('/profile', function () {
    return view('profile.index');
})->name('profile.index');

Route::prefix('announcements')->group(function () {
    Route::get('/', function () {
        return view('modules.announcements.index');
    })->name('announcements.index');
});

Route::prefix('reports')->group(function () {
    Route::get('/', function () {
        return view('modules.reports.index');
    })->name('reports.index');
});