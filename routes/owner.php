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
        Route::delete('client-delete-client/{client}', [App\Http\Controllers\Owner\ClientController::class, 'destroy'])->name('delete.clients');

        Route::get('impersonate/{client}', [App\Http\Controllers\Owner\ClientController::class, 'impersonate'])->name('impersonate.clients');

        
        Route::resource('permissions', App\Http\Controllers\Permission\PermissionController::class);
        Route::get('permissions/{permissionId}/delete', [App\Http\Controllers\Permission\PermissionController::class, 'destroy']);

        Route::resource('roles', App\Http\Controllers\Roles\RoleController::class);
        Route::get('roles/{roleId}/delete', [App\Http\Controllers\Roles\RoleController::class, 'destroy']);
        Route::get('roles/{roleId}/give-permissions', [App\Http\Controllers\Roles\RoleController::class, 'addPermissionToRole']);
        Route::put('roles/{roleId}/give-permissions', [App\Http\Controllers\Roles\RoleController::class, 'givePermissionToRole']);

        Route::resource('users', App\Http\Controllers\User\UserController::class);
        Route::get('users/{userId}/delete', [App\Http\Controllers\User\UserController::class, 'destroy']);

        // Package Manage Here
        Route::get('package', [App\Http\Controllers\Owner\PackageController::class, 'index'])->name('owner.list.package');
        Route::get('packagecreate', [App\Http\Controllers\Owner\PackageController::class, 'create'])->name('owner.create.package');
        Route::post('packagestore', [App\Http\Controllers\Owner\PackageController::class, 'store'])->name('owner.store.package');
        Route::get('package-edit-package/{kodapackage}', [App\Http\Controllers\Owner\PackageController::class, 'edit'])->name('owner.edit.package');
        Route::put('package-update-package/{kodapackage}', [App\Http\Controllers\Owner\PackageController::class, 'update'])->name('owner.update.package');
        Route::get('package-show-package/{kodapackage}', [App\Http\Controllers\Owner\PackageController::class, 'show'])->name('owner.show.package');
        Route::delete('package-delete-package/{kodapackage}', [App\Http\Controllers\Owner\PackageController::class, 'delete'])->name('owner.delete.package');


        // Sendername Manage Here
        Route::get('sendername', [App\Http\Controllers\Owner\SendernameController::class, 'index'])->name('owner.list.sendername');
        Route::get('sendernamecreate', [App\Http\Controllers\Owner\SendernameController::class, 'create'])->name('owner.create.sendername');
        Route::post('sendername/store', [App\Http\Controllers\Owner\SendernameController::class, 'store'])->name('owner.store.sendername');
        Route::get('sendername/edit/{sendername}', [App\Http\Controllers\Owner\SendernameController::class, 'edit'])->name('owner.edit.sendername');
        Route::put('sendername/update/{sendername}', [App\Http\Controllers\Owner\SendernameController::class, 'update'])->name('owner.update.sendername');
        Route::get('sendername/show/{sendername}', [App\Http\Controllers\Owner\SendernameController::class, 'show'])->name('owner.show.sendername');
        Route::delete('sendername/delete/{sendername}', [App\Http\Controllers\Owner\SendernameController::class, 'delete'])->name('owner.delete.sendername');


        // Message Amount Manage Here
        Route::get('customermessage', [App\Http\Controllers\Owner\CustomermessageController::class, 'index'])->name('owner.list.customermessage');
        Route::get('customermessagecreate', [App\Http\Controllers\Owner\CustomermessageController::class, 'create'])->name('owner.create.customermessage');
        Route::post('customermessage/store', [App\Http\Controllers\Owner\CustomermessageController::class, 'store'])->name('owner.store.customermessage');
        Route::get('customermessage/edit/{customermessage}', [App\Http\Controllers\Owner\CustomermessageController::class, 'edit'])->name('owner.edit.customermessage');
        Route::put('customermessage/update/{customermessage}', [App\Http\Controllers\Owner\CustomermessageController::class, 'update'])->name('owner.update.customermessage');
        Route::get('customermessage/show/{customermessage}', [App\Http\Controllers\Owner\CustomermessageController::class, 'show'])->name('owner.show.customermessage');
        Route::delete('customermessage/delete/{customermessage}', [App\Http\Controllers\Owner\CustomermessageController::class, 'delete'])->name('owner.delete.customermessage');
      
         //Owner Manage Subscription
         Route::get('/owner-list-subscriptions', [\App\Http\Controllers\Owner\SubscriptionController::class, 'list_subscriptions'])->name('list.subscriptions');
         Route::get('/owner-create-subscriptions', [\App\Http\Controllers\Owner\SubscriptionController::class, 'create'])->name('create.subscriptions');
         Route::post('/owner-store-subscriptions', [\App\Http\Controllers\Owner\SubscriptionController::class, 'store'])->name('store.subscriptions');
         Route::get('/owner-edit-subscriptions/{subscription}', [\App\Http\Controllers\Owner\SubscriptionController::class, 'edit'])->name('edit.subscriptions');
         Route::post('/owner-update-subscriptions/{subscription}', [\App\Http\Controllers\Owner\SubscriptionController::class, 'update'])->name('update.subscriptions');
         Route::get('/owner-show-subscriptions/{subscription}', [\App\Http\Controllers\Owner\SubscriptionController::class, 'show'])->name('show.subscriptions');
         Route::delete('/owner-delete-subscriptions/{subscription}', [\App\Http\Controllers\Owner\SubscriptionController::class, 'delete'])->name('delete.subscriptions');
         Route::get('/get-modules', [\App\Http\Controllers\Owner\SubscriptionController::class, 'get_modules_by_id'])->name('modules.get');
         Route::post('/store-package-by-ajax', [\App\Http\Controllers\Owner\SubscriptionController::class, 'store_package_by_ajax'])->name('store.packages.byajax');
         Route::get('/get-subscription-status', [\App\Http\Controllers\Owner\SubscriptionController::class, 'get_subscription_status'])->name('subscriptions.status');

         Route::post('/_generate_subscription_token_/{id}', [\App\Http\Controllers\Owner\SubscriptionController::class, 'generate_subscriptions_tokens'])->name('generate.subscriptions_tokens_');


           //Owner Manage Subscription
           Route::get('/demorequest', [\App\Http\Controllers\Owner\DemorequestController::class, 'index'])->name('owner.list.demorequest');


    });
