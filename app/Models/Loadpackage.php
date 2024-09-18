<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loadpackage extends Model
{
    use HasFactory;
    protected $fillable = [
        'load_no',
        'load_description',
        'load_name',
        'load_location',
        'client_id',
        'loadpackagetype_id',
        'customer_id',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function loadpackagetype()
    {
        return $this->belongsTo(Loadpackagetype::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
