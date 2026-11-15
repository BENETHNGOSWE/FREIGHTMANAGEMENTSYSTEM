@extends('layouts._master')

@section('content')
<div class="container-fluid">
    <div class="form-head align-items-center d-flex mb-sm-4 mb-3">
        <div class="me-auto">
            <h2 class="text-black font-w600">Chart of Accounts</h2>
            <p class="mb-1">Dashboard / <span><a class="text-primary" href="{{ route('accounts.dashboard') }}">Accounts</a></span></p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <form method="GET" action="{{ route('accounts.chart.index') }}" class="d-flex">
                <input type="text" name="search" value="{{ $search }}" class="form-control me-2"
                       placeholder="Search account or number">
                <button class="btn btn-outline-primary">Search</button>
            </form>
            <a href="{{ route('accounts.chart.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> New Account
            </a>
        </div>
    </div>

    @include('dashboards.accounts._tabs')

    @foreach ($rootTypes as $root)
        @php $rows = $grouped[$root] ?? collect(); @endphp
        @continue($rows->isEmpty())
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">{{ $root }}s <span class="badge bg-light text-dark">{{ $rows->count() }}</span></h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-borderless">
                        <thead>
                            <tr>
                                <th style="width:110px">Number</th>
                                <th>Account</th>
                                <th>Type</th>
                                <th>Parent</th>
                                <th class="text-end">Opening balance</th>
                                <th style="width:150px" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rows as $account)
                                <tr>
                                    <td>{{ $account->account_number ?: '-' }}</td>
                                    <td>
                                        @if ($account->parent)
                                            <span class="text-muted">{{ $account->parent->name }} /</span>
                                        @endif
                                        <strong>{{ $account->name }}</strong>
                                        @if ($account->is_group)
                                            <span class="badge bg-info ms-1">group</span>
                                        @endif
                                        @if ($account->disabled)
                                            <span class="badge bg-secondary ms-1">disabled</span>
                                        @endif
                                    </td>
                                    <td>{{ $account->account_type ?: '-' }}</td>
                                    <td>{{ $account->parent->name ?? '-' }}</td>
                                    <td class="text-end">{{ number_format((float) $account->opening_balance, 2) }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('accounts.ledger.index', ['account_id' => $account->id]) }}"
                                           class="btn btn-sm btn-outline-primary me-1" title="Ledger">
                                            <i class="fas fa-list"></i>
                                        </a>
                                        <a href="{{ route('accounts.chart.edit', $account) }}"
                                           class="btn btn-sm btn-outline-success me-1" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('accounts.chart.destroy', $account) }}" method="POST"
                                              class="d-inline" onsubmit="return confirm('Delete {{ $account->name }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection
