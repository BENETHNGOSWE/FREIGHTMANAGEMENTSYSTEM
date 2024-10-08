<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;
    protected $fillable = [
        'invoice_no',
        'prefix',
        'total_amount',
        'total_balance',
        'total_transportfee',
        'status',
        'customer_id',
        'client_id',
    ];

    // Optional: Define relationships if needed
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function clientadmin()
    {
        return $this->belongsTo(ClientAdmin::class);
    }

    public function invoiceload()
    {
        return $this->hasMany(InvoiceLoad::class);
    }

    public function loadpackage()
    {
        return $this->hasMany(Loadpackage::class);
    }
}
