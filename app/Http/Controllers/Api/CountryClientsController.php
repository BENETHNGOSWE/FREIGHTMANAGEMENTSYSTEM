<?php

namespace App\Http\Controllers\Api;

use App\Models\Country;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ClientResource;
use App\Http\Resources\ClientCollection;

class CountryClientsController extends Controller
{
    public function index(Request $request, Country $country): ClientCollection
    {
        $this->authorize('view', $country);

        $search = $request->get('search', '');

        $clients = $country
            ->clients()
            ->search($search)
            ->latest()
            ->paginate();

        return new ClientCollection($clients);
    }

    public function store(Request $request, Country $country): ClientResource
    {
        $this->authorize('create', Client::class);

        $validated = $request->validate([
            'name' => ['required', 'max:255', 'string'],
            'logo' => ['nullable', 'max:255', 'string'],
            'address' => ['required', 'max:255', 'string'],
            'city' => ['nullable', 'max:255', 'string'],
            'registration_number' => ['required', 'max:255', 'string'],
            'subdomain' => ['nullable', 'max:255', 'string'],
            'client_type_id' => ['required', 'exists:client_types,id'],
        ]);

        $client = $country->clients()->create($validated);

        return new ClientResource($client);
    }
}
