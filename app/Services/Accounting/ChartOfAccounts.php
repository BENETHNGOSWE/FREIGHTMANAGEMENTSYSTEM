<?php

namespace App\Services\Accounting;

use App\Models\Account;
use App\Models\AccountingSetting;

/**
 * The default chart of accounts, modelled on ERPNext's standard tree but trimmed to what a
 * freight forwarder actually uses. Seeded per client; every account can be renamed, moved
 * or disabled afterwards without touching code.
 */
class ChartOfAccounts
{
    /**
     * number, name, parent number, root type, account type, is group, description
     */
    public const DEFAULTS = [
        // ---------------------------------------------------------------- Assets
        ['1000', 'Assets', null, 'Asset', null, true, 'Everything the business owns'],
        ['1100', 'Cash and Bank', '1000', 'Asset', null, true, null],
        ['1110', 'Cash in Hand', '1100', 'Asset', 'Cash', false, 'Money kept at the office'],
        ['1120', 'Bank Accounts', '1100', 'Asset', null, true, null],
        ['1121', 'CRDB Bank', '1120', 'Asset', 'Bank', false, null],
        ['1122', 'NMB Bank', '1120', 'Asset', 'Bank', false, null],
        ['1123', 'Mobile Money (M-Pesa / Tigo Pesa / Airtel)', '1120', 'Asset', 'Bank', false, null],
        ['1200', 'Accounts Receivable', '1000', 'Asset', 'Receivable', false, 'What customers still owe us'],
        ['1300', 'Prepaid Expenses', '1000', 'Asset', 'Current Asset', false, null],
        ['1400', 'Fixed Assets', '1000', 'Asset', null, true, null],
        ['1410', 'Furniture and Equipment', '1400', 'Asset', 'Fixed Asset', false, null],
        ['1420', 'Motor Vehicles', '1400', 'Asset', 'Fixed Asset', false, null],

        // ------------------------------------------------------------ Liabilities
        ['2000', 'Liabilities', null, 'Liability', null, true, 'Everything the business owes'],
        ['2100', 'Accounts Payable', '2000', 'Liability', 'Payable', false, 'What we still owe suppliers'],
        ['2200', 'Accrued Expenses', '2000', 'Liability', 'Current Liability', false, null],
        ['2300', 'Customer Deposits', '2000', 'Liability', 'Current Liability', false, 'Money received before invoicing'],
        ['2400', 'Taxes', '2000', 'Liability', null, true, null],
        ['2410', 'VAT Payable', '2400', 'Liability', 'Tax', false, null],
        ['2420', 'Withholding Tax Payable', '2400', 'Liability', 'Tax', false, null],
        ['2500', 'Loans', '2000', 'Liability', 'Fixed Liability', false, null],

        // ----------------------------------------------------------------- Equity
        ['3000', 'Equity', null, 'Equity', null, true, null],
        ['3100', 'Owner Capital', '3000', 'Equity', 'Equity', false, null],
        ['3200', 'Retained Earnings', '3000', 'Equity', 'Equity', false, 'Profit carried from previous years'],
        ['3300', 'Drawings', '3000', 'Equity', 'Equity', false, null],

        // ----------------------------------------------------------------- Income
        ['4000', 'Income', null, 'Income', null, true, null],
        ['4100', 'Freight Income', '4000', 'Income', 'Income Account', false, 'CBM / KG billed on cargo loads'],
        ['4200', 'Transport Income', '4000', 'Income', 'Income Account', false, 'Trucking / transport fee'],
        ['4300', 'Other Income', '4000', 'Income', 'Income Account', false, null],

        // --------------------------------------------------------------- Expenses
        ['5000', 'Expenses', null, 'Expense', null, true, null],
        ['5100', 'Cost of Services', '5000', 'Expense', null, true, null],
        ['5110', 'Trucking and Transport Cost', '5100', 'Expense', 'Cost of Goods Sold', false, null],
        ['5120', 'Port and Clearing Charges', '5100', 'Expense', 'Cost of Goods Sold', false, null],
        ['5130', 'Container Handling', '5100', 'Expense', 'Cost of Goods Sold', false, null],
        ['5200', 'Salaries and Wages', '5000', 'Expense', 'Expense Account', false, null],
        ['5300', 'Fuel', '5000', 'Expense', 'Expense Account', false, null],
        ['5400', 'Rent', '5000', 'Expense', 'Expense Account', false, null],
        ['5500', 'Utilities', '5000', 'Expense', 'Expense Account', false, 'Electricity, water, internet'],
        ['5600', 'Office Expenses', '5000', 'Expense', 'Expense Account', false, null],
        ['5700', 'Bank Charges', '5000', 'Expense', 'Expense Account', false, null],
        ['5800', 'Repairs and Maintenance', '5000', 'Expense', 'Expense Account', false, null],
        ['5900', 'Miscellaneous Expenses', '5000', 'Expense', 'Expense Account', false, null],
    ];

    /** Account number => accounting_settings column */
    public const SETTING_MAP = [
        '1200' => 'receivable_account_id',
        '2100' => 'payable_account_id',
        '4100' => 'income_account_id',
        '4200' => 'transport_income_account_id',
        '1110' => 'cash_account_id',
        '1121' => 'bank_account_id',
        '1123' => 'mobile_money_account_id',
        '5900' => 'expense_account_id',
        '3100' => 'equity_account_id',
    ];

    /** Create any missing default account and return the client's settings. */
    public function ensureForClient(int $clientId): AccountingSetting
    {
        $byNumber = [];

        foreach (self::DEFAULTS as [$number, $name, $parentNumber, $rootType, $accountType, $isGroup, $description]) {
            $parentId = $parentNumber ? ($byNumber[$parentNumber] ?? null) : null;

            $account = Account::firstOrCreate(
                ['client_id' => $clientId, 'name' => $name],
                [
                    'parent_id' => $parentId,
                    'account_number' => $number,
                    'root_type' => $rootType,
                    'account_type' => $accountType,
                    'is_group' => (bool) $isGroup,
                    'currency' => 'TZS',
                    'description' => $description,
                ]
            );

            $byNumber[$number] = $account->id;
        }

        $setting = AccountingSetting::firstOrCreate(['client_id' => $clientId]);

        foreach (self::SETTING_MAP as $number => $column) {
            if (! $setting->{$column} && isset($byNumber[$number])) {
                $setting->{$column} = $byNumber[$number];
            }
        }

        $setting->save();

        return $setting;
    }

    /** First account of a given number, creating the chart if the client has none yet. */
    public function byNumber(int $clientId, string $number): ?Account
    {
        $account = Account::forClient($clientId)->where('account_number', $number)->first();

        if (! $account) {
            $this->ensureForClient($clientId);
            $account = Account::forClient($clientId)->where('account_number', $number)->first();
        }

        return $account;
    }
}
