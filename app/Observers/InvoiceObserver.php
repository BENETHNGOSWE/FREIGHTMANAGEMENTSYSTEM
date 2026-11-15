<?php

namespace App\Observers;

use App\Models\Invoice;
use App\Services\Accounting\LedgerService;

/**
 * Keeps the ledger in sync with invoices.
 *
 * Accounting must never break the invoice screen: any failure is reported to the log and the
 * user continues working. `Accounts -> Settings -> Post existing records` repairs the gap.
 */
class InvoiceObserver
{
    public function saved(Invoice $invoice): void
    {
        $this->safely(fn () => app(LedgerService::class)->postInvoice($invoice));
    }

    public function deleted(Invoice $invoice): void
    {
        $this->safely(fn () => app(LedgerService::class)->cancelReference(
            (int) $invoice->client_id,
            'Invoice',
            (int) $invoice->id,
            'Invoice deleted'
        ));
    }

    private function safely(callable $action): void
    {
        try {
            $action();
        } catch (\Throwable $e) {
            report($e);
            logger()->warning('Accounting: invoice posting failed - ' . $e->getMessage());
        }
    }
}
