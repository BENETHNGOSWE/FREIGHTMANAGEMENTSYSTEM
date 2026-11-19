<?php

use App\Http\Controllers\Accounting\AccountingDashboardController;
use App\Http\Controllers\Accounting\AccountingReportController;
use App\Http\Controllers\Accounting\AccountingSettingsController;
use App\Http\Controllers\Accounting\ChartOfAccountsController;
use App\Http\Controllers\Accounting\JournalEntryController;
use App\Http\Controllers\Accounting\LedgerController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Accounts (the accounting module)
|--------------------------------------------------------------------------
| Everything under /accounts: chart of accounts, journal entries, ledgers and
| the financial reports. Every query inside is scoped to the logged-in user's client.
*/

Route::prefix('accounts')
    ->name('accounts.')
    ->middleware(['auth', 'web', 'module:accounts'])
    ->group(function () {
        Route::get('/', [AccountingDashboardController::class, 'index'])->name('dashboard');

        // Chart of accounts
        Route::get('chart', [ChartOfAccountsController::class, 'index'])->name('chart.index');
        Route::get('chart/create', [ChartOfAccountsController::class, 'create'])->name('chart.create');
        Route::post('chart', [ChartOfAccountsController::class, 'store'])->name('chart.store');
        Route::get('chart/{account}/edit', [ChartOfAccountsController::class, 'edit'])->name('chart.edit');
        Route::put('chart/{account}', [ChartOfAccountsController::class, 'update'])->name('chart.update');
        Route::delete('chart/{account}', [ChartOfAccountsController::class, 'destroy'])->name('chart.destroy');

        // Journal entries (manual vouchers + everything posted automatically)
        Route::get('journal', [JournalEntryController::class, 'index'])->name('journal.index');
        Route::get('journal/create', [JournalEntryController::class, 'create'])->name('journal.create');
        Route::post('journal', [JournalEntryController::class, 'store'])->name('journal.store');
        Route::get('journal/{entry}', [JournalEntryController::class, 'show'])->name('journal.show');
        Route::get('journal/{entry}/edit', [JournalEntryController::class, 'edit'])->name('journal.edit');
        Route::put('journal/{entry}', [JournalEntryController::class, 'update'])->name('journal.update');
        Route::delete('journal/{entry}', [JournalEntryController::class, 'destroy'])->name('journal.destroy');

        // Ledgers
        Route::get('ledger', [LedgerController::class, 'index'])->name('ledger.index');
        Route::get('ledger/customer/{customer}', [LedgerController::class, 'customer'])->name('ledger.customer');

        // Reports
        Route::get('reports/trial-balance', [AccountingReportController::class, 'trialBalance'])->name('reports.trial_balance');
        Route::get('reports/profit-loss', [AccountingReportController::class, 'profitLoss'])->name('reports.profit_loss');
        Route::get('reports/balance-sheet', [AccountingReportController::class, 'balanceSheet'])->name('reports.balance_sheet');
        Route::get('reports/receivables', [AccountingReportController::class, 'receivables'])->name('reports.receivables');
        Route::get('reports/cash-bank', [AccountingReportController::class, 'cashBank'])->name('reports.cash_bank');
        Route::get('reports/export/{report}', [AccountingReportController::class, 'export'])->name('reports.export');

        // Settings
        Route::get('settings', [AccountingSettingsController::class, 'index'])->name('settings');
        Route::put('settings', [AccountingSettingsController::class, 'update'])->name('settings.update');
        Route::post('settings/seed-chart', [AccountingSettingsController::class, 'seedChart'])->name('settings.seed');
        Route::post('settings/post-existing', [AccountingSettingsController::class, 'postExisting'])->name('settings.post_existing');
    });
