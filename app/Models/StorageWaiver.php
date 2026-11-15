<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StorageWaiver extends Model
{
    protected $fillable = ['client_id', 'cargo_storage_id', 'loadpackage_id', 'amount', 'reason', 'waived_by'];

    protected $casts = ['amount' => 'decimal:2'];

    public function storage(): BelongsTo
    {
        return $this->belongsTo(CargoStorage::class, 'cargo_storage_id');
    }
}
