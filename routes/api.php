<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\BookController;
use App\Http\Controllers\Admin\CartController;
use App\Http\Controllers\Admin\LoanController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\TransactionController;

Route::get('/books', [BookController::class, 'indexAPI'])->name('api.book');
Route::get('/books/{id}', [BookController::class, 'showAPI'])->name('api.book.show');
Route::post('/books/{book}/borrow', [LoanController::class, 'store'])->name('api.loans.store');
Route::post('/orders', [OrderController::class, 'store'])->name('api.orders.store');
Route::post('/orders/{order}/payments', [PaymentController::class, 'create'])->name('api.payments.create');
Route::post('/orders/{order}/payments/confirm', [PaymentController::class, 'confirm'])->name('api.payments.confirm');
Route::post('/orders/{order}/payments/cancel', [PaymentController::class, 'cancel'])->name('api.payments.cancel');
Route::prefix('users/{user}')->group(function () {
    // Riwayat pembelian saja
    Route::get('/orders', [TransactionController::class, 'orders'])->name('api.users.orders');

    // Riwayat peminjaman saja
    Route::get('/loans', [TransactionController::class, 'loans'])->name('api.users.loans');

    // Riwayat gabungan (orders + loans)
    Route::get('/transactions', [TransactionController::class, 'transactions'])->name('api.users.transactions');
});
Route::prefix('cart')->group(function () {
    Route::get('/',                 [CartController::class, 'show']);        // lihat keranjang
    Route::post('/items',           [CartController::class, 'addItem']);     // tambah/merge item
    Route::put('/items/{item}',     [CartController::class, 'updateItem']);  // update qty
    Route::delete('/items/{item}',  [CartController::class, 'removeItem']);  // hapus item
    Route::post('/clear',           [CartController::class, 'clear']);       // kosongkan
});
