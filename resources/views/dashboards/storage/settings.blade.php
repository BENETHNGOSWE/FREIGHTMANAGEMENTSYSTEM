@extends('layouts._master')

@section('content')
<div class="container-fluid">
    <div class="form-head align-items-center d-flex mb-sm-4 mb-3">
        <div class="me-auto">
            <h2 class="text-black font-w600">Storage Settings</h2>
            <p class="mb-1">Dashboard / <a class="text-primary" href="{{ route('storage.index') }}">Storage</a> / Settings</p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header"><h4 class="card-title mb-0">Default rate</h4></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('storage.settings.update') }}">
                        @csrf @method('PUT')
                        <div class="mb-3">
                            <label class="form-label">Free days</label>
                            <input type="number" name="free_days" class="form-control" min="0" required value="{{ $settings->free_days }}">
                            <small class="text-muted">Storage starts the day after the free period ends.</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Daily rate (TZS)</label>
                            <input type="number" step="0.01" name="daily_rate" class="form-control" min="0" required value="{{ (float) $settings->daily_rate }}">
                        </div>
                        <div class="form-check mb-3">
                            <input type="checkbox" name="auto_accrue" value="1" class="form-check-input" id="auto" @checked($settings->auto_accrue)>
                            <label class="form-check-label" for="auto">Add the daily charge automatically (nightly job)</label>
                        </div>
                        <button class="btn btn-primary">Save default</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card">
                <div class="card-header"><h4 class="card-title mb-0">Per-customer rate</h4></div>
                <div class="card-body">
                    <p class="text-muted">A customer with an agreement gets their own rate. Everyone else uses the default.</p>
                    <form method="POST" action="{{ route('storage.customer_rate') }}" class="row g-2 align-items-end">
                        @csrf
                        <div class="col-md-4">
                            <label class="form-label mb-1">Customer</label>
                            <select name="customer_id" class="form-control" required>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->customer_firstname }} {{ $customer->customer_lastname }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label mb-1">Free days</label>
                            <input type="number" name="free_days" class="form-control" min="0" placeholder="{{ $settings->free_days }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label mb-1">Rate / day</label>
                            <input type="number" step="0.01" name="daily_rate" class="form-control" min="0" placeholder="{{ (float) $settings->daily_rate }}">
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-primary w-100">Save</button>
                        </div>
                    </form>

                    <table class="table table-sm table-borderless mt-4">
                        <thead><tr><th>Customer</th><th class="text-end">Free days</th><th class="text-end">Rate / day</th><th>Note</th></tr></thead>
                        <tbody>
                            @forelse ($overrides as $override)
                                <tr>
                                    <td>{{ optional($override->customer)->customer_firstname }} {{ optional($override->customer)->customer_lastname }}</td>
                                    <td class="text-end">{{ $override->free_days ?? '—' }}</td>
                                    <td class="text-end">{{ $override->daily_rate !== null ? number_format((float) $override->daily_rate, 2) : '—' }}</td>
                                    <td>{{ $override->notes }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-muted">No customer has a special rate yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
