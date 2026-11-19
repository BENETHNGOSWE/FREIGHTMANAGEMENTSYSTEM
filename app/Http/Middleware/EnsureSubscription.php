<?php

namespace App\Http\Middleware;

use App\Services\Platform\SubscriptionService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Soft lock. When the subscription is over (or the owner locked the client) the client keeps
 * logging in, but every page except the unlock screen, logout and profile redirects to the
 * unlock page. Nothing is deleted and the data stays intact.
 */
class EnsureSubscription
{
    public function __construct(private SubscriptionService $subscriptions)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check()) {
            return $next($request);
        }

        $user = auth()->user();

        // The platform owner is never locked, and impersonation must still work.
        if ($user->hasRole('owner') || session()->has('impersonator_id')) {
            return $next($request);
        }

        // A user with no client is a platform/admin user: do not lock them out.
        if (! $user->client_id) {
            return $next($request);
        }

        if ($this->subscriptions->isUsable($user->client)) {
            return $next($request);
        }

        $allowed = ['subscription.unlock', 'subscription.unlock.post', 'logout', 'profile'];

        if ($request->routeIs(...$allowed) || $request->is('css/*') || $request->is('assets/*')) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Subscription expired. Enter the unlock token to continue.',
                'status' => $this->subscriptions->status($user->client),
            ], 402);
        }

        return redirect()->route('subscription.unlock')
            ->with('warning', 'Your subscription has ended. Enter the token from your provider to continue.');
    }
}
