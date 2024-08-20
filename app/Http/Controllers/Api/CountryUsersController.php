<?php

namespace App\Http\Controllers\Api;

use App\Models\Country;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\UserCollection;

class CountryUsersController extends Controller
{
    public function index(Request $request, Country $country): UserCollection
    {
        $this->authorize('view', $country);

        $search = $request->get('search', '');

        $users = $country
            ->users()
            ->search($search)
            ->latest()
            ->paginate();

        return new UserCollection($users);
    }

    public function store(Request $request, Country $country): UserResource
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
            'role_id' => ['required', 'exists:roles,id'],
            'client_id' => ['nullable', 'exists:clients,id'],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('public');
        }

        $user = $country->users()->create($validated);

        $user->syncRoles($request->roles);

        return new UserResource($user);
    }
}
