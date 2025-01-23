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
    public function handle(Request $request, Closure $next): Response
    {
        $allowedRoles = ['manager', 'admin', 'kodasmspackage', 'chiefoperation', 'godownmanager'];

        if (auth()->check() && auth()->user()->hasAnyRole($allowedRoles)) {
            return $next($request);
        }

        abort(403, 'Unauthorized access.');
    }
}
