@extends('layouts._master')

@section('content')
<div class="container-fluid">
    <div class="form-head align-items-center d-flex mb-sm-4 mb-3">
        <div class="me-auto">
            <h2 class="text-black font-w600">{{ $entry->entry_no }}</h2>
            <p class="mb-1">
                Dashboard / <a class="text-primary" href="{{ route('accounts.journal.index') }}">Journal Entries</a> /
                {{ $entry->entry_no }}
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('accounts.journal.index') }}" class="btn btn-outline-primary">Back</a>
            @if ($entry->isEditable())
                <a href="{{ route('accounts.journal.edit', $entry) }}" class="btn btn-outline-success">Correct this entry</a>
            @endif
            @if ($entry->status === 'posted')
                <form method="POST" action="{{ route('accounts.journal.destroy', $entry) }}"
                      onsubmit="return confirm('Cancel {{ $entry->entry_no }}? The entry stays in the audit trail.')">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-outline-danger">Cancel entry</button>
                </form>
            @endif
        </div>
    </div>

    @if ($entry->status !== 'posted')
        <div class="alert alert-secondary">This entry is {{ $entry->status }} and no longer affects the reports.</div>
    @endif

    <div class="row">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <table class="table table-borderless table-sm mb-0">
                        <tr><th>Date</th><td>{{ $entry->posting_date?->format('d M Y') }}</td></tr>
                        <tr><th>Type</th><td>{{ $entry->entry_type }}</td></tr>
                        <tr><th>Source</th><td>{{ $entry->sourceLabel() }}</td></tr>
                        <tr><th>Status</th>
                            <td><span class="badge {{ $entry->status === 'posted' ? 'bg-success' : 'bg-secondary' }}">{{ ucfirst($entry->status) }}</span></td>
                        </tr>
                        <tr><th>Posted by</th><td>{{ optional($entry->creator)->name ?? 'System' }}</td></tr>
                        <tr><th>Posted at</th><td>{{ optional($entry->posted_at)->format('d M Y H:i') ?? '-' }}</td></tr>
                        <tr><th>Remarks</th><td>{{ $entry->remarks ?: '-' }}</td></tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card">
                <div class="card-header"><h4 class="card-title mb-0">Lines</h4></div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-borderless">
                            <thead>
                                <tr>
                                    <th>Account</th>
                                    <th>Description</th>
                                    <th class="text-end">Debit</th>
                                    <th class="text-end">Credit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($entry->lines as $line)
                                    <tr>
                                        <td>
                                            {{ $line->account->account_number }} — {{ $line->account->name }}
                                            <small class="text-muted d-block">{{ $line->account->root_type }}</small>
                                        </td>
                                        <td>{{ $line->description }}</td>
                                        <td class="text-end">{{ (float) $line->debit ? number_format((float) $line->debit, 2) : '-' }}</td>
                                        <td class="text-end">{{ (float) $line->credit ? number_format((float) $line->credit, 2) : '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="fw-bold">
                                    <th colspan="2">Total</th>
                                    <th class="text-end">{{ number_format((float) $entry->total_debit, 2) }}</th>
                                    <th class="text-end">{{ number_format((float) $entry->total_credit, 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
