<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClientPayment extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = ['amount', 'date', 'payment_type_id'];

    protected $searchableFields = ['*'];

    protected $table = 'client_payments';

    protected $casts = [
        'date' => 'date',
    ];

    public function paymentType()
    {
        return $this->belongsTo(PaymentType::class);
    }
}
