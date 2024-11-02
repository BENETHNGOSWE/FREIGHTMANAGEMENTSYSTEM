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
        'load_price',             
        'load_transport_date',    
        'load_delivery_date',         
        'load_weight_value',         
        'load_trucking_number',          
        'client_id',             
        'loadpackagetype_id',     
        'customer_id',
        'unittype_id',            
        'user_id',
        'container_id',            
    ];
    

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function container()
    {
        return $this->belongsTo(Container::class, 'container_id');
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function loadpackagetype()
    {
        return $this->belongsTo(Loadpackagetype::class);
    }

    public function unittype()
    {
        return $this->belongsTo(UnitType::class, 'unittype_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function invoiceload()
    {
        return $this->belongsTo(InvoiceLoad::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
