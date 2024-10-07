<?php
use Illuminate\Support\Facades\Route;

Route::prefix('/')
    ->middleware('auth')
    ->group(function () {

        // Manage Clients Here
        // Route::get('index',[App\Http\Controllers\Admin\StaffController::class, 'list_staffs'])->name('admin.list.staff');
        Route::get('staff',[App\Http\Controllers\Admin\StaffController::class, 'list_staffs'])->name('admin.list.staff');
        Route::get('staffcreate',[App\Http\Controllers\Admin\StaffController::class, 'create'])->name('admin.create.staff');
        Route::post('staffstore',[App\Http\Controllers\Admin\StaffController::class, 'store'])->name('admin.store.staff');
        Route::get('staff-edit-staff/{staff}', [App\Http\Controllers\Admin\StaffController::class, 'edit'])->name('admin.edit.staff');
        Route::put('staff-update-staff/{staff}', [App\Http\Controllers\Admin\StaffController::class, 'update'])->name('admin.update.staff');
        Route::get('staff-show-staff/{staff}', [App\Http\Controllers\Admin\StaffController::class, 'show'])->name('admin.show.staff');
        Route::delete('staff-delete-staff/{staff}', [App\Http\Controllers\Admin\StaffController::class, 'delete'])->name('admin.delete.staff');


        // Manage loadpackagetype Here
        Route::get('loadtype',[App\Http\Controllers\Admin\LoadpackagetypeController::class, 'index'])->name('admin.list.loadpackagetpye');
        Route::get('loadtypecreate',[App\Http\Controllers\Admin\LoadpackagetypeController::class, 'create'])->name('admin.create.loadpackagetpye');
        Route::post('loadtype/store',[App\Http\Controllers\Admin\LoadpackagetypeController::class, 'store'])->name('admin.store.loadpackagetpye');
        Route::get('loadtype/edit/{loadpackagetype}',[App\Http\Controllers\Admin\LoadpackagetypeController::class, 'edit'])->name('admin.edit.loadpackagetpye');
        Route::put('loadtype/update/{loadpackagetype}',[App\Http\Controllers\Admin\LoadpackagetypeController::class, 'update'])->name('admin.update.loadpackagetpye');
        Route::get('loadtype/show/{loadpackagetype}',[App\Http\Controllers\Admin\LoadpackagetypeController::class, 'show'])->name('admin.show.loadpackagetpye');
        Route::delete('loadtype/delete/{loadpackagetype}',[App\Http\Controllers\Admin\LoadpackagetypeController::class, 'delete'])->name('admin.delete.loadpackagetpye');


        // Manage loadpackage Here
        Route::get('loadpackage',[App\Http\Controllers\Admin\LoadpackageController::class, 'index'])->name('admin.list.loadpackage');
        Route::get('loadpackagecreate',[App\Http\Controllers\Admin\LoadpackageController::class, 'create'])->name('admin.create.loadpackage');
        Route::post('loadpackage/store',[App\Http\Controllers\Admin\LoadpackageController::class, 'store'])->name('admin.store.loadpackage');
        Route::get('loadpackage/edit/{loadpackage}',[App\Http\Controllers\Admin\LoadpackageController::class, 'edit'])->name('admin.edit.loadpackage');
        Route::put('loadpackage/update/{loadpackage}',[App\Http\Controllers\Admin\LoadpackageController::class, 'update'])->name('admin.update.loadpackage');
        Route::get('loadpackage/show/{loadpackage}',[App\Http\Controllers\Admin\LoadpackageController::class, 'show'])->name('admin.show.loadpackage');
        Route::delete('loadpackage/delete/{loadpackage}',[App\Http\Controllers\Admin\LoadpackageController::class, 'delete'])->name('admin.delete.loadpackage');

        // Tracking Manage Here
        Route::get('loadtracking',[App\Http\Controllers\Admin\LoadpackageController::class, 'loadtrackingpage'])->name('admin.loadtrackingpage.loadpackage');
        Route::post('tracking/load',[App\Http\Controllers\Admin\LoadpackageController::class, 'loadtracking'])->name('admin.loadtracking.loadpackage');


        // Manage Customer Here
        Route::get('customer',[App\Http\Controllers\Admin\CustomerController::class, 'index'])->name('admin.list.customer');
        Route::get('customercreate',[App\Http\Controllers\Admin\CustomerController::class, 'create'])->name('admin.create.customer');
        Route::post('customer/store',[App\Http\Controllers\Admin\CustomerController::class, 'store'])->name('admin.store.customer');
        Route::get('customer/edit/{customer}',[App\Http\Controllers\Admin\CustomerController::class, 'edit'])->name('admin.edit.customer');
        Route::put('customer/update/{customer}',[App\Http\Controllers\Admin\CustomerController::class, 'update'])->name('admin.update.customer');
        Route::get('customer/show/{customer}',[App\Http\Controllers\Admin\CustomerController::class, 'show'])->name('admin.show.customer');
        Route::delete('customer/delete/{customer}',[App\Http\Controllers\Admin\CustomerController::class, 'delete'])->name('admin.delete.customer');


        // Message Manage Here
        Route::post('/message', [App\Http\Controllers\Admin\MessageController::class, 'sendMessage' ])->name('sendmessage');
        Route::get('/message-form', [App\Http\Controllers\Admin\MessageController::class, 'showSendMessageForm' ])->name('message');


        // Invoices Manage Here
        Route::get('invoicelist', [App\Http\Controllers\Admin\InvoiceController::class, 'index'])->name('admin.list.invoice');
        Route::get('invoiceshow/{invoice}', [App\Http\Controllers\Admin\InvoiceController::class, 'show'])->name('admin.show.invoice');
        Route::get('invoice/edit/{invoice}', [App\Http\Controllers\Admin\InvoiceController::class, 'edit'])->name('admin.edit.invoice');
        Route::put('invoice/update/{invoice}', [App\Http\Controllers\Admin\InvoiceController::class, 'update'])->name('admin.update.invoice');
        Route::get('invoice/pdf/{invoice}', [App\Http\Controllers\Admin\InvoiceController::class, 'generateInvoicePDF'])->name('admin.pdf.invoice');
        

        
       
    });