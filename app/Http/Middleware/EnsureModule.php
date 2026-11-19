<?php

namespace App\Http\Middleware;

use App\Services\Platform\ModuleAccess;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * usage: ->middleware('module:cargo')
 * A client can only reach a module their package includes. The owner always can.
 */
class EnsureModule
{
    public function __construct(private ModuleAccess $access)
    {
    }

    public function handle(Request $request, Closure $next, string $module): Response
    {
        if (! auth()->check()) {
            return $next($request);
        }

        if (! $this->access->allows($module, auth()->user())) {
            abort(403, 'This module is not included in your subscription package.');
        }

        return $next($request);
    }
}
