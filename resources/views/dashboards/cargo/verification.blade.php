@extends('layouts._master')

@section('content')
<div class="container-fluid">
    <div class="form-head align-items-center d-flex mb-sm-4 mb-3">
        <div class="me-auto">
            <h2 class="text-black font-w600">Warehouse Verification</h2>
            <p class="mb-1">Dashboard / <a class="text-primary" href="{{ route('cargo.index') }}">Cargo</a> / Verification</p>
        </div>
    </div>

    <div class="alert alert-info">
        Confirm what physically arrived. If the weight differs from China, the invoice is re-priced
        automatically and the original weight stays on record.
    </div>

    <div class="card">
        <div class="card-header"><h4 class="card-title mb-0">Waiting for verification ({{ $loads->count() }})</h4></div>
        <div class="card-body">
            @forelse ($loads as $load)
                <div class="border rounded p-3 mb-3">
                    <form method="POST" action="{{ route('cargo.verification.store', $load) }}">
                        @csrf
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <strong>{{ $load->load_no }}</strong> — {{ $load->load_name }}
                                <div class="small text-muted">
                                    {{ $load->customer->customer_firstname ?? '-' }} ·
                                    China weight: <strong>{{ $load->load_weight_value }}</strong> {{ optional($load->unittype)->unittype_name }} ·
                                    {{ $load->china_location ?: 'no location' }}
                                </div>
                            </div>
                            <a href="{{ route('cargo.show', $load) }}" class="btn btn-sm btn-outline-secondary">Open</a>
                        </div>

                        <div class="row g-2 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label mb-1">Actual weight ({{ optional($load->unittype)->unittype_name }})</label>
                                <input type="number" step="0.001" name="weight" class="form-control" required
                                       value="{{ $load->load_weight_value }}">
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input type="checkbox" name="has_damage" value="1" class="form-check-input" id="d{{ $load->id }}">
                                    <label class="form-check-label" for="d{{ $load->id }}">Damaged</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" name="has_missing" value="1" class="form-check-input" id="m{{ $load->id }}">
                                    <label class="form-check-label" for="m{{ $load->id }}">Missing</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" name="has_wrong_item" value="1" class="form-check-input" id="w{{ $load->id }}">
                                    <label class="form-check-label" for="w{{ $load->id }}">Wrong item</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label mb-1">Notes</label>
                                <input type="text" name="notes" class="form-control" placeholder="Condition, package count...">
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-primary w-100">Verify</button>
                            </div>
                        </div>
                    </form>
                </div>
            @empty
                <p class="text-muted mb-0">Nothing is waiting for verification.</p>
            @endforelse
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h4 class="card-title mb-0">Recently verified</h4></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-borderless">
                    <thead>
                        <tr>
                            <th>Tracking</th><th>Customer</th><th>China weight</th><th>Verified</th>
                            <th class="text-end">Price affected</th><th>Flags</th><th>When</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recent as $load)
                            <tr>
                                <td><a href="{{ route('cargo.show', $load) }}">{{ $load->load_no }}</a></td>
                                <td>{{ $load->customer->customer_firstname ?? '-' }}</td>
                                <td>{{ $load->original_weight_value }}</td>
                                <td><strong>{{ $load->verified_weight_value }}</strong> {{ optional($load->unittype)->unittype_name }}</td>
                                <td class="text-end {{ (float) $load->weight_price_impact != 0 ? 'text-warning fw-bold' : '' }}">
                                    {{ number_format((float) $load->weight_price_impact, 2) }}
                                </td>
                                <td>
                                    @if ($load->has_damage) <span class="badge bg-danger">damaged</span> @endif
                                    @if ($load->has_missing) <span class="badge bg-dark">missing</span> @endif
                                    @if ($load->has_wrong_item) <span class="badge bg-warning text-dark">wrong</span> @endif
                                </td>
                                <td>{{ optional($load->verified_at)->format('d M Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-muted">No cargo verified yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
