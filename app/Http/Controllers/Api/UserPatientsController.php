<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\PatientResource;
use App\Http\Resources\PatientCollection;

class UserPatientsController extends Controller
{
    public function index(Request $request, User $user): PatientCollection
    {
        $this->authorize('view', $user);

        $search = $request->get('search', '');

        $patients = $user
            ->patients()
            ->search($search)
            ->latest()
            ->paginate();

        return new PatientCollection($patients);
    }

    public function store(Request $request, User $user): PatientResource
    {
        $this->authorize('create', Patient::class);

        $validated = $request->validate([
            'patientID' => ['required', 'max:255', 'string'],
            'has_insurance' => ['required', 'boolean'],
            'city' => ['nullable', 'max:255', 'string'],
        ]);

        $patient = $user->patients()->create($validated);

        return new PatientResource($patient);
    }
}
