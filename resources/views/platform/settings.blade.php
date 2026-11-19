@extends('layouts._master')

@section('content')
<div class="container-fluid">
    <div class="form-head align-items-center d-flex mb-sm-4 mb-3">
        <div class="me-auto">
            <h2 class="text-black font-w600">Platform settings</h2>
            <p class="mb-1">Dashboard / <span class="text-primary">Platform</span> / Settings</p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header"><h4 class="card-title mb-0">Grace period</h4></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('platform.settings.update') }}">
                        @csrf @method('PUT')
                        <div class="mb-3">
                            <label class="form-label">Days after expiry before the system locks</label>
                            <input type="number" name="grace_days" class="form-control" min="0" max="60" value="{{ $graceDays }}" required>
                            <small class="text-muted">
                                During the grace period the client keeps working and sees a warning in the header.
                                After it, every page redirects to the unlock screen.
                            </small>
                        </div>
                        <div class="form-check mb-3">
                            <input type="checkbox" class="form-check-input" name="enforce_all" value="1" id="enforce_all"
                                   @checked($enforceForEveryone)>
                            <label class="form-check-label" for="enforce_all">
                                Lock a client that has <strong>no subscription</strong> (recommended)
                            </label>
                            <div class="small text-muted">
                                On: a new client cannot use the system until you assign a package and they enter
                                their token — the money-first flow. Off: clients with no subscription keep working
                                on their product's modules, which is only useful while you migrate existing clients.
                            </div>
                        </div>
                        <button class="btn btn-primary">Save</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header"><h4 class="card-title mb-0">This platform</h4></div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr><th>Clients</th><td class="text-end">{{ $counts['clients'] }}</td></tr>
                        <tr><th>Freight clients</th><td class="text-end">{{ $counts['freight'] }}</td></tr>
                        <tr><th>SMS clients</th><td class="text-end">{{ $counts['sms'] }}</td></tr>
                        <tr><th>Subscriptions</th><td class="text-end">{{ $counts['subscriptions'] }}</td></tr>
                        <tr><th>Expired subscriptions</th><td class="text-end text-danger">{{ $counts['expired'] }}</td></tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
