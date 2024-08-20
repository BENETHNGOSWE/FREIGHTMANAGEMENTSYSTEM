<?php

namespace App\Http\Controllers\Api;

use App\Models\ClientType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ClientResource;
use App\Http\Resources\ClientCollection;

class ClientTypeClientsController extends Controller
{
    public function index(
        Request $request,
        ClientType $clientType
    ): ClientCollection {
        $this->authorize('view', $clientType);

        $search = $request->get('search', '');

        $clients = $clientType
            ->clients()
            ->search($search)
            ->latest()
            ->paginate();

        return new ClientCollection($clients);
    }

    public function store(
        Request $request,
        ClientType $clientType
    ): ClientResource {
        $this->authorize('create', Client::class);

        $validated = $request->validate([
            'name' => ['required', 'max:255', 'string'],
            'logo' => ['nullable', 'max:255', 'string'],
            'address' => ['required', 'max:255', 'string'],
            'country_id' => ['required', 'exists:countries,id'],
            'city' => ['nullable', 'max:255', 'string'],
            'registration_number' => ['required', 'max:255', 'string'],
            'subdomain' => ['nullable', 'max:255', 'string'],
        ]);

        $client = $clientType->clients()->create($validated);

        return new ClientResource($client);
    }
}
