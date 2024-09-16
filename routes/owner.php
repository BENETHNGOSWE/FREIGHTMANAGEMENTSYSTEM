<?php
use Illuminate\Support\Facades\Route;

Route::prefix('/')
    ->middleware('auth')
    ->group(function () {

        // Manage Clients Here
        Route::get('index',[App\Http\Controllers\Owner\ClientController::class, 'index'])->name('list.clients');
        Route::get('clientcreate',[App\Http\Controllers\Owner\ClientController::class, 'create'])->name('create.clients');
        Route::post('clientstore',[App\Http\Controllers\Owner\ClientController::class, 'store'])->name('store.clients');
        Route::get('client-edit-client/{client}', [App\Http\Controllers\Owner\ClientController::class, 'edit'])->name('edit.clients');
        Route::put('client-update-client/{client}', [App\Http\Controllers\Owner\ClientController::class, 'update'])->name('update.clients');
        Route::get('client-show-client/{client}', [App\Http\Controllers\Owner\ClientController::class, 'show'])->name('show.clients');

        
        Route::resource('permissions', App\Http\Controllers\Permission\PermissionController::class);
        Route::get('permissions/{permissionId}/delete', [App\Http\Controllers\Permission\PermissionController::class, 'destroy']);

        Route::resource('roles', App\Http\Controllers\Roles\RoleController::class);
        Route::get('roles/{roleId}/delete', [App\Http\Controllers\Roles\RoleController::class, 'destroy']);
        Route::get('roles/{roleId}/give-permissions', [App\Http\Controllers\Roles\RoleController::class, 'addPermissionToRole']);
        Route::put('roles/{roleId}/give-permissions', [App\Http\Controllers\Roles\RoleController::class, 'givePermissionToRole']);

        Route::resource('users', App\Http\Controllers\User\UserController::class);
        Route::get('users/{userId}/delete', [App\Http\Controllers\User\UserController::class, 'destroy']);
    });
