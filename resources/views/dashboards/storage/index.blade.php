@extends('layouts._master')

@section('content')
<div class="container-fluid">
    <div class="form-head align-items-center d-flex mb-sm-4 mb-3">
        <div class="me-auto">
            <h2 class="text-black font-w600">Cargo in Storage</h2>
            <p class="mb-1">Dashboard / <span class="text-primary">Storage</span></p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('storage.settings') }}" class="btn btn-outline-primary">Storage Settings</a>
            <form method="POST" action="{{ route('storage.accrue') }}">
                @csrf
                <button class="btn btn-primary">Add today's charges</button>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3"><div class="card"><div class="card-body">
            <p class="mb-1">Outstanding storage</p>
            <h4 class="text-danger mb-0">{{ number_format($summary['total_outstanding'], 2) }}</h4>
            <small class="text-muted">{{ $summary['cargo_outstanding'] }} cargo owing</small>
        </div></div></div>
        <div class="col-md-3"><div class="card"><div class="card-body">
            <p class="mb-1">Charged</p>
            <h4 class="mb-0">{{ number_format($summary['total_charged'], 2) }}</h4>
            <small class="text-muted">{{ $summary['cargo_active'] }} cargo still in the warehouse</small>
        </div></div></div>
        <div class="col-md-3"><div class="card"><div class="card-body">
            <p class="mb-1">Paid</p>
            <h4 class="text-success mb-0">{{ number_format($summary['total_paid'], 2) }}</h4>
            <a href="{{ route('storage.payments') }}" class="small">View payments</a>
        </div></div></div>
        <div class="col-md-3"><div class="card"><div class="card-body">
            <p class="mb-1">Waived</p>
            <h4 class="mb-0">{{ number_format($summary['total_waived'], 2) }}</h4>
            <small class="text-muted">Free period: {{ $settings->free_days }} days · {{ number_format((float) $settings->daily_rate, 2) }}/day</small>
        </div></div></div>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-end mb-3">
                <div class="col-md-5">
                    <input type="text" name="search" value="{{ $search }}" class="form-control" placeholder="Tracking number or customer">
                </div>
                <div class="col-md-2"><button class="btn btn-primary w-100">Search</button></div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover table-borderless">
                    <thead>
                        <tr>
                            <th>Tracking</th><th>Customer</th><th>Since</th><th class="text-center">Free</th>
                            <th class="text-end">Rate/day</th><th class="text-center">Days</th>
                            <th class="text-end">Charged</th><th class="text-end">Paid</th><th class="text-end">Balance</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($summary['rows'] as $row)
                            <tr>
                                <td>
                                    @if ($row->loadpackage)
                                        <a href="{{ route('storage.show', $row) }}"><strong>{{ $row->loadpackage->load_no }}</strong></a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ optional(optional($row->loadpackage)->customer)->customer_firstname ?? '-' }}</td>
                                <td>{{ optional($row->started_on)->format('d M y') }}</td>
                                <td class="text-center">{{ $row->free_days }}</td>
                                <td class="text-end">{{ number_format((float) $row->daily_rate, 0) }}</td>
                                <td class="text-center">{{ $row->days_charged }}</td>
                                <td class="text-end">{{ number_format($row->charged, 2) }}</td>
                                <td class="text-end">{{ number_format($row->paid, 2) }}</td>
                                <td class="text-end {{ $row->balance > 0 ? 'text-danger fw-bold' : 'text-success' }}">
                                    {{ number_format($row->balance, 2) }}
                                </td>
                                <td>
                                    @if ($row->status === 'active')
                                        <span class="badge bg-primary">in store</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $row->status }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="10" class="text-center text-muted">No cargo in storage yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <form method="POST" action="{{ route('storage.start_existing') }}" class="mt-2">
                @csrf
                <button class="btn btn-sm btn-outline-secondary">Start tracking cargo that is already in the warehouse</button>
            </form>
        </div>
    </div>
</div>
@endsection
