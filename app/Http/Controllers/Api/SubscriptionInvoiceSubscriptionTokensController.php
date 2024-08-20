<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\SubscriptionInvoice;
use App\Http\Controllers\Controller;
use App\Http\Resources\SubscriptionTokenResource;
use App\Http\Resources\SubscriptionTokenCollection;

class SubscriptionInvoiceSubscriptionTokensController extends Controller
{
    public function index(
        Request $request,
        SubscriptionInvoice $subscriptionInvoice
    ): SubscriptionTokenCollection {
        $this->authorize('view', $subscriptionInvoice);

        $search = $request->get('search', '');

        $subscriptionTokens = $subscriptionInvoice
            ->subscriptionTokens()
            ->search($search)
            ->latest()
            ->paginate();

        return new SubscriptionTokenCollection($subscriptionTokens);
    }

    public function store(
        Request $request,
        SubscriptionInvoice $subscriptionInvoice
    ): SubscriptionTokenResource {
        $this->authorize('create', SubscriptionToken::class);

        $validated = $request->validate([
            'subscription_id' => ['required', 'exists:subscriptions,id'],
            'token_' => ['required', 'max:255', 'string'],
            'expire_date' => ['nullable', 'date'],
            'status' => ['required', 'boolean'],
            'client_id' => ['required', 'exists:clients,id'],
        ]);

        $subscriptionToken = $subscriptionInvoice
            ->subscriptionTokens()
            ->create($validated);

        return new SubscriptionTokenResource($subscriptionToken);
    }
}
