@extends('layouts._master')
@section('content')
    <div class="container-fluid">
        <div class="form-head d-flex align-items-center flex-wrap mb-sm-4 mb-3">
            <div class="me-auto">
                <h2 class="text-black font-w600">Platform overview</h2>
                <p class="mb-0">Every client, what they bought, and where each subscription stands.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('platform.clients') }}" class="btn btn-primary">Clients</a>
                <a href="{{ route('platform.subscriptions') }}" class="btn btn-outline-primary">Subscriptions</a>
                <a href="{{ route('platform.tokens') }}" class="btn btn-outline-primary">Unlock tokens</a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="row">
            @foreach ([
                ['All clients', $totals['clients'], 'text-black', route('platform.clients')],
                ['Active', $totals['active'], 'text-success', route('platform.clients')],
                ['In grace', $totals['grace'], 'text-warning', route('platform.clients')],
                ['Expired', $totals['expired'], 'text-danger', route('platform.clients')],
                ['Locked', $totals['locked'], 'text-danger', route('platform.clients')],
                ['KodaSMS clients', $totals['sms'], 'text-info', route('platform.clients')],
            ] as [$label, $value, $class, $link])
                <div class="col-xl-2 col-md-4 col-sm-6">
                    <a href="{{ $link }}" class="card">
                        <div class="card-body">
                            <h2 class="fs-30 {{ $class }} font-w600 mb-1">{{ $value }}</h2>
                            <span class="fs-14">{{ $label }}</span>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>

        <div class="row">
            <div class="col-xl-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Renewals to chase</h4>
                    </div>
                    <div class="card-body">
                        @forelse ($renewals as $row)
                            <div class="d-flex align-items-center border-bottom py-2">
                                <div class="me-auto">
                                    <strong>{{ optional($row->client)->client_name }}</strong>
                                    <div class="small text-muted">
                                        {{ $products[$row->product] ?? $row->product }}
                                        · expires {{ $row->expires ? $row->expires->format('d M Y') : 'never set' }}
                                    </div>
                                </div>
                                <span class="badge bg-{{ $row->status === 'locked' ? 'secondary' : ($row->status === 'grace' ? 'warning' : 'danger') }} me-2">
                                    {{ ucfirst($row->status) }}
                                </span>
                                <form action="{{ route('platform.subscriptions.store') }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="renew_client_id" value="{{ $row->client->id }}">
                                    <input type="hidden" name="months" value="12">
                                    <button class="btn btn-sm btn-outline-primary">Renew 12 months</button>
                                </form>
                            </div>
                        @empty
                            <p class="mb-0 text-muted">Nothing to chase — every subscription is current.</p>
                        @endforelse
                    </div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="card">
                    <div class="card-header"><h4 class="card-title">Running out within 30 days</h4></div>
                    <div class="card-body">
                        @forelse ($expiringSoon as $row)
                            <div class="d-flex justify-content-between py-1">
                                <span>{{ optional($row->client)->client_name }}</span>
                                <span class="text-warning">{{ $row->days_left }}d</span>
                            </div>
                        @empty
                            <p class="mb-0 text-muted">Nothing expires in the next month.</p>
                        @endforelse
                    </div>
                </div>
                <div class="card">
                    <div class="card-header"><h4 class="card-title">Platform settings</h4></div>
                    <div class="card-body">
                        <p class="mb-1">Grace period: <strong>{{ $graceDays }} day(s)</strong></p>
                        <p class="mb-3">Enforce for every client: <strong>{{ $enforceAll ? 'yes' : 'no' }}</strong></p>
                        <p class="mb-3">Subscription value on file: <strong>{{ number_format((float) ($subscriptionValue ?? 0), 2) }}</strong></p>
                        <a href="{{ route('platform.settings') }}" class="btn btn-sm btn-outline-primary">Change settings</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header"><h4 class="card-title">Latest subscription invoices</h4></div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
                                <thead>
                                    <tr><th>Client</th><th>Amount</th><th>Balance</th><th>Billed</th><th>Due</th><th>Status</th></tr>
                                </thead>
                                <tbody>
                                @forelse ($recentInvoices as $invoice)
                                    <tr>
                                        <td>{{ optional($invoice->client)->client_name ?? '—' }}</td>
                                        <td>{{ number_format((float) $invoice->amount, 2) }}</td>
                                        <td>{{ number_format((float) $invoice->balance, 2) }}</td>
                                        <td>{{ $invoice->bill_date ? $invoice->bill_date->format('d M Y') : '—' }}</td>
                                        <td>{{ $invoice->due_date ? $invoice->due_date->format('d M Y') : '—' }}</td>
                                        <td>
                                            <span class="badge bg-{{ (int) $invoice->status === 1 ? 'success' : ((int) $invoice->status === 2 ? 'warning' : 'secondary') }}">
                                                {{ ['Pending', 'Paid', 'Partial'][(int) $invoice->status] ?? $invoice->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="text-muted">No subscription invoices yet.</td></tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
