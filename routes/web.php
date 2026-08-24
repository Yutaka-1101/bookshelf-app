<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;

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
    Route::resource('books', BookController::class)
        ->only(['create', 'store', 'edit', 'update', 'destroy']);
});

Route::get('books/{book}', [BookController::class, 'show'])->name('books.show');