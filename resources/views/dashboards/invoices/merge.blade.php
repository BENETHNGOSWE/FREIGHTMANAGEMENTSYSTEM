@extends('layouts._master')

@section('content')
<div class="container-fluid">
    <div class="form-head align-items-center d-flex mb-sm-4 mb-3">
        <div class="me-auto">
            <h2 class="text-black font-w600">Merge Payment</h2>
            <p class="mb-1">Dashboard / <a class="text-primary" href="{{ route('admin.list.invoice') }}">Invoices</a> / Merge payment</p>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="GET" action="{{ route('invoices.merge.create') }}" class="row g-2 align-items-end mb-4">
                <div class="col-md-5">
                    <label class="form-label mb-1">Customer</label>
                    <select name="customer_id" class="form-control" onchange="this.form.submit()">
                        <option value="">— all customers with a balance —</option>
                        @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}" @selected((int) $selectedCustomer === $customer->id)>
                                {{ $customer->customer_firstname }} {{ $customer->customer_lastname }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>

            <form method="POST" action="{{ route('invoices.merge.preview') }}">
                @csrf
                <input type="hidden" name="customer_id" value="{{ $selectedCustomer }}">

                <p class="text-muted mb-2">
                    Tick the cargo of <strong>one customer</strong> — that is what makes them mergeable into a single payment.
                </p>

                <div class="table-responsive">
                    <table class="table table-hover table-borderless">
                        <thead>
                            <tr><th></th><th>Invoice</th><th>Customer</th><th>Weight</th><th class="text-end">Total</th><th class="text-end">Balance</th></tr>
                        </thead>
                        <tbody>
                            @forelse ($invoices as $invoice)
                                <tr>
                                    <td><input type="checkbox" name="invoice_ids[]" value="{{ $invoice->id }}" class="form-check-input"
                                        @checked(in_array($invoice->id, old('invoice_ids', $selectedIds ?? [])))></td>
                                    <td>{{ $invoice->invoice_no }}</td>
                                    <td>{{ optional($invoice->customer)->customer_firstname }}</td>
                                    <td>{{ $invoice->weight }}</td>
                                    <td class="text-end">{{ number_format($invoice->amountDue(), 2) }}</td>
                                    <td class="text-end text-danger">{{ number_format(max($invoice->balanceDue(), 0), 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center text-muted">No invoice with a balance for this customer.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <button class="btn btn-outline-primary">Preview merge</button>
            </form>
        </div>
    </div>

    @if ($preview)
        <div class="card">
            <div class="card-header"><h4 class="card-title mb-0">Preview</h4></div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3"><small class="text-muted">Combined weight</small><h5>{{ $preview['combined_weight'] }} KG</h5></div>
                    <div class="col-md-3"><small class="text-muted">Billable weight</small><h5>{{ $preview['billable_weight'] }} KG</h5></div>
                    <div class="col-md-3"><small class="text-muted">Re-pricing</small>
                        <h5 class="{{ $preview['difference'] < 0 ? 'text-danger' : 'text-success' }}">{{ number_format($preview['difference'], 2) }}</h5>
                    </div>
                    <div class="col-md-3"><small class="text-muted">Balance after merge</small><h5>{{ number_format($preview['balance_due'], 2) }}</h5></div>
                </div>

                @if ($preview['minimum_applied'])
                    <div class="alert alert-warning">
                        Combined weight is below 1 KG, so the 1 KG minimum applies:
                        {{ $preview['combined_weight'] }} KG → billable {{ $preview['billable_weight'] }} KG.
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-sm table-borderless">
                        <thead><tr><th>Invoice</th><th>Weight</th><th class="text-end">Old total</th><th class="text-end">New total</th><th class="text-end">Difference</th></tr></thead>
                        <tbody>
                            @foreach ($preview['rows'] as $row)
                                <tr>
                                    <td>{{ $row['invoice']->invoice_no }}</td>
                                    <td>{{ $row['weight'] }} {{ $row['is_kg'] ? 'KG' : '' }}</td>
                                    <td class="text-end">{{ number_format($row['old_total'], 2) }}</td>
                                    <td class="text-end">{{ number_format($row['new_total'], 2) }}</td>
                                    <td class="text-end">{{ number_format($row['new_total'] - $row['old_total'], 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <hr>
                <form method="POST" action="{{ route('invoices.merge.store') }}" class="row g-2 align-items-end">
                    @csrf
                    <input type="hidden" name="customer_id" value="{{ $selectedCustomer }}">
                    @foreach ($selectedIds as $id)
                        <input type="hidden" name="invoice_ids[]" value="{{ $id }}">
                    @endforeach
                    <div class="col-md-3">
                        <label class="form-label mb-1">Amount received</label>
                        <input type="number" step="0.01" name="amount" class="form-control" required value="{{ $preview['balance_due'] }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label mb-1">Date</label>
                        <input type="date" name="payment_date" class="form-control" required value="{{ now()->toDateString() }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label mb-1">Method</label>
                        <select name="payment_method" class="form-control">
                            @foreach (['CASH', 'BANK', 'MOBILE_MONEY', 'CHEQUE', 'OTHER'] as $method)
                                <option value="{{ $method }}">{{ str_replace('_', ' ', $method) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label mb-1">Reference</label>
                        <input type="text" name="reference_no" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-primary w-100">Record merged payment</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <div class="card">
        <div class="card-header"><h4 class="card-title mb-0">Recent merged payments</h4></div>
        <div class="card-body">
            <table class="table table-sm table-borderless mb-0">
                <thead><tr><th>Date</th><th>Customer</th><th>Invoices</th><th>Weight</th><th class="text-end">Amount</th></tr></thead>
                <tbody>
                    @forelse ($recent as $payment)
                        <tr>
                            <td>{{ optional($payment->payment_date)->format('d M Y') }}</td>
                            <td>{{ optional(\App\Models\Customer::find($payment->customer_id))->customer_firstname }}</td>
                            <td>{{ $payment->lines->count() }}</td>
                            <td>
                                {{ $payment->combined_weight }} → {{ $payment->billable_weight }} KG
                                @if ($payment->minimum_applied) <span class="badge bg-warning text-dark">1 KG min</span> @endif
                            </td>
                            <td class="text-end">{{ number_format((float) $payment->amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-muted text-center">No merged payments yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
