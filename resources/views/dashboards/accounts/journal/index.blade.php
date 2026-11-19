@extends('layouts._master')

@section('content')
<div class="container-fluid">
    <div class="form-head align-items-center d-flex mb-sm-4 mb-3">
        <div class="me-auto">
            <h2 class="text-black font-w600">Journal Entries</h2>
            <p class="mb-1">Dashboard / <span><a class="text-primary" href="{{ route('accounts.dashboard') }}">Accounts</a></span></p>
        </div>
        <a href="{{ route('accounts.journal.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> New Journal Entry
        </a>
    </div>

    @include('dashboards.accounts._tabs')

    <div class="card">
        <div class="card-body">
            <form method="GET" action="{{ route('accounts.journal.index') }}" class="row g-2 mb-3 align-items-end">
                <div class="col-md-2">
                    <label class="form-label mb-1">From</label>
                    <input type="date" name="from" value="{{ $from }}" class="form-control">
                </div>
                <div class="col-md-2">
                    <label class="form-label mb-1">To</label>
                    <input type="date" name="to" value="{{ $to }}" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label mb-1">Type</label>
                    <select name="entry_type" class="form-control">
                        <option value="">All types</option>
                        @foreach ($types as $available)
                            <option value="{{ $available }}" @selected($type === $available)>{{ $available }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label mb-1">Search</label>
                    <input type="text" name="search" value="{{ $search }}" class="form-control" placeholder="Entry no, invoice no, remark">
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button class="btn btn-primary w-100">Filter</button>
                    <a href="{{ route('accounts.journal.index', ['all_dates' => 1]) }}" class="btn btn-outline-secondary">All</a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover table-borderless">
                    <thead>
                        <tr>
                            <th>Entry no</th>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Source</th>
                            <th>Remarks</th>
                            <th class="text-end">Debit</th>
                            <th class="text-end">Credit</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($entries as $entry)
                            <tr>
                                <td><a href="{{ route('accounts.journal.show', $entry) }}">{{ $entry->entry_no }}</a></td>
                                <td>{{ $entry->posting_date?->format('d M Y') }}</td>
                                <td>{{ $entry->entry_type }}</td>
                                <td>{{ $entry->reference_no ?: '-' }}</td>
                                <td class="text-truncate" style="max-width: 260px">{{ $entry->remarks }}</td>
                                <td class="text-end">{{ number_format((float) $entry->total_debit, 2) }}</td>
                                <td class="text-end">{{ number_format((float) $entry->total_credit, 2) }}</td>
                                <td>
                                    <span class="badge {{ $entry->status === 'posted' ? 'bg-success' : ($entry->status === 'cancelled' ? 'bg-secondary' : 'bg-warning') }}">
                                        {{ ucfirst($entry->status) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('accounts.journal.show', $entry) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="9" class="text-center text-muted">No entries in this period.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $entries->links() }}
        </div>
    </div>
</div>
@endsection
