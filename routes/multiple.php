<?php
use Illuminate\Support\Facades\Route;

Route::prefix('/')
    ->middleware('auth')
    ->group(function () {
        
        // Manage loadpackagetype Here
        Route::get('loadtype', [App\Http\Controllers\Admin\LoadpackagetypeController::class, 'index'])->name('admin.list.loadpackagetpye');
        Route::get('loadtypecreate', [App\Http\Controllers\Admin\LoadpackagetypeController::class, 'create'])->name('admin.create.loadpackagetpye');
        Route::post('loadtype/store', [App\Http\Controllers\Admin\LoadpackagetypeController::class, 'store'])->name('admin.store.loadpackagetpye');
        Route::get('loadtype/edit/{loadpackagetype}', [App\Http\Controllers\Admin\LoadpackagetypeController::class, 'edit'])->name('admin.edit.loadpackagetpye');
        Route::put('loadtype/update/{loadpackagetype}', [App\Http\Controllers\Admin\LoadpackagetypeController::class, 'update'])->name('admin.update.loadpackagetpye');
        Route::get('loadtype/show/{loadpackagetype}', [App\Http\Controllers\Admin\LoadpackagetypeController::class, 'show'])->name('admin.show.loadpackagetpye');
        Route::delete('loadtype/delete/{loadpackagetype}', [App\Http\Controllers\Admin\LoadpackagetypeController::class, 'delete'])->name('admin.delete.loadpackagetpye');

        // Manage loadpackage Here
        Route::get('loadpackage', [App\Http\Controllers\Admin\LoadpackageController::class, 'index'])->name('admin.list.loadpackage');
        Route::get('loadpackagecreate', [App\Http\Controllers\Admin\LoadpackageController::class, 'create'])->name('admin.create.loadpackage');
        Route::post('loadpackage/store', [App\Http\Controllers\Admin\LoadpackageController::class, 'store'])->name('admin.store.loadpackage');
        Route::get('loadpackage/edit/{loadpackage}', [App\Http\Controllers\Admin\LoadpackageController::class, 'edit'])->name('admin.edit.loadpackage');
        Route::put('loadpackage/update/{loadpackage}', [App\Http\Controllers\Admin\LoadpackageController::class, 'update'])->name('admin.update.loadpackage');
        Route::get('loadpackage/show/{loadpackage}', [App\Http\Controllers\Admin\LoadpackageController::class, 'show'])->name('admin.show.loadpackage');
        Route::delete('loadpackage/delete/{loadpackage}', [App\Http\Controllers\Admin\LoadpackageController::class, 'delete'])->name('admin.delete.loadpackage');

        // Tracking Manage Here
        Route::get('loadtracking', [App\Http\Controllers\Admin\LoadpackageController::class, 'loadtrackingpage'])->name('admin.loadtrackingpage.loadpackage');
        Route::post('tracking/load', [App\Http\Controllers\Admin\LoadpackageController::class, 'loadtracking'])->name('admin.loadtracking.loadpackage');

        // Manage Customer Here
        Route::get('customer', [App\Http\Controllers\Admin\CustomerController::class, 'index'])->name('admin.list.customer');
        Route::get('customercreate', [App\Http\Controllers\Admin\CustomerController::class, 'create'])->name('admin.create.customer');
        Route::post('customer/store', [App\Http\Controllers\Admin\CustomerController::class, 'store'])->name('admin.store.customer');
        Route::get('customer/edit/{customer}', [App\Http\Controllers\Admin\CustomerController::class, 'edit'])->name('admin.edit.customer');
        Route::put('customer/update/{customer}', [App\Http\Controllers\Admin\CustomerController::class, 'update'])->name('admin.update.customer');
        Route::get('customer/show/{customer}', [App\Http\Controllers\Admin\CustomerController::class, 'show'])->name('admin.show.customer');
        Route::delete('customer/delete/{customer}', [App\Http\Controllers\Admin\CustomerController::class, 'delete'])->name('admin.delete.customer');

        // Invoices Manage Here
        Route::get('invoicelist', [App\Http\Controllers\Admin\InvoiceController::class, 'index'])->name('admin.list.invoice');
        Route::get('invoiceshow/{invoice}', [App\Http\Controllers\Admin\InvoiceController::class, 'show'])->name('admin.show.invoice');
        Route::get('invoice/edit/{invoice}', [App\Http\Controllers\Admin\InvoiceController::class, 'edit'])->name('admin.edit.invoice');
        Route::put('invoice/update/{invoice}', [App\Http\Controllers\Admin\InvoiceController::class, 'update'])->name('admin.update.invoice');
        Route::get('invoice/pdf/{invoice}', [App\Http\Controllers\Admin\InvoiceController::class, 'generateInvoicePDF'])->name('admin.pdf.invoice');

          // Payment Manage Here
          Route::get('payment', [App\Http\Controllers\Admin\PaymentController::class, 'index'])->name('admin.list.payment');
          Route::get('paymentcreate', [App\Http\Controllers\Admin\PaymentController::class, 'create'])->name('admin.create.payment');
          Route::post('payment/store', [App\Http\Controllers\Admin\PaymentController::class, 'store'])->name('admin.store.payment');
          Route::get('payment/edit/{payment}', [App\Http\Controllers\Admin\PaymentController::class, 'edit'])->name('admin.edit.payment');
          Route::put('payment/update/{payment}', [App\Http\Controllers\Admin\PaymentController::class, 'update'])->name('admin.update.payment');
          Route::get('payment/show/{payment}', [App\Http\Controllers\Admin\PaymentController::class, 'show'])->name('admin.show.payment');
          Route::delete('payment/delete/{payment}', [App\Http\Controllers\Admin\PaymentController::class, 'delete'])->name('admin.delete.payment');


            // Unity Type Manage Here
        Route::get('unittype', [App\Http\Controllers\Admin\UnittypeController::class, 'index'])->name('admin.list.unittype');
        Route::get('unittypecreate', [App\Http\Controllers\Admin\UnittypeController::class, 'create'])->name('admin.create.unittype');
        Route::post('unittype/store', [App\Http\Controllers\Admin\UnittypeController::class, 'store'])->name('admin.store.unittype');
        Route::get('unittype/edit/{unittype}', [App\Http\Controllers\Admin\UnittypeController::class, 'edit'])->name('admin.edit.unittype');
        Route::put('unittype/update/{unittype}', [App\Http\Controllers\Admin\UnittypeController::class, 'update'])->name('admin.update.unittype');
        Route::get('unittype/show/{unittype}', [App\Http\Controllers\Admin\UnittypeController::class, 'show'])->name('admin.show.unittype');
        Route::delete('unittype/delete/{unittype}', [App\Http\Controllers\Admin\UnittypeController::class, 'delete'])->name('admin.delete.unittype');
    });
