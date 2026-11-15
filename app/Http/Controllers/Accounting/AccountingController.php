<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\AccountingSetting;
use App\Services\Accounting\ChartOfAccounts;
use App\Services\Accounting\LedgerService;
use App\Support\Accounting\ClientContext;

abstract class AccountingController extends Controller
{
    protected function clientId(): int
    {
        return ClientContext::id();
    }

    /**
     * Accounts is open to the accounting roles; `owner` and `admin` always pass so the module
     * works on a fresh install before the permission seeder has been re-run.
     */
    protected function allow(string $permission = 'list accounts'): void
    {
        $user = auth()->user();

        abort_unless($user, 401);

        // These roles run the freight desks; they keep their access even before the
        // permission seeder has been re-run on the site.
        if ($user->hasAnyRole(['owner', 'admin', 'chiefoperation', 'manager', 'Cashier'])) {
            return;
        }

        abort_unless(
            $user->can($permission) || $user->can('list accounts'),
            403,
            'You do not have access to the Accounts module.'
        );
    }

    protected function ledger(): LedgerService
    {
        return app(LedgerService::class);
    }

    protected function chart(): ChartOfAccounts
    {
        return app(ChartOfAccounts::class);
    }

    protected function settings(): AccountingSetting
    {
        return $this->ledger()->settings($this->clientId());
    }
}
