<?php

namespace App\Http\Controllers\Api;

use App\Models\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\SubscriptionTokenResource;
use App\Http\Resources\SubscriptionTokenCollection;

class ClientSubscriptionTokensController extends Controller
{
    public function index(
        Request $request,
        Client $client
    ): SubscriptionTokenCollection {
        $this->authorize('view', $client);

        $search = $request->get('search', '');

        $subscriptionTokens = $client
            ->subscriptionTokens()
            ->search($search)
            ->latest()
            ->paginate();

        return new SubscriptionTokenCollection($subscriptionTokens);
    }

    public function store(
        Request $request,
        Client $client
    ): SubscriptionTokenResource {
        $this->authorize('create', SubscriptionToken::class);

        $validated = $request->validate([
            'subscription_id' => ['required', 'exists:subscriptions,id'],
            'subscription_invoice_id' => [
                'required',
                'exists:subscription_invoices,id',
            ],
            'token_' => ['required', 'max:255', 'string'],
            'expire_date' => ['nullable', 'date'],
            'status' => ['required', 'boolean'],
        ]);

        $subscriptionToken = $client->subscriptionTokens()->create($validated);

        return new SubscriptionTokenResource($subscriptionToken);
    }
}
