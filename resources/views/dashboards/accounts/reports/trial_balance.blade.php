@extends('layouts._master')

@section('content')
<div class="container-fluid">
    <div class="form-head align-items-center d-flex mb-sm-4 mb-3">
        <div class="me-auto">
            <h2 class="text-black font-w600">Trial Balance</h2>
            <p class="mb-1">Dashboard / <a class="text-primary" href="{{ route('accounts.dashboard') }}">Accounts</a> / Trial Balance</p>
        </div>
        <a href="{{ route('accounts.reports.export', ['report' => 'trial-balance', 'from' => $from, 'to' => $to]) }}"
           class="btn btn-outline-success">Export CSV</a>
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
                            <th>Type</th>
                            <th class="text-end">Opening</th>
                            <th class="text-end">Debit</th>
                            <th class="text-end">Credit</th>
                            <th class="text-end">Closing</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($report['rows'] as $row)
                            <tr>
                                <td>{{ $row['account']->account_number }} — {{ $row['account']->name }}</td>
                                <td>{{ $row['account']->root_type }}</td>
                                <td class="text-end">{{ number_format($row['opening'], 2) }}</td>
                                <td class="text-end">{{ $row['debit'] ? number_format($row['debit'], 2) : '-' }}</td>
                                <td class="text-end">{{ $row['credit'] ? number_format($row['credit'], 2) : '-' }}</td>
                                <td class="text-end">
                                    {{ number_format(abs($row['closing']), 2) }}
                                    <small class="text-muted">{{ $row['closing'] >= 0 ? ($row['account']->isDebitNormal() ? 'Dr' : 'Cr') : ($row['account']->isDebitNormal() ? 'Cr' : 'Dr') }}</small>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted">Nothing posted in this period.</td></tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="fw-bold">
                            <th colspan="2">Totals</th>
                            <th class="text-end">{{ number_format($report['totals']['opening_debit'] - $report['totals']['opening_credit'], 2) }}</th>
                            <th class="text-end">{{ number_format($report['totals']['debit'], 2) }}</th>
                            <th class="text-end">{{ number_format($report['totals']['credit'], 2) }}</th>
                            <th class="text-end">{{ number_format($report['totals']['closing_debit'] - $report['totals']['closing_credit'], 2) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            @php
                $balanced = abs($report['totals']['debit'] - $report['totals']['credit']) < 0.01;
            @endphp
            <div class="alert {{ $balanced ? 'alert-success' : 'alert-danger' }} mt-3 mb-0">
                Debits {{ number_format($report['totals']['debit'], 2) }}
                {{ $balanced ? '=' : '≠' }}
                Credits {{ number_format($report['totals']['credit'], 2) }}
                {{ $balanced ? '— the books balance.' : '— something is wrong, check the journal entries.' }}
            </div>
        </div>
    </div>
</div>
@endsection
