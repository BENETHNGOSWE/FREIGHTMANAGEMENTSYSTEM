@extends('layouts._master')

@section('content')
<div class="container-fluid">
    <div class="form-head align-items-center d-flex mb-sm-4 mb-3">
        <div class="me-auto">
            <h2 class="text-black font-w600">Accounts</h2>
            <p class="mb-1">Dashboard / <span><a class="text-primary" href="#">Accounting</a></span></p>
        </div>
        <div class="d-flex align-items-center">
            <a href="{{ route('accounts.journal.create') }}" class="btn btn-primary me-2">
                <i class="fas fa-plus me-1"></i> New Journal Entry
            </a>
            <a href="{{ route('accounts.reports.trial_balance') }}" class="btn btn-outline-primary">Trial Balance</a>
        </div>
    </div>

    @include('dashboards.accounts._tabs')

    <div class="row">
        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <p class="mb-1">Cash &amp; Bank</p>
                    <h3 class="text-black mb-0">{{ number_format($summary['cash_bank'], 2) }}</h3>
                    <small class="text-muted">In this month: +{{ number_format($summary['cash_in'], 2) }} / -{{ number_format($summary['cash_out'], 2) }}</small>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <p class="mb-1">Customers Owe Us</p>
                    <h3 class="text-black mb-0">{{ number_format($summary['receivable'], 2) }}</h3>
                    <small class="text-danger">Overdue 30+ days: {{ number_format($summary['overdue'], 2) }}</small>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <p class="mb-1">Income This Month</p>
                    <h3 class="text-black mb-0">{{ number_format($summary['month_income'], 2) }}</h3>
                    <small class="text-muted">Expenses: {{ number_format($summary['month_expense'], 2) }}</small>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <p class="mb-1">Profit</p>
                    <h3 class="mb-0 {{ $summary['month_profit'] >= 0 ? 'text-success' : 'text-danger' }}">
                        {{ number_format($summary['month_profit'], 2) }}
                    </h3>
                    <small class="text-muted">This fiscal year: {{ number_format($summary['year_profit'], 2) }}</small>
                </div>
            </div>
        </div>
    </div>

    @unless ($summary['settings_ready'])
        <div class="alert alert-warning">
            The books are not fully wired yet. Open
            <a href="{{ route('accounts.settings') }}">Accounts Settings</a> and pick the default accounts
            (or press <strong>Create standard chart</strong>).
        </div>
    @endunless

    <div class="row">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Latest entries</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-borderless">
                            <thead>
                                <tr>
                                    <th>Entry</th>
                                    <th>Date</th>
                                    <th>Source</th>
                                    <th class="text-end">Debit</th>
                                    <th class="text-end">Credit</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($summary['entries'] as $entry)
                                    <tr>
                                        <td><a href="{{ route('accounts.journal.show', $entry) }}">{{ $entry->entry_no }}</a></td>
                                        <td>{{ $entry->posting_date?->format('d M Y') }}</td>
                                        <td>{{ $entry->sourceLabel() }}</td>
                                        <td class="text-end">{{ number_format((float) $entry->total_debit, 2) }}</td>
                                        <td class="text-end">{{ number_format((float) $entry->total_credit, 2) }}</td>
                                        <td>{{ ucfirst($entry->status) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="text-center text-muted">No entries yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card">
                <div class="card-header"><h4 class="card-title mb-0">What is inside</h4></div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between">
                            <a href="{{ route('accounts.chart.index') }}">Chart of Accounts</a>
                            <span class="badge bg-primary">{{ $summary['accounts_count'] }}</span>
                        </li>
                        <li class="list-group-item"><a href="{{ route('accounts.journal.index') }}">Journal Entries</a></li>
                        <li class="list-group-item"><a href="{{ route('accounts.ledger.index') }}">General Ledger</a></li>
                        <li class="list-group-item"><a href="{{ route('accounts.reports.profit_loss') }}">Profit &amp; Loss</a></li>
                        <li class="list-group-item"><a href="{{ route('accounts.reports.balance_sheet') }}">Balance Sheet</a></li>
                        <li class="list-group-item"><a href="{{ route('accounts.reports.receivables') }}">Receivables &amp; Ageing</a></li>
                        <li class="list-group-item"><a href="{{ route('accounts.reports.cash_bank') }}">Cash &amp; Bank Book</a></li>
                    </ul>
                    <p class="text-muted mt-3 mb-0 small">
                        Invoices, payments and office expenses post themselves into these books.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
