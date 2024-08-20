<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserRoleResource;
use App\Http\Resources\UserRoleCollection;

class UserUserRolesController extends Controller
{
    public function index(Request $request, User $user): UserRoleCollection
    {
        $this->authorize('view', $user);

        $search = $request->get('search', '');

        $userRoles = $user
            ->userRoles()
            ->search($search)
            ->latest()
            ->paginate();

        return new UserRoleCollection($userRoles);
    }

    public function store(Request $request, User $user): UserRoleResource
    {
        $this->authorize('create', UserRole::class);

        $validated = $request->validate([
            'role_id' => ['required', 'exists:roles,id'],
        ]);

        $userRole = $user->userRoles()->create($validated);

        return new UserRoleResource($userRole);
    }
}
