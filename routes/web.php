<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ReviewLikeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [BookController::class, 'index'])->name('books.index');

Route::middleware('auth')->group(function () {
    //書籍
    Route::resource('books', BookController::class)
        ->only(['create', 'store', 'edit', 'update', 'destroy']);

    //レビュー
    Route::post('books/{book}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::resource('reviews', ReviewController::class)
        ->only(['edit', 'update', 'destroy']);

    //ジャンル
    Route::resource('genres', GenreController::class);

    //お気に入り
    Route::get('favorites', [FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('books/{book}/favorites', [FavoriteController::class, 'toggle'])->name('favorites.toggle');

    //レビューいいね
    Route::post('reviews/{review}/like', [ReviewLikeController::class, 'toggle'])->name('reviews.like');
});

Route::get('books/{book}', [BookController::class, 'show'])->name('books.show');