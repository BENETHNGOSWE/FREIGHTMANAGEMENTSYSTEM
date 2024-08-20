<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use App\Models\ClientType;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\ClientTypeStoreRequest;
use App\Http\Requests\ClientTypeUpdateRequest;

class ClientTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $this->authorize('view-any', ClientType::class);

        $search = $request->get('search', '');

        $clientTypes = ClientType::search($search)
            ->latest()
            ->paginate(5)
            ->withQueryString();

        return view('app.client_types.index', compact('clientTypes', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        $this->authorize('create', ClientType::class);

        return view('app.client_types.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ClientTypeStoreRequest $request): RedirectResponse
    {
        $this->authorize('create', ClientType::class);

        $validated = $request->validated();

        $clientType = ClientType::create($validated);

        return redirect()
            ->route('client-types.edit', $clientType)
            ->withSuccess(__('crud.common.created'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, ClientType $clientType): View
    {
        $this->authorize('view', $clientType);

        return view('app.client_types.show', compact('clientType'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, ClientType $clientType): View
    {
        $this->authorize('update', $clientType);

        return view('app.client_types.edit', compact('clientType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        ClientTypeUpdateRequest $request,
        ClientType $clientType
    ): RedirectResponse {
        $this->authorize('update', $clientType);

        $validated = $request->validated();

        $clientType->update($validated);

        return redirect()
            ->route('client-types.edit', $clientType)
            ->withSuccess(__('crud.common.saved'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(
        Request $request,
        ClientType $clientType
    ): RedirectResponse {
        $this->authorize('delete', $clientType);

        $clientType->delete();

        return redirect()
            ->route('client-types.index')
            ->withSuccess(__('crud.common.removed'));
    }
}
