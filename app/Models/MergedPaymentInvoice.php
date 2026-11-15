<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MergedPaymentInvoice extends Model
{
    protected $fillable = ['merged_payment_id', 'invoice_id', 'amount_applied', 'old_total', 'new_total'];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}
