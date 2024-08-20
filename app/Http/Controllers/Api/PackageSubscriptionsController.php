<?php

namespace App\Http\Controllers\Api;

use App\Models\Package;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\SubscriptionResource;
use App\Http\Resources\SubscriptionCollection;

class PackageSubscriptionsController extends Controller
{
    public function index(
        Request $request,
        Package $package
    ): SubscriptionCollection {
        $this->authorize('view', $package);

        $search = $request->get('search', '');

        $subscriptions = $package
            ->subscriptions()
            ->search($search)
            ->latest()
            ->paginate();

        return new SubscriptionCollection($subscriptions);
    }

    public function store(
        Request $request,
        Package $package
    ): SubscriptionResource {
        $this->authorize('create', Subscription::class);

        $validated = $request->validate([
            'client_id' => ['required', 'exists:clients,id'],
            'start_date' => ['required', 'date'],
            'expire_date' => ['required', 'date'],
            'status' => ['required', 'in:0,1,2'],
        ]);

        $subscription = $package->subscriptions()->create($validated);

        return new SubscriptionResource($subscription);
    }
}
