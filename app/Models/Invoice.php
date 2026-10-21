<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;
    protected $fillable = ['invoice_no', 'prefix', 'total_amount', 'total_balance', 'total_transportfee', 'status', 'customer_id', 'client_id', 'user_id','exchange_rate','cbm_price'];

    
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
    public function invoicePayments()
    {
        return $this->hasMany(InvoicePayment::class);
    }

    /** What the customer must pay in total (your decision: amount + transport fee) */
    public function amountDue(): float
    {
        return (float) $this->total_amount + (float) $this->total_transportfee;
    }

    /** What has actually been received so far */
    public function totalReceived(): float
    {
        return (float) $this->invoicePayments()->sum('amount_received');
    }

    /** What remains. Never show a negative balance in the UI later. */
    public function balanceDue(): float
    {
        return $this->amountDue() - $this->totalReceived();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
