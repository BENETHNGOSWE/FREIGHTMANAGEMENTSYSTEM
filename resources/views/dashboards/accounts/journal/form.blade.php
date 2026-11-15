@extends('layouts._master')

@section('content')
<div class="container-fluid">
    <div class="form-head align-items-center d-flex mb-sm-4 mb-3">
        <div class="me-auto">
            <h2 class="text-black font-w600">{{ $entry ? 'Correct Entry ' . $entry->entry_no : 'New Journal Entry' }}</h2>
            <p class="mb-1">
                Dashboard / <a class="text-primary" href="{{ route('accounts.journal.index') }}">Journal Entries</a> /
                {{ $entry ? 'Correct' : 'Create' }}
            </p>
        </div>
        <a href="{{ route('accounts.journal.index') }}" class="btn btn-outline-primary">Back</a>
    </div>

    @if ($entry)
        <div class="alert alert-warning">
            Saving posts a corrected entry and cancels {{ $entry?->entry_no }}. Nothing is deleted.
        </div>
    @endif

    <form method="POST" action="{{ $entry ? route('accounts.journal.update', $entry) : route('accounts.journal.store') }}">
        @csrf
        @if ($entry) @method('PUT') @endif

        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Posting date</label>
                        <input type="date" name="posting_date" class="form-control" required
                               value="{{ old('posting_date', $entry?->posting_date?->toDateString() ?? now()->toDateString()) }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Entry type</label>
                        <select name="entry_type" class="form-control">
                            @foreach (['Journal Entry', 'Opening Entry', 'Adjustment', 'Transfer'] as $type)
                                <option value="{{ $type }}" @selected(old('entry_type', $entry?->entry_type ?? 'Journal Entry') === $type)>{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Reference (optional)</label>
                        <input type="text" name="reference_no" class="form-control" value="{{ old('reference_no', $entry?->reference_no ?? '') }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Remarks</label>
                        <input type="text" name="remarks" class="form-control" value="{{ old('remarks', $entry?->remarks ?? '') }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Lines</h4>
                <button type="button" class="btn btn-sm btn-outline-primary" id="add-line">
                    <i class="fas fa-plus me-1"></i> Add line
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table" id="lines-table">
                        <thead>
                            <tr>
                                <th style="width:38%">Account</th>
                                <th style="width:18%">Debit</th>
                                <th style="width:18%">Credit</th>
                                <th>Description</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="lines-body">
                            @php
                                $oldLines = old('lines', $entry ? $entry->lines->map(fn ($l) => [
                                    'account_id' => $l->account_id,
                                    'debit' => (float) $l->debit,
                                    'credit' => (float) $l->credit,
                                    'description' => $l->description,
                                ])->all() : [null, null]);
                            @endphp
                            @foreach ($oldLines as $index => $line)
                                <tr>
                                    <td>
                                        <select name="lines[{{ $index }}][account_id]" class="form-control">
                                            <option value="">— choose account —</option>
                                            @foreach ($accounts as $account)
                                                <option value="{{ $account->id }}"
                                                    @selected((int) ($line['account_id'] ?? 0) === $account->id)>
                                                    {{ $account->account_number }} — {{ $account->name }} ({{ $account->root_type }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td><input type="number" step="0.01" min="0" class="form-control line-debit"
                                               name="lines[{{ $index }}][debit]" value="{{ $line['debit'] ?? '' }}"></td>
                                    <td><input type="number" step="0.01" min="0" class="form-control line-credit"
                                               name="lines[{{ $index }}][credit]" value="{{ $line['credit'] ?? '' }}"></td>
                                    <td><input type="text" class="form-control" name="lines[{{ $index }}][description]"
                                               value="{{ $line['description'] ?? '' }}"></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-danger remove-line">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Total</th>
                                <th id="total-debit">0.00</th>
                                <th id="total-credit">0.00</th>
                                <th colspan="2">
                                    <span id="balance-note" class="fw-bold"></span>
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <button class="btn btn-primary" id="submit-entry">Post entry</button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const body = document.getElementById('lines-body');
    const templateOptions = @json($accounts->map(fn ($a) => [
        'id' => $a->id,
        'label' => trim(($a->account_number ? $a->account_number . ' — ' : '') . $a->name . ' (' . $a->root_type . ')'),
    ]));

    let index = body.querySelectorAll('tr').length;

    function options(selected) {
        let html = '<option value="">— choose account —</option>';
        templateOptions.forEach(function (account) {
            html += '<option value="' + account.id + '"' + (Number(selected) === account.id ? ' selected' : '') + '>' + account.label + '</option>';
        });
        return html;
    }

    function addRow(selected, debit, credit, description) {
        const row = document.createElement('tr');
        row.innerHTML =
            '<td><select name="lines[' + index + '][account_id]" class="form-control">' + options(selected) + '</select></td>' +
            '<td><input type="number" step="0.01" min="0" class="form-control line-debit" name="lines[' + index + '][debit]" value="' + (debit || '') + '"></td>' +
            '<td><input type="number" step="0.01" min="0" class="form-control line-credit" name="lines[' + index + '][credit]" value="' + (credit || '') + '"></td>' +
            '<td><input type="text" class="form-control" name="lines[' + index + '][description]" value="' + (description || '') + '"></td>' +
            '<td><button type="button" class="btn btn-sm btn-outline-danger remove-line"><i class="fas fa-times"></i></button></td>';
        body.appendChild(row);
        index++;
    }

    function recalc() {
        let debit = 0, credit = 0;
        body.querySelectorAll('.line-debit').forEach(function (input) { debit += parseFloat(input.value) || 0; });
        body.querySelectorAll('.line-credit').forEach(function (input) { credit += parseFloat(input.value) || 0; });

        document.getElementById('total-debit').textContent = debit.toFixed(2);
        document.getElementById('total-credit').textContent = credit.toFixed(2);

        const note = document.getElementById('balance-note');
        const difference = Math.round((debit - credit) * 100) / 100;

        if (difference === 0 && debit > 0) {
            note.textContent = 'Balanced ✓';
            note.className = 'fw-bold text-success';
        } else {
            note.textContent = 'Difference: ' + difference.toFixed(2);
            note.className = 'fw-bold text-danger';
        }
    }

    document.getElementById('add-line').addEventListener('click', function () {
        addRow();
        recalc();
    });

    body.addEventListener('click', function (event) {
        const button = event.target.closest('.remove-line');
        if (!button) return;
        if (body.querySelectorAll('tr').length <= 2) {
            alert('A journal entry needs at least two lines.');
            return;
        }
        button.closest('tr').remove();
        recalc();
    });

    body.addEventListener('input', recalc);
    recalc();
})();
</script>
@endpush
