@extends('layouts._master')

@section('content')
<div class="container-fluid">
    <div class="form-head align-items-center d-flex mb-sm-4 mb-3">
        <div class="me-auto">
            <h2 class="text-black font-w600">Profit &amp; Loss</h2>
            <p class="mb-1">Dashboard / <a class="text-primary" href="{{ route('accounts.dashboard') }}">Accounts</a> / Profit &amp; Loss</p>
        </div>
        <a href="{{ route('accounts.reports.export', ['report' => 'profit-loss', 'from' => $from, 'to' => $to]) }}"
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
                <table class="table table-borderless">
                    <tbody>
                        <tr class="table-light"><th colspan="2">Income</th></tr>
                        @forelse ($report['income'] as $row)
                            <tr>
                                <td>{{ $row['account']->account_number }} — {{ $row['account']->name }}</td>
                                <td class="text-end">{{ number_format($row['amount'], 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="text-muted">No income in this period.</td></tr>
                        @endforelse
                        <tr class="fw-bold">
                            <td>Total income</td>
                            <td class="text-end">{{ number_format($report['total_income'], 2) }}</td>
                        </tr>

                        <tr class="table-light"><th colspan="2">Expenses</th></tr>
                        @forelse ($report['expenses'] as $row)
                            <tr>
                                <td>{{ $row['account']->account_number }} — {{ $row['account']->name }}</td>
                                <td class="text-end">{{ number_format($row['amount'], 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="text-muted">No expenses in this period.</td></tr>
                        @endforelse
                        <tr class="fw-bold">
                            <td>Total expenses</td>
                            <td class="text-end">{{ number_format($report['total_expense'], 2) }}</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr class="fw-bold {{ $report['net_profit'] >= 0 ? 'text-success' : 'text-danger' }}">
                            <th>{{ $report['net_profit'] >= 0 ? 'Net profit' : 'Net loss' }}</th>
                            <th class="text-end">{{ number_format(abs($report['net_profit']), 2) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
