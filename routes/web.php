<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\UserRoleController;
use App\Http\Controllers\ClientTypeController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ClientAdminController;
use App\Http\Controllers\PaymentTypeController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\ClientPaymentController;
use App\Http\Controllers\SubscriptionTokenController;
use App\Http\Controllers\SubscriptionInvoiceController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/



Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/',[App\Http\Controllers\DashboardController::class,'index'])->name('dashboard');
Route::get('/add-patient',[App\Http\Controllers\DashboardController::class,'add_patient'])->name('patient.add');
Route::get('/list-patient',[App\Http\Controllers\DashboardController::class,'list_patient'])->name('patient.list');
Route::get('/add-appointment',[App\Http\Controllers\DashboardController::class,'add_appointment'])->name('appointment.add');
Route::get('/list-appointment',[App\Http\Controllers\DashboardController::class,'list_appointment'])->name('appointment.list');
Route::get('/list-doctors-by-schedule',[App\Http\Controllers\DashboardController::class,'list_doctors_by_schedule'])->name('doctor.ondute');
Route::get('/list-doctors',[App\Http\Controllers\DashboardController::class,'list_doctors'])->name('doctor.list');
Route::get('/list-departments',[App\Http\Controllers\DashboardController::class,'department_list'])->name('department.list');

Route::prefix('/')
    ->middleware('auth')
    ->group(function () {
        Route::resource('roles', RoleController::class);
        Route::resource('permissions', PermissionController::class);

        Route::resource('banks', BankController::class);
        Route::resource('branches', BranchController::class);
        Route::resource('clients', ClientController::class);
        Route::resource('client-admins', ClientAdminController::class);
        Route::resource('client-payments', ClientPaymentController::class);
        Route::resource('client-types', ClientTypeController::class);
        Route::resource('countries', CountryController::class);
        Route::resource('messages', MessageController::class);
        Route::resource('modules', ModuleController::class);
        Route::resource('packages', PackageController::class);
        Route::resource('patients', PatientController::class);
        Route::resource('payment-types', PaymentTypeController::class);
        Route::resource('roles', RoleController::class);
        Route::resource('subscriptions', SubscriptionController::class);
        Route::resource(
            'subscription-invoices',
            SubscriptionInvoiceController::class
        );
        Route::resource(
            'subscription-tokens',
            SubscriptionTokenController::class
        );
        Route::resource('users', UserController::class);
        Route::resource('user-roles', UserRoleController::class);
    });
