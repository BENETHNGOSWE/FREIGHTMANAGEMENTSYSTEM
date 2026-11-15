<?php

namespace App\Http\Controllers\Accounting;

use App\Models\AccountingSetting;
use App\Services\Accounting\AccountingReports;
use Illuminate\View\View;

class AccountingDashboardController extends AccountingController
{
    public function index(AccountingReports $reports): View
    {
        $this->allow();

        $clientId = $this->clientId();

        // First visit: make sure the books exist so the page is never empty.
        $this->chart()->ensureForClient($clientId);

        return view('dashboards.accounts.dashboard', [
            'summary' => $reports->dashboard($clientId),
            'settings' => AccountingSetting::where('client_id', $clientId)->first(),
        ]);
    }
}
