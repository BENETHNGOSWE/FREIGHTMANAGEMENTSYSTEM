<?php

use App\Http\Controllers\Cargo\CargoOperationsController;
use App\Http\Controllers\Cargo\MergePaymentController;
use App\Http\Controllers\Cargo\ReleaseControlController;
use App\Http\Controllers\Cargo\StorageController;
use App\Http\Controllers\Cargo\VerificationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Cargo operations, storage and release control
|--------------------------------------------------------------------------
| Everything is scoped to the logged-in user's client. The public tracking pages
| live in routes/web.php because they must work without a login.
*/

Route::middleware(['auth', 'web', 'module:cargo'])->group(function () {

    // Cargo hub
    Route::get('cargo', [CargoOperationsController::class, 'index'])->name('cargo.index');
    Route::get('cargo/{loadpackage}', [CargoOperationsController::class, 'show'])->name('cargo.show');
    Route::post('cargo/{loadpackage}/details', [CargoOperationsController::class, 'updateDetails'])->name('cargo.details');
    Route::delete('cargo/{loadpackage}/photo/{photo}', [CargoOperationsController::class, 'deletePhoto'])->name('cargo.photo.delete');
    Route::post('cargo/{loadpackage}/status', [CargoOperationsController::class, 'changeStatus'])->name('cargo.status');

    // Dar warehouse verification
    Route::get('verification', [VerificationController::class, 'index'])->name('cargo.verification');
    Route::post('verification/{loadpackage}', [VerificationController::class, 'store'])->name('cargo.verification.store');

    // Release control / pickup
    Route::get('release-control', [ReleaseControlController::class, 'index'])->name('cargo.release.index');
    Route::post('release-control/{loadpackage}/release', [ReleaseControlController::class, 'release'])->name('cargo.release.release');
    Route::post('release-control/{loadpackage}/collect', [ReleaseControlController::class, 'collect'])->name('cargo.release.collect');
    Route::get('pickup-note/{invoice}', [ReleaseControlController::class, 'pickupNote'])->name('cargo.pickup.note');
    Route::post('pickup-note/{note}/cancel', [ReleaseControlController::class, 'cancelNote'])->name('cargo.pickup.cancel');

    // Storage and release control are separate modules a package may or may not include.
    // NOTE: the path is warehouse/storage, not /storage: public/storage is the
    // upload symlink, so a bare /storage URL is swallowed by the web server.
    Route::get('warehouse/storage', [StorageController::class, 'index'])->name('storage.index');
    Route::get('warehouse/storage/payments', [StorageController::class, 'payments'])->name('storage.payments');
    Route::get('warehouse/storage/settings', [StorageController::class, 'settings'])->name('storage.settings');
    Route::put('warehouse/storage/settings', [StorageController::class, 'updateSettings'])->name('storage.settings.update');
    Route::post('warehouse/storage/customer-rate', [StorageController::class, 'updateCustomerRate'])->name('storage.customer_rate');
    Route::post('warehouse/storage/accrue', [StorageController::class, 'accrue'])->name('storage.accrue');
    Route::post('warehouse/storage/start-existing', [StorageController::class, 'startForLoads'])->name('storage.start_existing');
    Route::get('warehouse/storage/{storage}', [StorageController::class, 'show'])->name('storage.show');
    Route::post('warehouse/storage/{storage}/pay', [StorageController::class, 'pay'])->name('storage.pay');
    Route::post('warehouse/storage/{storage}/waive', [StorageController::class, 'waive'])->name('storage.waive');

    // Merge payment
    Route::get('merge-payment', [MergePaymentController::class, 'create'])->name('invoices.merge.create');
    Route::post('merge-payment/preview', [MergePaymentController::class, 'preview'])->name('invoices.merge.preview');
    Route::post('merge-payment', [MergePaymentController::class, 'store'])->name('invoices.merge.store');
});
