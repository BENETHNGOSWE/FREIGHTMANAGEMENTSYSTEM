@extends('layouts._master')

@section('content')
<div class="container-fluid">
    <div class="form-head align-items-center d-flex mb-sm-4 mb-3">
        <div class="me-auto">
            <h2 class="text-black font-w600">Subscriptions</h2>
            <p class="mb-1">Dashboard / <span class="text-primary">Platform</span> / Subscriptions</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('platform.clients') }}" class="btn btn-outline-primary">Clients overview</a>
            <a href="{{ route('owner.list.package') }}" class="btn btn-outline-primary">Packages</a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header"><h4 class="card-title mb-0">Assign a package</h4></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('platform.subscriptions.store') }}">
                        @csrf
                        <div class="mb-2">
                            <label class="form-label mb-1">Client</label>
                            <select name="client_id" class="form-control" required>
                                @foreach (\App\Models\Client::orderBy('client_name')->get() as $client)
                                    <option value="{{ $client->id }}">{{ $client->client_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-2">
                            <label class="form-label mb-1">Product</label>
                            <select name="product" class="form-control" required>
                                @foreach ($products as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">KodaSMS clients do not need to be a freight company.</small>
                        </div>
                        <div class="mb-2">
                            <label class="form-label mb-1">Package</label>
                            <select name="package_id" class="form-control" required>
                                @foreach (\App\Models\Kodapackage::orderBy('name')->get() as $package)
                                    <option value="{{ $package->id }}">
                                        {{ $package->name }} — {{ number_format((float) $package->monthly_fee, 0) }}/mo, {{ number_format((float) $package->yearly_fee, 0) }}/yr
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">What is inside a package is set under Products → Modules.</small>
                        </div>
                        <div class="mb-2">
                            <label class="form-label mb-1">SMS included (0 for freight only)</label>
                            <input type="number" name="sms_quota" class="form-control" min="0" value="0">
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <label class="form-label mb-1">Start date</label>
                                <input type="date" name="start_date" class="form-control" value="{{ now()->toDateString() }}" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label mb-1">Months</label>
                                <input type="number" name="duration_in_month" class="form-control" min="1" max="120" value="12" required>
                            </div>
                        </div>
                        <div class="mb-2">
                            <label class="form-label mb-1">Amount (leave empty = package price)</label>
                            <input type="number" step="0.01" name="amount" class="form-control">
                        </div>
                        <button class="btn btn-primary w-100">Create subscription</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex gap-2 mb-3 flex-wrap">
                        <a href="{{ route('platform.subscriptions') }}" class="btn btn-sm {{ $status ? 'btn-outline-primary' : 'btn-primary' }}">All</a>
                        @foreach (['1' => 'Paid', '2' => 'Partial', '0' => 'Not paid', 'expired' => 'Expired', 'none' => 'No subscription'] as $key => $label)
                            <a href="{{ route('platform.subscriptions', ['status' => $key]) }}"
                               class="btn btn-sm {{ $status === $key ? 'btn-primary' : 'btn-outline-primary' }}">{{ $label }}</a>
                        @endforeach
                    </div>

                    @if (($unsubscribed ?? collect())->isNotEmpty())
                        <div class="alert alert-warning">
                            <strong>{{ $unsubscribed->count() }} client(s) have no subscription yet.</strong>
                            They can sign in but land on the token screen until a package is assigned.
                            <ul class="mb-0 mt-2">
                                @foreach ($unsubscribed as $waiting)
                                    <li>{{ $waiting->client_name }} <span class="text-muted">({{ $waiting->client_email }})</span></li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover table-borderless align-middle">
                            <thead>
                                <tr><th>Client</th><th>Package</th><th>Product</th><th>Period</th><th>SMS</th><th class="text-end">Amount</th><th></th></tr>
                            </thead>
                            <tbody>
                                @forelse ($subscriptions as $subscription)
                                    <tr>
                                        <td>{{ optional($subscription->client)->client_name ?? '—' }}</td>
                                        <td>{{ optional($subscription->kodapackages)->name ?? '—' }}</td>
                                        <td><span class="badge bg-light text-dark">{{ $products[$subscription->product] ?? $subscription->product }}</span></td>
                                        <td class="small">
                                            {{ $subscription->start_date?->format('d M Y') }} →
                                            <strong class="{{ $subscription->expire_date && $subscription->expire_date->isPast() ? 'text-danger' : '' }}">
                                                {{ $subscription->expire_date?->format('d M Y') }}
                                            </strong>
                                        </td>
                                        <td class="small">{{ number_format($subscription->smsLeft()) }} / {{ number_format((int) $subscription->sms_quota) }}</td>
                                        <td class="text-end">{{ number_format((float) $subscription->amount, 2) }}</td>
                                        <td class="text-end">
                                            <form method="POST" action="{{ route('platform.subscriptions.renew', $subscription) }}" class="d-flex gap-1 justify-content-end">
                                                @csrf
                                                <select name="months" class="form-select form-select-sm" style="width:auto">
                                                    @foreach ([1, 3, 6, 12] as $m) <option value="{{ $m }}">+{{ $m }} mo</option> @endforeach
                                                </select>
                                                <button class="btn btn-sm btn-primary">Renew</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="text-center text-muted">No subscription yet. Assign a package on the left.</td></tr>
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
