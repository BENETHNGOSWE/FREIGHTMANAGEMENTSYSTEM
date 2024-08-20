<?php

namespace App\Http\Controllers\Api;

use App\Models\Subscription;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\SubscriptionTokenResource;
use App\Http\Resources\SubscriptionTokenCollection;

class SubscriptionSubscriptionTokensController extends Controller
{
    public function index(
        Request $request,
        Subscription $subscription
    ): SubscriptionTokenCollection {
        $this->authorize('view', $subscription);

        $search = $request->get('search', '');

        $subscriptionTokens = $subscription
            ->subscriptionTokens()
            ->search($search)
            ->latest()
            ->paginate();

        return new SubscriptionTokenCollection($subscriptionTokens);
    }

    public function store(
        Request $request,
        Subscription $subscription
    ): SubscriptionTokenResource {
        $this->authorize('create', SubscriptionToken::class);

        $validated = $request->validate([
            'subscription_invoice_id' => [
                'required',
                'exists:subscription_invoices,id',
            ],
            'token_' => ['required', 'max:255', 'string'],
            'expire_date' => ['nullable', 'date'],
            'status' => ['required', 'boolean'],
            'client_id' => ['required', 'exists:clients,id'],
        ]);

        $subscriptionToken = $subscription
            ->subscriptionTokens()
            ->create($validated);

        return new SubscriptionTokenResource($subscriptionToken);
    }
}
