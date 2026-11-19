@extends('layouts._master')

@section('content')
<div class="container-fluid">
    <div class="form-head align-items-center d-flex mb-sm-4 mb-3">
        <div class="me-auto">
            <h2 class="text-black font-w600">Accounts Settings</h2>
            <p class="mb-1">Dashboard / <a class="text-primary" href="{{ route('accounts.dashboard') }}">Accounts</a> / Settings</p>
        </div>
    </div>

    @include('dashboards.accounts._tabs')

    <div class="row">
        <div class="col-lg-8">
            <form method="POST" action="{{ route('accounts.settings.update') }}">
                @csrf
                @method('PUT')

                <div class="card">
                    <div class="card-header"><h4 class="card-title mb-0">Default accounts</h4></div>
                    <div class="card-body">
                        <div class="row">
                            @foreach ($fields as $field => $label)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">{{ $label }}</label>
                                    <select name="{{ $field }}" class="form-control">
                                        <option value="">— not set —</option>
                                        @foreach ($accounts as $account)
                                            <option value="{{ $account->id }}" @selected((int) $setting->{$field} === $account->id)>
                                                {{ $account->account_number }} — {{ $account->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endforeach

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Fiscal year starts in month</label>
                                <select name="fiscal_year_start_month" class="form-control">
                                    @for ($month = 1; $month <= 12; $month++)
                                        <option value="{{ $month }}" @selected((int) $setting->fiscal_year_start_month === $month)>
                                            {{ \Illuminate\Support\Carbon::create()->month($month)->format('F') }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        <h5 class="mt-3">Automatic posting</h5>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="auto_invoices" name="auto_post_invoices" value="1"
                                   @checked($setting->auto_post_invoices)>
                            <label class="form-check-label" for="auto_invoices">
                                Post invoices into the ledger (Dr Accounts Receivable / Cr Freight Income)
                            </label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="auto_payments" name="auto_post_payments" value="1"
                                   @checked($setting->auto_post_payments)>
                            <label class="form-check-label" for="auto_payments">
                                Post payments into the ledger (Dr Cash / Bank / Mobile money / Cr Accounts Receivable)
                            </label>
                        </div>
                        <div class="form-check mb-3">
                            <input type="checkbox" class="form-check-input" id="auto_expenses" name="auto_post_expenses" value="1"
                                   @checked($setting->auto_post_expenses)>
                            <label class="form-check-label" for="auto_expenses">
                                Post office expenses into the ledger (Dr expense account / Cr Cash in Hand)
                            </label>
                        </div>

                        <button class="btn btn-primary">Save settings</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header"><h4 class="card-title mb-0">Standard chart</h4></div>
                <div class="card-body">
                    <p class="text-muted">
                        Creates the full ERPNext-style tree (assets, liabilities, equity, income, expenses)
                        with sensible TZS defaults. Accounts you already have are left untouched.
                    </p>
                    <form method="POST" action="{{ route('accounts.settings.seed') }}">
                        @csrf
                        <button class="btn btn-outline-primary w-100">Create standard chart</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h4 class="card-title mb-0">Existing records</h4></div>
                <div class="card-body">
                    <p class="text-muted">
                        Posts every invoice, payment and office expense that was created before the
                        accounts module existed. Safe to run more than once - nothing is duplicated.
                    </p>
                    <form method="POST" action="{{ route('accounts.settings.post_existing') }}">
                        @csrf
                        <button class="btn btn-outline-success w-100">Post existing invoices &amp; payments</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
