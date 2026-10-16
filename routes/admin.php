<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\RolePermissionController;
use App\Http\Controllers\Admin\UserRoleController;
use App\Http\Controllers\Admin\WebsiteSettingsController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'can:admin.access'])->group(function (): void {
    Route::middleware('can:pages.manage')->group(function (): void {
        Route::get('/pages', [PageController::class, 'index'])->name('pages.index');
        Route::get('/pages/create', [PageController::class, 'create'])->name('pages.create');
        Route::post('/pages', [PageController::class, 'store'])->name('pages.store');
        Route::get('/pages/{page}/edit', [PageController::class, 'edit'])->name('pages.edit');
        Route::patch('/pages/{page}', [PageController::class, 'update'])->name('pages.update');
        Route::delete('/pages/{page}', [PageController::class, 'destroy'])->name('pages.destroy');
    });
    Route::middleware('can:media.manage')->group(function (): void {
        Route::get('/media', [MediaController::class, 'index'])->name('media.index');
        Route::post('/media', [MediaController::class, 'store'])->name('media.store');
        Route::patch('/media/{media}', [MediaController::class, 'update'])->name('media.update');
        Route::delete('/media/{media}', [MediaController::class, 'destroy'])->name('media.destroy');
    });
    Route::redirect('/', '/admin/dashboard');

    Route::get('/dashboard', DashboardController::class)
        ->name('dashboard');

    Route::get('/users', [UserRoleController::class, 'index'])
        ->middleware('can:users.view')
        ->name('users.index');

    Route::patch('/users/{user}/role', [UserRoleController::class, 'update'])
        ->middleware('can:users.assign_roles')
        ->name('users.role.update');

    Route::get('/roles', [RolePermissionController::class, 'index'])
        ->middleware('can:roles.view')
        ->name('roles.index');

    Route::patch('/roles/{role}/permissions', [RolePermissionController::class, 'update'])
        ->middleware('can:roles.manage_permissions')
        ->name('roles.permissions.update');

    Route::get('/settings', [WebsiteSettingsController::class, 'edit'])
        ->middleware('can:settings.manage')
        ->name('settings.edit');

    Route::patch('/settings', [WebsiteSettingsController::class, 'update'])
        ->middleware('can:settings.manage')
        ->name('settings.update');
});
