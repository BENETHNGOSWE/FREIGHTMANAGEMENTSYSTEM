<?php

namespace App\Http\Controllers\Api;

use App\Models\Role;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\UserCollection;

class RoleUsersController extends Controller
{
    public function index(Request $request, Role $role): UserCollection
    {
        $this->authorize('view', $role);

        $search = $request->get('search', '');

        $users = $role
            ->users()
            ->search($search)
            ->latest()
            ->paginate();

        return new UserCollection($users);
    }

    public function store(Request $request, Role $role): UserResource
    {
        $this->authorize('create', User::class);

        $validated = $request->validate([
            'name' => ['required', 'max:255', 'string'],
            'email' => ['required', 'unique:users,email', 'email'],
            'password' => ['required'],
            'phone' => ['nullable', 'max:255', 'string'],
            'address' => ['required', 'max:255', 'string'],
            'registration_date' => ['required', 'date'],
            'photo' => ['nullable', 'file'],
            'gender' => ['nullable', 'in:male,female,other'],
            'dob' => ['nullable', 'date'],
            'status' => ['required', 'in:0,1,2,3,4'],
            'country_id' => ['nullable', 'exists:countries,id'],
            'client_id' => ['nullable', 'exists:clients,id'],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('public');
        }

        $user = $role->users()->create($validated);

        $user->syncRoles($request->roles);

        return new UserResource($user);
    }
}
