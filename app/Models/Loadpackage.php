<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loadpackage extends Model
{
    use HasFactory;
    protected $fillable = [
        'load_no', 'load_description', 'load_name', 'load_price', 'load_transport_date',
        'load_delivery_date', 'load_weight_value', 'load_trucking_number', 'client_id',
        'loadpackagetype_id', 'customer_id', 'unittype_id', 'user_id', 'container_id',
        'load_length', 'load_width', 'load_height', 'load_volume',
        // cargo registration / warehouse
        'china_location', 'batch_no', 'qr_token', 'cargo_status', 'arrived_at',
        'original_weight_value', 'verified_weight_value', 'verified_at', 'has_damage',
        'has_missing', 'has_wrong_item', 'verification_notes', 'weight_price_impact',
        'released_at', 'collected_at', 'released_by',
    ];

    protected $casts = [
        'arrived_at' => 'date',
        'verified_at' => 'datetime',
        'released_at' => 'datetime',
        'collected_at' => 'datetime',
        'has_damage' => 'boolean',
        'has_missing' => 'boolean',
        'has_wrong_item' => 'boolean',
        'weight_price_impact' => 'decimal:2',
    ];

    /** The journey a cargo walks through, in order. */
    public const STATUSES = [
        'Received' => 'Received (China warehouse)',
        'In Transit' => 'In Transit',
        'Arrived' => 'Arrived Tanzania',
        'Verified' => 'Verified (Dar warehouse)',
        'Invoiced' => 'Invoiced',
        'Paid' => 'Paid',
        'Ready for Pickup' => 'Ready for Pickup',
        'Collected' => 'Collected',
    ];

    public function photos()
    {
        return $this->hasMany(\App\Models\CargoPhoto::class, 'loadpackage_id')->latest();
    }

    public function statusHistories()
    {
        return $this->hasMany(\App\Models\CargoStatusHistory::class, 'loadpackage_id')->latest();
    }

    public function storage()
    {
        return $this->hasOne(\App\Models\CargoStorage::class, 'loadpackage_id');
    }

    public function getRelatedInvoices()
    {
        return \App\Models\Invoice::whereIn('id', $this->getRelatedInvoiceIds())->get();
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->cargo_status] ?? ($this->cargo_status ?: 'Received');
    }

    public function isCollected(): bool
    {
        return $this->cargo_status === 'Collected' || $this->collected_at !== null;
    }

    /** Weight used for pricing: the verified weight once the warehouse confirmed it. */
    public function billableWeight(): float
    {
        return (float) ($this->verified_weight_value ?? $this->load_weight_value);
    }

    /**
     * Total billable weight of every cargo on one invoice.
     *
     * The sum is done in PHP on purpose: on PostgreSQL verified_weight_value is numeric while
     * the old load_weight_value is a varchar, so "coalesce(...)" inside SQL blows up with
     * "COALESCE types numeric and character varying cannot be matched".
     */
    public static function weightForInvoice($invoiceId): float
    {
        $ids = \App\Models\InvoiceLoad::where('invoice_id', $invoiceId)->pluck('loadpackage_id')->all();

        if (empty($ids)) {
            return 0.0;
        }

        return (float) static::whereIn('id', $ids)->get()->sum(fn ($load) => $load->billableWeight());
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function containers()
    {
        return $this->belongsToMany(Container::class, 'containerloadings');
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
