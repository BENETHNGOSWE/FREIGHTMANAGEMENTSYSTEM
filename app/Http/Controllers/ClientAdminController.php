<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Client;
use Illuminate\View\View;
use App\Models\ClientAdmin;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\ClientAdminStoreRequest;
use App\Http\Requests\ClientAdminUpdateRequest;

class ClientAdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $this->authorize('view-any', ClientAdmin::class);

        $search = $request->get('search', '');

        $clientAdmins = ClientAdmin::search($search)
            ->latest()
            ->paginate(5)
            ->withQueryString();

        return view(
            'app.client_admins.index',
            compact('clientAdmins', 'search')
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        $this->authorize('create', ClientAdmin::class);

        $users = User::pluck('name', 'id');
        $clients = Client::pluck('name', 'id');

        return view('app.client_admins.create', compact('users', 'clients'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ClientAdminStoreRequest $request): RedirectResponse
    {
        $this->authorize('create', ClientAdmin::class);

        $validated = $request->validated();

        $clientAdmin = ClientAdmin::create($validated);

        return redirect()
            ->route('client-admins.edit', $clientAdmin)
            ->withSuccess(__('crud.common.created'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, ClientAdmin $clientAdmin): View
    {
        $this->authorize('view', $clientAdmin);

        return view('app.client_admins.show', compact('clientAdmin'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, ClientAdmin $clientAdmin): View
    {
        $this->authorize('update', $clientAdmin);

        $users = User::pluck('name', 'id');
        $clients = Client::pluck('name', 'id');

        return view(
            'app.client_admins.edit',
            compact('clientAdmin', 'users', 'clients')
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        ClientAdminUpdateRequest $request,
        ClientAdmin $clientAdmin
    ): RedirectResponse {
        $this->authorize('update', $clientAdmin);

        $validated = $request->validated();

        $clientAdmin->update($validated);

        return redirect()
            ->route('client-admins.edit', $clientAdmin)
            ->withSuccess(__('crud.common.saved'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(
        Request $request,
        ClientAdmin $clientAdmin
    ): RedirectResponse {
        $this->authorize('delete', $clientAdmin);

        $clientAdmin->delete();

        return redirect()
            ->route('client-admins.index')
            ->withSuccess(__('crud.common.removed'));
    }
}
