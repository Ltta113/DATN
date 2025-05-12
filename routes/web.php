<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\ComboController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PublisherController;
use App\Http\Controllers\UserController;
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
            Route::post('/{book}/apply-discount', [BookController::class, 'applyDiscount'])->name('admin.books.apply-discount');
            Route::post('/{book}/remove-discount', [BookController::class, 'removeDiscount'])->name('admin.books.remove-discount');
            Route::post('/{book}/change-status', [BookController::class, 'changeStatus'])->name('admin.books.change-status');
            Route::get('/{book}/edit', [BookController::class, 'edit'])->name('admin.books.edit');
            Route::put('/{book}', [BookController::class, 'update'])->name('admin.books.update');
            Route::delete('/{book}', [BookController::class, 'destroy'])->name('admin.books.destroy');
            Route::get('/{book}', [BookController::class, 'showByAdmin'])->name('admin.books.show');
            Route::delete('/delete-image/{public_id}', [BookController::class, 'deleteImage'])->name('admin.books.delete-image')->where('public_id', '.*');
        });

        Route::prefix('users')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('admin.users.index');
            Route::get('/{user}', [UserController::class, 'show'])->name('admin.users.show');
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

        Route::prefix('orders')->group(function () {
            Route::get('/', [OrderController::class, 'index'])->name('admin.orders.index');
            Route::post('/{order}/ship', [OrderController::class, 'shipped'])->name('admin.orders.ship');
            Route::post('/{order}/cancel', [OrderController::class, 'markOutOfStock'])->name('admin.orders.cancel');
            Route::post('/{order}/refund', [OrderController::class, 'refundOrder'])->name('admin.orders.refund');
            Route::get('/{order}', [OrderController::class, 'show'])->name('admin.orders.show');
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

        Route::prefix('authors')->group(function () {
            Route::get('/', [AuthorController::class, 'index'])->name('admin.authors.index');
            Route::get('/create', [AuthorController::class, 'create'])->name('admin.authors.create');
            Route::get('/{author}', [AuthorController::class, 'show'])->name('admin.authors.show');
            Route::post('/', [AuthorController::class, 'store'])->name('admin.authors.store');
            Route::get('/{author}/edit', [AuthorController::class, 'edit'])->name('admin.authors.edit');
            Route::put('/{author}', [AuthorController::class, 'update'])->name('admin.authors.update');
            Route::delete('/{author}', [AuthorController::class, 'destroy'])->name('admin.authors.destroy');
        });

        Route::prefix('discounts')->group(function () {
            Route::get('/', [DiscountController::class, 'index'])->name('admin.discounts.index');
            Route::get('/create', [DiscountController::class, 'create'])->name('admin.discounts.create');
            Route::post('/', [DiscountController::class, 'store'])->name('admin.discounts.store');
            Route::get('/{discount}', [DiscountController::class, 'show'])->name('admin.discounts.show');
            Route::get('/{discount}/edit', [DiscountController::class, 'edit'])->name('admin.discounts.edit');
            Route::put('/{discount}', [DiscountController::class, 'update'])->name('admin.discounts.update');
            Route::delete('/{discount}', [DiscountController::class, 'destroy'])->name('admin.discounts.destroy');
            Route::post('/{discount}/add-books', [DiscountController::class, 'assignToBooks'])->name('admin.discounts.add-books');
            Route::post('/{discount}/remove-books', [DiscountController::class, 'removeFromBooks'])->name('admin.discounts.remove-books');
            Route::get('/{discount}/books', [DiscountController::class, 'booksByDiscount'])->name('admin.discounts.books');
        });

        Route::prefix('combos')->group(function () {
            Route::get('/', [ComboController::class, 'index'])->name('admin.combos.index');
            Route::get('/create', [ComboController::class, 'create'])->name('admin.combos.create');
            Route::post('/', [ComboController::class, 'store'])->name('admin.combos.store');
            Route::get('/{combo}', [ComboController::class, 'show'])->name('admin.combos.show');
            Route::get('/{combo}/edit', [ComboController::class, 'edit'])->name('admin.combos.edit');
            Route::put('/{combo}', [ComboController::class, 'update'])->name('admin.combos.update');
            Route::delete('/{combo}', [ComboController::class, 'destroy'])->name('admin.combos.destroy');
            Route::post('/toggle-book', [ComboController::class, 'toggleBook'])->name('admin.combos.toggle-book');
            Route::post('/{id}/restore', [ComboController::class, 'restore'])->name('admin.combos.restore');
            Route::delete('/{id}/force-delete', [ComboController::class, 'forceDelete'])->name('admin.combos.force-delete');
        });
    });
});
