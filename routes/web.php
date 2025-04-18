<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\PublisherController;
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
        Route::prefix('books')->group(function () {
            Route::get('/', [BookController::class, 'index'])->name('admin.books.index');
            Route::get('/create', [BookController::class, 'create'])->name('admin.books.create');
            Route::post('/', [BookController::class, 'store'])->name('admin.books.store');
            Route::post('/{book}/change-status', [BookController::class, 'changeStatus'])->name('admin.books.change-status');
            Route::get('/{book}/edit', [BookController::class, 'edit'])->name('admin.books.edit');
            Route::put('/{book}', [BookController::class, 'update'])->name('admin.books.update');
            Route::delete('/{book}', [BookController::class, 'destroy'])->name('admin.books.destroy');
            Route::get('/{book}', [BookController::class, 'showByAdmin'])->name('admin.books.show');
        });

        Route::prefix('categories')->group(function () {
            Route::get('/', [CategoryController::class, 'index'])->name('admin.categories.index');
            Route::get('/create', [CategoryController::class, 'create'])->name('admin.categories.create');
            Route::get('/{category}', [CategoryController::class, 'show'])->name('admin.categories.show');
            Route::post('/', [CategoryController::class, 'store'])->name('admin.categories.store');
            Route::get('/{category}/edit', [CategoryController::class, 'edit'])->name('admin.categories.edit');
            Route::put('/{category}', [CategoryController::class, 'update'])->name('admin.categories.update');
            Route::delete('/{category}', [CategoryController::class, 'destroy'])->name('admin.categories.destroy');
        });

        Route::prefix('publishers')->group(function () {
            Route::get('/', [PublisherController::class, 'index'])->name('admin.publishers.index');
            Route::get('/create', [PublisherController::class, 'create'])->name('admin.publishers.create');
            Route::post('/', [PublisherController::class, 'store'])->name('admin.publishers.store');
            Route::get('/{publisher}', [PublisherController::class, 'show'])->name('admin.publishers.show');
            Route::get('/{publisher}/edit', [PublisherController::class, 'edit'])->name('admin.publishers.edit');
            Route::put('/{publisher}', [PublisherController::class, 'update'])->name('admin.publishers.update');
            Route::delete('/{publisher}', [PublisherController::class, 'destroy'])->name('admin.publishers.destroy');
        });
    });
});
