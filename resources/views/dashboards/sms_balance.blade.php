@extends('layouts._master')
@section('content')
    <div class="container-fluid">
        <div class="form-head d-flex align-items-center mb-sm-4 mb-3">
            <div class="me-auto">
                <h2 class="text-black font-w600">SMS balance</h2>
                <p class="mb-0">What your package includes and what is left of it.</p>
            </div>
            <a href="{{ route('admin.message.lead') }}" class="btn btn-primary">Send a message</a>
        </div>

        <div class="row">
            <div class="col-xl-3 col-sm-6">
                <div class="card">
                    <div class="card-body">
                        <h2 class="fs-34 text-black font-w600">{{ number_format($quota) }}</h2>
                        <span>Assigned in the package</span>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card">
                    <div class="card-body">
                        <h2 class="fs-34 text-black font-w600">{{ number_format($used) }}</h2>
                        <span>Used so far</span>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card">
                    <div class="card-body">
                        <h2 class="fs-34 {{ $left > 0 ? 'text-success' : 'text-danger' }} font-w600">{{ number_format($left) }}</h2>
                        <span>Still available</span>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card">
                    <div class="card-body">
                        <h2 class="fs-34 text-black font-w600">{{ number_format($sentToday) }}</h2>
                        <span>Sent today</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-7">
                <div class="card">
                    <div class="card-header"><h4 class="card-title">Allowance</h4></div>
                    <div class="card-body">
                        @php($pct = $quota > 0 ? min(100, round(($quota - $left) / $quota * 100)) : 0)
                        <div class="progress mb-3" style="height:12px;">
                            <div class="progress-bar {{ $pct > 90 ? 'bg-danger' : ($pct > 70 ? 'bg-warning' : 'bg-primary') }}"
                                 style="width: {{ $pct }}%"></div>
                        </div>
                        <p class="mb-1 text-muted">{{ $pct }}% of the assigned SMS used.</p>
                        @if ($left <= 0)
                            <div class="alert alert-warning mb-0">
                                The allowance is finished, so sending is blocked. Ask the platform owner to add
                                more SMS to your package — nothing is deleted and your messages stay on record.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-xl-5">
                <div class="card">
                    <div class="card-header"><h4 class="card-title">Package</h4></div>
                    <div class="card-body">
                        <table class="table table-sm mb-0">
                            <tr><td>Product</td><td class="text-end">{{ $product === 'sms' ? 'KodaSMS (bulk SMS)' : ($product === 'both' ? 'KodaFreight + KodaSMS' : 'KodaFreight') }}</td></tr>
                            <tr><td>Status</td><td class="text-end">{{ ucfirst($status) }}</td></tr>
                            <tr><td>Expires</td><td class="text-end">{{ $expires ? $expires->format('d M Y') : '—' }}</td></tr>
                            <tr><td>Days left</td><td class="text-end">{{ $daysLeft === null ? '—' : $daysLeft }}</td></tr>
                            <tr><td>Failed messages</td><td class="text-end">{{ number_format($failed) }}</td></tr>
                            <tr><td>Last message</td><td class="text-end">{{ $lastMessage ? $lastMessage->created_at->format('d M Y H:i') : 'none yet' }}</td></tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
