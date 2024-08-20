<?php

namespace App\Http\Controllers\Api;

use App\Models\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\SubscriptionResource;
use App\Http\Resources\SubscriptionCollection;

class ClientSubscriptionsController extends Controller
{
    public function index(
        Request $request,
        Client $client
    ): SubscriptionCollection {
        $this->authorize('view', $client);

        $search = $request->get('search', '');

        $subscriptions = $client
            ->subscriptions()
            ->search($search)
            ->latest()
            ->paginate();

        return new SubscriptionCollection($subscriptions);
    }

    public function store(
        Request $request,
        Client $client
    ): SubscriptionResource {
        $this->authorize('create', Subscription::class);

        $validated = $request->validate([
            'start_date' => ['required', 'date'],
            'expire_date' => ['required', 'date'],
            'package_id' => ['required', 'exists:packages,id'],
            'status' => ['required', 'in:0,1,2'],
        ]);

        $subscription = $client->subscriptions()->create($validated);

        return new SubscriptionResource($subscription);
    }
}
