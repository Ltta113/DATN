<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\BookBookmarkController;
use App\Http\Controllers\API\SocialAuthController;
use App\Http\Controllers\API\BookController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\ComboController;
use App\Http\Controllers\API\DiscountController;
use App\Http\Controllers\API\NotificationController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\ReviewController;
use App\Http\Controllers\API\TransactionController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\ZaloPayController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('user/register', [AuthController::class, 'register'])->name('user.register');
Route::post('user/login', [AuthController::class, 'login'])->name('user.login');

Route::post('/books/{book}/bookmark', [BookBookmarkController::class, 'toggleBookmark'])->middleware('auth:sanctum')->name('books.bookmark');
Route::middleware('auth:sanctum')->prefix('user')->group(function () {
    Route::get('logout', [AuthController::class, 'logout'])->name('user.logout');
    Route::get('profile', [UserController::class, 'getUserInfo'])->name('user.profile');
    Route::put('update', [UserController::class, 'updateUserInfo'])->name('user.update');
    Route::post('avatar', [UserController::class, 'avatarManager'])->name('user.avatar');

    Route::get('dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    Route::prefix('reviews')->group(function () {
        Route::post('/', [ReviewController::class, 'store'])->name('reviews.store');
        Route::delete('/', [ReviewController::class, 'destroy'])->name('reviews.destroy');
        Route::put('/{review}', [ReviewController::class, 'update'])->name('reviews.update');
    });

    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('user.notifications');
        Route::patch('/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('user.notifications.markAllAsRead');
        Route::patch('/{notification}', [NotificationController::class, 'markAsRead'])->name('user.notifications.markAsRead');
    });

    Route::prefix('orders')->group(function () {
        Route::get('/', [OrderController::class, 'getOrders'])->name('orders.index');
        Route::post('/', [OrderController::class, 'checkOrder'])->name('orders.checkOrder');
        Route::post('/create', [OrderController::class, 'createOrder'])->name('orders.createOrder');
        Route::post('/{order}/cancel', [OrderController::class, 'cancelOrder'])->name('orders.cancelOrder');
        Route::post('/{order}/receive', [OrderController::class, 'receivedOrder'])->name('orders.receiveOrder');
        Route::post('/{order}/refund', [OrderController::class, 'needRefundOrder'])->name('orders.refundOrder');
        Route::post('/{order}/complete', [OrderController::class, 'completeOrder'])->name('orders.completeOrder');
        Route::get('/{order}', [OrderController::class, 'getOrderDetail'])->name('orders.getOrderDetail');
    });

    Route::prefix('transactions')->group(function () {
        Route::get('/', [TransactionController::class, 'getTransactions'])->name('transactions.index');
        Route::post('/deposit', [TransactionController::class, 'deposit'])->name('transactions.deposit');
        Route::post('/withdraw', [TransactionController::class, 'withdraw'])->name('transactions.withdraw');
    });

    Route::get('/bookmarks', [BookBookmarkController::class, 'getListBookmarks'])->name('user.bookmarks');
});

Route::post('user/transactions/callback', [TransactionController::class, 'callbackDeposite'])->name('transactions.updateStatus');
Route::post('user/orders/callback', [OrderController::class, 'callbackOrder'])->name('orders.updateStatus');

Route::get('/auth/{provider}/redirect', [SocialAuthController::class, 'redirectToProvider']);
Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callbackProvider']);

Route::prefix('books')->group(function () {
    Route::get('/', [BookController::class, 'getNewestBooks'])->name('books.index');
    Route::get('/search', [BookController::class, 'search'])->name('books.search');
    Route::get('/best-sold', [BookController::class, 'getListBestSoldBooks'])->name('books.best-sold');
    Route::get('/{slug}', [BookController::class, 'show'])->name('books.show');
});

Route::prefix('categories')->group(function () {
    Route::get('/', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/{slug}', [CategoryController::class, 'getBooksByCategory'])->name('categories.show');
});

Route::prefix('authors')->group(function () {
    Route::get('/', [\App\Http\Controllers\API\AuthorController::class, 'index'])->name('authors.index');
    Route::get('/top', [\App\Http\Controllers\API\AuthorController::class, 'getAuthorManyBooks'])->name('authors.top');
    Route::get('/{slug}', [\App\Http\Controllers\API\AuthorController::class, 'show'])->name('authors.show');
});

Route::prefix('discounts')->group(function () {
    Route::get('/', [DiscountController::class, 'getAllDiscountsWithProducts'])->name('discounts.index');
    Route::get('/{discount}', [DiscountController::class, 'show'])->name('discounts.show');
});

Route::prefix('combos')->group(function () {
    Route::get('/', [ComboController::class, 'getListCombos'])->name('combos.index');
    Route::get('/best-sold', [ComboController::class, 'getBestSoldCombos'])->name('combos.best-sold');
    Route::get('/best-sold-this-month', [ComboController::class, 'getBestSoldCombosThisMonth'])->name('combos.best-sold-this-month');
    Route::get('/{slug}', [ComboController::class, 'getComboDetail'])->name('combos.show');
});
