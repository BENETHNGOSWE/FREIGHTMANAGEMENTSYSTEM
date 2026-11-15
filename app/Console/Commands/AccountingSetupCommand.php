<?php

namespace App\Console\Commands;

use App\Models\Client;
use App\Services\Accounting\ChartOfAccounts;
use App\Services\Accounting\LedgerService;
use Illuminate\Console\Command;

class AccountingSetupCommand extends Command
{
    protected $signature = 'accounting:setup
                            {--client= : Client id to prepare (default: every client)}
                            {--no-backfill : Only create the chart, do not post existing invoices}';

    protected $description = 'Create the standard chart of accounts and post existing invoices, payments and expenses into the ledger.';

    public function handle(ChartOfAccounts $chart, LedgerService $ledger): int
    {
        $clients = $this->option('client')
            ? Client::whereKey($this->option('client'))->get()
            : Client::orderBy('id')->get();

        if ($clients->isEmpty()) {
            $this->error('No client found.');

            return self::FAILURE;
        }

        foreach ($clients as $client) {
            $this->info('Client #' . $client->id . ' — ' . $client->client_name);

            $chart->ensureForClient((int) $client->id);
            $this->line('  chart of accounts ready');

            if ($this->option('no-backfill')) {
                continue;
            }

            $result = $ledger->backfill((int) $client->id);
            $this->line(sprintf(
                '  posted %d invoices, %d payments, %d expenses (%d skipped)',
                $result['invoices'],
                $result['payments'],
                $result['expenses'],
                $result['skipped']
            ));

            foreach (array_slice($result['errors'], 0, 10) as $error) {
                $this->warn('  ! ' . $error);
            }
        }

        $this->info('Done. Open /accounts to review the books.');

        return self::SUCCESS;
    }
}
