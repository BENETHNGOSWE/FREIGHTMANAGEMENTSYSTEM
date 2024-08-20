<?php

namespace App\Http\Controllers\Api;

use App\Models\ClientAdmin;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\ClientAdminResource;
use App\Http\Resources\ClientAdminCollection;
use App\Http\Requests\ClientAdminStoreRequest;
use App\Http\Requests\ClientAdminUpdateRequest;

class ClientAdminController extends Controller
{
    public function index(Request $request): ClientAdminCollection
    {
        $this->authorize('view-any', ClientAdmin::class);

        $search = $request->get('search', '');

        $clientAdmins = ClientAdmin::search($search)
            ->latest()
            ->paginate();

        return new ClientAdminCollection($clientAdmins);
    }

    public function store(ClientAdminStoreRequest $request): ClientAdminResource
    {
        $this->authorize('create', ClientAdmin::class);

        $validated = $request->validated();

        $clientAdmin = ClientAdmin::create($validated);

        return new ClientAdminResource($clientAdmin);
    }

    public function show(
        Request $request,
        ClientAdmin $clientAdmin
    ): ClientAdminResource {
        $this->authorize('view', $clientAdmin);

        return new ClientAdminResource($clientAdmin);
    }

    public function update(
        ClientAdminUpdateRequest $request,
        ClientAdmin $clientAdmin
    ): ClientAdminResource {
        $this->authorize('update', $clientAdmin);

        $validated = $request->validated();

        $clientAdmin->update($validated);

        return new ClientAdminResource($clientAdmin);
    }

    public function destroy(
        Request $request,
        ClientAdmin $clientAdmin
    ): Response {
        $this->authorize('delete', $clientAdmin);

        $clientAdmin->delete();

        return response()->noContent();
    }
}
