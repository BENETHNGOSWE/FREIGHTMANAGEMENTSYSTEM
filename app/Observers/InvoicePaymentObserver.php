<?php

namespace App\Observers;

use App\Models\InvoicePayment;
use App\Services\Accounting\LedgerService;

/** Every payment received is posted: Dr cash / bank, Cr accounts receivable. */
class InvoicePaymentObserver
{
    public function saved(InvoicePayment $payment): void
    {
        $this->safely(fn () => app(LedgerService::class)->postPayment($payment));
    }

    public function deleted(InvoicePayment $payment): void
    {
        $this->safely(fn () => app(LedgerService::class)->cancelReference(
            (int) $payment->client_id,
            'InvoicePayment',
            (int) $payment->id,
            'Payment deleted'
        ));
    }

    private function safely(callable $action): void
    {
        try {
            $action();
        } catch (\Throwable $e) {
            report($e);
            logger()->warning('Accounting: payment posting failed - ' . $e->getMessage());
        }
    }
}
