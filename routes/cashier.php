<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Cashier\InvoiceController;

/*
|--------------------------------------------------------------------------
| Cashier Routes
|--------------------------------------------------------------------------
|
| Các route dành cho Thu ngân: tra cứu, tạo và thanh toán hóa đơn.
|
*/

Route::middleware(['auth', 'role:ADMIN,CASHIER'])->group(function () {
    Route::prefix('invoices')->name('invoices.')->group(function () {
        Route::get('/', [InvoiceController::class, 'index'])->name('index');
        Route::get('/ajax/search', [InvoiceController::class, 'ajaxSearch'])->name('ajax.search');
        Route::get('/ajax/pending', [InvoiceController::class, 'ajaxPending'])->name('ajax.pending');
        Route::get('/{id}/edit', [InvoiceController::class, 'edit'])->name('edit');
        Route::put('/{id}', [InvoiceController::class, 'update'])->name('update');
        Route::get('/{id}/show', [InvoiceController::class, 'show'])->name('show');
        Route::put('/{id}/pay', [InvoiceController::class, 'pay'])->name('pay');
    });
});
