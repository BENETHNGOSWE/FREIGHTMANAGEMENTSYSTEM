<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Models\Subscription;
use App\Models\SubscriptionInvoice;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\SubscriptionInvoiceStoreRequest;
use App\Http\Requests\SubscriptionInvoiceUpdateRequest;

class SubscriptionInvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $this->authorize('view-any', SubscriptionInvoice::class);

        $search = $request->get('search', '');

        $subscriptionInvoices = SubscriptionInvoice::search($search)
            ->latest()
            ->paginate(5)
            ->withQueryString();

        return view(
            'app.subscription_invoices.index',
            compact('subscriptionInvoices', 'search')
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        $this->authorize('create', SubscriptionInvoice::class);

        $subscriptions = Subscription::pluck('start_date', 'id');
        $clients = Client::pluck('name', 'id');

        return view(
            'app.subscription_invoices.create',
            compact('subscriptions', 'clients')
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(
        SubscriptionInvoiceStoreRequest $request
    ): RedirectResponse {
        $this->authorize('create', SubscriptionInvoice::class);

        $validated = $request->validated();

        $subscriptionInvoice = SubscriptionInvoice::create($validated);

        return redirect()
            ->route('subscription-invoices.edit', $subscriptionInvoice)
            ->withSuccess(__('crud.common.created'));
    }

    /**
     * Display the specified resource.
     */
    public function show(
        Request $request,
        SubscriptionInvoice $subscriptionInvoice
    ): View {
        $this->authorize('view', $subscriptionInvoice);

        return view(
            'app.subscription_invoices.show',
            compact('subscriptionInvoice')
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(
        Request $request,
        SubscriptionInvoice $subscriptionInvoice
    ): View {
        $this->authorize('update', $subscriptionInvoice);

        $subscriptions = Subscription::pluck('start_date', 'id');
        $clients = Client::pluck('name', 'id');

        return view(
            'app.subscription_invoices.edit',
            compact('subscriptionInvoice', 'subscriptions', 'clients')
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        SubscriptionInvoiceUpdateRequest $request,
        SubscriptionInvoice $subscriptionInvoice
    ): RedirectResponse {
        $this->authorize('update', $subscriptionInvoice);

        $validated = $request->validated();

        $subscriptionInvoice->update($validated);

        return redirect()
            ->route('subscription-invoices.edit', $subscriptionInvoice)
            ->withSuccess(__('crud.common.saved'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(
        Request $request,
        SubscriptionInvoice $subscriptionInvoice
    ): RedirectResponse {
        $this->authorize('delete', $subscriptionInvoice);

        $subscriptionInvoice->delete();

        return redirect()
            ->route('subscription-invoices.index')
            ->withSuccess(__('crud.common.removed'));
    }
}
