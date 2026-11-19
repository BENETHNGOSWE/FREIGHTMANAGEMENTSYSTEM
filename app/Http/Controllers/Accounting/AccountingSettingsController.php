<?php

namespace App\Http\Controllers\Accounting;

use App\Models\Account;
use App\Models\AccountingSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccountingSettingsController extends AccountingController
{
    public function index(): View
    {
        $this->allow('manage accounting settings');
        $clientId = $this->clientId();
        $setting = $this->settings();

        return view('dashboards.accounts.settings', [
            'setting' => $setting,
            'accounts' => Account::forClient($clientId)->active()->leaves()
                ->orderByRaw('coalesce(account_number, name) asc')->get(),
            'fields' => [
                'receivable_account_id' => 'Accounts Receivable (customers owe us)',
                'income_account_id' => 'Freight Income (CBM / KG price x exchange rate)',
                'transport_income_account_id' => 'Transport Income (transport fee)',
                'cash_account_id' => 'Cash in Hand',
                'bank_account_id' => 'Bank (also used for cheques)',
                'mobile_money_account_id' => 'Mobile Money',
                'expense_account_id' => 'Default expense account',
                'payable_account_id' => 'Accounts Payable (we owe suppliers)',
                'equity_account_id' => 'Owner capital / equity',
            ],
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $this->allow('manage accounting settings');

        $clientId = $this->clientId();
        $setting = AccountingSetting::firstOrCreate(['client_id' => $clientId]);

        $data = $request->validate([
            'receivable_account_id' => ['nullable', 'integer'],
            'income_account_id' => ['nullable', 'integer'],
            'transport_income_account_id' => ['nullable', 'integer'],
            'cash_account_id' => ['nullable', 'integer'],
            'bank_account_id' => ['nullable', 'integer'],
            'mobile_money_account_id' => ['nullable', 'integer'],
            'expense_account_id' => ['nullable', 'integer'],
            'payable_account_id' => ['nullable', 'integer'],
            'equity_account_id' => ['nullable', 'integer'],
            'fiscal_year_start_month' => ['nullable', 'integer', 'between:1,12'],
        ]);

        // Only accounts of this tenant may be mapped.
        foreach ($data as $key => $value) {
            if ($value && str_ends_with($key, '_account_id')) {
                abort_unless(
                    Account::forClient($clientId)->whereKey($value)->exists(),
                    422,
                    'That account does not belong to your company.'
                );
            }
        }

        $data['auto_post_invoices'] = $request->boolean('auto_post_invoices');
        $data['auto_post_payments'] = $request->boolean('auto_post_payments');
        $data['auto_post_expenses'] = $request->boolean('auto_post_expenses');
        $data['fiscal_year_start_month'] = str_pad((string) ($data['fiscal_year_start_month'] ?: 1), 2, '0', STR_PAD_LEFT);

        $setting->update($data);

        return back()->with('success', 'Accounting settings saved.');
    }

    /** Re-create the standard chart and wiring for this client (safe to run repeatedly). */
    public function seedChart(): RedirectResponse
    {
        $this->allow('manage accounting settings');

        $this->chart()->ensureForClient($this->clientId());

        return back()->with('success', 'Standard chart of accounts is in place.');
    }

    /** Post every existing invoice, payment and expense into the ledger. */
    public function postExisting(): RedirectResponse
    {
        $this->allow('manage accounting settings');

        $result = $this->ledger()->backfill($this->clientId());

        $message = sprintf(
            'Posted %d invoices, %d payments and %d expenses into the ledger.',
            $result['invoices'],
            $result['payments'],
            $result['expenses']
        );

        if (! empty($result['errors'])) {
            return back()->with('warning', $message . ' Some records were skipped: ' . implode(' | ', array_slice($result['errors'], 0, 5)));
        }

        return back()->with('success', $message);
    }
}
