<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\SubscriptionToken;
use App\Http\Controllers\Controller;
use App\Http\Resources\SubscriptionTokenResource;
use App\Http\Resources\SubscriptionTokenCollection;
use App\Http\Requests\SubscriptionTokenStoreRequest;
use App\Http\Requests\SubscriptionTokenUpdateRequest;

class SubscriptionTokenController extends Controller
{
    public function index(Request $request): SubscriptionTokenCollection
    {
        $this->authorize('view-any', SubscriptionToken::class);

        $search = $request->get('search', '');

        $subscriptionTokens = SubscriptionToken::search($search)
            ->latest()
            ->paginate();

        return new SubscriptionTokenCollection($subscriptionTokens);
    }

    public function store(
        SubscriptionTokenStoreRequest $request
    ): SubscriptionTokenResource {
        $this->authorize('create', SubscriptionToken::class);

        $validated = $request->validated();

        $subscriptionToken = SubscriptionToken::create($validated);

        return new SubscriptionTokenResource($subscriptionToken);
    }

    public function show(
        Request $request,
        SubscriptionToken $subscriptionToken
    ): SubscriptionTokenResource {
        $this->authorize('view', $subscriptionToken);

        return new SubscriptionTokenResource($subscriptionToken);
    }

    public function update(
        SubscriptionTokenUpdateRequest $request,
        SubscriptionToken $subscriptionToken
    ): SubscriptionTokenResource {
        $this->authorize('update', $subscriptionToken);

        $validated = $request->validated();

        $subscriptionToken->update($validated);

        return new SubscriptionTokenResource($subscriptionToken);
    }

    public function destroy(
        Request $request,
        SubscriptionToken $subscriptionToken
    ): Response {
        $this->authorize('delete', $subscriptionToken);

        $subscriptionToken->delete();

        return response()->noContent();
    }
}
