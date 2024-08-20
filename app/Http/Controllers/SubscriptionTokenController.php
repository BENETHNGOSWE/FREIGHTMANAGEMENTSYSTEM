<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Models\Subscription;
use App\Models\SubscriptionToken;
use App\Models\SubscriptionInvoice;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\SubscriptionTokenStoreRequest;
use App\Http\Requests\SubscriptionTokenUpdateRequest;

class SubscriptionTokenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $this->authorize('view-any', SubscriptionToken::class);

        $search = $request->get('search', '');

        $subscriptionTokens = SubscriptionToken::search($search)
            ->latest()
            ->paginate(5)
            ->withQueryString();

        return view(
            'app.subscription_tokens.index',
            compact('subscriptionTokens', 'search')
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        $this->authorize('create', SubscriptionToken::class);

        $subscriptions = Subscription::pluck('start_date', 'id');
        $subscriptionInvoices = SubscriptionInvoice::pluck('bill_date', 'id');
        $clients = Client::pluck('name', 'id');

        return view(
            'app.subscription_tokens.create',
            compact('subscriptions', 'subscriptionInvoices', 'clients')
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(
        SubscriptionTokenStoreRequest $request
    ): RedirectResponse {
        $this->authorize('create', SubscriptionToken::class);

        $validated = $request->validated();

        $subscriptionToken = SubscriptionToken::create($validated);

        return redirect()
            ->route('subscription-tokens.edit', $subscriptionToken)
            ->withSuccess(__('crud.common.created'));
    }

    /**
     * Display the specified resource.
     */
    public function show(
        Request $request,
        SubscriptionToken $subscriptionToken
    ): View {
        $this->authorize('view', $subscriptionToken);

        return view(
            'app.subscription_tokens.show',
            compact('subscriptionToken')
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(
        Request $request,
        SubscriptionToken $subscriptionToken
    ): View {
        $this->authorize('update', $subscriptionToken);

        $subscriptions = Subscription::pluck('start_date', 'id');
        $subscriptionInvoices = SubscriptionInvoice::pluck('bill_date', 'id');
        $clients = Client::pluck('name', 'id');

        return view(
            'app.subscription_tokens.edit',
            compact(
                'subscriptionToken',
                'subscriptions',
                'subscriptionInvoices',
                'clients'
            )
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        SubscriptionTokenUpdateRequest $request,
        SubscriptionToken $subscriptionToken
    ): RedirectResponse {
        $this->authorize('update', $subscriptionToken);

        $validated = $request->validated();

        $subscriptionToken->update($validated);

        return redirect()
            ->route('subscription-tokens.edit', $subscriptionToken)
            ->withSuccess(__('crud.common.saved'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(
        Request $request,
        SubscriptionToken $subscriptionToken
    ): RedirectResponse {
        $this->authorize('delete', $subscriptionToken);

        $subscriptionToken->delete();

        return redirect()
            ->route('subscription-tokens.index')
            ->withSuccess(__('crud.common.removed'));
    }
}
