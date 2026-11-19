@extends('layouts._master')

@section('content')
<div class="container-fluid">
    <div class="form-head align-items-center d-flex mb-sm-4 mb-3">
        <div class="me-auto">
            <h2 class="text-black font-w600">Unlock tokens</h2>
            <p class="mb-1">Dashboard / <span class="text-primary">Platform</span> / Tokens</p>
        </div>
        <a href="{{ route('platform.clients') }}" class="btn btn-outline-primary">Clients overview</a>
    </div>

    <div class="alert alert-info">
        A client whose subscription has ended (or who you locked) sees only an unlock screen.
        Send them a token from here — it stays valid until its expiry date and can be used once.
    </div>

    <div class="row">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header"><h4 class="card-title mb-0">New token</h4></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('platform.tokens.issue') }}">
                        @csrf
                        <div class="mb-2">
                            <label class="form-label mb-1">Subscription</label>
                            <select name="subscription_id" class="form-control" required>
                                @foreach ($subscriptions as $subscription)
                                    <option value="{{ $subscription->id }}">
                                        {{ optional($subscription->client)->client_name }} — {{ optional($subscription->kodapackages)->name }}
                                        (ends {{ $subscription->expire_date?->format('d M Y') }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-2">
                            <label class="form-label mb-1">Valid until (optional)</label>
                            <input type="date" name="expire_date" class="form-control">
                            <small class="text-muted">Leave empty to use the subscription's expiry date.</small>
                        </div>
                        <button class="btn btn-primary w-100">Generate token</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card">
                <div class="card-header"><h4 class="card-title mb-0">Token history</h4></div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderless align-middle">
                            <thead><tr><th>Token</th><th>Client</th><th>Valid until</th><th>State</th><th></th></tr></thead>
                            <tbody>
                                @forelse ($tokens as $token)
                                    <tr>
                                        <td><code class="fw-bold">{{ $token->token_ }}</code></td>
                                        <td>{{ optional($token->client)->client_name ?? '—' }}</td>
                                        <td>{{ $token->expire_date?->format('d M Y') }}</td>
                                        <td>
                                            @if ($token->used_at)
                                                <span class="badge bg-secondary">used {{ $token->used_at->format('d M Y') }}</span>
                                            @elseif ($token->status)
                                                <span class="badge bg-success">active</span>
                                            @else
                                                <span class="badge bg-secondary">revoked</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            @if ($token->status)
                                                <form method="POST" action="{{ route('platform.tokens.revoke', $token) }}">
                                                    @csrf <button class="btn btn-sm btn-outline-danger">Revoke</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center text-muted">No token has been generated yet.</td></tr>
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
