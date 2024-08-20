<?php

namespace App\Http\Controllers\Api;

use App\Models\Role;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserRoleResource;
use App\Http\Resources\UserRoleCollection;

class RoleUserRolesController extends Controller
{
    public function index(Request $request, Role $role): UserRoleCollection
    {
        $this->authorize('view', $role);

        $search = $request->get('search', '');

        $userRoles = $role
            ->userRoles()
            ->search($search)
            ->latest()
            ->paginate();

        return new UserRoleCollection($userRoles);
    }

    public function store(Request $request, Role $role): UserRoleResource
    {
        $this->authorize('create', UserRole::class);

        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
        ]);

        $userRole = $role->userRoles()->create($validated);

        return new UserRoleResource($userRole);
    }
}
