<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ClientAdminResource;
use App\Http\Resources\ClientAdminCollection;

class UserClientAdminsController extends Controller
{
    public function index(Request $request, User $user): ClientAdminCollection
    {
        $this->authorize('view', $user);

        $search = $request->get('search', '');

        $clientAdmins = $user
            ->clientAdmins()
            ->search($search)
            ->latest()
            ->paginate();

        return new ClientAdminCollection($clientAdmins);
    }

    public function store(Request $request, User $user): ClientAdminResource
    {
        $this->authorize('create', ClientAdmin::class);

        $validated = $request->validate([
            'client_id' => ['required', 'exists:clients,id'],
        ]);

        $clientAdmin = $user->clientAdmins()->create($validated);

        return new ClientAdminResource($clientAdmin);
    }
}
