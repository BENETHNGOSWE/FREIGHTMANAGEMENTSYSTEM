<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SubscriptionToken extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = [
        'subscription_id',
        'subscription_invoice_id',
        'token_',
        'expire_date',
        'status',
        'client_id',
    ];

    protected $searchableFields = ['*'];

    protected $table = 'subscription_tokens';

    protected $casts = [
        'expire_date' => 'date',
        'status' => 'boolean',
    ];

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function subscriptionInvoice()
    {
        return $this->belongsTo(SubscriptionInvoice::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
