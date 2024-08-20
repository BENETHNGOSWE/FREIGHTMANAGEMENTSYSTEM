<?php

namespace App\Http\Controllers\Api;

use App\Models\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ClientAdminResource;
use App\Http\Resources\ClientAdminCollection;

class ClientClientAdminsController extends Controller
{
    public function index(
        Request $request,
        Client $client
    ): ClientAdminCollection {
        $this->authorize('view', $client);

        $search = $request->get('search', '');

        $clientAdmins = $client
            ->clientAdmins()
            ->search($search)
            ->latest()
            ->paginate();

        return new ClientAdminCollection($clientAdmins);
    }

    public function store(Request $request, Client $client): ClientAdminResource
    {
        $this->authorize('create', ClientAdmin::class);

        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
        ]);

        $clientAdmin = $client->clientAdmins()->create($validated);

        return new ClientAdminResource($clientAdmin);
    }
}
