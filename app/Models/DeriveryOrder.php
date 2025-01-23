<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeriveryOrder extends Model
{
    use HasFactory;
    protected $fillable = [
        'client_id',
        'invoice_id',
        'driver_name',
        'driver_number',
        'vehicle_reg_no',
    ];

    public function invoice(){
        return $this->belongsTo(Invoice::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
