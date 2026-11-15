@extends('layouts._master')

@section('content')
<div class="container-fluid">
    <div class="form-head align-items-center d-flex mb-sm-4 mb-3">
        <div class="me-auto">
            <h2 class="text-black font-w600">{{ $account->exists ? 'Edit Account' : 'New Account' }}</h2>
            <p class="mb-1">
                Dashboard /
                <a class="text-primary" href="{{ route('accounts.chart.index') }}">Chart of Accounts</a> /
                {{ $account->exists ? $account->name : 'Create' }}
            </p>
        </div>
        <a href="{{ route('accounts.chart.index') }}" class="btn btn-outline-primary">Back to chart</a>
    </div>

    @if (! empty($hasEntries))
        <div class="alert alert-info">
            This account already has journal entries. Its type and group flag can no longer change.
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form method="POST"
                  action="{{ $account->exists ? route('accounts.chart.update', $account) : route('accounts.chart.store') }}">
                @csrf
                @if ($account->exists) @method('PUT') @endif

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Account name</label>
                        <input type="text" name="name" class="form-control" required
                               value="{{ old('name', $account->name) }}">
                        @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Account number</label>
                        <input type="text" name="account_number" class="form-control"
                               value="{{ old('account_number', $account->account_number) }}" placeholder="e.g. 4110">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Root type</label>
                        <select name="root_type" class="form-control">
                            @foreach ($rootTypes as $type)
                                <option value="{{ $type }}" @selected(old('root_type', $account->root_type) === $type)>{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Account type</label>
                        <select name="account_type" class="form-control">
                            <option value="">— none —</option>
                            @foreach ($accountTypes as $type)
                                <option value="{{ $type }}" @selected(old('account_type', $account->account_type) === $type)>{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Parent account</label>
                        <select name="parent_id" class="form-control">
                            <option value="">— top level —</option>
                            @foreach ($parents as $parent)
                                <option value="{{ $parent->id }}" @selected((int) old('parent_id', $account->parent_id) === $parent->id)>
                                    {{ $parent->account_number }} {{ $parent->name }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">The root type is taken from the parent.</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Currency</label>
                        <input type="text" name="currency" class="form-control" value="{{ old('currency', $account->currency ?: 'TZS') }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Opening balance</label>
                        <input type="number" step="0.01" name="opening_balance" class="form-control"
                               value="{{ old('opening_balance', $account->opening_balance ?? 0) }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Opening date</label>
                        <input type="date" name="opening_date" class="form-control"
                               value="{{ old('opening_date', optional($account->opening_date)->toDateString()) }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="is_group" value="1" class="form-check-input" id="is_group"
                                   @checked(old('is_group', $account->is_group))>
                            <label class="form-check-label" for="is_group">This is a group (has sub-accounts)</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" name="disabled" value="1" class="form-check-input" id="disabled"
                                   @checked(old('disabled', $account->disabled))>
                            <label class="form-check-label" for="disabled">Disabled</label>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Description</label>
                        <input type="text" name="description" class="form-control"
                               value="{{ old('description', $account->description) }}">
                    </div>
                </div>

                <button class="btn btn-primary">{{ $account->exists ? 'Save changes' : 'Create account' }}</button>
            </form>
        </div>
    </div>
</div>
@endsection
