@extends('layouts._master')

@section('content')
<div class="container-fluid">
    <div class="form-head align-items-center d-flex mb-sm-4 mb-3">
        <div class="me-auto">
            <h2 class="text-black font-w600">Cargo</h2>
            <p class="mb-1">Dashboard / <span class="text-primary">Cargo Operations</span></p>
        </div>
        <a href="{{ route('cargo.release.index') }}" class="btn btn-outline-primary">Release Control</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-end mb-3">
                <div class="col-md-5">
                    <label class="form-label mb-1">Search</label>
                    <input type="text" name="search" value="{{ $search }}" class="form-control"
                           placeholder="Tracking number, product, China location or batch">
                </div>
                <div class="col-md-4">
                    <label class="form-label mb-1">Status</label>
                    <select name="status" class="form-control">
                        <option value="">All statuses</option>
                        @foreach ($statuses as $key => $label)
                            <option value="{{ $key }}" @selected($statusFilter === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button class="btn btn-primary w-100">Filter</button>
                    <a href="{{ route('cargo.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover table-borderless">
                    <thead>
                        <tr>
                            <th>Tracking</th>
                            <th>Customer</th>
                            <th>Weight</th>
                            <th>China location / batch</th>
                            <th>Status</th>
                            <th class="text-end">Storage owed</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($loads as $load)
                            <tr>
                                <td>
                                    <a href="{{ route('cargo.show', $load) }}"><strong>{{ $load->load_no }}</strong></a>
                                    <div class="small text-muted">{{ $load->load_name }}</div>
                                </td>
                                <td>{{ $load->customer->customer_firstname ?? '-' }}</td>
                                <td>{{ $load->billableWeight() }} {{ optional($load->unittype)->unittype_name }}</td>
                                <td>
                                    {{ $load->china_location ?: '-' }}
                                    @if ($load->batch_no) <span class="badge bg-light text-dark">{{ $load->batch_no }}</span> @endif
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ \Illuminate\Support\Str::before($load->statusLabel(), ' (') }}</span>
                                    @if ($load->has_damage) <span class="badge bg-danger">damaged</span> @endif
                                    @if ($load->has_missing) <span class="badge bg-dark">missing</span> @endif
                                    @if ($load->has_wrong_item) <span class="badge bg-warning text-dark">wrong item</span> @endif
                                </td>
                                <td class="text-end {{ $load->storage_balance > 0 ? 'text-danger fw-bold' : '' }}">
                                    {{ number_format($load->storage_balance, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted">No cargo yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $loads->links() }}
        </div>
    </div>
</div>
@endsection
