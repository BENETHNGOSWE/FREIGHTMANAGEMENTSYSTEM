@extends('layouts._master')

@section('content')
<div class="container-fluid">
    <div class="form-head align-items-center d-flex mb-sm-4 mb-3">
        <div class="me-auto">
            <h2 class="text-black font-w600">Storage Payments</h2>
            <p class="mb-1">Dashboard / <a class="text-primary" href="{{ route('storage.index') }}">Storage</a> / Payments</p>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-end mb-3">
                <div class="col-md-3"><label class="form-label mb-1">From</label><input type="date" name="from" value="{{ $from }}" class="form-control"></div>
                <div class="col-md-3"><label class="form-label mb-1">To</label><input type="date" name="to" value="{{ $to }}" class="form-control"></div>
                <div class="col-md-2"><button class="btn btn-primary w-100">Show</button></div>
                <div class="col-md-4 text-end"><h5 class="mb-0">Total: {{ number_format($total, 2) }}</h5></div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover table-borderless">
                    <thead>
                        <tr><th>Date</th><th>Tracking</th><th>Customer</th><th>Method</th><th>Reference</th><th class="text-end">Amount</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($payments as $payment)
                            <tr>
                                <td>{{ optional($payment->payment_date)->format('d M Y') }}</td>
                                <td>{{ optional($payment->loadpackage)->load_no }}</td>
                                <td>{{ optional(optional($payment->loadpackage)->customer)->customer_firstname ?? '-' }}</td>
                                <td>{{ $payment->payment_method }}</td>
                                <td>{{ $payment->reference_no ?: '-' }}</td>
                                <td class="text-end">{{ number_format((float) $payment->amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted">No storage payments in this period.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
