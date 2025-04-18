<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\SocialAuthController;
use App\Http\Controllers\API\BookController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('user/register', [AuthController::class, 'register'])->name('user.register');
Route::post('user/login', [AuthController::class, 'login'])->name('user.login');

Route::middleware('auth:sanctum')->prefix('user')->group(function () {
    Route::get('logout', [AuthController::class, 'logout'])->name('user.logout');
    Route::get('profile', [AuthController::class, 'profile'])->name('user.profile');
    Route::put('update', [UserController::class, 'update'])->name('user.update');
});

Route::get('/auth/{provider}/redirect', [SocialAuthController::class, 'redirectToProvider']);
Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callbackProvider']);

Route::prefix('books')->group(function () {
    Route::get('/', [BookController::class, 'getNewestBooks'])->name('books.index');
    Route::get('/{slug}', [BookController::class, 'show'])->name('books.show');
});

Route::prefix('categories')->group(function () {
    Route::get('/', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/{slug}', [CategoryController::class, 'getBooksByCategory'])->name('categories.show');
});
