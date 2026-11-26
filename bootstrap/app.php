<?php

use App\Console\Commands\InitializeWebsiteAddresses;
use App\Console\Commands\InitializeWebsiteContacts;
use App\Console\Commands\ProvisionSuperAdmin;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {

            Route::middleware('web')
                ->group(base_path('routes/frontend.php'));

            Route::middleware(['web'])
                ->prefix('admin')
                ->name('admin.')
                ->group(base_path('routes/admin.php'));
        },
    )
    ->withCommands([
        ProvisionSuperAdmin::class,
        InitializeWebsiteContacts::class,
        InitializeWebsiteAddresses::class,
    ])
    ->withMiddleware(function (Middleware $middleware): void {
        // Contact validation must see controls instead of silently trimming them away.
        $middleware->trimStrings(except: ['emails.*.value', 'phones.*.value', 'addresses.*.*', 'addresses.*.emails.*.value', 'addresses.*.phones.*.value']);
        $middleware->redirectUsersTo(fn (): string => route('admin.dashboard', absolute: false));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
