<?php

namespace App\Services\Accounting;

use App\Models\Account;
use App\Models\AccountingSetting;
use App\Models\Invoice;
use App\Models\JournalEntry;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Every report is derived from journal_entry_accounts - nothing is stored twice.
 * Balances are signed in the account's normal direction: a positive number means the
 * account behaves as expected (asset/expense = debit, liability/equity/income = credit).
 */
class AccountingReports
{
    public function __construct(private ChartOfAccounts $chart)
    {
    }

    /** @return Collection<string, object{debit: float, credit: float}> keyed by account id */
    public function totals(int $clientId, ?string $from, string $to): Collection
    {
        $rows = DB::table('journal_entry_accounts as l')
            ->join('journal_entries as e', 'e.id', '=', 'l.journal_entry_id')
            ->where('e.client_id', $clientId)
            ->where('e.status', 'posted')
            ->whereDate('e.posting_date', '<=', $to)
            ->when($from, fn ($q) => $q->whereDate('e.posting_date', '>=', $from))
            ->groupBy('l.account_id')
            ->selectRaw('l.account_id, SUM(l.debit) as debit, SUM(l.credit) as credit')
            ->get();

        return $rows->keyBy('account_id')->map(fn ($row) => (object) [
            'debit' => (float) $row->debit,
            'credit' => (float) $row->credit,
        ]);
    }

    public function accounts(int $clientId): Collection
    {
        return Account::forClient($clientId)
            ->orderByRaw('coalesce(account_number, name) asc')
            ->orderBy('name')
            ->get();
    }

    /** Signed opening balance in the account's normal direction. */
    public function openingBalance(Account $account): float
    {
        return round((float) $account->opening_balance, 2);
    }

    public function signed(Account $account, float $debit, float $credit, float $opening = 0.0): float
    {
        $movement = $account->isDebitNormal() ? ($debit - $credit) : ($credit - $debit);

        return round($movement + $opening, 2);
    }

    // ------------------------------------------------------------------ trial balance

    public function trialBalance(int $clientId, string $from, string $to): array
    {
        $accounts = $this->accounts($clientId)->where('is_group', false);
        $period = $this->totals($clientId, $from, $to);
        $before = $this->totals($clientId, null, Carbon::parse($from)->subDay()->toDateString());

        $rows = [];
        $totals = ['opening_debit' => 0.0, 'opening_credit' => 0.0, 'debit' => 0.0, 'credit' => 0.0,
                   'closing_debit' => 0.0, 'closing_credit' => 0.0];

        foreach ($accounts as $account) {
            $periodRow = $period->get($account->id);
            $beforeRow = $before->get($account->id);

            $debit = (float) ($periodRow->debit ?? 0);
            $credit = (float) ($periodRow->credit ?? 0);
            $opening = $this->signed(
                $account,
                (float) ($beforeRow->debit ?? 0),
                (float) ($beforeRow->credit ?? 0),
                $this->openingBalance($account)
            );

            if (abs($opening) < 0.005 && abs($debit) < 0.005 && abs($credit) < 0.005) {
                continue;
            }

            $closing = $this->signed($account, $debit, $credit, $opening);

            $rows[] = [
                'account' => $account,
                'opening' => $opening,
                'debit' => round($debit, 2),
                'credit' => round($credit, 2),
                'closing' => $closing,
                'closing_debit' => $closing > 0 ? $closing : 0.0,
                'closing_credit' => $closing < 0 ? -$closing : 0.0,
            ];

            $totals['opening_debit'] += $opening > 0 ? $opening : 0;
            $totals['opening_credit'] += $opening < 0 ? -$opening : 0;
            $totals['debit'] += $debit;
            $totals['credit'] += $credit;
            $totals['closing_debit'] += $closing > 0 ? $closing : 0;
            $totals['closing_credit'] += $closing < 0 ? -$closing : 0;
        }

        return ['rows' => $rows, 'totals' => $totals];
    }

    // ------------------------------------------------------------------ profit and loss

    public function profitLoss(int $clientId, string $from, string $to): array
    {
        $accounts = $this->accounts($clientId)->where('is_group', false);
        $period = $this->totals($clientId, $from, $to);

        $income = [];
        $expenses = [];

        foreach ($accounts as $account) {
            if (! in_array($account->root_type, ['Income', 'Expense'], true)) {
                continue;
            }

            $row = $period->get($account->id);
            $debit = (float) ($row->debit ?? 0);
            $credit = (float) ($row->credit ?? 0);

            if (abs($debit) < 0.005 && abs($credit) < 0.005) {
                continue;
            }

            $amount = $account->root_type === 'Income' ? ($credit - $debit) : ($debit - $credit);
            $entry = ['account' => $account, 'amount' => round($amount, 2), 'debit' => $debit, 'credit' => $credit];

            if ($account->root_type === 'Income') {
                $income[] = $entry;
            } else {
                $expenses[] = $entry;
            }
        }

        $totalIncome = round(array_sum(array_column($income, 'amount')), 2);
        $totalExpense = round(array_sum(array_column($expenses, 'amount')), 2);

        return [
            'income' => $income,
            'expenses' => $expenses,
            'total_income' => $totalIncome,
            'total_expense' => $totalExpense,
            'net_profit' => round($totalIncome - $totalExpense, 2),
        ];
    }

    // ------------------------------------------------------------------ balance sheet

    public function fiscalYearStart(int $clientId, string $asOf): string
    {
        $setting = AccountingSetting::where('client_id', $clientId)->first();
        $month = (int) ($setting->fiscal_year_start_month ?? 1);
        $month = max(1, min(12, $month));

        $date = Carbon::parse($asOf);
        $start = $date->copy()->month($month)->startOfMonth();

        if ($start->greaterThan($date)) {
            $start->subYear();
        }

        return $start->toDateString();
    }

    public function balanceSheet(int $clientId, string $asOf): array
    {
        $accounts = $this->accounts($clientId)->where('is_group', false);
        $cumulative = $this->totals($clientId, null, $asOf);
        $fyStart = $this->fiscalYearStart($clientId, $asOf);

        $sections = ['Asset' => [], 'Liability' => [], 'Equity' => []];
        $sectionTotals = ['Asset' => 0.0, 'Liability' => 0.0, 'Equity' => 0.0];

        foreach ($accounts as $account) {
            if (! array_key_exists($account->root_type, $sections)) {
                continue;
            }

            $row = $cumulative->get($account->id);
            $balance = $this->signed(
                $account,
                (float) ($row->debit ?? 0),
                (float) ($row->credit ?? 0),
                $this->openingBalance($account)
            );

            if (abs($balance) < 0.005) {
                continue;
            }

            $sections[$account->root_type][] = ['account' => $account, 'balance' => $balance];
            $sectionTotals[$account->root_type] += $balance;
        }

        $periodResult = $this->profitLoss($clientId, $fyStart, $asOf);
        $profit = $periodResult['net_profit'];

        $sectionTotals['Equity'] = round($sectionTotals['Equity'] + $profit, 2);
        $liabilitiesAndEquity = round($sectionTotals['Liability'] + $sectionTotals['Equity'], 2);

        return [
            'sections' => $sections,
            'totals' => $sectionTotals,
            'profit' => $profit,
            'liabilities_and_equity' => $liabilitiesAndEquity,
            'difference' => round($sectionTotals['Asset'] - $liabilitiesAndEquity, 2),
            'fy_start' => $fyStart,
        ];
    }

    // ------------------------------------------------------------------ general ledger

    public function generalLedger(int $clientId, Account $account, string $from, string $to): array
    {
        $before = $this->totals($clientId, null, Carbon::parse($from)->subDay()->toDateString())->get($account->id);
        $opening = $this->signed(
            $account,
            (float) ($before->debit ?? 0),
            (float) ($before->credit ?? 0),
            $this->openingBalance($account)
        );

        $lines = DB::table('journal_entry_accounts as l')
            ->join('journal_entries as e', 'e.id', '=', 'l.journal_entry_id')
            ->where('e.client_id', $clientId)
            ->where('e.status', 'posted')
            ->where('l.account_id', $account->id)
            ->whereDate('e.posting_date', '>=', $from)
            ->whereDate('e.posting_date', '<=', $to)
            ->orderBy('e.posting_date')
            ->orderBy('e.id')
            ->select([
                'l.debit', 'l.credit', 'l.description', 'l.party_type', 'l.party_id',
                'e.id as entry_id', 'e.entry_no', 'e.posting_date', 'e.entry_type', 'e.reference_no', 'e.remarks',
            ])
            ->get();

        $running = $opening;
        $rows = [];

        foreach ($lines as $line) {
            $signed = $this->signed($account, (float) $line->debit, (float) $line->credit);
            $running = round($running + $signed, 2);
            $rows[] = [
                'date' => $line->posting_date,
                'entry_id' => $line->entry_id,
                'entry_no' => $line->entry_no,
                'entry_type' => $line->entry_type,
                'reference_no' => $line->reference_no,
                'description' => $line->description ?: $line->remarks,
                'debit' => (float) $line->debit,
                'credit' => (float) $line->credit,
                'balance' => $running,
            ];
        }

        return [
            'account' => $account,
            'opening' => $opening,
            'rows' => $rows,
            'closing' => $running,
            'total_debit' => round(array_sum(array_column($rows, 'debit')), 2),
            'total_credit' => round(array_sum(array_column($rows, 'credit')), 2),
        ];
    }

    // ------------------------------------------------------------------ receivables

    /**
     * Customer balances, straight from the invoices (the same numbers the invoice screen shows)
     * plus an ageing split. This is the "statement" the cashier works from.
     */
    public function receivables(int $clientId, ?string $asOf = null): array
    {
        $asOf = $asOf ?: now()->toDateString();
        $today = Carbon::parse($asOf);

        $customers = [];
        $totals = ['invoiced' => 0.0, 'received' => 0.0, 'balance' => 0.0,
                   'current' => 0.0, 'days_31_60' => 0.0, 'days_61_90' => 0.0, 'over_90' => 0.0];

        $invoices = Invoice::with(['customer', 'invoicePayments'])
            ->where('client_id', $clientId)
            ->orderBy('created_at')
            ->get();

        foreach ($invoices as $invoice) {
            $received = $invoice->invoicePayments
                ->filter(fn ($payment) => ! $payment->payment_date || $payment->payment_date->toDateString() <= $asOf)
                ->sum('amount_received');
            $invoiced = $invoice->amountDue();
            $balance = round($invoiced - $received, 2);

            if ($invoiced <= 0) {
                continue;
            }

            $name = optional($invoice->customer)->customer_firstname
                ?: (optional($invoice->customer)->customer_lastname ?: 'Customer #' . $invoice->customer_id);

            $key = (string) $invoice->customer_id;

            if (! isset($customers[$key])) {
                $customers[$key] = [
                    'customer_id' => $invoice->customer_id,
                    'name' => $name,
                    'invoices' => 0, 'invoiced' => 0.0, 'received' => 0.0, 'balance' => 0.0,
                    'current' => 0.0, 'days_31_60' => 0.0, 'days_61_90' => 0.0, 'over_90' => 0.0,
                ];
            }

            $customers[$key]['invoices']++;
            $customers[$key]['invoiced'] += $invoiced;
            $customers[$key]['received'] += $received;
            $customers[$key]['balance'] += $balance;

            if ($balance > 0) {
                $age = (int) Carbon::parse(optional($invoice->created_at)->toDateString() ?: $asOf)->diffInDays($today);
                $bucket = $age <= 30 ? 'current' : ($age <= 60 ? 'days_31_60' : ($age <= 90 ? 'days_61_90' : 'over_90'));
                $customers[$key][$bucket] += $balance;
                $totals[$bucket] += $balance;
            }

            $totals['invoiced'] += $invoiced;
            $totals['received'] += $received;
            $totals['balance'] += $balance;
        }

        usort($customers, fn ($a, $b) => $b['balance'] <=> $a['balance']);

        return ['customers' => $customers, 'totals' => $totals];
    }

    // ------------------------------------------------------------------ cash and bank

    public function cashAndBank(int $clientId, string $from, string $to): array
    {
        $accounts = $this->accounts($clientId)
            ->where('is_group', false)
            ->filter(fn (Account $account) => in_array($account->account_type, ['Cash', 'Bank'], true));

        $before = $this->totals($clientId, null, Carbon::parse($from)->subDay()->toDateString());
        $period = $this->totals($clientId, $from, $to);

        $rows = [];
        $totals = ['opening' => 0.0, 'in' => 0.0, 'out' => 0.0, 'closing' => 0.0];

        foreach ($accounts as $account) {
            $beforeRow = $before->get($account->id);
            $periodRow = $period->get($account->id);

            $opening = $this->signed($account, (float) ($beforeRow->debit ?? 0), (float) ($beforeRow->credit ?? 0), $this->openingBalance($account));
            $in = (float) ($periodRow->debit ?? 0);
            $out = (float) ($periodRow->credit ?? 0);
            $closing = round($opening + $in - $out, 2);

            $rows[] = [
                'account' => $account,
                'opening' => $opening,
                'in' => $in,
                'out' => $out,
                'closing' => $closing,
            ];

            $totals['opening'] += $opening;
            $totals['in'] += $in;
            $totals['out'] += $out;
            $totals['closing'] += $closing;
        }

        return ['rows' => $rows, 'totals' => $totals];
    }

    // ------------------------------------------------------------------ dashboard

    public function dashboard(int $clientId): array
    {
        $today = now()->toDateString();
        $monthStart = now()->startOfMonth()->toDateString();
        $yearStart = $this->fiscalYearStart($clientId, $today);

        $cash = $this->cashAndBank($clientId, $monthStart, $today);
        $receivable = $this->receivables($clientId, $today);
        $month = $this->profitLoss($clientId, $monthStart, $today);
        $year = $this->profitLoss($clientId, $yearStart, $today);

        return [
            'cash_bank' => $cash['totals']['closing'],
            'cash_in' => $cash['totals']['in'],
            'cash_out' => $cash['totals']['out'],
            'receivable' => (float) $receivable['totals']['balance'],
            'overdue' => (float) ($receivable['totals']['days_31_60'] + $receivable['totals']['days_61_90'] + $receivable['totals']['over_90']),
            'month_income' => $month['total_income'],
            'month_expense' => $month['total_expense'],
            'month_profit' => $month['net_profit'],
            'year_profit' => $year['net_profit'],
            'entries' => JournalEntry::forClient($clientId)->with('lines')->orderByDesc('posting_date')->orderByDesc('id')->limit(8)->get(),
            'accounts_count' => Account::forClient($clientId)->count(),
            'settings_ready' => (bool) AccountingSetting::where('client_id', $clientId)->whereNotNull('receivable_account_id')->exists(),
        ];
    }
}
