<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesReport extends Model
{
    use HasFactory;
    protected $fillable = [
        'receipt',
        'date',
        'cbm',
        'nature',
        'hub',
        'amount1',
        'amount2',
        'client_id',
        'customer_id',
        'staff_id',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
    
    public function customers()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function staffs()
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }
}
