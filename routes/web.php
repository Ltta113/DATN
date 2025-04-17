<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\BookController;
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

    Route::prefix('books')->group(function () {
        Route::get('/', [BookController::class, 'index'])->name('admin.books.index');
        Route::get('/create', [BookController::class, 'create'])->name('admin.books.create');
        Route::post('/', [BookController::class, 'store'])->name('admin.books.store');
        Route::get('/{book}/edit', [BookController::class, 'edit'])->name('admin.books.edit');
        Route::put('/{book}', [BookController::class, 'update'])->name('admin.books.update');
        Route::delete('/{book}', [BookController::class, 'destroy'])->name('admin.books.destroy');
    });
});
