<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CargoStatusHistory extends Model
{
    protected $fillable = ['client_id', 'loadpackage_id', 'status', 'note', 'changed_by'];

    public function loadpackage(): BelongsTo
    {
        return $this->belongsTo(Loadpackage::class, 'loadpackage_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
