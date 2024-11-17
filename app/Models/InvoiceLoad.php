<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceLoad extends Model
{
    use HasFactory;
    protected $fillable = [
        'load_price',
        'loadpackage_id',
        'invoice_id',
        'client_id',
    ];

    // Optional: Define relationships if needed
    public function loadpackage()
    {
        return $this->belongsTo(Loadpackage::class, 'loadpackage_id');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
