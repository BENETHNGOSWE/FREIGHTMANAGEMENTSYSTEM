<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Multiple
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $permission = null): Response
    {
        if (!auth()->check()) {
            abort(403, 'Unauthorized access');
        }

        if ($permission && !auth()->user()->can($permission)) {
            abort(403, 'You do not have permission to view this resource.');
        }

        return $next($request);
    }

}
