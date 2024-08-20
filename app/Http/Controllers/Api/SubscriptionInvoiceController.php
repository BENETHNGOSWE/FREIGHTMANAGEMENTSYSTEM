<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\SubscriptionInvoice;
use App\Http\Controllers\Controller;
use App\Http\Resources\SubscriptionInvoiceResource;
use App\Http\Resources\SubscriptionInvoiceCollection;
use App\Http\Requests\SubscriptionInvoiceStoreRequest;
use App\Http\Requests\SubscriptionInvoiceUpdateRequest;

class SubscriptionInvoiceController extends Controller
{
    public function index(Request $request): SubscriptionInvoiceCollection
    {
        $this->authorize('view-any', SubscriptionInvoice::class);

        $search = $request->get('search', '');

        $subscriptionInvoices = SubscriptionInvoice::search($search)
            ->latest()
            ->paginate();

        return new SubscriptionInvoiceCollection($subscriptionInvoices);
    }

    public function store(
        SubscriptionInvoiceStoreRequest $request
    ): SubscriptionInvoiceResource {
        $this->authorize('create', SubscriptionInvoice::class);

        $validated = $request->validated();

        $subscriptionInvoice = SubscriptionInvoice::create($validated);

        return new SubscriptionInvoiceResource($subscriptionInvoice);
    }

    public function show(
        Request $request,
        SubscriptionInvoice $subscriptionInvoice
    ): SubscriptionInvoiceResource {
        $this->authorize('view', $subscriptionInvoice);

        return new SubscriptionInvoiceResource($subscriptionInvoice);
    }

    public function update(
        SubscriptionInvoiceUpdateRequest $request,
        SubscriptionInvoice $subscriptionInvoice
    ): SubscriptionInvoiceResource {
        $this->authorize('update', $subscriptionInvoice);

        $validated = $request->validated();

        $subscriptionInvoice->update($validated);

        return new SubscriptionInvoiceResource($subscriptionInvoice);
    }

    public function destroy(
        Request $request,
        SubscriptionInvoice $subscriptionInvoice
    ): Response {
        $this->authorize('delete', $subscriptionInvoice);

        $subscriptionInvoice->delete();

        return response()->noContent();
    }
}
