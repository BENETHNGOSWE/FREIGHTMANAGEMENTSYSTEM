<?php

namespace App\Console\Commands;

use App\Models\Client;
use App\Services\Cargo\StorageService;
use Illuminate\Console\Command;

class AccrueStorageCommand extends Command
{
    protected $signature = 'storage:accrue {--date= : Charge up to this date (default today)} {--client= : One client id (default all)}';

    protected $description = 'Add the daily storage charge to every cargo past its free period. Safe to run repeatedly.';

    public function handle(StorageService $storage): int
    {
        $clients = $this->option('client')
            ? Client::whereKey($this->option('client'))->get()
            : Client::orderBy('id')->get();

        if ($clients->isEmpty()) {
            $this->error('No client found.');

            return self::FAILURE;
        }

        foreach ($clients as $client) {
            $result = $storage->accrue((int) $client->id, $this->option('date'));

            $this->line(sprintf(
                'Client #%d %s: %d charge(s) on %d cargo, %s added%s',
                $client->id,
                $client->client_name,
                $result['added'],
                $result['cargo'],
                number_format($result['amount'], 2),
                $result['skipped'] ? ' (' . $result['skipped'] . ' cargo have no rate)' : ''
            ));
        }

        return self::SUCCESS;
    }
}
