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
        Route::get('staff-edit-staff/{staff}', [App\Http\Controllers\Admin\StaffController::class, 'edit'])->name('edit.clients');
        Route::put('staff-update-staff/{staff}', [App\Http\Controllers\Admin\StaffController::class, 'update'])->name('update.clients');
        Route::get('staff-show-staff/{staff}', [App\Http\Controllers\Admin\StaffController::class, 'show'])->name('show.clients');


        // Manage loadpackagetype Here
        Route::get('loadtype',[App\Http\Controllers\Admin\LoadpackagetypeController::class, 'index'])->name('admin.list.loadpackagetpye');
        Route::get('loadtypecreate',[App\Http\Controllers\Admin\LoadpackagetypeController::class, 'create'])->name('admin.create.loadpackagetpye');
        Route::post('loadtype/store',[App\Http\Controllers\Admin\LoadpackagetypeController::class, 'store'])->name('admin.store.loadpackagetpye');
        Route::get('loadtype/edit/{loadpackagetype}',[App\Http\Controllers\Admin\LoadpackagetypeController::class, 'edit'])->name('admin.edit.loadpackagetpye');
        Route::put('loadtype/update/{loadpackagetype}',[App\Http\Controllers\Admin\LoadpackagetypeController::class, 'update'])->name('admin.update.loadpackagetpye');
        Route::get('loadtype/show/{loadpackagetype}',[App\Http\Controllers\Admin\LoadpackagetypeController::class, 'show'])->name('admin.show.loadpackagetpye');
        Route::delete('loadtype/delete/{loadpackagetype}',[App\Http\Controllers\Admin\LoadpackagetypeController::class, 'delete'])->name('admin.delete.loadpackagetpye');

        // Manage Customer Here
        Route::get('customer',[App\Http\Controllers\Admin\CustomerController::class, 'index'])->name('admin.list.customer');
        Route::get('customercreate',[App\Http\Controllers\Admin\CustomerController::class, 'create'])->name('admin.create.customer');
        Route::post('customer/store',[App\Http\Controllers\Admin\CustomerController::class, 'store'])->name('admin.store.customer');
        Route::get('customer/edit/{customer}',[App\Http\Controllers\Admin\CustomerController::class, 'edit'])->name('admin.edit.customer');
        Route::put('customer/update/{customer}',[App\Http\Controllers\Admin\CustomerController::class, 'update'])->name('admin.update.customer');
        Route::get('customer/show/{customer}',[App\Http\Controllers\Admin\CustomerController::class, 'show'])->name('admin.show.customer');
        Route::delete('customer/delete/{customer}',[App\Http\Controllers\Admin\CustomerController::class, 'delete'])->name('admin.delete.customer');

        
       
    });