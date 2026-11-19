@extends('layouts._master')

@section('content')
<div class="container-fluid">
    <div class="form-head align-items-center d-flex mb-sm-4 mb-3">
        <div class="me-auto">
            <h2 class="text-black font-w600">General Ledger</h2>
            <p class="mb-1">Dashboard / <span><a class="text-primary" href="{{ route('accounts.dashboard') }}">Accounts</a></span></p>
        </div>
    </div>

    @include('dashboards.accounts._tabs')

    <div class="card">
        <div class="card-body">
            <form method="GET" action="{{ route('accounts.ledger.index') }}" class="row g-2 align-items-end mb-4">
                <div class="col-md-5">
                    <label class="form-label mb-1">Account</label>
                    <select name="account_id" class="form-control" required>
                        <option value="">— choose an account —</option>
                        @foreach ($accounts as $account)
                            <option value="{{ $account->id }}" @selected(optional($selected)->id === $account->id)>
                                {{ $account->account_number }} — {{ $account->name }} ({{ $account->root_type }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label mb-1">From</label>
                    <input type="date" name="from" value="{{ $from }}" class="form-control">
                </div>
                <div class="col-md-2">
                    <label class="form-label mb-1">To</label>
                    <input type="date" name="to" value="{{ $to }}" class="form-control">
                </div>
                <div class="col-md-3">
                    <button class="btn btn-primary w-100">Show ledger</button>
                </div>
            </form>

            @if ($ledger)
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h4 class="mb-0">{{ $ledger['account']->account_number }} — {{ $ledger['account']->name }}</h4>
                        <small class="text-muted">
                            {{ $ledger['account']->root_type }} · {{ $from }} to {{ $to }}
                            · {{ $ledger['account']->isDebitNormal() ? 'debit' : 'credit' }}-normal
                        </small>
                    </div>
                    <div class="text-end">
                        <div>Opening: <strong>{{ number_format($ledger['opening'], 2) }}</strong></div>
                        <div>Closing: <strong>{{ number_format($ledger['closing'], 2) }}</strong></div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover table-borderless">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Entry</th>
                                <th>Description</th>
                                <th class="text-end">Debit</th>
                                <th class="text-end">Credit</th>
                                <th class="text-end">Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="table-light">
                                <td colspan="6"><strong>Opening balance</strong></td>
                                <td class="text-end"><strong>{{ number_format($ledger['opening'], 2) }}</strong></td>
                            </tr>
                            @forelse ($ledger['rows'] as $row)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ \Illuminate\Support\Carbon::parse($row['date'])->format('d M Y') }}</td>
                                    <td><a href="{{ route('accounts.journal.show', $row['entry_id']) }}">{{ $row['entry_no'] }}</a></td>
                                    <td>{{ $row['description'] }}</td>
                                    <td class="text-end">{{ $row['debit'] ? number_format($row['debit'], 2) : '-' }}</td>
                                    <td class="text-end">{{ $row['credit'] ? number_format($row['credit'], 2) : '-' }}</td>
                                    <td class="text-end">{{ number_format($row['balance'], 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="text-center text-muted">No movement in this period.</td></tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="fw-bold">
                                <th colspan="4">Movement / closing</th>
                                <th class="text-end">{{ number_format($ledger['total_debit'], 2) }}</th>
                                <th class="text-end">{{ number_format($ledger['total_credit'], 2) }}</th>
                                <th class="text-end">{{ number_format($ledger['closing'], 2) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <p class="text-muted mb-0">Pick an account above to see every debit and credit posted to it.</p>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h4 class="card-title mb-0">Balances as at {{ $to }}</h4></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-hover table-borderless">
                    <thead>
                        <tr><th>Account</th><th>Type</th><th class="text-end">Balance</th><th></th></tr>
                    </thead>
                    <tbody>
                        @foreach ($accounts as $account)
                            <tr>
                                <td>{{ $account->account_number }} — {{ $account->name }}</td>
                                <td>{{ $account->root_type }}</td>
                                <td class="text-end">{{ number_format($balances[$account->id] ?? 0, 2) }}</td>
                                <td class="text-end">
                                    <a href="{{ route('accounts.ledger.index', ['account_id' => $account->id, 'from' => $from, 'to' => $to]) }}"
                                       class="btn btn-sm btn-outline-primary">Open</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
