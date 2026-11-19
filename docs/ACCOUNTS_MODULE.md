# Accounts — the accounting module

The Laravel app had freight operations but **no accounting**: invoices carried amounts and a
status, and nothing ever became a ledger. This module adds the part ERPNext was doing:
a chart of accounts, double-entry journals, ledgers and the financial reports — inside the
existing app, multi-tenant like everything else, using `client_id` for isolation.

## What is inside

| Page | Route | What it does |
|---|---|---|
| Accounts Dashboard | `/accounts` | Cash & bank, what customers owe, income / expenses / profit, latest entries |
| Chart of Accounts | `/accounts/chart` | The account tree per client (Asset / Liability / Equity / Income / Expense) |
| Journal Entries | `/accounts/journal` | Every entry — manual vouchers **and** everything posted automatically |
| General Ledger | `/accounts/ledger` | Every debit and credit of one account with a running balance |
| Customer Statement | `/accounts/ledger/customer/{id}` | Invoices and payments of one customer, balance after each line |
| Trial Balance | `/accounts/reports/trial-balance` | Debit and credit per account, with a balance check |
| Profit & Loss | `/accounts/reports/profit-loss` | Income − expenses for any period |
| Balance Sheet | `/accounts/reports/balance-sheet` | Assets vs liabilities + equity, profit for the period included |
| Receivables & Ageing | `/accounts/reports/receivables` | Per customer: invoiced, received, balance, 0-30 / 31-60 / 61-90 / 90+ |
| Cash & Bank Book | `/accounts/reports/cash-bank` | Opening, money in, money out, closing per cash / bank account |
| Accounts Settings | `/accounts/settings` | Which account each automatic posting uses, fiscal year, back-post existing data |

Every report can be exported to CSV.

## The rule that makes it work

**Total debit must equal total credit.** That single rule is enforced in
`App\Services\Accounting\LedgerService::post()`; every report is then just arithmetic over
`journal_entry_accounts`. Nothing is stored twice, so a report can never disagree with the
journal.

Posting rules shipped with the module:

| Event | Debit | Credit |
|---|---|---|
| Sales invoice saved | Accounts Receivable (amount + transport fee) | Freight Income (CBM / KG amount) and Transport Income (transport fee) |
| Payment recorded on an invoice | Cash in Hand / Bank / Mobile Money (by `payment_method`) | Accounts Receivable |
| Office expense saved | Matching expense account (by category name, else Miscellaneous Expenses) | Cash in Hand |
| Manual voucher | whatever the accountant enters, must balance | |

Changing an invoice re-prices its entry in place (same entry number, no duplicate).
Deleting an invoice, payment or expense cancels the entry; cancelled entries stay for the
audit trail but never count in any report.

## How the automatic posting is wired

Model observers — `InvoiceObserver`, `InvoicePaymentObserver`, `OfficeExpenseObserver`,
registered in `AppServiceProvider`. Accounting must never break the invoice screen: any
failure is logged (`storage/logs/laravel.log`, message starts with `Accounting:`) and the
user keeps working. `Accounts → Settings → Post existing invoices & payments` repairs the gap,
and is safe to run repeatedly — `backfill()` never duplicates.

Invoices and payments created **before** this module existed are not in the ledger until that
button (or `php artisan accounting:setup`) runs.

## Install on the server

```bash
cd /path/to/FREIGHTMANAGEMENTSYSTEM
git pull
composer install --no-dev        # nothing new is required, safe to skip
php artisan migrate              # creates accounts, journal_entries, journal_entry_accounts, accounting_settings
php artisan db:seed --class=PermissionsSeeder   # adds the accounting permissions to the roles
php artisan accounting:setup     # standard chart for every client + post existing invoices/payments/expenses
php artisan optimize:clear
```

Then open **Accounts** in the sidebar. On a client's first visit the standard chart is created
automatically, so `/accounts` is never an empty page.

## The chart that is seeded (ERPNext style, TZS)

Assets `1000` (Cash in Hand `1110`, CRDB `1121`, NMB `1122`, Mobile Money `1123`,
Accounts Receivable `1200`, Fixed Assets `1400`) · Liabilities `2000` (Accounts Payable `2100`,
Customer Deposits `2300`, VAT Payable `2410`) · Equity `3000` (Owner Capital `3100`,
Retained Earnings `3200`) · Income `4000` (Freight Income `4100`, Transport Income `4200`) ·
Expenses `5000` (Cost of Services `5100`, Salaries `5200`, Fuel `5300`, Rent `5400`,
Utilities `5500`, Office `5600`, Bank Charges `5700`, Miscellaneous `5900`).

Every account can be renamed, moved, disabled or extended. Deleting an account that already
has entries is refused — disable it instead.

## Multi-tenant

`App\Support\Accounting\ClientContext` resolves the client from the logged-in user
(`users.client_id`). Platform staff (owner / admin) without a client see the first client or
`?client_id=`. Every query is scoped; a foreign account id returns 404, and posting to an
account of another client raises an exception (both covered by tests).

## Tests

```bash
php artisan test --filter=AccountsModuleTest
```

10 tests / 48 assertions: every page renders, the forms post, an unbalanced entry is refused,
the chart is created for a new client, another tenant's account is invisible, a visitor is
redirected, settings save, and an invoice plus its payment land in the trial balance and the
receivables report.

The rest of the repository's test suite is unrelated and was already failing before this
module (its feature tests use `RefreshDatabase`, which cannot replay the legacy
`2025_01_24_201531_modify_customerleads_user_id_foreign` migration on a fresh database).

## Not included (deliberately, next steps)

* Purchase invoices / supplier bills (Accounts Payable is in the chart, unused so far)
* VAT computation on invoices (VAT Payable account exists)
* Multi-currency ledger — amounts are TZS; your invoices already store the exchange rate, so
  USD reporting can be added as a column without changing the ledger
* Bank reconciliation against imported statements
* Cost centres (the column exists on lines, unused)
