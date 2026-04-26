<?php

namespace App\Providers;

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
        \Illuminate\Support\Facades\Gate::define('view-sensitive-data', function (\App\Models\User $user) {
            return in_array($user->role, ['admin', 'manager']);
        });

        // Contoh gate tambahan untuk otorisasi spesifik lainnya
        \Illuminate\Support\Facades\Gate::define('manage-users', function (\App\Models\User $user) {
            return $user->role === 'admin';
        });
    }
}
