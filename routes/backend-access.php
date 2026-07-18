<?php

use App\Http\Controllers\Access\BackendInvitationController;
use Illuminate\Support\Facades\Route;

Route::middleware('web')
    ->prefix('setup-account')
    ->name('backend-invitation.')
    ->group(function (): void {
        Route::get('/{token}', [BackendInvitationController::class, 'show'])
            ->name('show');

        Route::post('/{token}', [BackendInvitationController::class, 'update'])
            ->name('update');
    });
