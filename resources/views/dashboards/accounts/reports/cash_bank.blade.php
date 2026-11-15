@extends('layouts._master')

@section('content')
<div class="container-fluid">
    <div class="form-head align-items-center d-flex mb-sm-4 mb-3">
        <div class="me-auto">
            <h2 class="text-black font-w600">Cash &amp; Bank Book</h2>
            <p class="mb-1">Dashboard / <a class="text-primary" href="{{ route('accounts.dashboard') }}">Accounts</a> / Cash &amp; Bank</p>
        </div>
    </div>

    @include('dashboards.accounts._tabs')

    <div class="card">
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-end mb-4">
                <div class="col-md-3">
                    <label class="form-label mb-1">From</label>
                    <input type="date" name="from" value="{{ $from }}" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label mb-1">To</label>
                    <input type="date" name="to" value="{{ $to }}" class="form-control">
                </div>
                <div class="col-md-2"><button class="btn btn-primary w-100">Run</button></div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover table-borderless">
                    <thead>
                        <tr>
                            <th>Account</th>
                            <th class="text-end">Opening</th>
                            <th class="text-end">Money in</th>
                            <th class="text-end">Money out</th>
                            <th class="text-end">Closing</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($report['rows'] as $row)
                            <tr>
                                <td>{{ $row['account']->account_number }} — {{ $row['account']->name }}</td>
                                <td class="text-end">{{ number_format($row['opening'], 2) }}</td>
                                <td class="text-end text-success">{{ number_format($row['in'], 2) }}</td>
                                <td class="text-end text-danger">{{ number_format($row['out'], 2) }}</td>
                                <td class="text-end fw-bold">{{ number_format($row['closing'], 2) }}</td>
                                <td class="text-end">
                                    <a href="{{ route('accounts.ledger.index', ['account_id' => $row['account']->id, 'from' => $from, 'to' => $to]) }}"
                                       class="btn btn-sm btn-outline-primary">Ledger</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted">No cash or bank account found.</td></tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="fw-bold">
                            <th>Total</th>
                            <th class="text-end">{{ number_format($report['totals']['opening'], 2) }}</th>
                            <th class="text-end">{{ number_format($report['totals']['in'], 2) }}</th>
                            <th class="text-end">{{ number_format($report['totals']['out'], 2) }}</th>
                            <th class="text-end">{{ number_format($report['totals']['closing'], 2) }}</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
