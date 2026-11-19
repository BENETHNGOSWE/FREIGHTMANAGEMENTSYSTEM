<?php

use App\Http\Controllers\Platform\PlatformController;
use App\Http\Controllers\Platform\UnlockController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Subscription lock / unlock (any signed-in user)
|--------------------------------------------------------------------------
| The lock screen must be reachable while the account is locked, so these two
| routes sit behind auth only - never behind the subscription middleware.
*/
Route::middleware(['auth', 'web'])->group(function () {
    Route::get('subscription', [UnlockController::class, 'show'])->name('subscription.unlock');
    Route::post('subscription', [UnlockController::class, 'store'])->name('subscription.unlock.post');
});

/*
|--------------------------------------------------------------------------
| Platform owner desk
|--------------------------------------------------------------------------
| Clients, subscriptions, packages/modules, unlock tokens and platform settings.
| Owner only - a client never sees these screens.
*/
Route::middleware(['auth', 'web', 'owner'])->prefix('platform')->group(function () {
    Route::get('clients', [PlatformController::class, 'clients'])->name('platform.clients');
    Route::get('subscriptions', [PlatformController::class, 'subscriptions'])->name('platform.subscriptions');
    Route::post('subscriptions', [PlatformController::class, 'storeSubscription'])->name('platform.subscriptions.store');
    Route::post('subscriptions/{subscription}/renew', [PlatformController::class, 'renew'])->name('platform.subscriptions.renew');
    Route::post('clients/{client}/lock', [PlatformController::class, 'lock'])->name('platform.clients.lock');
    Route::post('clients/{client}/unlock', [PlatformController::class, 'unlock'])->name('platform.clients.unlock');

    Route::get('tokens', [PlatformController::class, 'tokens'])->name('platform.tokens');
    Route::post('tokens', [PlatformController::class, 'issueToken'])->name('platform.tokens.issue');
    Route::post('tokens/{token}/revoke', [PlatformController::class, 'revokeToken'])->name('platform.tokens.revoke');

    Route::get('modules', [PlatformController::class, 'modules'])->name('platform.modules');
    Route::post('modules/{package}', [PlatformController::class, 'syncModules'])->name('platform.modules.sync');

    Route::get('settings', [PlatformController::class, 'settings'])->name('platform.settings');
    Route::put('settings', [PlatformController::class, 'updateSettings'])->name('platform.settings.update');
});
