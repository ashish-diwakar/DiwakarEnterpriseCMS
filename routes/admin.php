<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::get('/dashboard', function () {
    return 'Admin Dashboard';
})->name('dashboard');