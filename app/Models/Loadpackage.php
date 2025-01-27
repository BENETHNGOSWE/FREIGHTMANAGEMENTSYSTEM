<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loadpackage extends Model
{
    use HasFactory;
    protected $fillable = ['load_no', 'load_description', 'load_name', 'load_price', 'load_transport_date', 'load_delivery_date', 'load_weight_value', 'load_trucking_number', 'client_id', 'loadpackagetype_id', 'customer_id', 'unittype_id', 'user_id', 'container_id', 'load_length', 'load_width', 'load_height', 'load_volume'];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function container()
    {
        return $this->belongsTo(Container::class, 'container_id');
    }

    public function containerLoadings()
    {
        return $this->hasMany(ContainerLoading::class, 'loadpackage_id');
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

    public function invoiceLoads()
    {
        return $this->hasMany(InvoiceLoad::class, 'loadpackage_id');
    }

    public function getRelatedInvoiceIds()
    {
        return $this->invoiceLoads()->pluck('invoice_id')->unique();
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($loadPackage) {
            $invoiceIds = $loadPackage->getRelatedInvoiceIds();

            $loadPackage->invoiceLoads()->delete();
            foreach ($invoiceIds as $invoiceId) {
                $remainingLoads = InvoiceLoad::where('invoice_id', $invoiceId)->count();
                if ($remainingLoads === 0) {
                    Invoice::find($invoiceId)->delete();
                }
            }
        });
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
