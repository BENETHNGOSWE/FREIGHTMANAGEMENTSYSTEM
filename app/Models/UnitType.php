<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitType extends Model
{
    use HasFactory;

    protected $fillable = ['client_id', 'unittype_name', 'unitytype_initial'];

    protected $searchableFields = ['*'];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function loadpackages()
    {
        return $this->hasmany(Loadpackage::class, 'unittype_id');
    }
}
