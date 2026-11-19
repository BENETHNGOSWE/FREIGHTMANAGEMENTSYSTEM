@extends('layouts._master')

@section('content')
<div class="container-fluid">
    <div class="form-head align-items-center d-flex mb-sm-4 mb-3">
        <div class="me-auto">
            <h2 class="text-black font-w600">Receivables &amp; Ageing</h2>
            <p class="mb-1">Dashboard / <a class="text-primary" href="{{ route('accounts.dashboard') }}">Accounts</a> / Receivables</p>
        </div>
        <a href="{{ route('accounts.reports.export', ['report' => 'receivables', 'to' => $asOf]) }}"
           class="btn btn-outline-success">Export CSV</a>
    </div>

    @include('dashboards.accounts._tabs')

    <div class="card">
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-end mb-4">
                <div class="col-md-3">
                    <label class="form-label mb-1">As at</label>
                    <input type="date" name="as_of" value="{{ $asOf }}" class="form-control">
                </div>
                <div class="col-md-2"><button class="btn btn-primary w-100">Run</button></div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover table-borderless">
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th class="text-end">Invoices</th>
                            <th class="text-end">Invoiced</th>
                            <th class="text-end">Received</th>
                            <th class="text-end">Balance</th>
                            <th class="text-end">0-30</th>
                            <th class="text-end">31-60</th>
                            <th class="text-end">61-90</th>
                            <th class="text-end">90+</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($report['customers'] as $row)
                            <tr>
                                <td>
                                    <a href="{{ route('accounts.ledger.customer', $row['customer_id']) }}"
                                       title="Open the customer statement">{{ $row['name'] }}</a>
                                </td>
                                <td class="text-end">{{ $row['invoices'] }}</td>
                                <td class="text-end">{{ number_format($row['invoiced'], 2) }}</td>
                                <td class="text-end">{{ number_format($row['received'], 2) }}</td>
                                <td class="text-end {{ $row['balance'] > 0 ? 'text-danger fw-bold' : 'text-success' }}">
                                    {{ number_format($row['balance'], 2) }}
                                </td>
                                <td class="text-end">{{ number_format($row['current'], 2) }}</td>
                                <td class="text-end">{{ number_format($row['days_31_60'], 2) }}</td>
                                <td class="text-end">{{ number_format($row['days_61_90'], 2) }}</td>
                                <td class="text-end">{{ number_format($row['over_90'], 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="9" class="text-center text-muted">No invoices yet.</td></tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="fw-bold">
                            <th>Total</th>
                            <th></th>
                            <th class="text-end">{{ number_format($report['totals']['invoiced'], 2) }}</th>
                            <th class="text-end">{{ number_format($report['totals']['received'], 2) }}</th>
                            <th class="text-end">{{ number_format($report['totals']['balance'], 2) }}</th>
                            <th class="text-end">{{ number_format($report['totals']['current'], 2) }}</th>
                            <th class="text-end">{{ number_format($report['totals']['days_31_60'], 2) }}</th>
                            <th class="text-end">{{ number_format($report['totals']['days_61_90'], 2) }}</th>
                            <th class="text-end">{{ number_format($report['totals']['over_90'], 2) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <p class="text-muted mb-0 small">
                Invoiced = CBM / KG amount + transport fee. Received = payments recorded on the invoice.
                Ageing is counted from the invoice date. Click a customer name to open the statement.
            </p>
        </div>
    </div>
</div>
@endsection
