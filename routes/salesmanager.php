<?php
use Illuminate\Support\Facades\Route;

Route::prefix('/')
    ->middleware('auth')
    ->group(function () {

       
     // CBM Sales report Manage Here
    //  Route::get('salesreport', [App\Http\Controllers\Sales\SalesreportController::class, 'index'])->name('sales.list.salesreport');
    //  Route::get('salesreportcreate', [App\Http\Controllers\Sales\SalesreportController::class, 'create'])->name('sales.create.salesreport');
    //  Route::post('salesreport/store', [App\Http\Controllers\Sales\SalesreportController::class, 'store'])->name('sales.store.salesreport');
    //  Route::get('salesreport/edit/{salesreport}', [App\Http\Controllers\Sales\SalesreportController::class, 'edit'])->name('sales.edit.salesreport');
    //  Route::put('salesreport/update/{salesreport}', [App\Http\Controllers\Sales\SalesreportController::class, 'update'])->name('sales.update.salesreport');
    //  Route::get('salesreport/show/{salesreport}', [App\Http\Controllers\Sales\SalesreportController::class, 'show'])->name('sales.show.salesreport');
    //  Route::delete('salesreport/delete/{salesreport}', [App\Http\Controllers\Sales\SalesreportController::class, 'delete'])->name('sales.delete.salesreport');


      
    });
