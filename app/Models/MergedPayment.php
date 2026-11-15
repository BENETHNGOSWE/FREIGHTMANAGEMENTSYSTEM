<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MergedPayment extends Model
{
    protected $fillable = [
        'client_id', 'customer_id', 'reference_no', 'amount', 'payment_date', 'payment_method',
        'status', 'billable_weight', 'combined_weight', 'minimum_applied', 'reprice_difference',
        'received_by', 'notes',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
        'combined_weight' => 'decimal:3',
        'billable_weight' => 'decimal:3',
        'minimum_applied' => 'boolean',
    ];

    public function lines(): HasMany
    {
        return $this->hasMany(MergedPaymentInvoice::class);
    }
}
