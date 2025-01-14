<?php
use Illuminate\Support\Facades\Route;

Route::prefix('/')
    ->middleware('auth')
    ->group(function () {

        Route::get('/validate_token', [\App\Http\Controllers\Admin\AdminDashboardController::class, 'validate_subscription_token'])->name('admin.validate_subscription_token');

        // Route::get('/messagemultiple', [App\Http\Controllers\Admin\MessageController::class, 'indexmultiple'])->name('admin.message.indexmultiple');
        Route::post('/messagecreatemultipe', [App\Http\Controllers\Admin\MessageController::class, 'sendMessagemultipesenderid'])->name('admin.sendmessage.multiple');        
        Route::get('/message-form-multiple', [App\Http\Controllers\Admin\MessageController::class, 'showSendMessageFormmultiple'])->name('admin.message.multiple');

        // Branches Manage Here
        Route::get('branch', [App\Http\Controllers\Admin\BranchController::class, 'index'])->name('admin.list.branch');
        Route::get('branchcreate', [App\Http\Controllers\Admin\BranchController::class, 'create'])->name('admin.create.branch');
        Route::post('branch/store', [App\Http\Controllers\Admin\BranchController::class, 'store'])->name('admin.store.branch');
        Route::get('branch/edit/{branch}', [App\Http\Controllers\Admin\BranchController::class, 'edit'])->name('admin.edit.branch');
        Route::put('branch/update/{branch}', [App\Http\Controllers\Admin\BranchController::class, 'update'])->name('admin.update.branch');
        Route::get('branch/show/{branch}', [App\Http\Controllers\Admin\BranchController::class, 'show'])->name('admin.show.branch');
        Route::delete('branch/delete/{branch}', [App\Http\Controllers\Admin\BranchController::class, 'delete'])->name('admin.delete.branch');

        // Container Manage Here
        Route::get('container', [App\Http\Controllers\Admin\ContainerController::class, 'index'])->name('admin.list.container');
        Route::get('containercreate', [App\Http\Controllers\Admin\ContainerController::class, 'create'])->name('admin.create.container');
        Route::post('container/store', [App\Http\Controllers\Admin\ContainerController::class, 'store'])->name('admin.store.container');
        Route::get('container/edit/{container}', [App\Http\Controllers\Admin\ContainerController::class, 'edit'])->name('admin.edit.container');
        Route::put('container/update/{container}', [App\Http\Controllers\Admin\ContainerController::class, 'update'])->name('admin.update.container');
        Route::get('container/show/{container}', [App\Http\Controllers\Admin\ContainerController::class, 'show'])->name('admin.show.container');
        Route::delete('container/delete/{container}', [App\Http\Controllers\Admin\ContainerController::class, 'delete'])->name('admin.delete.container');

        // Unity Type Manage Here
        Route::get('unittype', [App\Http\Controllers\Admin\UnittypeController::class, 'index'])->name('admin.list.unittype');
        Route::get('unittypecreate', [App\Http\Controllers\Admin\UnittypeController::class, 'create'])->name('admin.create.unittype');
        Route::post('unittype/store', [App\Http\Controllers\Admin\UnittypeController::class, 'store'])->name('admin.store.unittype');
        Route::get('unittype/edit/{unittype}', [App\Http\Controllers\Admin\UnittypeController::class, 'edit'])->name('admin.edit.unittype');
        Route::put('unittype/update/{unittype}', [App\Http\Controllers\Admin\UnittypeController::class, 'update'])->name('admin.update.unittype');
        Route::get('unittype/show/{unittype}', [App\Http\Controllers\Admin\UnittypeController::class, 'show'])->name('admin.show.unittype');
        Route::delete('unittype/delete/{unittype}', [App\Http\Controllers\Admin\UnittypeController::class, 'delete'])->name('admin.delete.unittype');

        // Activename Manage Here
        Route::get('activename', [App\Http\Controllers\Admin\ActivesendernameController::class, 'index'])->name('admin.list.activename');
        Route::get('activenamecreate', [App\Http\Controllers\Admin\ActivesendernameController::class, 'create'])->name('admin.create.activename');
        Route::post('activename/store', [App\Http\Controllers\Admin\ActivesendernameController::class, 'store'])->name('admin.store.activename');
        Route::get('activename/edit/{activename}', [App\Http\Controllers\Admin\ActivesendernameController::class, 'edit'])->name('admin.edit.activename');
        Route::put('activename/update/{activename}', [App\Http\Controllers\Admin\ActivesendernameController::class, 'update'])->name('admin.update.activename');
        Route::get('activename/show/{activename}', [App\Http\Controllers\Admin\ActivesendernameController::class, 'show'])->name('admin.show.activename');
        Route::delete('activename/delete/{activename}', [App\Http\Controllers\Admin\ActivesendernameController::class, 'delete'])->name('admin.delete.activename');
    });
