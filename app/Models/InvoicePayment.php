<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoicePayment extends Model
{
    use HasFactory;
     protected $fillable = [
        'invoice_id', 'client_id', 'amount_received', 'payment_date',
        'payment_method', 'reference_no', 'received_by', 'notes',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount_received' => 'decimal:2',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function receivedBy()
    {
        return $this->belongsTo(User::class, 'received_by');
    }
}
