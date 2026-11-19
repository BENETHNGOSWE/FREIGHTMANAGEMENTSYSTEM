<?php

namespace App\Http\Controllers\Accounting;

use App\Models\Account;
use App\Models\Customer;
use App\Models\Invoice;
use App\Services\Accounting\AccountingReports;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LedgerController extends AccountingController
{
    /** Account ledger: pick an account and a period, see every movement with a running balance. */
    public function index(Request $request, AccountingReports $reports): View
    {
        $this->allow();

        $clientId = $this->clientId();
        $from = $request->get('from') ?: now()->startOfMonth()->toDateString();
        $to = $request->get('to') ?: now()->toDateString();

        $accounts = Account::forClient($clientId)
            ->leaves()
            ->orderByRaw('coalesce(account_number, name) asc')
            ->get();

        $selected = null;
        $ledger = null;

        if ($request->filled('account_id')) {
            $selected = Account::forClient($clientId)->findOrFail($request->integer('account_id'));
            $ledger = $reports->generalLedger($clientId, $selected, $from, $to);
        }

        // Balances as of the end date, so the picker is useful without opening each account.
        $totals = $reports->totals($clientId, null, $to);
        $balances = $accounts->mapWithKeys(function (Account $account) use ($totals, $reports) {
            $row = $totals->get($account->id);

            return [$account->id => $reports->signed(
                $account,
                (float) ($row->debit ?? 0),
                (float) ($row->credit ?? 0),
                (float) $account->opening_balance
            )];
        });

        return view('dashboards.accounts.ledger.index', [
            'accounts' => $accounts,
            'balances' => $balances,
            'selected' => $selected,
            'ledger' => $ledger,
            'from' => $from,
            'to' => $to,
        ]);
    }

    /** Customer statement: every invoice and every payment in date order, with a balance. */
    public function customer(Request $request, Customer $customer): View
    {
        $this->allow();

        $clientId = $this->clientId();
        abort_unless((int) $customer->client_id === $clientId, 404);

        $from = $request->get('from') ?: now()->subYear()->startOfMonth()->toDateString();
        $to = $request->get('to') ?: now()->toDateString();

        $invoices = Invoice::with('invoicePayments')
            ->where('client_id', $clientId)
            ->where('customer_id', $customer->id)
            ->whereDate('created_at', '<=', $to)
            ->get();

        $events = [];

        foreach ($invoices as $invoice) {
            $date = optional($invoice->created_at)->toDateString() ?: $to;
            $events[] = [
                'date' => $date,
                'type' => 'Invoice',
                'reference' => $invoice->invoice_no,
                'description' => 'Cargo load invoice',
                'debit' => (float) $invoice->amountDue(),
                'credit' => 0.0,
            ];

            foreach ($invoice->invoicePayments as $payment) {
                $paidOn = optional($payment->payment_date)->toDateString() ?: $date;
                $events[] = [
                    'date' => $paidOn,
                    'type' => 'Payment',
                    'reference' => $payment->reference_no ?: 'PAY-' . $payment->id,
                    'description' => strtoupper(str_replace('_', ' ', (string) $payment->payment_method)),
                    'debit' => 0.0,
                    'credit' => (float) $payment->amount_received,
                ];
            }
        }

        usort($events, fn ($a, $b) => [$a['date'], $a['type']] <=> [$b['date'], $b['type']]);

        $opening = 0.0;
        $running = $opening;
        $rows = [];

        foreach ($events as $event) {
            if ($event['date'] < $from) {
                $opening += $event['debit'] - $event['credit'];
                $running = $opening;
                continue;
            }

            $running += $event['debit'] - $event['credit'];
            $event['balance'] = round($running, 2);
            $rows[] = $event;
        }

        return view('dashboards.accounts.ledger.customer', [
            'customer' => $customer,
            'rows' => $rows,
            'opening' => round($opening, 2),
            'closing' => round($running, 2),
            'from' => $from,
            'to' => $to,
            'customers' => Customer::where('client_id', $clientId)->orderBy('customer_firstname')->get(),
        ]);
    }
}
