<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Container extends Model
{
    use HasFactory;

    protected $fillable = ['client_id', 'container_number', 'container_size', 'container_volume', 'container_status'];

    protected $searchableFields = ['*'];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function loadpackages()
    {
        return $this->hasMany(Loadpackage::class, 'container_id');
    }

    public function containerLoadings()
    {
        return $this->hasMany(ContainerLoading::class, 'container_id');
    }
}
