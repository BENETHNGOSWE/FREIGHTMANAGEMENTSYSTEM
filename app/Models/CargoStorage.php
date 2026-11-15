<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CargoStorage extends Model
{
    protected $fillable = [
        'client_id', 'loadpackage_id', 'customer_id', 'started_on', 'free_days',
        'daily_rate', 'last_charged_on', 'days_charged', 'waived_amount', 'status', 'collected_at',
    ];

    protected $casts = [
        'started_on' => 'date',
        'last_charged_on' => 'date',
        'collected_at' => 'datetime',
        'daily_rate' => 'decimal:2',
        'waived_amount' => 'decimal:2',
    ];

    public function loadpackage(): BelongsTo
    {
        return $this->belongsTo(Loadpackage::class, 'loadpackage_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function charges(): HasMany
    {
        return $this->hasMany(StorageCharge::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(StoragePayment::class);
    }

    public function waivers(): HasMany
    {
        return $this->hasMany(StorageWaiver::class);
    }

    public function firstChargeDate(): ?\Illuminate\Support\Carbon
    {
        return $this->started_on ? $this->started_on->copy()->addDays((int) $this->free_days) : null;
    }

    public function chargedTotal(): float
    {
        return round((float) $this->charges()->sum('amount'), 2);
    }

    public function paidTotal(): float
    {
        return round((float) $this->payments()->sum('amount'), 2);
    }

    public function waivedTotal(): float
    {
        return round((float) $this->waivers()->sum('amount'), 2);
    }

    /** What the customer still owes for storage. Never negative. */
    public function balance(): float
    {
        return round(max(0, $this->chargedTotal() - $this->waivedTotal() - $this->paidTotal()), 2);
    }

    public function isSettled(): bool
    {
        return $this->balance() <= 0.0;
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
