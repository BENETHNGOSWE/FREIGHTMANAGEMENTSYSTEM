<?php
use Illuminate\Support\Facades\Route;

Route::prefix('rec')
    ->group(function () {

        Route::get('customer_list', [App\Http\Controllers\Reception\PublicController::class, 'reception_index'])->name('reception.list.customer');
        Route::get('load_list', [App\Http\Controllers\Reception\PublicController::class, 'loadpackage_index'])->name('reception.list.loadpackage');
        Route::get('invoice_list', [App\Http\Controllers\Reception\PublicController::class, 'invoice_index'])->name('reception.list.invoice');
        Route::get('customerlead_list', [App\Http\Controllers\Reception\PublicController::class, 'customerlead_index'])->name('reception.list.customerlead');
    });