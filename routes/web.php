<?php

use App\Http\Controllers\AdminController;
use App\Http\Middleware\AdminAuth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin/dashboard');
})->name('home');

Route::prefix('admin')->group(function () {
    Route::get('/login', [AdminController::class, 'showLoginForm'])->name('admin.login');

    Route::post('/login', [AdminController::class, 'login'])->name('admin.login.submit');

    Route::post('/logout', [AdminController::class, 'logout'])->name('admin.logout');

    Route::middleware(AdminAuth::class)->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    });
});
