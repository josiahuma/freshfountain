<?php

namespace App\Providers;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class BackendAccessServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        require base_path('routes/backend-access.php');

        Event::listen(Login::class, function (Login $event): void {
            if (method_exists($event->user, 'forceFill')) {
                $event->user->forceFill([
                    'last_login_at' => now(),
                ])->saveQuietly();
            }
        });
    }
}
