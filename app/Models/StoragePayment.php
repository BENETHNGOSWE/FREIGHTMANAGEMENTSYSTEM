<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoragePayment extends Model
{
    protected $fillable = [
        'client_id', 'cargo_storage_id', 'loadpackage_id', 'customer_id', 'amount',
        'payment_date', 'payment_method', 'reference_no', 'received_by', 'notes',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function storage(): BelongsTo
    {
        return $this->belongsTo(CargoStorage::class, 'cargo_storage_id');
    }

    public function loadpackage(): BelongsTo
    {
        return $this->belongsTo(Loadpackage::class, 'loadpackage_id');
    }
}
