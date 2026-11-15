<?php

namespace App\Services\Cargo;

use App\Models\CargoStatusHistory;
use App\Models\Loadpackage;
use App\Models\PickupNote;
use Illuminate\Support\Str;

/**
 * The status timeline of one cargo, and the little helpers around it
 * (tokens for QR codes, pickup notes).
 */
class CargoStatusService
{
    public function token(): string
    {
        return Str::lower(Str::random(24));
    }

    /** Make sure the cargo has a QR token (done once, kept for ever). */
    public function ensureToken(Loadpackage $load): string
    {
        if (blank($load->qr_token)) {
            $load->forceFill(['qr_token' => $this->token()])->saveQuietly();
        }

        return $load->qr_token;
    }

    /** Move a cargo to a status and write the history row. */
    public function set(Loadpackage $load, string $status, ?string $note = null): Loadpackage
    {
        $load->forceFill(['cargo_status' => $status])->save();

        CargoStatusHistory::create([
            'client_id' => $load->client_id,
            'loadpackage_id' => $load->id,
            'status' => $status,
            'note' => $note,
            'changed_by' => auth()->id(),
        ]);

        return $load;
    }

    /** Every step of the journey with its date, for the public page and the staff page. */
    public function timeline(Loadpackage $load): array
    {
        $history = $load->statusHistories()->get()->keyBy('status');
        $order = array_keys(Loadpackage::STATUSES);
        $current = array_search($load->cargo_status, $order, true);
        $current = $current === false ? 0 : $current;

        // Money steps do not depend on somebody remembering to change the status:
        // an invoice on the cargo means it is invoiced, a cleared balance means it is paid.
        $invoices = $load->getRelatedInvoices();
        $paid = $invoices->isNotEmpty() && $invoices->every(fn ($invoice) => $invoice->balanceDue() <= 0);
        $lastPayment = $invoices->flatMap(fn ($invoice) => $invoice->invoicePayments)->max('payment_date');

        $proof = [
            'Invoiced' => $invoices->isNotEmpty(),
            'Paid' => $paid,
            'Ready for Pickup' => filled($load->released_at),
            'Collected' => filled($load->collected_at),
        ];

        $rows = [];

        foreach ($order as $index => $status) {
            $entry = $history->get($status);

            $date = $entry?->created_at;
            $date = match ($status) {
                'Received' => $date ?? $load->created_at,
                'Arrived' => $date ?? $load->arrived_at,
                'Verified' => $date ?? $load->verified_at,
                'Invoiced' => $date ?? $invoices->min('created_at'),
                'Paid' => $date ?? $lastPayment,
                'Ready for Pickup' => $date ?? $load->released_at,
                'Collected' => $date ?? $load->collected_at,
                default => $date,
            };

            $rows[] = [
                'status' => $status,
                'label' => Loadpackage::STATUSES[$status],
                'done' => $index <= $current || ($proof[$status] ?? false),
                'current' => $index === $current,
                'date' => $date,
                'note' => $entry?->note,
            ];
        }

        return $rows;
    }

    /** One pickup note per invoice; the QR authorises collection. */
    public function pickupNoteFor($invoice): PickupNote
    {
        $existing = PickupNote::where('client_id', $invoice->client_id)
            ->where('invoice_id', $invoice->id)
            ->where('status', '!=', 'cancelled')
            ->first();

        if ($existing) {
            return $existing;
        }

        return PickupNote::create([
            'client_id' => $invoice->client_id,
            'invoice_id' => $invoice->id,
            'customer_id' => $invoice->customer_id,
            'note_no' => 'PU-' . now()->format('Ymd') . '-' . strtoupper(Str::random(5)),
            'qr_token' => $this->token(),
            'status' => 'issued',
            'issued_by' => auth()->id(),
            'issued_at' => now(),
        ]);
    }
}
