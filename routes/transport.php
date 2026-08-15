<?php

use App\Http\Controllers\TransportBookingController;
use Illuminate\Support\Facades\Route;

Route::prefix('transport')
    ->name('transport.')
    ->group(function (): void {
        Route::get('/', [TransportBookingController::class, 'index'])
            ->name('index');

        Route::get('/book/{pickupEvent}', [TransportBookingController::class, 'create'])
            ->name('book');

        Route::post('/book/{pickupEvent}', [TransportBookingController::class, 'store'])
            ->middleware('throttle:20,1')
            ->name('store');

        Route::get('/confirmation', [TransportBookingController::class, 'confirmation'])
            ->name('confirmation');
    });
