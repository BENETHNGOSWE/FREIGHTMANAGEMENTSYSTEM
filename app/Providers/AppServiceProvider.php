<?php

namespace App\Providers;

use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\OfficeExpense;
use App\Observers\InvoiceObserver;
use App\Observers\InvoicePaymentObserver;
use App\Observers\OfficeExpenseObserver;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Paginator::useBootstrap();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }

        // Accounting: invoices, payments and expenses post themselves into the ledger.
        Invoice::observe(InvoiceObserver::class);
        InvoicePayment::observe(InvoicePaymentObserver::class);
        OfficeExpense::observe(OfficeExpenseObserver::class);
    }
}
