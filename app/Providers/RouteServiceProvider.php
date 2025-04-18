<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/dashboard';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            Route::middleware('api')->prefix('api')->group(base_path('routes/api.php'));

            Route::middleware('web')->group(base_path('routes/web.php'));

            //Addition route
            Route::middleware(['auth', 'web', 'admin',])->group(base_path('routes/admin.php'));
            Route::middleware(['auth', 'web', 'chiefoperation',])->group(base_path('routes/chiefoperation.php'));
            Route::middleware(['auth', 'web', 'manager'])->group(base_path('routes/manager.php'));
            Route::middleware(['auth', 'web', 'multiple'])->group(base_path('routes/multiple.php'));
            Route::middleware(['auth', 'web', 'godownmanager'])->group(base_path('routes/godownmanager.php'));
            Route::middleware(['auth', 'web', 'salesperson'])->group(base_path('routes/salesperson.php'));
            Route::middleware(['auth', 'web', 'SalesManager'])->group(base_path('routes/salesmanager.php'));
            Route::middleware(['auth', 'web', 'receptionist'])->group(base_path('routes/reception.php'));
            Route::middleware(['auth', 'web', 'owner'])->group(base_path('routes/owner.php'));
        });
    }
}
