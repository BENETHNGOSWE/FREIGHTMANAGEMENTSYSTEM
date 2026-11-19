<?php

namespace App\Services\Accounting;

use App\Models\Account;
use App\Models\AccountingSetting;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\JournalEntry;
use App\Models\OfficeExpense;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * The double-entry engine.
 *
 * Everything that touches money goes through `post()`: invoices, payments, expenses and the
 * manual journal voucher screen. One rule is enforced on every write - total debit must equal
 * total credit - which is what makes the trial balance, P&L and balance sheet trustworthy.
 */
class LedgerService
{
    public function __construct(private ChartOfAccounts $chart)
    {
    }

    public function settings(int $clientId): AccountingSetting
    {
        return $this->chart->ensureForClient($clientId);
    }

    /**
     * Post a balanced entry.
     *
     * @param  array{posting_date?:string, entry_type?:string, reference_doctype?:string,
     *               reference_id?:int, reference_no?:string, remarks?:string, is_opening?:bool,
     *               is_auto?:bool, lines:array<int, array>}  $data
     */
    public function post(int $clientId, array $data): JournalEntry
    {
        $lines = $this->normaliseLines($clientId, $data['lines'] ?? []);

        $postingDate = $data['posting_date'] ?? now()->toDateString();
        $totalDebit = round(array_sum(array_column($lines, 'debit')), 2);
        $totalCredit = round(array_sum(array_column($lines, 'credit')), 2);

        if (abs($totalDebit - $totalCredit) > 0.005) {
            throw new RuntimeException(sprintf(
                'This entry does not balance: debit %s vs credit %s.',
                number_format($totalDebit, 2),
                number_format($totalCredit, 2)
            ));
        }

        return DB::transaction(function () use ($clientId, $data, $lines, $postingDate, $totalDebit, $totalCredit) {
            $entry = JournalEntry::create([
                'client_id' => $clientId,
                'entry_no' => $this->nextEntryNo($clientId, $postingDate),
                'posting_date' => $postingDate,
                'entry_type' => $data['entry_type'] ?? 'Journal Entry',
                'reference_doctype' => $data['reference_doctype'] ?? null,
                'reference_id' => $data['reference_id'] ?? null,
                'reference_no' => $data['reference_no'] ?? null,
                'status' => 'posted',
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
                'remarks' => $data['remarks'] ?? null,
                'is_opening' => (bool) ($data['is_opening'] ?? false),
                'is_auto' => (bool) ($data['is_auto'] ?? false),
                'posted_at' => now(),
            ]);

            $entry->lines()->createMany($lines);

            return $entry->fresh('lines.account');
        });
    }

    /** Cancel an entry. Cancelled entries stay for the audit trail but never count. */
    public function cancel(JournalEntry $entry, string $reason = ''): JournalEntry
    {
        if ($entry->status === 'cancelled') {
            return $entry;
        }

        $entry->update([
            'status' => 'cancelled',
            'remarks' => trim(($entry->remarks ? $entry->remarks . ' | ' : '') . 'Cancelled: ' . ($reason ?: 'no reason given')),
        ]);

        return $entry;
    }

    // ------------------------------------------------------------------ automatic posting

    /** Sales Invoice -> Dr Accounts Receivable, Cr Freight Income (+ transport income). */
    public function postInvoice(Invoice $invoice): ?JournalEntry
    {
        $clientId = (int) $invoice->client_id;
        $setting = $this->settings($clientId);

        if (! $setting->auto_post_invoices) {
            return null;
        }

        $freight = round((float) $invoice->total_amount, 2);
        $transport = round((float) $invoice->total_transportfee, 2);
        $total = round($freight + $transport, 2);

        if ($total <= 0) {
            $this->cancelReference($clientId, 'Invoice', (int) $invoice->id, 'Invoice total is zero');

            return null;
        }

        $lines = [
            $this->line($this->accountId($setting->receivable_account_id, $clientId, '1200'), $total, 0,
                'Invoice ' . $invoice->invoice_no, 'Customer', $invoice->customer_id),
            $this->line($this->accountId($setting->income_account_id, $clientId, '4100'), 0, $freight,
                'Freight income ' . $invoice->invoice_no),
        ];

        if ($transport > 0) {
            $lines[] = $this->line($this->accountId($setting->transport_income_account_id, $clientId, '4200'), 0, $transport,
                'Transport fee ' . $invoice->invoice_no);
        }

        return $this->upsertReference($clientId, 'Invoice', (int) $invoice->id, $invoice->invoice_no,
            optional($invoice->created_at)->toDateString() ?: now()->toDateString(),
            $lines, 'Sales Invoice', 'Invoice ' . $invoice->invoice_no . ' - ' . $invoice->customer?->customer_firstname);
    }

    /** Payment received -> Dr Cash / Bank / Mobile money, Cr Accounts Receivable. */
    public function postPayment(InvoicePayment $payment): ?JournalEntry
    {
        $invoice = $payment->invoice;
        $clientId = (int) ($payment->client_id ?: optional($invoice)->client_id);

        if (! $invoice || ! $clientId) {
            return null;
        }

        $setting = $this->settings($clientId);

        if (! $setting->auto_post_payments) {
            return null;
        }

        $amount = round((float) $payment->amount_received, 2);

        if ($amount <= 0) {
            $this->cancelReference($clientId, 'InvoicePayment', (int) $payment->id, 'Payment amount is zero');

            return null;
        }

        $lines = [
            $this->line($this->paymentAccountId($setting, (string) $payment->payment_method), $amount, 0,
                'Received on ' . $invoice->invoice_no . ' (' . $payment->payment_method . ')'),
            $this->line($this->accountId($setting->receivable_account_id, $clientId, '1200'), 0, $amount,
                'Payment for ' . $invoice->invoice_no, 'Customer', $invoice->customer_id),
        ];

        return $this->upsertReference($clientId, 'InvoicePayment', (int) $payment->id,
            $payment->reference_no ?: 'PAY-' . $payment->id,
            optional($payment->payment_date)->toDateString() ?: now()->toDateString(),
            $lines, 'Payment Entry', 'Payment ' . number_format($amount, 2) . ' on ' . $invoice->invoice_no);
    }

    /** Office expense -> Dr expense account, Cr Cash in Hand. */
    public function postExpense(OfficeExpense $expense): ?JournalEntry
    {
        $clientId = (int) $expense->client_id;
        $setting = $this->settings($clientId);

        if (! $setting->auto_post_expenses) {
            return null;
        }

        $amount = round((float) $expense->expenses_cost, 2);

        if ($amount <= 0) {
            $this->cancelReference($clientId, 'OfficeExpense', (int) $expense->id, 'Expense amount is zero');

            return null;
        }

        $expenseAccountId = $this->expenseAccountFor($clientId, $expense, $setting);

        $lines = [
            $this->line($expenseAccountId, $amount, 0, $expense->expenses_description),
            $this->line($this->accountId($setting->cash_account_id, $clientId, '1110'), 0, $amount,
                'Paid: ' . $expense->expenses_description),
        ];

        return $this->upsertReference($clientId, 'OfficeExpense', (int) $expense->id, 'EXP-' . $expense->id,
            optional($expense->created_at)->toDateString() ?: now()->toDateString(),
            $lines, 'Expense Entry', 'Expense: ' . $expense->expenses_description);
    }

    // ------------------------------------------------------------------ storage

    /** Daily storage charges -> Dr Accounts Receivable, Cr Storage Income (one entry per day). */
    public function postStorageAccrual(int $clientId, string $date, float $amount): ?JournalEntry
    {
        $setting = $this->settings($clientId);

        if (! $setting->auto_post_invoices || $amount <= 0) {
            return null;
        }

        $lines = [
            $this->line($this->accountId($setting->receivable_account_id, $clientId, '1200'), $amount, 0,
                'Storage charges ' . $date),
            $this->line($this->accountId($setting->storage_income_account_id ?: $setting->transport_income_account_id, $clientId, '4400'), 0, $amount,
                'Storage income ' . $date),
        ];

        return $this->upsertReference($clientId, 'StorageAccrual', (int) str_replace('-', '', $date), $date,
            $date, $lines, 'Storage Accrual', 'Daily storage charges ' . $date);
    }

    /** Storage money received -> Dr Cash / Bank / Mobile money, Cr Accounts Receivable. */
    public function postStoragePayment(\App\Models\StoragePayment $payment): ?JournalEntry
    {
        $clientId = (int) $payment->client_id;
        $setting = $this->settings($clientId);
        $amount = round((float) $payment->amount, 2);

        if (! $setting->auto_post_payments || $amount <= 0) {
            return null;
        }

        $lines = [
            $this->line($this->paymentAccountId($setting, (string) $payment->payment_method), $amount, 0,
                'Storage payment ' . ($payment->reference_no ?: '#' . $payment->id)),
            $this->line($this->accountId($setting->receivable_account_id, $clientId, '1200'), 0, $amount,
                'Storage payment cleared'),
        ];

        return $this->upsertReference($clientId, 'StoragePayment', (int) $payment->id,
            $payment->reference_no ?: 'STP-' . $payment->id,
            optional($payment->payment_date)->toDateString() ?: now()->toDateString(),
            $lines, 'Storage Payment', 'Storage payment ' . number_format($amount, 2));
    }

    /** Waived storage -> Dr Storage Income, Cr Accounts Receivable (revenue reversed, debt removed). */
    public function postStorageWaiver(\App\Models\StorageWaiver $waiver): ?JournalEntry
    {
        $clientId = (int) $waiver->client_id;
        $setting = $this->settings($clientId);
        $amount = round((float) $waiver->amount, 2);

        if ($amount <= 0) {
            return null;
        }

        $lines = [
            $this->line($this->accountId($setting->storage_income_account_id ?: $setting->transport_income_account_id, $clientId, '4400'), $amount, 0,
                'Storage waived: ' . ($waiver->reason ?: 'no reason')),
            $this->line($this->accountId($setting->receivable_account_id, $clientId, '1200'), 0, $amount,
                'Storage waived'),
        ];

        return $this->upsertReference($clientId, 'StorageWaiver', (int) $waiver->id, 'STW-' . $waiver->id,
            now()->toDateString(), $lines, 'Storage Waiver', 'Storage waived ' . number_format($amount, 2));
    }

    /** Used when a record is deleted or becomes worthless. */
    public function cancelReference(int $clientId, string $doctype, int $id, string $reason = ''): void
    {
        $entries = JournalEntry::forClient($clientId)
            ->where('reference_doctype', $doctype)
            ->where('reference_id', $id)
            ->where('status', '!=', 'cancelled')
            ->get();

        foreach ($entries as $entry) {
            $this->cancel($entry, $reason);
        }
    }

    /** Post every invoice, payment and expense that has no entry yet. Safe to re-run. */
    public function backfill(int $clientId, bool $includeExpenses = true): array
    {
        $counts = ['invoices' => 0, 'payments' => 0, 'expenses' => 0, 'skipped' => 0, 'errors' => []];

        Invoice::where('client_id', $clientId)->orderBy('id')->chunk(100, function ($invoices) use (&$counts) {
            foreach ($invoices as $invoice) {
                try {
                    $this->postInvoice($invoice) ? $counts['invoices']++ : $counts['skipped']++;
                } catch (\Throwable $e) {
                    $counts['errors'][] = 'Invoice ' . $invoice->invoice_no . ': ' . $e->getMessage();
                }
            }
        });

        InvoicePayment::where('client_id', $clientId)->orderBy('id')->chunk(100, function ($payments) use (&$counts) {
            foreach ($payments as $payment) {
                try {
                    $this->postPayment($payment) ? $counts['payments']++ : $counts['skipped']++;
                } catch (\Throwable $e) {
                    $counts['errors'][] = 'Payment #' . $payment->id . ': ' . $e->getMessage();
                }
            }
        });

        if ($includeExpenses) {
            OfficeExpense::where('client_id', $clientId)->orderBy('id')->chunk(100, function ($expenses) use (&$counts) {
                foreach ($expenses as $expense) {
                    try {
                        $this->postExpense($expense) ? $counts['expenses']++ : $counts['skipped']++;
                    } catch (\Throwable $e) {
                        $counts['errors'][] = 'Expense #' . $expense->id . ': ' . $e->getMessage();
                    }
                }
            });
        }

        return $counts;
    }

    // ------------------------------------------------------------------ helpers

    /** Create or refresh the single entry that belongs to an invoice / payment / expense. */
    private function upsertReference(
        int $clientId,
        string $doctype,
        int $id,
        ?string $referenceNo,
        string $postingDate,
        array $lines,
        string $entryType,
        string $remarks
    ): JournalEntry {
        $existing = JournalEntry::forClient($clientId)
            ->where('reference_doctype', $doctype)
            ->where('reference_id', $id)
            ->where('status', '!=', 'cancelled')
            ->first();

        if (! $existing) {
            return $this->post($clientId, [
                'posting_date' => $postingDate,
                'entry_type' => $entryType,
                'reference_doctype' => $doctype,
                'reference_id' => $id,
                'reference_no' => $referenceNo,
                'remarks' => $remarks,
                'is_auto' => true,
                'lines' => $lines,
            ]);
        }

        // Re-price in place: the entry keeps its number, the amounts follow the document.
        DB::transaction(function () use ($existing, $lines, $postingDate, $referenceNo, $remarks) {
            $existing->lines()->delete();
            $existing->lines()->createMany($lines);
            $existing->update([
                'posting_date' => $postingDate,
                'reference_no' => $referenceNo,
                'remarks' => $remarks,
                'total_debit' => round(array_sum(array_column($lines, 'debit')), 2),
                'total_credit' => round(array_sum(array_column($lines, 'credit')), 2),
                'status' => 'posted',
                'posted_at' => now(),
            ]);
        });

        return $existing->fresh('lines.account');
    }

    private function normaliseLines(int $clientId, array $lines): array
    {
        $prepared = [];

        foreach ($lines as $line) {
            $debit = round((float) ($line['debit'] ?? 0), 2);
            $credit = round((float) ($line['credit'] ?? 0), 2);

            if ($debit == 0.0 && $credit == 0.0) {
                continue;
            }

            if ($debit != 0.0 && $credit != 0.0) {
                throw new RuntimeException('A single line cannot have both a debit and a credit.');
            }

            $accountId = (int) ($line['account_id'] ?? 0);
            $account = Account::forClient($clientId)->find($accountId);

            if (! $account) {
                throw new RuntimeException('Choose an account for every line.');
            }

            if (! $account->isPostable()) {
                throw new RuntimeException(sprintf('"%s" is a group account; post to a leaf account instead.', $account->name));
            }

            $prepared[] = [
                'account_id' => $account->id,
                'party_type' => $line['party_type'] ?? null,
                'party_id' => $line['party_id'] ?? null,
                'debit' => $debit,
                'credit' => $credit,
                'description' => isset($line['description']) ? mb_substr((string) $line['description'], 0, 250) : null,
            ];
        }

        if (count($prepared) < 2) {
            throw new RuntimeException('An entry needs at least two lines.');
        }

        return $prepared;
    }

    private function line(?int $accountId, float $debit, float $credit, ?string $description = null, ?string $partyType = null, $partyId = null): array
    {
        if (! $accountId) {
            throw new RuntimeException('This client has no default account configured for that line. Open Accounts -> Settings and pick one.');
        }

        return [
            'account_id' => $accountId,
            'debit' => round($debit, 2),
            'credit' => round($credit, 2),
            'description' => $description,
            'party_type' => $partyType,
            'party_id' => $partyId,
        ];
    }

    private function accountId(?int $configured, int $clientId, string $fallbackNumber): ?int
    {
        if ($configured) {
            return (int) $configured;
        }

        return $this->chart->byNumber($clientId, $fallbackNumber)?->id;
    }

    private function paymentAccountId(AccountingSetting $setting, string $method): ?int
    {
        $method = strtoupper($method);

        return match ($method) {
            'CASH' => $this->accountId($setting->cash_account_id, (int) $setting->client_id, '1110'),
            'MOBILE_MONEY' => $this->accountId(
                $setting->mobile_money_account_id ?: $setting->bank_account_id,
                (int) $setting->client_id,
                '1123'
            ),
            'BANK', 'CHEQUE' => $this->accountId($setting->bank_account_id, (int) $setting->client_id, '1121'),
            default => $this->accountId(
                $setting->bank_account_id ?: $setting->cash_account_id,
                (int) $setting->client_id,
                '1121'
            ),
        };
    }

    private function expenseAccountFor(int $clientId, OfficeExpense $expense, AccountingSetting $setting): ?int
    {
        $categoryName = trim((string) optional($expense->categoryexpense)->name);

        if ($categoryName !== '') {
            $match = Account::forClient($clientId)
                ->leaves()
                ->ofRoot('Expense')
                ->where(function ($query) use ($categoryName) {
                    $query->where('name', $categoryName)
                        ->orWhere('name', 'like', '%' . $categoryName . '%');
                })
                ->orderByRaw('length(name) asc')
                ->first();

            if ($match) {
                return $match->id;
            }
        }

        return $this->accountId($setting->expense_account_id, $clientId, '5900');
    }

    public function nextEntryNo(int $clientId, string $postingDate): string
    {
        $year = substr($postingDate, 0, 4) ?: now()->format('Y');
        $prefix = 'JE-' . $year . '-';

        $last = JournalEntry::forClient($clientId)
            ->where('entry_no', 'like', $prefix . '%')
            ->orderByDesc('entry_no')
            ->value('entry_no');

        $sequence = 1;

        if ($last) {
            $sequence = ((int) substr($last, strlen($prefix))) + 1;
        }

        return $prefix . str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
    }
}
