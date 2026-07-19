<?php

use App\Http\Controllers\AttendanceEntryController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function (): void {
    Route::get('/attendance-entry', [AttendanceEntryController::class, 'create'])
        ->name('attendance.entry.create');

    Route::post('/attendance-entry', [AttendanceEntryController::class, 'store'])
        ->middleware('throttle:30,1')
        ->name('attendance.entry.store');
});
