@extends('layouts._master')

@section('content')
<div class="container-fluid">
    <div class="form-head align-items-center d-flex mb-sm-4 mb-3">
        <div class="me-auto">
            <h2 class="text-black font-w600">Release Control</h2>
            <p class="mb-1">Dashboard / <a class="text-primary" href="{{ route('cargo.index') }}">Cargo</a> / Release Control</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('cargo.release.index') }}" class="btn btn-sm {{ $filter ? 'btn-outline-primary' : 'btn-primary' }}">All</a>
            <a href="{{ route('cargo.release.index', ['filter' => 'ready']) }}" class="btn btn-sm {{ $filter === 'ready' ? 'btn-primary' : 'btn-outline-primary' }}">Ready</a>
            <a href="{{ route('cargo.release.index', ['filter' => 'blocked']) }}" class="btn btn-sm {{ $filter === 'blocked' ? 'btn-primary' : 'btn-outline-primary' }}">Blocked</a>
            <a href="{{ route('cargo.release.index', ['filter' => 'storage']) }}" class="btn btn-sm {{ $filter === 'storage' ? 'btn-primary' : 'btn-outline-primary' }}">Storage owing</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-borderless align-middle">
                    <thead>
                        <tr>
                            <th>Tracking</th>
                            <th>Customer</th>
                            <th>Status</th>
                            <th>Invoice</th>
                            <th>Storage</th>
                            <th>Pickup note</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($loads as $load)
                            @php
                                $checks = collect($load->release_checks)->keyBy('key');
                                $badge = fn ($key) => $checks[$key]['ok'] ?? false
                                    ? '<span class="badge bg-success">✓</span>'
                                    : '<span class="badge bg-danger">✕</span>';
                            @endphp
                            <tr>
                                <td>
                                    <a href="{{ route('cargo.show', $load) }}"><strong>{{ $load->load_no }}</strong></a>
                                    <div class="small text-muted">{{ $load->load_name }}</div>
                                </td>
                                <td>{{ $load->customer->customer_firstname ?? '-' }}</td>
                                <td><span class="badge bg-primary">{{ $load->statusLabel() }}</span></td>
                                <td>
                                    {!! $badge('invoice') !!}
                                    @if ($load->invoice_balance !== null)
                                        <small class="{{ $load->invoice_balance > 0 ? 'text-danger' : 'text-muted' }}">
                                            {{ number_format($load->invoice_balance, 2) }}
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    {!! $badge('storage') !!}
                                    @if ($load->storage_balance > 0)
                                        <small class="text-danger fw-bold">{{ number_format($load->storage_balance, 2) }}</small>
                                    @endif
                                </td>
                                <td>{!! $badge('pickup_note') !!}</td>
                                <td class="text-end" style="min-width:170px">
                                    @if ($load->can_release)
                                        <form method="POST" action="{{ route('cargo.release.release', $load) }}" class="d-inline">
                                            @csrf
                                            <button class="btn btn-sm btn-primary">Release</button>
                                        </form>
                                    @endif
                                    @if ($load->can_collect)
                                        <form method="POST" action="{{ route('cargo.release.collect', $load) }}" class="d-inline">
                                            @csrf
                                            <button class="btn btn-sm btn-outline-success">Collected</button>
                                        </form>
                                    @endif
                                    @if (! $load->can_release && ! $load->can_collect)
                                        <span class="text-danger small">Blocked</span>
                                    @endif
                                </td>
                            </tr>
                            @if (! $load->can_release && ! $load->can_collect)
                                <tr class="table-light">
                                    <td colspan="7" class="pt-0">
                                        @foreach (collect($load->release_checks)->where('ok', false) as $blocked)
                                            <span class="badge bg-white text-danger border me-1 mb-1" title="{{ $blocked['detail'] }}">
                                                {{ $blocked['label'] }}
                                            </span>
                                        @endforeach
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr><td colspan="7" class="text-center text-muted">No cargo waiting for release.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h4 class="card-title mb-0">Recent pickup notes</h4></div>
        <div class="card-body">
            <table class="table table-sm table-borderless mb-0">
                <thead><tr><th>Note</th><th>Invoice</th><th>Status</th><th>Issued</th><th></th></tr></thead>
                <tbody>
                    @forelse ($notes as $note)
                        <tr>
                            <td>{{ $note->note_no }}</td>
                            <td>{{ optional($note->invoice)->invoice_no }}</td>
                            <td><span class="badge {{ $note->status === 'issued' ? 'bg-success' : 'bg-secondary' }}">{{ $note->status }}</span></td>
                            <td>{{ optional($note->issued_at)->format('d M Y H:i') }}</td>
                            <td class="text-end">
                                @if ($note->status === 'issued')
                                    <a href="{{ route('cargo.pickup.note', $note->invoice_id) }}" class="btn btn-sm btn-outline-primary">Open</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-muted text-center">No pickup notes yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
