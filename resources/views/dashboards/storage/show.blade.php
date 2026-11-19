@extends('layouts._master')

@section('content')
<div class="container-fluid">
    <div class="form-head align-items-center d-flex mb-sm-4 mb-3">
        <div class="me-auto">
            <h2 class="text-black font-w600">Storage — {{ optional($storage->loadpackage)->load_no }}</h2>
            <p class="mb-1">Dashboard / <a class="text-primary" href="{{ route('storage.index') }}">Storage</a> / {{ optional($storage->loadpackage)->load_no }}</p>
        </div>
        <a href="{{ route('storage.index') }}" class="btn btn-outline-primary">Back</a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><h4 class="card-title mb-0">Daily charges</h4></div>
                <div class="card-body">
                    <div class="table-responsive" style="max-height:330px;overflow:auto">
                        <table class="table table-sm table-borderless">
                            <thead><tr><th>Date</th><th class="text-end">Amount</th></tr></thead>
                            <tbody>
                                @forelse ($storage->charges->sortBy('charge_date') as $charge)
                                    <tr>
                                        <td>{{ optional($charge->charge_date)->format('d M Y') }}</td>
                                        <td class="text-end">{{ number_format((float) $charge->amount, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="2" class="text-muted">No charges yet — the free period may still be running.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h4 class="card-title mb-0">Payments &amp; waivers</h4></div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <thead><tr><th>Date</th><th>Type</th><th>Reference / reason</th><th class="text-end">Amount</th></tr></thead>
                        <tbody>
                            @foreach ($storage->payments as $payment)
                                <tr>
                                    <td>{{ optional($payment->payment_date)->format('d M Y') }}</td>
                                    <td>Payment ({{ $payment->payment_method }})</td>
                                    <td>{{ $payment->reference_no ?: '-' }}</td>
                                    <td class="text-end text-success">{{ number_format((float) $payment->amount, 2) }}</td>
                                </tr>
                            @endforeach
                            @foreach ($storage->waivers as $waiver)
                                <tr>
                                    <td>{{ optional($waiver->created_at)->format('d M Y') }}</td>
                                    <td>Waived</td>
                                    <td>{{ $waiver->reason ?: '-' }}</td>
                                    <td class="text-end text-warning">{{ number_format((float) $waiver->amount, 2) }}</td>
                                </tr>
                            @endforeach
                            @if ($storage->payments->isEmpty() && $storage->waivers->isEmpty())
                                <tr><td colspan="4" class="text-muted">Nothing paid or waived yet.</td></tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between"><span>Charged</span><strong>{{ number_format($charged, 2) }}</strong></div>
                    <div class="d-flex justify-content-between"><span>Paid</span><strong>{{ number_format($paid, 2) }}</strong></div>
                    <div class="d-flex justify-content-between"><span>Waived</span><strong>{{ number_format($waived, 2) }}</strong></div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span>Balance</span>
                        <strong class="{{ $balance > 0 ? 'text-danger' : 'text-success' }}">{{ number_format($balance, 2) }}</strong>
                    </div>
                    <p class="small text-muted mt-2 mb-0">
                        Free {{ $storage->free_days }} days from {{ optional($storage->started_on)->format('d M Y') }},
                        then {{ number_format((float) $storage->daily_rate, 2) }} per day.
                    </p>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h4 class="card-title mb-0">Record payment</h4></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('storage.pay', $storage) }}">
                        @csrf
                        <div class="mb-2">
                            <input type="number" step="0.01" name="amount" class="form-control" placeholder="Amount" value="{{ $balance }}" required>
                        </div>
                        <div class="mb-2">
                            <input type="date" name="payment_date" class="form-control" value="{{ now()->toDateString() }}" required>
                        </div>
                        <div class="mb-2">
                            <select name="payment_method" class="form-control">
                                @foreach (['CASH', 'BANK', 'MOBILE_MONEY', 'CHEQUE', 'OTHER'] as $method)
                                    <option value="{{ $method }}">{{ str_replace('_', ' ', $method) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-2">
                            <input type="text" name="reference_no" class="form-control" placeholder="Reference (optional)">
                        </div>
                        <button class="btn btn-primary w-100" @disabled($balance <= 0)>Save payment</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h4 class="card-title mb-0">Waive storage</h4></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('storage.waive', $storage) }}">
                        @csrf
                        <div class="mb-2">
                            <input type="number" step="0.01" name="amount" class="form-control" placeholder="Amount" value="{{ $balance }}" required>
                        </div>
                        <div class="mb-2">
                            <input type="text" name="reason" class="form-control" placeholder="Reason (required)" required>
                        </div>
                        <button class="btn btn-outline-danger w-100" @disabled($balance <= 0)>Waive</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
