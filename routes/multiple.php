<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::prefix('/')
    ->middleware('multiple')
    ->group(function () {

        // Branches Manage Here
        Route::get('godownaddress', [App\Http\Controllers\Admin\GodownController::class, 'index'])->name('admin.list.godownaddress');
        Route::get('godownaddresscreate', [App\Http\Controllers\Admin\GodownController::class, 'create'])->name('admin.create.godownaddress');
        Route::post('godownaddress/store', [App\Http\Controllers\Admin\GodownController::class, 'store'])->name('admin.store.godownaddress');
        Route::get('godownaddress/edit/{godownaddress}', [App\Http\Controllers\Admin\GodownController::class, 'edit'])->name('admin.edit.godownaddress');
        Route::put('godownaddress/update/{godownaddress}', [App\Http\Controllers\Admin\GodownController::class, 'update'])->name('admin.update.godownaddress');
        Route::get('godownaddress/show/{godownaddress}', [App\Http\Controllers\Admin\GodownController::class, 'show'])->name('admin.show.godownaddress');
        Route::delete('godownaddress/delete/{godownaddress}', [App\Http\Controllers\Admin\GodownController::class, 'delete'])->name('admin.delete.godownaddress');


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
          Route::get('container/{id}/loadpackages', [App\Http\Controllers\Admin\ContainerController::class, 'showloadstoload'])->name('admin.load.container');
          Route::post('container/{id}/attachloadpackages', [App\Http\Controllers\Admin\ContainerController::class, 'attachPackagetoContainer'])->name('admin.attachload.container');

          
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
        Route::post('/admin/toggle-notification', [App\Http\Controllers\Admin\LoadpackageController::class, 'toggleNotification'])->name('admin.toggle.notification');

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
        Route::post('/customers/import', [App\Http\Controllers\Admin\CustomerController::class, 'importExcel'])->name('customers.import');
        Route::get('/customers/template', [App\Http\Controllers\Admin\CustomerController::class, 'downloadTemplate'])->name('customers.template');


        // Invoices Manage Here
        Route::get('invoicelist', [App\Http\Controllers\Admin\InvoiceController::class, 'index'])->name('admin.list.invoice');
        Route::get('invoiceshow/{invoice}', [App\Http\Controllers\Admin\InvoiceController::class, 'show'])->name('admin.show.invoice');
        Route::get('invoice/edit/{invoice}', [App\Http\Controllers\Admin\InvoiceController::class, 'edit'])->name('admin.edit.invoice');
        Route::put('invoice/update/{invoice}', [App\Http\Controllers\Admin\InvoiceController::class, 'update'])->name('admin.update.invoice');
        Route::post('invoice/{invoice}/payments', [App\Http\Controllers\Admin\InvoiceController::class, 'recordPayment'])->name('admin.invoice.payments.store');
        Route::get('invoice/pdf/{invoice}', [App\Http\Controllers\Admin\InvoiceController::class, 'generateInvoicePDF'])->name('admin.pdf.invoice');
        Route::get('finalInvoice/pdf/{invoice}', [App\Http\Controllers\Admin\InvoiceController::class, 'UpdatedInvoicePDF'])->name('admin.pdf.finalinvoice');
        Route::post('invoices/merge-pdf', [App\Http\Controllers\Admin\InvoiceController::class, 'InvoiceMerge'])->name('admin.invoices.mergepdf');


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



        Route::get('customerlead/template', [App\Http\Controllers\Admin\CustomerleadController::class, 'downloadTemplate'])->name('admin.downloadTemplate.customerlead');
        Route::post('customerlead/import', [App\Http\Controllers\Admin\CustomerleadController::class, 'import'])->name('admin.import.customerlead');
      //   Route::post('customerlead/import-test', function(Request $request) {
      //     \Log::info('Test route called');
      //     return response()->json(['status' => 'success', 'data' => $request->all()]);
      // })->name('admin.import.customerlead');

        // Manage Clients Here
        Route::get('staff', [App\Http\Controllers\Admin\StaffController::class, 'list_staffs'])->name('admin.list.staff');
        Route::get('staffcreate', [App\Http\Controllers\Admin\StaffController::class, 'create'])->name('admin.create.staff');
        Route::post('staffstore', [App\Http\Controllers\Admin\StaffController::class, 'store'])->name('admin.store.staff');
        Route::get('staff-edit-staff/{staff}', [App\Http\Controllers\Admin\StaffController::class, 'edit'])->name('admin.edit.staff');
        Route::put('staff-update-staff/{staff}', [App\Http\Controllers\Admin\StaffController::class, 'update'])->name('admin.update.staff');
        Route::get('staff-show-staff/{staff}', [App\Http\Controllers\Admin\StaffController::class, 'show'])->name('admin.show.staff');
        Route::delete('staff-delete-staff/{staff}', [App\Http\Controllers\Admin\StaffController::class, 'delete'])->name('admin.delete.staff');


        // Company Invoice Details Manage Here
        Route::get('companyinvoicedetail', [App\Http\Controllers\Admin\CompanyinvoicedetailController::class, 'index'])->name('admin.list.companyinvoicedetail');
        Route::get('companyinvoicedetailcreate', [App\Http\Controllers\Admin\CompanyinvoicedetailController::class, 'create'])->name('admin.create.companyinvoicedetail');
        Route::post('companyinvoicedetail/store', [App\Http\Controllers\Admin\CompanyinvoicedetailController::class, 'store'])->name('admin.store.companyinvoicedetail');
        Route::get('companyinvoicedetail/edit/{companyinvoicedetail}', [App\Http\Controllers\Admin\CompanyinvoicedetailController::class, 'edit'])->name('admin.edit.companyinvoicedetail');
        Route::put('companyinvoicedetail/update/{companyinvoicedetail}', [App\Http\Controllers\Admin\CompanyinvoicedetailController::class, 'update'])->name('admin.update.companyinvoicedetail');
        Route::get('companyinvoicedetail/show/{companyinvoicedetail}', [App\Http\Controllers\Admin\CompanyinvoicedetailController::class, 'show'])->name('admin.show.companyinvoicedetail');
        Route::delete('companyinvoicedetail/delete/{companyinvoicedetail}', [App\Http\Controllers\Admin\CompanyinvoicedetailController::class, 'delete'])->name('admin.delete.companyinvoicedetail');

        Route::get('viewprofile', [App\Http\Controllers\Admin\StaffController::class, 'viewProfile'])->name('admin.viewprofile.staff');
        Route::put('updateprofile', [App\Http\Controllers\Admin\StaffController::class, 'updateProfile'])->name('admin.updateprofile.staff');

        // Message Manage Here
        // Messaging is part of the bulk SMS module: a KodaFreight package must not reach it by URL.
        Route::middleware('module:sms_bulk')->group(function () {
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
        Route::get('/sms-balance', [App\Http\Controllers\Admin\SmsBalanceController::class, 'index'])->name('admin.sms.balance');
        });



        // ExpensesCategoryInvoice Manage Here
        Route::get('categoryexpense', [App\Http\Controllers\Admin\OfficeExpenseCategoryController::class, 'index'])->name('admin.list.categoryexpense');
        Route::get('categoryexpensecreate', [App\Http\Controllers\Admin\OfficeExpenseCategoryController::class, 'create'])->name('admin.create.categoryexpense');
        Route::post('categoryexpense/store', [App\Http\Controllers\Admin\OfficeExpenseCategoryController::class, 'store'])->name('admin.store.categoryexpense');
        Route::get('categoryexpense/edit/{officeexpense}', [App\Http\Controllers\Admin\OfficeExpenseCategoryController::class, 'edit'])->name('admin.edit.categoryexpense');
        Route::put('categoryexpense/update/{officeexpense}', [App\Http\Controllers\Admin\OfficeExpenseCategoryController::class, 'update'])->name('admin.update.categoryexpense');
        Route::get('categoryexpense/show/{officeexpense}', [App\Http\Controllers\Admin\OfficeExpenseCategoryController::class, 'show'])->name('admin.show.categoryexpense');
        Route::delete('categoryexpense/delete/{officeexpense}', [App\Http\Controllers\Admin\OfficeExpenseCategoryController::class, 'delete'])->name('admin.delete.categoryexpense');


        // ExpensesInvoice Manage Here
        Route::get('officeexpense', [App\Http\Controllers\Admin\OfficeExpenseController::class, 'index'])->name('admin.list.officeexpense');
        Route::get('officeexpensecreate', [App\Http\Controllers\Admin\OfficeExpenseController::class, 'create'])->name('admin.create.officeexpense');
        Route::post('officeexpense/store', [App\Http\Controllers\Admin\OfficeExpenseController::class, 'store'])->name('admin.store.officeexpense');
        Route::get('officeexpense/edit/{officeexpense}', [App\Http\Controllers\Admin\OfficeExpenseController::class, 'edit'])->name('admin.edit.officeexpense');
        Route::put('officeexpense/update/{officeexpense}', [App\Http\Controllers\Admin\OfficeExpenseController::class, 'update'])->name('admin.update.officeexpense');
        Route::get('officeexpense/show/{officeexpense}', [App\Http\Controllers\Admin\OfficeExpenseController::class, 'show'])->name('admin.show.officeexpense');
        Route::delete('officeexpense/delete/{officeexpense}', [App\Http\Controllers\Admin\OfficeExpenseController::class, 'delete'])->name('admin.delete.officeexpense');
        Route::get('invoice/{id}/pdf', [App\Http\Controllers\Admin\OfficeExpenseController::class, 'pdfgenerate'])->name('admin.pdf.generate');
        Route::get('expense-report', [App\Http\Controllers\Admin\OfficeExpenseController::class, 'expenseReport'])->name('admin.expense.report');
        Route::get('expense-report/export', [App\Http\Controllers\Admin\OfficeExpenseController::class, 'exportReport'])->name('admin.export.report');
        Route::get('/expense/pdf/{ids}', [App\Http\Controllers\Admin\OfficeExpenseController::class, 'printExpenseInvoice'])->name('admin.expense.pdf');
        
        // Reports Manage Here
        Route::get('/sales-reports/export', [App\Http\Controllers\Sales\SalesreportController::class, 'exportExcel'])->name('sales.excel.salesreport');
         Route::get('salesreport', [App\Http\Controllers\Sales\SalesreportController::class, 'index'])->name('sales.list.salesreport');
        Route::get('salesreportcreate', [App\Http\Controllers\Sales\SalesreportController::class, 'create'])->name('sales.create.salesreport');
        Route::post('salesreport/store', [App\Http\Controllers\Sales\SalesreportController::class, 'store'])->name('sales.store.salesreport');
        Route::get('salesreport/edit/{salesreport}', [App\Http\Controllers\Sales\SalesreportController::class, 'edit'])->name('sales.edit.salesreport');
        Route::put('salesreport/update/{salesreport}', [App\Http\Controllers\Sales\SalesreportController::class, 'update'])->name('sales.update.salesreport');
        Route::get('salesreport/show/{salesreport}', [App\Http\Controllers\Sales\SalesreportController::class, 'show'])->name('sales.show.salesreport');
        Route::delete('salesreport/delete/{salesreport}', [App\Http\Controllers\Sales\SalesreportController::class, 'delete'])->name('sales.delete.salesreport');


        Route::get('rolewithpermission/{roleId}/', [App\Http\Controllers\RoleController::class, 'roleByPermission'])->name('all.permission.role');
        
    });
