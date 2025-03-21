<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CustomercategoryController;
use App\Http\Controllers\Api\DemorequestController;



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/login', [AuthController::class, 'login'])->name('api.login');



// Demo request API Manage Here
Route::prefix('demorequest')->middleware('throttle:60,1')->group(function (){
    Route::post('/', [DemorequestController::class, 'index']);
    Route::post('/storedemorequests', [DemorequestController::class, 'store']);
});

Route::middleware('auth:sanctum')
    ->get('/user', function (Request $request) {
        return $request->user();
    })
    ->name('api.user');



Route::name('api.')
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::apiResource('user-roles', UserRoleController::class);

        Route::prefix('customer-categories')->group(function () {
            Route::get('/', [CustomercategoryController::class, 'index']);
            Route::post('/', [CustomercategoryController::class, 'store']);
            Route::get('/{id}', [CustomercategoryController::class, 'show']);
            Route::put('/{id}', [CustomercategoryController::class, 'update']);
            Route::delete('/{id}', [CustomercategoryController::class, 'destroy']);
        });

       
    });
