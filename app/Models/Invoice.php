<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;
    protected $fillable = ['invoice_no', 'prefix', 'total_amount', 'total_balance', 'total_transportfee', 'status', 'customer_id', 'client_id', 'user_id','exchange_rate','cbm_price'];

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

    public function deliveryOrders()
    {
        return $this->hasMany(DeliveryOrder::class);
    }

    public function invoiceLoads()
    {
        return $this->hasMany(InvoiceLoad::class);
    }

    public function loadpackages()
    {
        return $this->belongsToMany(Loadpackage::class, 'invoice_loads', 'invoice_id', 'loadpackage_id')
                ->withPivot('load_price', 'client_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
