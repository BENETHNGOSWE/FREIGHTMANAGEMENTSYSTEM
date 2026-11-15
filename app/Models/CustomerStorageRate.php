<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerStorageRate extends Model
{
    protected $fillable = ['client_id', 'customer_id', 'free_days', 'daily_rate', 'notes'];

    protected $casts = [
        'daily_rate' => 'decimal:2',
        'free_days' => 'integer',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
