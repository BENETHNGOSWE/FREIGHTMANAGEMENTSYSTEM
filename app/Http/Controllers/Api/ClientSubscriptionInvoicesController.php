<?php

namespace App\Http\Controllers\Api;

use App\Models\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\SubscriptionInvoiceResource;
use App\Http\Resources\SubscriptionInvoiceCollection;

class ClientSubscriptionInvoicesController extends Controller
{
    public function index(
        Request $request,
        Client $client
    ): SubscriptionInvoiceCollection {
        $this->authorize('view', $client);

        $search = $request->get('search', '');

        $subscriptionInvoices = $client
            ->subscriptionInvoices()
            ->search($search)
            ->latest()
            ->paginate();

        return new SubscriptionInvoiceCollection($subscriptionInvoices);
    }

    public function store(
        Request $request,
        Client $client
    ): SubscriptionInvoiceResource {
        $this->authorize('create', SubscriptionInvoice::class);

        $validated = $request->validate([
            'amount' => ['required', 'numeric'],
            'balance' => ['required', 'numeric'],
            'bill_date' => ['required', 'date'],
            'due_date' => ['required', 'date'],
            'status' => ['required', 'in:0,1,2'],
            'subscription_id' => ['required', 'exists:subscriptions,id'],
        ]);

        $subscriptionInvoice = $client
            ->subscriptionInvoices()
            ->create($validated);

        return new SubscriptionInvoiceResource($subscriptionInvoice);
    }
}
