<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BankController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BranchController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\ModuleController;
use App\Http\Controllers\Api\CountryController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\PackageController;
use App\Http\Controllers\Api\PatientController;
use App\Http\Controllers\Api\UserRoleController;
use App\Http\Controllers\Api\RoleUsersController;
use App\Http\Controllers\Api\ClientTypeController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\ClientUsersController;
use App\Http\Controllers\Api\ClientAdminController;
use App\Http\Controllers\Api\PaymentTypeController;
use App\Http\Controllers\Api\CountryUsersController;
use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\Api\UserPatientsController;
use App\Http\Controllers\Api\ClientModulesController;
use App\Http\Controllers\Api\ClientPaymentController;
use App\Http\Controllers\Api\RoleUserRolesController;
use App\Http\Controllers\Api\UserUserRolesController;
use App\Http\Controllers\Api\ClientBranchesController;
use App\Http\Controllers\Api\ClientPackagesController;
use App\Http\Controllers\Api\ClientMessagesController;
use App\Http\Controllers\Api\CountryClientsController;
use App\Http\Controllers\Api\ModulePackagesController;
use App\Http\Controllers\Api\PackageModulesController;
use App\Http\Controllers\Api\UserClientAdminsController;
use App\Http\Controllers\Api\ClientTypeClientsController;
use App\Http\Controllers\Api\SubscriptionTokenController;
use App\Http\Controllers\Api\ClientClientAdminsController;
use App\Http\Controllers\Api\ClientSubscriptionsController;
use App\Http\Controllers\Api\SubscriptionInvoiceController;
use App\Http\Controllers\Api\PackageSubscriptionsController;
use App\Http\Controllers\Api\ClientSubscriptionTokensController;
use App\Http\Controllers\Api\ClientSubscriptionInvoicesController;
use App\Http\Controllers\Api\SubscriptionSubscriptionTokensController;
use App\Http\Controllers\Api\SubscriptionInvoiceSubscriptionTokensController;

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

Route::middleware('auth:sanctum')
    ->get('/user', function (Request $request) {
        return $request->user();
    })
    ->name('api.user');

Route::name('api.')
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::apiResource('roles', RoleController::class);
        Route::apiResource('permissions', PermissionController::class);

        Route::apiResource('banks', BankController::class);

        Route::apiResource('branches', BranchController::class);

        Route::apiResource('clients', ClientController::class);

        // Client Users
        Route::get('/clients/{client}/users', [
            ClientUsersController::class,
            'index',
        ])->name('clients.users.index');
        Route::post('/clients/{client}/users', [
            ClientUsersController::class,
            'store',
        ])->name('clients.users.store');

        // Client Branches
        Route::get('/clients/{client}/branches', [
            ClientBranchesController::class,
            'index',
        ])->name('clients.branches.index');
        Route::post('/clients/{client}/branches', [
            ClientBranchesController::class,
            'store',
        ])->name('clients.branches.store');

        // Client Subscriptions
        Route::get('/clients/{client}/subscriptions', [
            ClientSubscriptionsController::class,
            'index',
        ])->name('clients.subscriptions.index');
        Route::post('/clients/{client}/subscriptions', [
            ClientSubscriptionsController::class,
            'store',
        ])->name('clients.subscriptions.store');

        // Client Subscription Tokens
        Route::get('/clients/{client}/subscription-tokens', [
            ClientSubscriptionTokensController::class,
            'index',
        ])->name('clients.subscription-tokens.index');
        Route::post('/clients/{client}/subscription-tokens', [
            ClientSubscriptionTokensController::class,
            'store',
        ])->name('clients.subscription-tokens.store');

        // Client Client Admins
        Route::get('/clients/{client}/client-admins', [
            ClientClientAdminsController::class,
            'index',
        ])->name('clients.client-admins.index');
        Route::post('/clients/{client}/client-admins', [
            ClientClientAdminsController::class,
            'store',
        ])->name('clients.client-admins.store');

        // Client Modules
        Route::get('/clients/{client}/modules', [
            ClientModulesController::class,
            'index',
        ])->name('clients.modules.index');
        Route::post('/clients/{client}/modules', [
            ClientModulesController::class,
            'store',
        ])->name('clients.modules.store');

        // Client Packages
        Route::get('/clients/{client}/packages', [
            ClientPackagesController::class,
            'index',
        ])->name('clients.packages.index');
        Route::post('/clients/{client}/packages', [
            ClientPackagesController::class,
            'store',
        ])->name('clients.packages.store');

        // Client Subscription Invoices
        Route::get('/clients/{client}/subscription-invoices', [
            ClientSubscriptionInvoicesController::class,
            'index',
        ])->name('clients.subscription-invoices.index');
        Route::post('/clients/{client}/subscription-invoices', [
            ClientSubscriptionInvoicesController::class,
            'store',
        ])->name('clients.subscription-invoices.store');

        // Client Messages
        Route::get('/clients/{client}/messages', [
            ClientMessagesController::class,
            'index',
        ])->name('clients.messages.index');
        Route::post('/clients/{client}/messages', [
            ClientMessagesController::class,
            'store',
        ])->name('clients.messages.store');

        Route::apiResource('client-admins', ClientAdminController::class);

        Route::apiResource('client-payments', ClientPaymentController::class);

        Route::apiResource('client-types', ClientTypeController::class);

        // ClientType Clients
        Route::get('/client-types/{clientType}/clients', [
            ClientTypeClientsController::class,
            'index',
        ])->name('client-types.clients.index');
        Route::post('/client-types/{clientType}/clients', [
            ClientTypeClientsController::class,
            'store',
        ])->name('client-types.clients.store');

        Route::apiResource('countries', CountryController::class);

        // Country Users
        Route::get('/countries/{country}/users', [
            CountryUsersController::class,
            'index',
        ])->name('countries.users.index');
        Route::post('/countries/{country}/users', [
            CountryUsersController::class,
            'store',
        ])->name('countries.users.store');

        // Country Clients
        Route::get('/countries/{country}/clients', [
            CountryClientsController::class,
            'index',
        ])->name('countries.clients.index');
        Route::post('/countries/{country}/clients', [
            CountryClientsController::class,
            'store',
        ])->name('countries.clients.store');

        Route::apiResource('messages', MessageController::class);

        Route::apiResource('modules', ModuleController::class);

        // Module Packages
        Route::get('/modules/{module}/packages', [
            ModulePackagesController::class,
            'index',
        ])->name('modules.packages.index');
        Route::post('/modules/{module}/packages/{package}', [
            ModulePackagesController::class,
            'store',
        ])->name('modules.packages.store');
        Route::delete('/modules/{module}/packages/{package}', [
            ModulePackagesController::class,
            'destroy',
        ])->name('modules.packages.destroy');

        Route::apiResource('packages', PackageController::class);

        // Package Subscriptions
        Route::get('/packages/{package}/subscriptions', [
            PackageSubscriptionsController::class,
            'index',
        ])->name('packages.subscriptions.index');
        Route::post('/packages/{package}/subscriptions', [
            PackageSubscriptionsController::class,
            'store',
        ])->name('packages.subscriptions.store');

        // Package Modules
        Route::get('/packages/{package}/modules', [
            PackageModulesController::class,
            'index',
        ])->name('packages.modules.index');
        Route::post('/packages/{package}/modules/{module}', [
            PackageModulesController::class,
            'store',
        ])->name('packages.modules.store');
        Route::delete('/packages/{package}/modules/{module}', [
            PackageModulesController::class,
            'destroy',
        ])->name('packages.modules.destroy');

        Route::apiResource('patients', PatientController::class);

        Route::apiResource('payment-types', PaymentTypeController::class);

        Route::apiResource('roles', RoleController::class);

        // Role Users
        Route::get('/roles/{role}/users', [
            RoleUsersController::class,
            'index',
        ])->name('roles.users.index');
        Route::post('/roles/{role}/users', [
            RoleUsersController::class,
            'store',
        ])->name('roles.users.store');

        // Role User Roles
        Route::get('/roles/{role}/user-roles', [
            RoleUserRolesController::class,
            'index',
        ])->name('roles.user-roles.index');
        Route::post('/roles/{role}/user-roles', [
            RoleUserRolesController::class,
            'store',
        ])->name('roles.user-roles.store');

        Route::apiResource('subscriptions', SubscriptionController::class);

        // Subscription Subscription Tokens
        Route::get('/subscriptions/{subscription}/subscription-tokens', [
            SubscriptionSubscriptionTokensController::class,
            'index',
        ])->name('subscriptions.subscription-tokens.index');
        Route::post('/subscriptions/{subscription}/subscription-tokens', [
            SubscriptionSubscriptionTokensController::class,
            'store',
        ])->name('subscriptions.subscription-tokens.store');

        Route::apiResource(
            'subscription-invoices',
            SubscriptionInvoiceController::class
        );

        // SubscriptionInvoice Subscription Tokens
        Route::get(
            '/subscription-invoices/{subscriptionInvoice}/subscription-tokens',
            [SubscriptionInvoiceSubscriptionTokensController::class, 'index']
        )->name('subscription-invoices.subscription-tokens.index');
        Route::post(
            '/subscription-invoices/{subscriptionInvoice}/subscription-tokens',
            [SubscriptionInvoiceSubscriptionTokensController::class, 'store']
        )->name('subscription-invoices.subscription-tokens.store');

        Route::apiResource(
            'subscription-tokens',
            SubscriptionTokenController::class
        );

        Route::apiResource('users', UserController::class);

        // User Patients
        Route::get('/users/{user}/patients', [
            UserPatientsController::class,
            'index',
        ])->name('users.patients.index');
        Route::post('/users/{user}/patients', [
            UserPatientsController::class,
            'store',
        ])->name('users.patients.store');

        // User Client Admins
        Route::get('/users/{user}/client-admins', [
            UserClientAdminsController::class,
            'index',
        ])->name('users.client-admins.index');
        Route::post('/users/{user}/client-admins', [
            UserClientAdminsController::class,
            'store',
        ])->name('users.client-admins.store');

        // User User Roles
        Route::get('/users/{user}/user-roles', [
            UserUserRolesController::class,
            'index',
        ])->name('users.user-roles.index');
        Route::post('/users/{user}/user-roles', [
            UserUserRolesController::class,
            'store',
        ])->name('users.user-roles.store');

        Route::apiResource('user-roles', UserRoleController::class);
    });
