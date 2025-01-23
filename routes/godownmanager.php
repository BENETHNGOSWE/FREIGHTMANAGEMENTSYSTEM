<?php
use Illuminate\Support\Facades\Route;

Route::prefix('/')
    ->middleware('auth')
    ->group(function () {
        // Derivery Order Manage Here
        Route::get('deriveryorder', [App\Http\Controllers\Godownmanager\DeriveryorderController::class, 'index'])->name('godownmanager.list.deriveryorder');
        Route::get('deriveryordercreate', [App\Http\Controllers\Godownmanager\DeriveryorderController::class, 'create'])->name('godownmanager.create.deriveryorder');
        Route::post('deriveryorder/store', [App\Http\Controllers\Godownmanager\DeriveryorderController::class, 'store'])->name('godownmanager.store.deriveryorder');
        Route::get('deriveryorder/edit/{deriveryorder}', [App\Http\Controllers\Godownmanager\DeriveryorderController::class, 'edit'])->name('godownmanager.edit.deriveryorder');
        Route::put('deriveryorder/update/{deriveryorder}', [App\Http\Controllers\Godownmanager\DeriveryorderController::class, 'update'])->name('godownmanager.update.deriveryorder');
        Route::get('deriveryorder/show/{deriveryorder}', [App\Http\Controllers\Godownmanager\DeriveryorderController::class, 'show'])->name('godownmanager.show.deriveryorder');
        Route::delete('deriveryorder/delete/{deriveryorder}', [App\Http\Controllers\Godownmanager\DeriveryorderController::class, 'delete'])->name('godownmanager.delete.deriveryorder');

        Route::get('deriveryorder/generate/{id}', [App\Http\Controllers\Godownmanager\DeriveryorderController::class, 'generateDeriveryOrder'])->name('godownmanager.generate.deriveryorder');
    });
