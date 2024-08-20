<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use App\Models\PaymentType;
use Illuminate\Http\Request;
use App\Models\ClientPayment;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\ClientPaymentStoreRequest;
use App\Http\Requests\ClientPaymentUpdateRequest;

class ClientPaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $this->authorize('view-any', ClientPayment::class);

        $search = $request->get('search', '');

        $clientPayments = ClientPayment::search($search)
            ->latest()
            ->paginate(5)
            ->withQueryString();

        return view(
            'app.client_payments.index',
            compact('clientPayments', 'search')
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        $this->authorize('create', ClientPayment::class);

        $paymentTypes = PaymentType::pluck('name', 'id');

        return view('app.client_payments.create', compact('paymentTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ClientPaymentStoreRequest $request): RedirectResponse
    {
        $this->authorize('create', ClientPayment::class);

        $validated = $request->validated();

        $clientPayment = ClientPayment::create($validated);

        return redirect()
            ->route('client-payments.edit', $clientPayment)
            ->withSuccess(__('crud.common.created'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, ClientPayment $clientPayment): View
    {
        $this->authorize('view', $clientPayment);

        return view('app.client_payments.show', compact('clientPayment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, ClientPayment $clientPayment): View
    {
        $this->authorize('update', $clientPayment);

        $paymentTypes = PaymentType::pluck('name', 'id');

        return view(
            'app.client_payments.edit',
            compact('clientPayment', 'paymentTypes')
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        ClientPaymentUpdateRequest $request,
        ClientPayment $clientPayment
    ): RedirectResponse {
        $this->authorize('update', $clientPayment);

        $validated = $request->validated();

        $clientPayment->update($validated);

        return redirect()
            ->route('client-payments.edit', $clientPayment)
            ->withSuccess(__('crud.common.saved'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(
        Request $request,
        ClientPayment $clientPayment
    ): RedirectResponse {
        $this->authorize('delete', $clientPayment);

        $clientPayment->delete();

        return redirect()
            ->route('client-payments.index')
            ->withSuccess(__('crud.common.removed'));
    }
}
