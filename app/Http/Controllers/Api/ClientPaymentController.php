<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\ClientPayment;
use App\Http\Controllers\Controller;
use App\Http\Resources\ClientPaymentResource;
use App\Http\Resources\ClientPaymentCollection;
use App\Http\Requests\ClientPaymentStoreRequest;
use App\Http\Requests\ClientPaymentUpdateRequest;

class ClientPaymentController extends Controller
{
    public function index(Request $request): ClientPaymentCollection
    {
        $this->authorize('view-any', ClientPayment::class);

        $search = $request->get('search', '');

        $clientPayments = ClientPayment::search($search)
            ->latest()
            ->paginate();

        return new ClientPaymentCollection($clientPayments);
    }

    public function store(
        ClientPaymentStoreRequest $request
    ): ClientPaymentResource {
        $this->authorize('create', ClientPayment::class);

        $validated = $request->validated();

        $clientPayment = ClientPayment::create($validated);

        return new ClientPaymentResource($clientPayment);
    }

    public function show(
        Request $request,
        ClientPayment $clientPayment
    ): ClientPaymentResource {
        $this->authorize('view', $clientPayment);

        return new ClientPaymentResource($clientPayment);
    }

    public function update(
        ClientPaymentUpdateRequest $request,
        ClientPayment $clientPayment
    ): ClientPaymentResource {
        $this->authorize('update', $clientPayment);

        $validated = $request->validated();

        $clientPayment->update($validated);

        return new ClientPaymentResource($clientPayment);
    }

    public function destroy(
        Request $request,
        ClientPayment $clientPayment
    ): Response {
        $this->authorize('delete', $clientPayment);

        $clientPayment->delete();

        return response()->noContent();
    }
}
