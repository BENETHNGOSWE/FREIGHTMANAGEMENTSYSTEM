<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PickupNote extends Model
{
    protected $fillable = [
        'client_id', 'invoice_id', 'customer_id', 'note_no', 'qr_token',
        'status', 'issued_by', 'issued_at', 'used_at', 'notes',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
        'used_at' => 'datetime',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function isValid(): bool
    {
        return $this->status === 'issued';
    }
}
