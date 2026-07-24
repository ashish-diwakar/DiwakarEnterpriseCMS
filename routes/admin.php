<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::redirect('/', '/admin/dashboard');

Route::get('/dashboard', function () {
    return view('admin.dashboard.index');
})->name('dashboard');