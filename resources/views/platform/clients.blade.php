@extends('layouts._master')

@section('content')
<div class="container-fluid">
    <div class="form-head align-items-center d-flex mb-sm-4 mb-3">
        <div class="me-auto">
            <h2 class="text-black font-w600">Clients &amp; subscriptions</h2>
            <p class="mb-1">Dashboard / <span class="text-primary">Platform</span> / Clients</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('create.clients') }}" class="btn btn-primary">+ New client</a>
            <a href="{{ route('platform.tokens') }}" class="btn btn-outline-primary">Unlock tokens</a>
        </div>
    </div>

    <div class="row">
        @foreach ([
            ['All clients', $summary['total'], 'text-black'],
            ['Active', $summary['active'], 'text-success'],
            ['Grace period', $summary['grace'], 'text-warning'],
            ['Expired / none', $summary['expired'], 'text-danger'],
            ['Locked', $summary['locked'], 'text-danger'],
            ['SMS clients', $summary['sms'], 'text-primary'],
        ] as [$label, $value, $class])
            <div class="col-xl-2 col-sm-4">
                <div class="card"><div class="card-body py-3">
                    <p class="mb-1 small text-muted">{{ $label }}</p>
                    <h4 class="mb-0 {{ $class }}">{{ $value }}</h4>
                </div></div>
            </div>
        @endforeach
    </div>

    <div class="card">
        <div class="card-header"><h4 class="card-title mb-0">Every client</h4></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-borderless align-middle">
                    <thead>
                        <tr>
                            <th>Client</th><th>Product</th><th>Expires</th>
                            <th>Status</th><th class="text-end">SMS left</th><th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rows as $row)
                            <tr>
                                <td>
                                    <strong>{{ $row->client->client_name }}</strong>
                                    <div class="small text-muted">{{ $row->client->client_email }}</div>
                                </td>
                                <td>
                                    <span class="badge {{ $row->product === 'sms' ? 'bg-info' : 'bg-primary' }}">
                                        {{ $row->product === 'sms' ? 'KodaSMS' : ($row->product === 'both' ? 'Freight + SMS' : 'KodaFreight') }}
                                    </span>
                                    <div class="small text-muted">
                                        {{ optional($row->subscription)->kodapackages->name ?? 'no package' }}
                                        · {{ count($row->modules) }} modules
                                    </div>
                                </td>
                                <td>
                                    @if ($row->subscription)
                                        {{ $row->subscription->expire_date?->format('d M Y') }}
                                        <div class="small {{ ($row->days_left ?? 0) < 0 ? 'text-danger' : 'text-muted' }}">
                                            {{ $row->days_left === null ? '' : ($row->days_left >= 0 ? $row->days_left . ' days left' : abs($row->days_left) . ' days ago') }}
                                        </div>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $badge = ['active' => 'bg-success', 'grace' => 'bg-warning text-dark', 'expired' => 'bg-danger', 'locked' => 'bg-dark', 'none' => 'bg-secondary'][$row->status] ?? 'bg-secondary';
                                    @endphp
                                    <span class="badge {{ $badge }}">{{ $statusLabels[$row->status] ?? $row->status }}</span>
                                </td>
                                <td class="text-end">
                                    {{ number_format($row->sms_left) }}
                                    <div class="small text-muted">of {{ number_format($row->sms_quota) }}</div>
                                </td>
                                <td class="text-end" style="min-width:230px">
                                    <div class="d-flex gap-1 justify-content-end flex-wrap">
                                        @if ($row->status === 'locked')
                                            <form method="POST" action="{{ route('platform.clients.unlock', $row->client) }}">
                                                @csrf <button class="btn btn-sm btn-outline-success">Unlock</button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('platform.clients.lock', $row->client) }}"
                                                  onsubmit="return confirm('Lock {{ $row->client->client_name }}? They will only see the unlock screen.')">
                                                @csrf <button class="btn btn-sm btn-outline-danger">Lock</button>
                                            </form>
                                        @endif
                                        @if ($row->subscription)
                                            <form method="POST" action="{{ route('platform.subscriptions.renew', $row->subscription) }}" class="d-flex gap-1">
                                                @csrf
                                                <select name="months" class="form-select form-select-sm" style="width:auto">
                                                    @foreach ([1, 3, 6, 12] as $m)
                                                        <option value="{{ $m }}">+{{ $m }} mo</option>
                                                    @endforeach
                                                </select>
                                                <button class="btn btn-sm btn-primary">Renew</button>
                                            </form>
                                            <form method="POST" action="{{ route('platform.tokens.issue') }}">
                                                @csrf
                                                <input type="hidden" name="subscription_id" value="{{ $row->subscription->id }}">
                                                <button class="btn btn-sm btn-outline-primary" title="Generate an unlock token for this client">Token</button>
                                            </form>
                                        @else
                                            <a href="{{ route('platform.subscriptions') }}" class="btn btn-sm btn-outline-secondary">Add subscription</a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center text-muted">No clients yet — create one first.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
