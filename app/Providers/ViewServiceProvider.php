<?php

namespace App\Providers;

use App\Services\Platform\ModuleAccess;
use App\Services\Platform\SidebarService;
use App\Services\Platform\SubscriptionService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;


class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // The menu depends on who is signed in: the platform owner gets the platform menu,
        // a client gets only the modules their package includes.
        View::composer('layouts.sidebar', function ($view) {
            if (! auth()->check()) {
                return;
            }

            // A locked client only gets their own account: showing them Cargo or Storage
            // they cannot open is exactly the confusion this layer exists to stop.
            // "No subscription" counts as locked too — they are on the token screen.
            $subscriptions = app(SubscriptionService::class);
            $locked = ! app(ModuleAccess::class)->isOwner(auth()->user())
                && ! $subscriptions->isUsable(auth()->user()->client);

            $view->with('menu', $locked
                ? app(SidebarService::class)->accountMenu()
                : app(SidebarService::class)->menu());
        });

        View::composer(['layouts.partials._nav_header', 'layouts.header'], function ($view) {
            if (! auth()->check()) {
                return;
            }

            $user = auth()->user();
            $subscriptions = app(SubscriptionService::class);

            $view->with([
                'subscriptionStatus' => $subscriptions->status($user->client),
                'subscriptionDaysLeft' => $subscriptions->daysLeft($user->client),
                'smsLeft' => $subscriptions->smsLeft($user->client),
                'isPlatformOwner' => app(ModuleAccess::class)->isOwner($user),
            ]);
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
