<?php

namespace App\Http\Controllers\Api;

use App\Models\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\PackageResource;
use App\Http\Resources\PackageCollection;

class ClientPackagesController extends Controller
{
    public function index(Request $request, Client $client): PackageCollection
    {
        $this->authorize('view', $client);

        $search = $request->get('search', '');

        $packages = $client
            ->packages()
            ->search($search)
            ->latest()
            ->paginate();

        return new PackageCollection($packages);
    }

    public function store(Request $request, Client $client): PackageResource
    {
        $this->authorize('create', Package::class);

        $validated = $request->validate([
            'name' => ['required', 'max:255', 'string'],
            'monthly_fee' => ['required', 'numeric'],
            'yearly_fee' => ['required', 'numeric'],
            'features' => ['nullable', 'max:255', 'string'],
        ]);

        $package = $client->packages()->create($validated);

        return new PackageResource($package);
    }
}
