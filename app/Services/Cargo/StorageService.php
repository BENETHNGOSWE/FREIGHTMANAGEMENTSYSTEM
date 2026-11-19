<?php

namespace App\Services\Cargo;

use App\Models\CargoStorage;
use App\Models\CustomerStorageRate;
use App\Models\Invoice;
use App\Models\Loadpackage;
use App\Models\StorageCharge;
use App\Models\StoragePayment;
use App\Models\StorageSetting;
use App\Models\StorageWaiver;
use App\Services\Accounting\LedgerService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Automatic storage.
 *
 * Rules (from the business flow):
 *  - every cargo gets a free period (client default, overridable per customer);
 *  - after the free period a fixed daily charge is added automatically, every day;
 *  - charges accumulate on top of what is already there - never restarted, never duplicated
 *    (the `storage_charges` table has a unique key on loadpackage + date);
 *  - storage stops the moment the cargo is collected;
 *  - an outstanding balance blocks the release; finance can take the money or waive it.
 */
class StorageService
{
    public function __construct(
        private LedgerService $ledger,
        private CargoStatusService $status,
    ) {
    }

    public function settings(int $clientId): StorageSetting
    {
        return StorageSetting::firstOrCreate(
            ['client_id' => $clientId],
            ['free_days' => 7, 'daily_rate' => 0, 'currency' => 'TZS', 'auto_accrue' => true, 'charge_basis' => 'per_cargo']
        );
    }

    /** Customer override wins, otherwise the client default applies. */
    public function rateFor(int $clientId, ?int $customerId): array
    {
        $settings = $this->settings($clientId);
        $override = $customerId
            ? CustomerStorageRate::where('client_id', $clientId)->where('customer_id', $customerId)->first()
            : null;

        return [
            'free_days' => (int) ($override->free_days ?? $settings->free_days),
            'daily_rate' => (float) ($override->daily_rate ?? $settings->daily_rate),
            'source' => $override && ($override->daily_rate !== null || $override->free_days !== null)
                ? 'customer override'
                : 'default',
        ];
    }

    /**
     * Open the storage clock for a cargo (idempotent).
     * The clock starts when the cargo physically arrived in Dar, else today.
     */
    public function start(Loadpackage $load, ?Carbon $startedOn = null): CargoStorage
    {
        $existing = CargoStorage::where('loadpackage_id', $load->id)->first();

        if ($existing) {
            return $existing;
        }

        $rate = $this->rateFor((int) $load->client_id, $load->customer_id);
        $startedOn = $startedOn
            ?? ($load->arrived_at ? Carbon::parse($load->arrived_at) : Carbon::parse($load->created_at ?? now()));

        return CargoStorage::create([
            'client_id' => $load->client_id,
            'loadpackage_id' => $load->id,
            'customer_id' => $load->customer_id,
            'started_on' => $startedOn->toDateString(),
            'free_days' => $rate['free_days'],
            'daily_rate' => $rate['daily_rate'],
            'status' => 'active',
            'days_charged' => 0,
        ]);
    }

    /**
     * Add every missing daily charge up to and including $until.
     *
     * @return array{added:int, amount:float, cargo:int, skipped:int}
     */
    public function accrue(int $clientId, ?string $until = null, ?int $loadpackageId = null): array
    {
        $untilDate = Carbon::parse($until ?? now()->toDateString())->startOfDay();
        $added = 0;
        $amount = 0.0;
        $cargo = 0;
        $skipped = 0;

        $query = CargoStorage::where('client_id', $clientId)->where('status', 'active');
        if ($loadpackageId) {
            $query->where('loadpackage_id', $loadpackageId);
        }

        foreach ($query->get() as $storage) {
            if ((float) $storage->daily_rate <= 0) {
                $skipped++;
                continue;
            }

            $firstCharge = $storage->firstChargeDate();
            if (! $firstCharge || $firstCharge->greaterThan($untilDate)) {
                continue; // still inside the free period
            }

            $existing = StorageCharge::where('cargo_storage_id', $storage->id)
                ->pluck('charge_date')
                ->map(fn ($date) => Carbon::parse($date)->toDateString())
                ->flip();

            $day = $firstCharge->copy();
            $cargoAdded = 0;

            while ($day->lessThanOrEqualTo($untilDate)) {
                $key = $day->toDateString();

                if (! isset($existing[$key])) {
                    StorageCharge::firstOrCreate(
                        ['loadpackage_id' => $storage->loadpackage_id, 'charge_date' => $key],
                        [
                            'client_id' => $storage->client_id,
                            'cargo_storage_id' => $storage->id,
                            'amount' => $storage->daily_rate,
                        ]
                    );
                    $cargoAdded++;
                    $amount += (float) $storage->daily_rate;
                }

                $day->addDay();
            }

            if ($cargoAdded > 0) {
                $added += $cargoAdded;
                $cargo++;
                $storage->forceFill([
                    'last_charged_on' => $untilDate->toDateString(),
                    'days_charged' => (int) $storage->days_charged + $cargoAdded,
                ])->save();
            }
        }

        if ($added > 0) {
            $this->postAccrualToLedger($clientId, $untilDate->toDateString());
        }

        return ['added' => $added, 'amount' => round($amount, 2), 'cargo' => $cargo, 'skipped' => $skipped];
    }

    /** Storage income for a day, posted once per day even if the job runs several times. */
    private function postAccrualToLedger(int $clientId, string $date): void
    {
        $total = (float) StorageCharge::where('client_id', $clientId)->where('charge_date', $date)->sum('amount');

        if ($total > 0) {
            $this->ledger->postStorageAccrual($clientId, $date, $total);
        }
    }

    public function pay(CargoStorage $storage, array $data): StoragePayment
    {
        $payment = $storage->payments()->create([
            'client_id' => $storage->client_id,
            'loadpackage_id' => $storage->loadpackage_id,
            'customer_id' => $storage->customer_id,
            'amount' => round((float) $data['amount'], 2),
            'payment_date' => $data['payment_date'] ?? now()->toDateString(),
            'payment_method' => strtoupper($data['payment_method'] ?? 'CASH'),
            'reference_no' => $data['reference_no'] ?? null,
            'received_by' => auth()->id(),
            'notes' => $data['notes'] ?? null,
        ]);

        $this->ledger->postStoragePayment($payment);

        return $payment;
    }

    public function waive(CargoStorage $storage, float $amount, ?string $reason = null): StorageWaiver
    {
        $waiver = $storage->waivers()->create([
            'client_id' => $storage->client_id,
            'loadpackage_id' => $storage->loadpackage_id,
            'amount' => round($amount, 2),
            'reason' => $reason,
            'waived_by' => auth()->id(),
        ]);

        $storage->forceFill([
            'waived_amount' => round((float) $storage->waived_amount + $amount, 2),
        ])->save();

        $this->ledger->postStorageWaiver($waiver);

        return $waiver;
    }

    /** Cargo left the warehouse: charges stop here. */
    public function stop(Loadpackage $load, string $status = 'collected'): void
    {
        $storage = CargoStorage::where('loadpackage_id', $load->id)->first();

        if (! $storage) {
            return;
        }

        // Charge today before closing the clock, so nothing is lost.
        $this->accrue((int) $storage->client_id, now()->toDateString(), (int) $storage->loadpackage_id);

        $storage->forceFill([
            'status' => 'collected',
            'collected_at' => now(),
        ])->save();
    }

    public function forLoad(Loadpackage $load): ?CargoStorage
    {
        return CargoStorage::where('loadpackage_id', $load->id)->first();
    }

    /** Everything finance and the warehouse need to see at a glance. */
    public function summary(int $clientId): array
    {
        $rows = CargoStorage::where('client_id', $clientId)
            ->with(['loadpackage.customer', 'loadpackage.unittype'])
            ->orderByDesc('started_on')
            ->get()
            ->map(function (CargoStorage $storage) {
                $storage->charged = $storage->chargedTotal();
                $storage->paid = $storage->paidTotal();
                $storage->waived = $storage->waivedTotal();
                $storage->balance = $storage->balance();

                return $storage;
            });

        return [
            'rows' => $rows,
            'total_charged' => round((float) $rows->sum('charged'), 2),
            'total_paid' => round((float) $rows->sum('paid'), 2),
            'total_waived' => round((float) $rows->sum('waived'), 2),
            'total_outstanding' => round((float) $rows->sum('balance'), 2),
            'cargo_active' => $rows->where('status', 'active')->count(),
            'cargo_outstanding' => $rows->filter(fn ($row) => $row->balance > 0)->count(),
        ];
    }
}
