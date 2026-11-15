<?php

namespace App\Observers;

use App\Models\OfficeExpense;
use App\Services\Accounting\LedgerService;

/** Office expenses are posted as Dr expense account, Cr cash in hand. */
class OfficeExpenseObserver
{
    public function saved(OfficeExpense $expense): void
    {
        $this->safely(fn () => app(LedgerService::class)->postExpense($expense));
    }

    public function deleted(OfficeExpense $expense): void
    {
        $this->safely(fn () => app(LedgerService::class)->cancelReference(
            (int) $expense->client_id,
            'OfficeExpense',
            (int) $expense->id,
            'Expense deleted'
        ));
    }

    private function safely(callable $action): void
    {
        try {
            $action();
        } catch (\Throwable $e) {
            report($e);
            logger()->warning('Accounting: expense posting failed - ' . $e->getMessage());
        }
    }
}
