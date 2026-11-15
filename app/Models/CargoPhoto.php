<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CargoPhoto extends Model
{
    protected $fillable = ['client_id', 'loadpackage_id', 'path', 'caption', 'uploaded_by'];

    public function loadpackage(): BelongsTo
    {
        return $this->belongsTo(Loadpackage::class, 'loadpackage_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function url(): string
    {
        return asset('storage/' . ltrim($this->path, '/'));
    }
}
