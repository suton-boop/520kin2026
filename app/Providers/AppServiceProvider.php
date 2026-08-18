<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        // Implicitly grant "Super Admin" role all permissions
        // This ensures admins can process everything in emergency situations
        Gate::before(function ($user, $ability) {
            return $user->hasRole(['admin', 'super-admin', 'superadmin']) ? true : null;
        });
    }
}
