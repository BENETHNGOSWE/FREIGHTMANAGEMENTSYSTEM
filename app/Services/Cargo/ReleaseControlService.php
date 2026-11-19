<?php

namespace App\Services\Cargo;

use App\Models\Invoice;
use App\Models\InvoiceLoad;
use App\Models\Loadpackage;
use App\Models\PickupNote;
use Illuminate\Support\Carbon;

/**
 * Warehouse release control.
 *
 * The warehouse may only hand cargo over when every check passes: the invoice is paid, the
 * storage is cleared, a valid pickup note exists and the cargo is physically here. Staff
 * cannot bypass a failing check - finance has to fix it first.
 */
class ReleaseControlService
{
    public function __construct(
        private StorageService $storage,
        private CargoStatusService $status,
    ) {
    }

    /** @return array{checks:array, can_release:bool, can_collect:bool, storage:?object, invoice:?Invoice, note:?PickupNote} */
    public function checks(Loadpackage $load): array
    {
        $invoice = $this->invoiceFor($load);
        $storage = $this->storage->forLoad($load);
        $note = $invoice
            ? PickupNote::where('invoice_id', $invoice->id)->where('status', '!=', 'cancelled')->latest('id')->first()
            : null;

        $balance = $storage ? $storage->balance() : 0.0;
        $invoiceBalance = $invoice ? max($invoice->balanceDue(), 0) : null;

        $checks = [
            [
                'key' => 'invoice',
                'label' => 'Invoice paid',
                'ok' => $invoice ? $invoiceBalance <= 0.004 : false,
                'detail' => $invoice
                    ? ($invoiceBalance <= 0.004
                        ? 'Invoice ' . $invoice->invoice_no . ' is fully paid'
                        : 'Outstanding on invoice: ' . number_format($invoiceBalance, 2))
                    : 'This cargo has no invoice yet',
            ],
            [
                'key' => 'storage',
                'label' => 'Storage cleared',
                'ok' => $balance <= 0.004,
                'detail' => $balance <= 0.004
                    ? 'No storage outstanding'
                    : 'Storage outstanding: ' . number_format($balance, 2) . ' — DO NOT RELEASE',
            ],
            [
                'key' => 'pickup_note',
                'label' => 'Pickup note issued',
                'ok' => (bool) $note && $note->isValid(),
                'detail' => $note
                    ? ($note->isValid() ? 'Pickup note ' . $note->note_no . ' is valid' : 'Pickup note ' . $note->note_no . ' is ' . $note->status)
                    : 'No pickup note has been issued',
            ],
            [
                'key' => 'status',
                'label' => 'Cargo status',
                'ok' => $this->statusAllowsRelease($load),
                'detail' => $this->statusAllowsRelease($load)
                    ? 'Status: ' . $load->statusLabel()
                    : 'Status "' . $load->statusLabel() . '" cannot be released (verify the cargo first)',
            ],
            [
                'key' => 'verification',
                'label' => 'Verified in Dar warehouse',
                'ok' => (bool) $load->verified_at,
                'detail' => $load->verified_at
                    ? 'Verified on ' . $load->verified_at->format('d M Y H:i')
                    : 'Cargo has not been verified yet',
            ],
            [
                'key' => 'physical',
                'label' => 'Cargo physically present',
                'ok' => ! $load->isCollected() && ! $load->has_missing,
                'detail' => $load->isCollected()
                    ? 'Already collected on ' . optional($load->collected_at)->format('d M Y H:i')
                    : ($load->has_missing ? 'Cargo is flagged as missing' : 'Cargo is in the warehouse'),
            ],
        ];

        $failed = collect($checks)->where('ok', false)->pluck('label')->all();

        return [
            'checks' => $checks,
            'can_release' => empty($failed),
            'can_collect' => $load->cargo_status === 'Ready for Pickup'
                && $balance <= 0.004
                && (bool) $note && $note->isValid()
                && ! $load->isCollected(),
            'blocked_by' => $failed,
            'storage' => $storage,
            'invoice' => $invoice,
            'note' => $note,
        ];
    }

    /** Hand the cargo over: status becomes "Ready for Pickup" and the storage clock is set. */
    public function release(Loadpackage $load): array
    {
        $result = $this->checks($load);

        if (! $result['can_release']) {
            return ['ok' => false, 'message' => 'Cannot release: ' . implode(', ', $result['blocked_by'])];
        }

        $load->forceFill(['released_at' => now(), 'released_by' => auth()->id()])->save();
        $this->status->set($load, 'Ready for Pickup', 'Release order issued by ' . (auth()->user()->name ?? 'staff'));

        return ['ok' => true, 'message' => 'Cargo released. The customer can collect it with pickup note ' . optional($result['note'])->note_no . '.'];
    }

    /** Final pickup: the cargo leaves. Storage stops automatically. */
    public function collect(Loadpackage $load): array
    {
        $result = $this->checks($load);

        if (! $result['can_collect']) {
            $reason = $load->cargo_status !== 'Ready for Pickup'
                ? 'the cargo has not been released yet'
                : 'storage or the pickup note is not clear';

            return ['ok' => false, 'message' => 'Cannot confirm pickup: ' . $reason . '.'];
        }

        $load->forceFill(['collected_at' => now()])->save();

        if ($result['note']) {
            $result['note']->forceFill(['status' => 'used', 'used_at' => now()])->save();
        }

        $this->status->set($load, 'Collected', 'Collected at the warehouse');
        $this->storage->stop($load);

        return ['ok' => true, 'message' => 'Cargo collected. Storage has stopped.'];
    }

    public function invoiceFor(Loadpackage $load): ?Invoice
    {
        $invoiceId = InvoiceLoad::where('loadpackage_id', $load->id)->value('invoice_id');

        return $invoiceId ? Invoice::find($invoiceId) : null;
    }

    private function statusAllowsRelease(Loadpackage $load): bool
    {
        return in_array($load->cargo_status, ['Verified', 'Invoiced', 'Paid'], true);
    }

    /** Cargo sitting in the warehouse with its checklist, for the release screen. */
    public function pending(int $clientId)
    {
        return Loadpackage::where('client_id', $clientId)
            ->whereNull('collected_at')
            ->whereIn('cargo_status', ['Arrived', 'Verified', 'Invoiced', 'Paid', 'Ready for Pickup'])
            ->with(['customer', 'unittype', 'storage'])
            ->orderBy('created_at')
            ->get()
            ->map(function (Loadpackage $load) {
                $result = $this->checks($load);
                $load->release_checks = $result['checks'];
                $load->can_release = $result['can_release'];
                $load->can_collect = $result['can_collect'];
                $load->storage_balance = $result['storage'] ? $result['storage']->balance() : 0.0;
                $load->invoice_balance = $result['invoice'] ? max($result['invoice']->balanceDue(), 0) : null;

                return $load;
            });
    }
}
