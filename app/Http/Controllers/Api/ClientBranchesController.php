<?php

namespace App\Http\Controllers\Api;

use App\Models\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\BranchResource;
use App\Http\Resources\BranchCollection;

class ClientBranchesController extends Controller
{
    public function index(Request $request, Client $client): BranchCollection
    {
        $this->authorize('view', $client);

        $search = $request->get('search', '');

        $branches = $client
            ->branches()
            ->search($search)
            ->latest()
            ->paginate();

        return new BranchCollection($branches);
    }

    public function store(Request $request, Client $client): BranchResource
    {
        $this->authorize('create', Branch::class);

        $validated = $request->validate([
            'branch_name' => ['required', 'max:255', 'string'],
        ]);

        $branch = $client->branches()->create($validated);

        return new BranchResource($branch);
    }
}
