<?php

namespace App\Http\Controllers\Accounting;

use App\Models\Account;
use App\Models\JournalEntry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class JournalEntryController extends AccountingController
{
    public function index(Request $request): View
    {
        $this->allow();

        $clientId = $this->clientId();
        $from = $request->get('from') ?: now()->startOfMonth()->toDateString();
        $to = $request->get('to') ?: now()->toDateString();
        $type = $request->get('entry_type');
        $search = trim((string) $request->get('search', ''));

        $entries = JournalEntry::forClient($clientId)
            ->withCount('lines')
            ->when($request->get('status'), fn ($q, $status) => $q->where('status', $status))
            ->when($type, fn ($q) => $q->where('entry_type', $type))
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($inner) use ($search) {
                    $inner->where('entry_no', 'like', "%{$search}%")
                        ->orWhere('reference_no', 'like', "%{$search}%")
                        ->orWhere('remarks', 'like', "%{$search}%");
                });
            })
            ->when($request->boolean('all_dates') === false, function ($q) use ($from, $to) {
                $q->whereDate('posting_date', '>=', $from)->whereDate('posting_date', '<=', $to);
            })
            ->orderByDesc('posting_date')
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        return view('dashboards.accounts.journal.index', [
            'entries' => $entries,
            'from' => $from,
            'to' => $to,
            'type' => $type,
            'search' => $search,
            'types' => JournalEntry::forClient($clientId)->distinct()->orderBy('entry_type')->pluck('entry_type'),
        ]);
    }

    public function create(): View
    {
        $this->allow('create journal_entries');
        $clientId = $this->clientId();
        $this->chart()->ensureForClient($clientId);

        return view('dashboards.accounts.journal.form', [
            'accounts' => $this->postableAccounts(),
            'entry' => null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->allow('create journal_entries');

        $clientId = $this->clientId();
        $payload = $this->validated($request);

        $lines = $this->linesFrom($payload['lines']);

        if (count($lines) < 2) {
            return back()->withInput()->with('error', 'Add at least two lines (one debit and one credit).');
        }

        try {
            $entry = $this->ledger()->post($clientId, [
                'posting_date' => $payload['posting_date'],
                'entry_type' => $payload['entry_type'] ?: 'Journal Entry',
                'remarks' => $payload['remarks'] ?? null,
                'reference_no' => $payload['reference_no'] ?? null,
                'is_opening' => ($payload['entry_type'] ?? '') === 'Opening Entry',
                'lines' => $lines,
            ]);
        } catch (RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('accounts.journal.show', $entry)
            ->with('success', 'Journal entry ' . $entry->entry_no . ' posted.');
    }

    public function show(JournalEntry $entry): View
    {
        $this->allow();
        abort_unless((int) $entry->client_id === $this->clientId(), 404);

        return view('dashboards.accounts.journal.show', [
            'entry' => $entry->load('lines.account', 'creator'),
        ]);
    }

    public function edit(JournalEntry $entry): View
    {
        $this->allow('update journal_entries');
        abort_unless((int) $entry->client_id === $this->clientId(), 404);

        if ($entry->is_auto || $entry->status !== 'posted') {
            return redirect()
                ->route('accounts.journal.show', $entry)
                ->with('error', 'Entries created automatically by an invoice or payment cannot be edited here. Correct the document instead.');
        }

        return view('dashboards.accounts.journal.form', [
            'accounts' => $this->postableAccounts(),
            'entry' => $entry->load('lines'),
        ]);
    }

    public function update(Request $request, JournalEntry $entry): RedirectResponse
    {
        $this->allow('update journal_entries');
        abort_unless((int) $entry->client_id === $this->clientId(), 404);

        if ($entry->is_auto || $entry->status !== 'posted') {
            return back()->with('error', 'This entry cannot be edited.');
        }

        $payload = $this->validated($request);
        $lines = $this->linesFrom($payload['lines']);

        if (count($lines) < 2) {
            return back()->withInput()->with('error', 'Add at least two lines (one debit and one credit).');
        }

        try {
            // Keep the audit trail: the old entry is voided, a corrected one is posted.
            $this->ledger()->cancel($entry, 'Replaced by a corrected entry');
            $replacement = $this->ledger()->post($this->clientId(), [
                'posting_date' => $payload['posting_date'],
                'entry_type' => $payload['entry_type'] ?: 'Journal Entry',
                'remarks' => $payload['remarks'] ?? null,
                'reference_no' => $payload['reference_no'] ?? null,
                'is_opening' => ($payload['entry_type'] ?? '') === 'Opening Entry',
                'lines' => $lines,
            ]);
        } catch (RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('accounts.journal.show', $replacement)
            ->with('success', 'Entry corrected: ' . $replacement->entry_no . ' replaces ' . $entry->entry_no . '.');
    }

    /** Void an entry (kept on file, never counted again). */
    public function destroy(Request $request, JournalEntry $entry): RedirectResponse
    {
        $this->allow('delete journal_entries');
        abort_unless((int) $entry->client_id === $this->clientId(), 404);

        $this->ledger()->cancel($entry, $request->get('reason') ?: 'Voided by ' . optional(auth()->user())->name);

        return redirect()
            ->route('accounts.journal.index')
            ->with('success', 'Entry ' . $entry->entry_no . ' cancelled. The record stays for the audit trail.');
    }

    private function postableAccounts()
    {
        return Account::forClient($this->clientId())
            ->active()
            ->leaves()
            ->orderByRaw('coalesce(account_number, name) asc')
            ->get();
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'posting_date' => ['required', 'date'],
            'entry_type' => ['nullable', 'string', 'max:40'],
            'reference_no' => ['nullable', 'string', 'max:120'],
            'remarks' => ['nullable', 'string', 'max:1000'],
            'lines' => ['required', 'array', 'min:2'],
            'lines.*.account_id' => ['nullable', 'integer'],
            'lines.*.debit' => ['nullable', 'numeric', 'min:0'],
            'lines.*.credit' => ['nullable', 'numeric', 'min:0'],
            'lines.*.description' => ['nullable', 'string', 'max:250'],
            'lines.*.party_type' => ['nullable', 'string', 'max:40'],
            'lines.*.party_id' => ['nullable', 'integer'],
        ]);
    }

    private function linesFrom(array $rows): array
    {
        $lines = [];

        foreach ($rows as $row) {
            $debit = round((float) ($row['debit'] ?? 0), 2);
            $credit = round((float) ($row['credit'] ?? 0), 2);

            if (empty($row['account_id']) || ($debit == 0.0 && $credit == 0.0)) {
                continue;
            }

            $lines[] = [
                'account_id' => (int) $row['account_id'],
                'debit' => $debit,
                'credit' => $credit,
                'description' => $row['description'] ?? null,
                'party_type' => $row['party_type'] ?? null,
                'party_id' => $row['party_id'] ?? null,
            ];
        }

        return $lines;
    }
}
