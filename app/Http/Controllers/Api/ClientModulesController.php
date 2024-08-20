<?php

namespace App\Http\Controllers\Api;

use App\Models\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ModuleResource;
use App\Http\Resources\ModuleCollection;

class ClientModulesController extends Controller
{
    public function index(Request $request, Client $client): ModuleCollection
    {
        $this->authorize('view', $client);

        $search = $request->get('search', '');

        $modules = $client
            ->modules()
            ->search($search)
            ->latest()
            ->paginate();

        return new ModuleCollection($modules);
    }

    public function store(Request $request, Client $client): ModuleResource
    {
        $this->authorize('create', Module::class);

        $validated = $request->validate([
            'name' => ['required', 'max:255', 'string'],
            'features' => ['nullable', 'max:255', 'string'],
        ]);

        $module = $client->modules()->create($validated);

        return new ModuleResource($module);
    }
}
