<?php

namespace App\Services\Platform;

/**
 * The products this business sells, and the modules that belong to each one.
 *
 * Freight clients get the cargo side. SMS clients (who may not be a freight company at
 * all) get the bulk-SMS side. A client can buy both, that is what "both" is for.
 */
class ProductCatalog
{
    public const PRODUCTS = [
        'freight' => 'KodaFreight (cargo & freight)',
        'sms' => 'KodaSMS (bulk SMS)',
        'both' => 'KodaFreight + KodaSMS',
    ];

    /** key => [name, group, description] */
    public const MODULES = [
        // ---------------------------------------------------------------- freight
        'cargo' => ['Cargo Operations', 'freight', 'Cargo list, photos, China location, QR code and the journey'],
        'verification' => ['Warehouse Verification', 'freight', 'Confirm weight, damage, missing and wrong items'],
        'storage' => ['Storage', 'freight', 'Daily storage charges, customer rates, payments and waivers'],
        'release' => ['Release Control', 'freight', 'Release checklist, pickup notes and collection'],
        'containers' => ['Containers', 'freight', 'Containers and container loading'],
        'loads' => ['Loads / Packages', 'freight', 'Load packages and package types'],
        'invoices' => ['Invoices & Payments', 'freight', 'Invoices, payments, merged payment and final invoice'],
        'customers' => ['Customers & Leads', 'freight', 'Customers, categories and leads'],
        'expenses' => ['Expenses', 'freight', 'Office expenses and expense categories'],
        'reports' => ['Sales & Financial Reports', 'freight', 'Sales reports, delivery orders and financial reports'],
        'accounts' => ['Accounting', 'freight', 'Chart of accounts, journals, ledgers and books reports'],

        // ---------------------------------------------------------------- sms
        'sms_bulk' => ['Bulk SMS Sending', 'sms', 'Send to many numbers, to customers or to a custom list'],
        'sms_contacts' => ['SMS Contacts', 'sms', 'Own address book of numbers/customers to message'],
        'sms_sender' => ['Sender Names', 'sms', 'Request and switch the sender name (alphanumeric ID)'],
        'sms_reports' => ['SMS Reports', 'sms', 'Delivery reports, failures and usage balance'],

        // ---------------------------------------------------------------- always on
        'dashboard' => ['Dashboard', 'both', 'The home screen'],
        'staff' => ['Staff & Roles', 'both', 'Staff, branches and permissions'],
    ];

    public static function products(): array
    {
        return self::PRODUCTS;
    }

    public static function modulesFor(string $product): array
    {
        return array_keys(array_filter(self::MODULES, function ($module) use ($product) {
            $group = $module[1];

            if ($group === 'both' || $group === $product) {
                return true;
            }

            // "both" products get the freight AND the sms modules
            return $product === 'both' && in_array($group, ['freight', 'sms'], true);
        }));
    }

    public static function allKeys(): array
    {
        return array_keys(self::MODULES);
    }

    public static function name(string $key): string
    {
        return self::MODULES[$key][0] ?? $key;
    }
}
