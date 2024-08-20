<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use App\Models\PaymentType;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\PaymentTypeStoreRequest;
use App\Http\Requests\PaymentTypeUpdateRequest;

class PaymentTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $this->authorize('view-any', PaymentType::class);

        $search = $request->get('search', '');

        $paymentTypes = PaymentType::search($search)
            ->latest()
            ->paginate(5)
            ->withQueryString();

        return view(
            'app.payment_types.index',
            compact('paymentTypes', 'search')
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        $this->authorize('create', PaymentType::class);

        return view('app.payment_types.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PaymentTypeStoreRequest $request): RedirectResponse
    {
        $this->authorize('create', PaymentType::class);

        $validated = $request->validated();

        $paymentType = PaymentType::create($validated);

        return redirect()
            ->route('payment-types.edit', $paymentType)
            ->withSuccess(__('crud.common.created'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, PaymentType $paymentType): View
    {
        $this->authorize('view', $paymentType);

        return view('app.payment_types.show', compact('paymentType'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, PaymentType $paymentType): View
    {
        $this->authorize('update', $paymentType);

        return view('app.payment_types.edit', compact('paymentType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        PaymentTypeUpdateRequest $request,
        PaymentType $paymentType
    ): RedirectResponse {
        $this->authorize('update', $paymentType);

        $validated = $request->validated();

        $paymentType->update($validated);

        return redirect()
            ->route('payment-types.edit', $paymentType)
            ->withSuccess(__('crud.common.saved'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(
        Request $request,
        PaymentType $paymentType
    ): RedirectResponse {
        $this->authorize('delete', $paymentType);

        $paymentType->delete();

        return redirect()
            ->route('payment-types.index')
            ->withSuccess(__('crud.common.removed'));
    }
}
