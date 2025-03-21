<?php
use Illuminate\Support\Facades\Route;

Route::prefix('/')
    ->middleware('auth')
    ->group(function () {

          // Container Manage Here
          Route::get('container', [App\Http\Controllers\Admin\ContainerController::class, 'index'])->name('admin.list.container');
          Route::get('containercreate', [App\Http\Controllers\Admin\ContainerController::class, 'create'])->name('admin.create.container');
          Route::post('container/store', [App\Http\Controllers\Admin\ContainerController::class, 'store'])->name('admin.store.container');
          Route::get('container/edit/{container}', [App\Http\Controllers\Admin\ContainerController::class, 'edit'])->name('admin.edit.container');
          Route::put('container/update/{container}', [App\Http\Controllers\Admin\ContainerController::class, 'update'])->name('admin.update.container');
          Route::get('container/show/{container}', [App\Http\Controllers\Admin\ContainerController::class, 'show'])->name('admin.show.container');
          Route::post('/containers/{container}/load', [App\Http\Controllers\Admin\ContainerController::class, 'loadContainer'])->name('admin.loadcontainer.container');
          Route::get('/containers/{containerId}/pdflist', [App\Http\Controllers\Admin\ContainerController::class, 'generateContainerloads'])->name('admin.loadlistpdf.container');
          Route::delete('container/delete/{container}', [App\Http\Controllers\Admin\ContainerController::class, 'delete'])->name('admin.delete.container');

          
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

        // Release Order Manage Here
        Route::get('release/pdf/{id}', [App\Http\Controllers\Admin\InvoiceController::class, 'generatereleasePDF'])->name('admin.pdf.release');

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

        // Customercategories Manage Here
        Route::get('category', [App\Http\Controllers\Admin\CustomercategoryController::class, 'index'])->name('admin.list.customercategory');
        Route::get('categorycreate', [App\Http\Controllers\Admin\CustomercategoryController::class, 'create'])->name('admin.create.customercategory');
        Route::post('category/store', [App\Http\Controllers\Admin\CustomercategoryController::class, 'store'])->name('admin.store.customercategory');
        Route::get('category/edit/{customercategory}', [App\Http\Controllers\Admin\CustomercategoryController::class, 'edit'])->name('admin.edit.customercategory');
        Route::put('category/update/{customercategory}', [App\Http\Controllers\Admin\CustomercategoryController::class, 'update'])->name('admin.update.customercategory');
        Route::get('category/show/{customercategory}', [App\Http\Controllers\Admin\CustomercategoryController::class, 'show'])->name('admin.show.customercategory');
        Route::delete('category/delete/{customercategory}', [App\Http\Controllers\Admin\CustomercategoryController::class, 'delete'])->name('admin.delete.customercategory');

        // Customerleads Manage Here
        Route::get('customerlead', [App\Http\Controllers\Admin\CustomerleadController::class, 'index'])->name('admin.list.customerlead');
        Route::get('customerleadcreate', [App\Http\Controllers\Admin\CustomerleadController::class, 'create'])->name('admin.create.customerlead');
        Route::post('customerlead/store', [App\Http\Controllers\Admin\CustomerleadController::class, 'store'])->name('admin.store.customerlead');
        Route::get('customerlead/edit/{customerlead}', [App\Http\Controllers\Admin\CustomerleadController::class, 'edit'])->name('admin.edit.customerlead');
        Route::put('customerlead/update/{customerlead}', [App\Http\Controllers\Admin\CustomerleadController::class, 'update'])->name('admin.update.customerlead');
        Route::get('customerlead/show/{customerlead}', [App\Http\Controllers\Admin\CustomerleadController::class, 'show'])->name('admin.show.customerlead');
        Route::delete('customerlead/delete/{customerlead}', [App\Http\Controllers\Admin\CustomerleadController::class, 'delete'])->name('admin.delete.customerlead');

        // Manage Clients Here
        Route::get('staff', [App\Http\Controllers\Admin\StaffController::class, 'list_staffs'])->name('admin.list.staff');
        Route::get('staffcreate', [App\Http\Controllers\Admin\StaffController::class, 'create'])->name('admin.create.staff');
        Route::post('staffstore', [App\Http\Controllers\Admin\StaffController::class, 'store'])->name('admin.store.staff');
        Route::get('staff-edit-staff/{staff}', [App\Http\Controllers\Admin\StaffController::class, 'edit'])->name('admin.edit.staff');
        Route::put('staff-update-staff/{staff}', [App\Http\Controllers\Admin\StaffController::class, 'update'])->name('admin.update.staff');
        Route::get('staff-show-staff/{staff}', [App\Http\Controllers\Admin\StaffController::class, 'show'])->name('admin.show.staff');
        Route::delete('staff-delete-staff/{staff}', [App\Http\Controllers\Admin\StaffController::class, 'delete'])->name('admin.delete.staff');

        Route::get('viewprofile', [App\Http\Controllers\Admin\StaffController::class, 'viewProfile'])->name('admin.viewprofile.staff');
        Route::put('updateprofile', [App\Http\Controllers\Admin\StaffController::class, 'updateProfile'])->name('admin.updateprofile.staff');

        // Message Manage Here
        Route::get('/message', [App\Http\Controllers\Admin\MessageController::class, 'index'])->name('admin.message.index');
        Route::post('/messagecreate', [App\Http\Controllers\Admin\MessageController::class, 'sendMessage'])->name('admin.sendmessage');
        Route::get('/message-form', [App\Http\Controllers\Admin\MessageController::class, 'showSendMessageForm'])->name('admin.message');

        // CustomerLead Manage Here
        Route::post('/messagecreatelead', [App\Http\Controllers\Admin\MessageController::class, 'sendMessageleadsenderid'])->name('admin.sendmessage.lead');
        // Route::get('/message-form-multiple', [App\Http\Controllers\Admin\MessageController::class, 'showsendMessageToExistingCustomerform'])->name('admin.message.multple');
        Route::get('/message-form-lead', [App\Http\Controllers\Admin\MessageController::class, 'showSendMessageFormlead'])->name('admin.message.lead');

        // MessageMultiple Here
        Route::post('/messagecreatemultipe', [App\Http\Controllers\Admin\MessageController::class, 'sendMessageToExistingCustomer'])->name('admin.sendmessage.multiple');
        Route::get('/message-form-multiple', [App\Http\Controllers\Admin\MessageController::class, 'showsendMessageToExistingCustomerform'])->name('admin.message.multiple');

        
    });
