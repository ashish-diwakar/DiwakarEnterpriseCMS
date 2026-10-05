<?php

namespace App\Providers;

use App\Models\User;
use App\Support\Rbac;
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
        Gate::before(function (User $user): ?bool {
            return $user->hasRole(Rbac::ROLE_SUPER_ADMIN) ? true : null;
        });

        Gate::define('super-admin.manage', fn (User $user): bool => false);
    }
}
