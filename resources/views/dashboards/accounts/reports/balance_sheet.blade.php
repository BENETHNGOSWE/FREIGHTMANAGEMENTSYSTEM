@extends('layouts._master')

@section('content')
<div class="container-fluid">
    <div class="form-head align-items-center d-flex mb-sm-4 mb-3">
        <div class="me-auto">
            <h2 class="text-black font-w600">Balance Sheet</h2>
            <p class="mb-1">Dashboard / <a class="text-primary" href="{{ route('accounts.dashboard') }}">Accounts</a> / Balance Sheet</p>
        </div>
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
                <div class="col-md-7 text-end text-muted">
                    Profit shown for the period {{ $report['fy_start'] }} → {{ $asOf }}
                </div>
            </form>

            <div class="row">
                <div class="col-lg-6">
                    <h4>Assets</h4>
                    <table class="table table-borderless">
                        <tbody>
                            @forelse ($report['sections']['Asset'] as $row)
                                <tr>
                                    <td>{{ $row['account']->account_number }} — {{ $row['account']->name }}</td>
                                    <td class="text-end">{{ number_format($row['balance'], 2) }}</td>
                                </tr>
                            @empty
                                <tr><td class="text-muted">No assets yet.</td></tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="fw-bold">
                                <th>Total assets</th>
                                <th class="text-end">{{ number_format($report['totals']['Asset'], 2) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="col-lg-6">
                    <h4>Liabilities</h4>
                    <table class="table table-borderless">
                        <tbody>
                            @forelse ($report['sections']['Liability'] as $row)
                                <tr>
                                    <td>{{ $row['account']->account_number }} — {{ $row['account']->name }}</td>
                                    <td class="text-end">{{ number_format($row['balance'], 2) }}</td>
                                </tr>
                            @empty
                                <tr><td class="text-muted">No liabilities.</td></tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="fw-bold">
                                <th>Total liabilities</th>
                                <th class="text-end">{{ number_format($report['totals']['Liability'], 2) }}</th>
                            </tr>
                        </tfoot>
                    </table>

                    <h4>Equity</h4>
                    <table class="table table-borderless">
                        <tbody>
                            @foreach ($report['sections']['Equity'] as $row)
                                <tr>
                                    <td>{{ $row['account']->account_number }} — {{ $row['account']->name }}</td>
                                    <td class="text-end">{{ number_format($row['balance'], 2) }}</td>
                                </tr>
                            @endforeach
                            <tr>
                                <td>{{ $report['profit'] >= 0 ? 'Profit for the period' : 'Loss for the period' }}</td>
                                <td class="text-end">{{ number_format($report['profit'], 2) }}</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr class="fw-bold">
                                <th>Total equity</th>
                                <th class="text-end">{{ number_format($report['totals']['Equity'], 2) }}</th>
                            </tr>
                            <tr class="fw-bold">
                                <th>Liabilities + equity</th>
                                <th class="text-end">{{ number_format($report['liabilities_and_equity'], 2) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="alert {{ abs($report['difference']) < 0.01 ? 'alert-success' : 'alert-warning' }} mb-0">
                Assets {{ number_format($report['totals']['Asset'], 2) }} ·
                Liabilities + equity {{ number_format($report['liabilities_and_equity'], 2) }} ·
                Difference {{ number_format($report['difference'], 2) }}
                @if (abs($report['difference']) >= 0.01)
                    — a difference usually means opening balances are missing (assets entered without the matching equity).
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
