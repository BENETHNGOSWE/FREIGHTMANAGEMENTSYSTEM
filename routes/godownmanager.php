<?php
use Illuminate\Support\Facades\Route;

Route::prefix('/')
    ->middleware('auth')
    ->group(function () {
        // Derivery Order Manage Here
        Route::get('deriveryorder', [App\Http\Controllers\Godownmanager\DeriveryorderController::class, 'index'])
        ->middleware('permission:view derivery_orders')
        ->name('godownmanager.list.deriveryorder');
        Route::get('deriveryordercreate', [App\Http\Controllers\Godownmanager\DeriveryorderController::class, 'create'])
        ->middleware('permission:create derivery_orders')
        ->name('godownmanager.create.deriveryorder');
        Route::post('deriveryorder/store', [App\Http\Controllers\Godownmanager\DeriveryorderController::class, 'store'])
        ->middleware('permission:create derivery_orders')
        ->name('godownmanager.store.deriveryorder');
        Route::get('deriveryorder/edit/{deriveryorder}', [App\Http\Controllers\Godownmanager\DeriveryorderController::class, 'edit'])
        ->middleware('permission:update derivery_orders')
        ->name('godownmanager.edit.deriveryorder');
        Route::put('deriveryorder/update/{deriveryorder}', [App\Http\Controllers\Godownmanager\DeriveryorderController::class, 'update'])
        ->middleware('permission:update derivery_orders')
        ->name('godownmanager.update.deriveryorder');
        Route::get('deriveryorder/show/{deriveryorder}', [App\Http\Controllers\Godownmanager\DeriveryorderController::class, 'show'])
        ->middleware('permission:view derivery_orders')
        ->name('godownmanager.show.deriveryorder');
        Route::delete('deriveryorder/delete/{deriveryorder}', [App\Http\Controllers\Godownmanager\DeriveryorderController::class, 'delete'])
        ->middleware('permission:delete derivery_orders')
        ->name('godownmanager.delete.deriveryorder');

        Route::get('deriveryorder/generate/{id}', [App\Http\Controllers\Godownmanager\DeriveryorderController::class, 'generateDeriveryOrder'])
        ->middleware('permission:view derivery_orders')
        ->name('godownmanager.generate.deriveryorder');
    });