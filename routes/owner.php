<?php
use Illuminate\Support\Facades\Route;

Route::prefix('/')
    ->middleware('auth')
    ->group(function () {

        // Manage Clients Here
        Route::get('clientcreate',[App\Http\Controllers\Owner\ClientController::class, 'create'])->name('clents.create');
        
        Route::resource('permissions', App\Http\Controllers\Permission\PermissionController::class);
        Route::get('permissions/{permissionId}/delete', [App\Http\Controllers\Permission\PermissionController::class, 'destroy']);

        Route::resource('roles', App\Http\Controllers\Roles\RoleController::class);
        Route::get('roles/{roleId}/delete', [App\Http\Controllers\Roles\RoleController::class, 'destroy']);
        Route::get('roles/{roleId}/give-permissions', [App\Http\Controllers\Roles\RoleController::class, 'addPermissionToRole']);
        Route::put('roles/{roleId}/give-permissions', [App\Http\Controllers\Roles\RoleController::class, 'givePermissionToRole']);

        Route::resource('users', App\Http\Controllers\User\UserController::class);
        Route::get('users/{userId}/delete', [App\Http\Controllers\User\UserController::class, 'destroy']);
    });
