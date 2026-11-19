@extends('layouts._master')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card">
                <div class="card-body">
                    @php
                        $badge = ['active' => 'bg-success', 'grace' => 'bg-warning text-dark', 'expired' => 'bg-danger', 'locked' => 'bg-dark', 'none' => 'bg-secondary'][$status] ?? 'bg-secondary';
                        $title = [
                            'active' => 'Your subscription is active',
                            'grace' => 'Grace period',
                            'expired' => 'Your subscription has ended',
                            'locked' => 'This account is locked',
                            'none' => 'No subscription yet',
                        ][$status] ?? 'Subscription';
                    @endphp

                    <div class="text-center mb-4">
                        <span class="badge {{ $badge }} mb-2">{{ strtoupper($status) }}</span>
                        <h2 class="text-black font-w600">{{ $title }}</h2>
                        <p class="text-muted mb-0">
                            {{ optional($client)->client_name }}
                            @if ($subscription)
                                · {{ optional($subscription->kodapackages)->name }} package
                                · ends {{ $subscription->expire_date?->format('d M Y') }}
                            @endif
                        </p>
                    </div>

                    <ul class="list-group list-group-flush mb-4">
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Days remaining</span>
                            <strong class="{{ ($daysLeft ?? 0) < 0 ? 'text-danger' : '' }}">
                                {{ $daysLeft === null ? '—' : ($daysLeft >= 0 ? $daysLeft . ' days' : abs($daysLeft) . ' days overdue') }}
                            </strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Grace period</span>
                            <strong>{{ $graceDays }} day(s) after the expiry date</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>SMS balance</span>
                            <strong>{{ number_format($smsLeft) }} of {{ number_format($smsQuota) }}</strong>
                        </li>
                    </ul>

                    @if (in_array($status, ['expired', 'locked', 'none'], true))
                        <div class="alert alert-warning">
                            Enter the token your provider sent you. It unlocks the system until the date printed
                            with the token — nothing is deleted, your data is safe.
                        </div>
                    @endif

                    <form method="POST" action="{{ route('subscription.unlock.post') }}" class="row g-2 align-items-end">
                        @csrf
                        <div class="col-md-8">
                            <label class="form-label mb-1">Unlock token</label>
                            <input type="text" name="token" class="form-control form-control-lg text-uppercase"
                                   placeholder="e.g. K7QM2XW9PL4RT8NB" value="{{ old('token') }}" required>
                        </div>
                        <div class="col-md-4">
                            <button class="btn btn-primary btn-lg w-100">Unlock</button>
                        </div>
                    </form>

                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">Need a new token? Contact your provider and quote your client name.</small>
                        <a href="{{ route('logout') }}"
                           onclick="event.preventDefault(); document.getElementById('logout-form-unlock').submit();"
                           class="btn btn-sm btn-outline-secondary">Sign out</a>
                    </div>
                    <form id="logout-form-unlock" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
