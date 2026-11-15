<?php

namespace App\Services\Cargo;

use App\Models\Invoice;
use App\Models\InvoiceLoad;
use App\Models\InvoicePayment;
use App\Models\Loadpackage;
use App\Models\MergedPayment;
use Illuminate\Support\Facades\DB;

/**
 * One payment for several cargo of the same customer.
 *
 * The tracking numbers stay separate; only the money is combined. For KG cargo the billable
 * weight is computed on the COMBINED weight, with a 1 KG minimum:
 *   0.3 + 0.4 = 0.7  -> billable 1.0
 *   0.6 + 0.8 = 1.4  -> billable 1.4
 */
class MergePaymentService
{
    public const MIN_KG = 1.0;

    /** Weights, the 1 KG rule and the amounts - without changing anything. */
    public function preview(int $clientId, array $invoiceIds): array
    {
        $invoices = Invoice::with('invoicePayments')
            ->where('client_id', $clientId)
            ->whereIn('id', $invoiceIds)
            ->orderBy('created_at')
            ->get();

        $rows = [];
        $combined = 0.0;
        $kgInvoices = [];
        $nonKgTotal = 0.0;

        foreach ($invoices as $invoice) {
            $weight = $this->weightOf($invoice);
            $isKg = $this->isKgInvoice($invoice);
            $balance = max($invoice->balanceDue(), 0);

            $rows[] = [
                'invoice' => $invoice,
                'weight' => $weight,
                'is_kg' => $isKg,
                'old_total' => (float) $invoice->amountDue(),
                'balance' => $balance,
            ];

            if ($isKg) {
                $combined += $weight;
                $kgInvoices[] = count($rows) - 1;
            } else {
                $nonKgTotal += $balance;
            }
        }

        $billable = $combined;
        $minimumApplied = false;

        if ($combined > 0 && $combined < self::MIN_KG) {
            $billable = self::MIN_KG;
            $minimumApplied = true;
        }

        // KG invoices are re-priced on the billable weight, split by their share of the weight.
        $difference = 0.0;

        foreach ($rows as $index => &$row) {
            $row['new_total'] = $row['old_total'];

            if (! $row['is_kg'] || $combined <= 0) {
                continue;
            }

            $invoice = $row['invoice'];
            $price = (float) $invoice->cbm_price;
            $fx = (float) ($invoice->exchange_rate ?: 1);

            if ($price <= 0) {
                continue;
            }

            $share = $row['weight'] / $combined;
            $row['billable_weight'] = round($billable * $share, 3);
            $row['new_total'] = round($row['billable_weight'] * $price * $fx + (float) $invoice->total_transportfee, 2);
            $difference += $row['new_total'] - $row['old_total'];
        }
        unset($row);

        return [
            'rows' => $rows,
            'combined_weight' => round($combined, 3),
            'billable_weight' => round($billable, 3),
            'minimum_applied' => $minimumApplied,
            'difference' => round($difference, 2),
            'total_due' => round((float) array_sum(array_column($rows, 'new_total')) - (float) collect($rows)->sum('received'), 2),
            'balance_due' => round((float) collect($rows)->sum(fn ($row) => max($row['new_total'] - $this->received($row['invoice']), 0)), 2),
        ];
    }

    /**
     * Apply: re-price the KG invoices (if the minimum bites), then record ONE payment spread
     * over the invoices, oldest first.
     */
    public function apply(int $clientId, array $invoiceIds, array $data): MergedPayment
    {
        $preview = $this->preview($clientId, $invoiceIds);

        return DB::transaction(function () use ($clientId, $invoiceIds, $data, $preview) {
            $payment = MergedPayment::create([
                'client_id' => $clientId,
                'customer_id' => $data['customer_id'],
                'reference_no' => $data['reference_no'] ?? null,
                'amount' => round((float) $data['amount'], 2),
                'payment_date' => $data['payment_date'] ?? now()->toDateString(),
                'payment_method' => strtoupper($data['payment_method'] ?? 'CASH'),
                'status' => 'applied',
                'combined_weight' => $preview['combined_weight'],
                'billable_weight' => $preview['billable_weight'],
                'minimum_applied' => $preview['minimum_applied'],
                'reprice_difference' => $preview['difference'],
                'received_by' => auth()->id(),
                'notes' => $data['notes'] ?? null,
            ]);

            $remaining = round((float) $data['amount'], 2);

            foreach ($preview['rows'] as $row) {
                /** @var Invoice $invoice */
                $invoice = $row['invoice'];
                $oldTotal = (float) $invoice->amountDue();

                if (abs($row['new_total'] - $oldTotal) > 0.004) {
                    $invoice->update([
                        'total_amount' => round($row['new_total'] - (float) $invoice->total_transportfee, 2),
                        'total_balance' => max($row['new_total'] - $this->received($invoice), 0),
                    ]);
                }

                $invoice->refresh();
                $balance = max($invoice->balanceDue(), 0);
                $apply = round(min($remaining, $balance), 2);

                if ($apply > 0) {
                    InvoicePayment::create([
                        'invoice_id' => $invoice->id,
                        'client_id' => $clientId,
                        'amount_received' => $apply,
                        'payment_date' => $data['payment_date'] ?? now()->toDateString(),
                        'payment_method' => strtoupper($data['payment_method'] ?? 'CASH'),
                        'reference_no' => $data['reference_no'] ?? ('MERGE-' . $payment->id),
                        'received_by' => auth()->id(),
                        'notes' => 'Merged payment #' . $payment->id,
                    ]);

                    $remaining = round($remaining - $apply, 2);
                }

                $fresh = $invoice->fresh();
                $fresh->update([
                    'total_balance' => max($fresh->balanceDue(), 0),
                    'status' => $fresh->totalReceived() <= 0 ? 0 : ($fresh->balanceDue() <= 0 ? 1 : 2),
                ]);

                $payment->lines()->create([
                    'invoice_id' => $invoice->id,
                    'amount_applied' => $apply,
                    'old_total' => $oldTotal,
                    'new_total' => $row['new_total'],
                ]);

                // Cargo status follows the money.
                foreach (InvoiceLoad::where('invoice_id', $invoice->id)->pluck('loadpackage_id') as $loadId) {
                    $load = Loadpackage::find($loadId);

                    if (! $load || $load->isCollected()) {
                        continue;
                    }

                    $target = $fresh->balanceDue() <= 0 ? 'Paid' : 'Invoiced';

                    if ($load->cargo_status !== $target) {
                        app(CargoStatusService::class)->set($load, $target, 'Merged payment #' . $payment->id);
                    }
                }
            }

            return $payment->load('lines');
        });
    }

    private function weightOf(Invoice $invoice): float
    {
        // PHP-side sum, see Loadpackage::weightForInvoice().
        return Loadpackage::weightForInvoice($invoice->id);
    }

    private function isKgInvoice(Invoice $invoice): bool
    {
        return InvoiceLoad::where('invoice_id', $invoice->id)
            ->join('loadpackages', 'loadpackages.id', '=', 'invoice_loads.loadpackage_id')
            ->join('unit_types', 'unit_types.id', '=', 'loadpackages.unittype_id')
            ->where('unit_types.unittype_name', 'like', '%KG%')
            ->exists();
    }

    private function received(Invoice $invoice): float
    {
        return (float) $invoice->invoicePayments()->sum('amount_received');
    }
}
