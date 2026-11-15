<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StorageSetting extends Model
{
    protected $fillable = [
        'client_id', 'free_days', 'daily_rate', 'currency', 'auto_accrue', 'charge_basis',
    ];

    protected $casts = [
        'auto_accrue' => 'boolean',
        'daily_rate' => 'decimal:2',
        'free_days' => 'integer',
    ];
}
