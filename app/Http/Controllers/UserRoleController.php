<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\UserRole;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\UserRoleStoreRequest;
use App\Http\Requests\UserRoleUpdateRequest;

class UserRoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $this->authorize('view-any', UserRole::class);

        $search = $request->get('search', '');

        $userRoles = UserRole::search($search)
            ->latest()
            ->paginate(5)
            ->withQueryString();

        return view('app.user_roles.index', compact('userRoles', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        $this->authorize('create', UserRole::class);

        $users = User::pluck('name', 'id');
        $roles = Role::pluck('name', 'id');

        return view('app.user_roles.create', compact('users', 'roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRoleStoreRequest $request): RedirectResponse
    {
        $this->authorize('create', UserRole::class);

        $validated = $request->validated();

        $userRole = UserRole::create($validated);

        return redirect()
            ->route('user-roles.edit', $userRole)
            ->withSuccess(__('crud.common.created'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, UserRole $userRole): View
    {
        $this->authorize('view', $userRole);

        return view('app.user_roles.show', compact('userRole'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, UserRole $userRole): View
    {
        $this->authorize('update', $userRole);

        $users = User::pluck('name', 'id');
        $roles = Role::pluck('name', 'id');

        return view(
            'app.user_roles.edit',
            compact('userRole', 'users', 'roles')
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        UserRoleUpdateRequest $request,
        UserRole $userRole
    ): RedirectResponse {
        $this->authorize('update', $userRole);

        $validated = $request->validated();

        $userRole->update($validated);

        return redirect()
            ->route('user-roles.edit', $userRole)
            ->withSuccess(__('crud.common.saved'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(
        Request $request,
        UserRole $userRole
    ): RedirectResponse {
        $this->authorize('delete', $userRole);

        $userRole->delete();

        return redirect()
            ->route('user-roles.index')
            ->withSuccess(__('crud.common.removed'));
    }
}
