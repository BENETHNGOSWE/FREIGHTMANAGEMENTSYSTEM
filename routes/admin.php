<?php
use Illuminate\Support\Facades\Route;

Route::prefix('/')
    ->middleware('auth')
    ->group(function () {

        //Profile Manage Here
        Route::get('profile',[App\Http\Controllers\Admin\AdminDashboardController::class, 'indexprofile'])->name('admin.list.profile'); 
        Route::get('profile/edit/{clientadmin}',[App\Http\Controllers\Admin\AdminDashboardController::class, 'edit'])->name('admin.edit.profile'); 
        Route::put('profile/update/{clientadmin}',[App\Http\Controllers\Admin\AdminDashboardController::class, 'update'])->name('admin.update.profile'); 
        Route::get('profile-show/{clientadmin}',[App\Http\Controllers\Admin\AdminDashboardController::class, 'show'])->name('admin.show.profile'); 

        
        

        // Manage Clients Here
        // Route::get('index',[App\Http\Controllers\Admin\StaffController::class, 'list_staffs'])->name('admin.list.staff');
        Route::get('staff',[App\Http\Controllers\Admin\StaffController::class, 'list_staffs'])->name('admin.list.staff');
        Route::get('staffcreate',[App\Http\Controllers\Admin\StaffController::class, 'create'])->name('admin.create.staff');
        Route::post('staffstore',[App\Http\Controllers\Admin\StaffController::class, 'store'])->name('admin.store.staff');
        Route::get('staff-edit-staff/{staff}', [App\Http\Controllers\Admin\StaffController::class, 'edit'])->name('admin.edit.staff');
        Route::put('staff-update-staff/{staff}', [App\Http\Controllers\Admin\StaffController::class, 'update'])->name('admin.update.staff');
        Route::get('staff-show-staff/{staff}', [App\Http\Controllers\Admin\StaffController::class, 'show'])->name('admin.show.staff');
        Route::delete('staff-delete-staff/{staff}', [App\Http\Controllers\Admin\StaffController::class, 'delete'])->name('admin.delete.staff');


        // Message Manage Here
        Route::post('/message', [App\Http\Controllers\Admin\MessageController::class, 'sendMessage' ])->name('admin.sendmessage');
        Route::get('/message-form', [App\Http\Controllers\Admin\MessageController::class, 'showSendMessageForm' ])->name('admin.message');


       
        // Branches Manage Here
        Route::get('branch',[App\Http\Controllers\Admin\BranchController::class, 'index'])->name('admin.list.branch');
        Route::get('branchcreate',[App\Http\Controllers\Admin\BranchController::class, 'create'])->name('admin.create.branch');
        Route::post('branch/store',[App\Http\Controllers\Admin\BranchController::class, 'store'])->name('admin.store.branch');
        Route::get('branch/edit/{branch}',[App\Http\Controllers\Admin\BranchController::class, 'edit'])->name('admin.edit.branch');
        Route::put('branch/update/{branch}',[App\Http\Controllers\Admin\BranchController::class, 'update'])->name('admin.update.branch');
        Route::get('branch/show/{branch}',[App\Http\Controllers\Admin\BranchController::class, 'show'])->name('admin.show.branch');
        Route::delete('branch/delete/{branch}',[App\Http\Controllers\Admin\BranchController::class, 'delete'])->name('admin.delete.branch');
        

        
       
    });