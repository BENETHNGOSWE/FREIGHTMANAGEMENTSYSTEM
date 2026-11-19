<?php

namespace App\Services\Cargo;

use App\Models\Invoice;
use App\Models\InvoiceLoad;
use App\Models\Loadpackage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Dar warehouse verification.
 *
 * The China weight is kept as history; the verified weight becomes the official one and the
 * invoice re-prices itself - the clerk never recalculates anything by hand.
 */
class VerificationService
{
    public function __construct(private CargoStatusService $status)
    {
    }

    /**
     * @param  array{weight?:float|string, has_damage?:bool, has_missing?:bool, has_wrong_item?:bool, notes?:?string}  $data
     */
    public function verify(Loadpackage $load, array $data): array
    {
        $oldWeight = (float) ($load->original_weight_value ?? $load->load_weight_value);
        $newWeight = (float) ($data['weight'] ?? $load->load_weight_value);

        $impact = 0.0;

        DB::transaction(function () use ($load, $data, $oldWeight, $newWeight, &$impact) {
            $load->forceFill([
                'original_weight_value' => $load->original_weight_value ?? $oldWeight,
                'verified_weight_value' => $newWeight,
                'load_weight_value' => (string) $newWeight,
                'verified_at' => now(),
                'has_damage' => (bool) ($data['has_damage'] ?? false),
                'has_missing' => (bool) ($data['has_missing'] ?? false),
                'has_wrong_item' => (bool) ($data['has_wrong_item'] ?? false),
                'verification_notes' => $data['notes'] ?? null,
                'arrived_at' => $load->arrived_at ?? Carbon::now()->toDateString(),
            ])->save();

            $impact = $this->repriceInvoices($load, $oldWeight, $newWeight);

            $note = sprintf('Weight %s → %s %s', $this->fmt($oldWeight), $this->fmt($newWeight), $this->unit($load));
            if (abs($impact) > 0.004) {
                $note .= sprintf(' — price affected by %s', number_format($impact, 2));
            }
            if (! empty($data['has_damage'])) {
                $note .= ' | damaged';
            }
            if (! empty($data['has_missing'])) {
                $note .= ' | missing';
            }
            if (! empty($data['has_wrong_item'])) {
                $note .= ' | wrong item';
            }

            $this->status->set($load->fresh(), 'Verified', $note);
        });

        return ['impact' => round($impact, 2), 'old_weight' => $oldWeight, 'new_weight' => $newWeight];
    }

    /**
     * Re-price every invoice this cargo belongs to.
     *
     * When the invoice already carries a CBM/KG price and an exchange rate (the Laravel
     * "Edit Invoice" screen), the amount is rebuilt from the verified weight of all its
     * cargo: amount = total weight x price x exchange rate. Otherwise only the line value
     * of this cargo is adjusted, keeping the behaviour of the old screen.
     */
    private function repriceInvoices(Loadpackage $load, float $oldWeight, float $newWeight): float
    {
        $difference = $newWeight - $oldWeight;
        $totalImpact = 0.0;

        $invoiceIds = InvoiceLoad::where('loadpackage_id', $load->id)->pluck('invoice_id')->unique();

        foreach ($invoiceIds as $invoiceId) {
            $invoice = Invoice::find($invoiceId);

            if (! $invoice) {
                continue;
            }

            $price = (float) $invoice->cbm_price;
            $fx = (float) ($invoice->exchange_rate ?: 1);
            $before = (float) $invoice->total_amount;

            if ($price > 0) {
                // Summed in PHP: verified_weight_value is numeric, load_weight_value is varchar,
                // and COALESCE of the two is rejected by PostgreSQL.
                $totalWeight = Loadpackage::weightForInvoice($invoice->id);
                $newTotal = round($totalWeight * $price * $fx, 2);
            } else {
                $newTotal = round($before + ($difference * (float) $load->load_price), 2);
            }

            $delta = round($newTotal - $before, 2);

            if (abs($delta) > 0.004) {
                $invoice->update([
                    'total_amount' => max($newTotal, 0),
                    'total_balance' => max((float) $invoice->balanceDue() + $delta, 0),
                ]);
                $totalImpact += $delta;
            }

            // Keep the line in step with the new weight, for the load list and the PDFs.
            InvoiceLoad::where('invoice_id', $invoice->id)
                ->where('loadpackage_id', $load->id)
                ->update(['load_price' => (string) ($price > 0 ? round($newWeight * $price * $fx, 2) : $load->load_price)]);
        }

        $load->forceFill(['weight_price_impact' => round($totalImpact, 2)])->save();

        return $totalImpact;
    }

    private function unit(Loadpackage $load): string
    {
        return optional($load->unittype)->unittype_name ?? '';
    }

    private function fmt(float $value): string
    {
        return rtrim(rtrim(number_format($value, 3, '.', ''), '0'), '.');
    }
}
