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
        
       
    });
