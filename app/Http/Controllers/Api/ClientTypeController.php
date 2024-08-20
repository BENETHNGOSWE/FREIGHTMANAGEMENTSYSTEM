<?php

namespace App\Http\Controllers\Api;

use App\Models\ClientType;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\ClientTypeResource;
use App\Http\Resources\ClientTypeCollection;
use App\Http\Requests\ClientTypeStoreRequest;
use App\Http\Requests\ClientTypeUpdateRequest;

class ClientTypeController extends Controller
{
    public function index(Request $request): ClientTypeCollection
    {
        $this->authorize('view-any', ClientType::class);

        $search = $request->get('search', '');

        $clientTypes = ClientType::search($search)
            ->latest()
            ->paginate();

        return new ClientTypeCollection($clientTypes);
    }

    public function store(ClientTypeStoreRequest $request): ClientTypeResource
    {
        $this->authorize('create', ClientType::class);

        $validated = $request->validated();

        $clientType = ClientType::create($validated);

        return new ClientTypeResource($clientType);
    }

    public function show(
        Request $request,
        ClientType $clientType
    ): ClientTypeResource {
        $this->authorize('view', $clientType);

        return new ClientTypeResource($clientType);
    }

    public function update(
        ClientTypeUpdateRequest $request,
        ClientType $clientType
    ): ClientTypeResource {
        $this->authorize('update', $clientType);

        $validated = $request->validated();

        $clientType->update($validated);

        return new ClientTypeResource($clientType);
    }

    public function destroy(Request $request, ClientType $clientType): Response
    {
        $this->authorize('delete', $clientType);

        $clientType->delete();

        return response()->noContent();
    }
}
