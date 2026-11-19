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
        'used_at',
        'used_by',
    ];

    protected $searchableFields = ['*'];

    protected $table = 'subscription_tokens';

    protected $casts = [
        'expire_date' => 'date',
        'status' => 'boolean',
        'used_at' => 'datetime',
    ];

    /** Still usable? active, not used, not past its own expiry date. */
    public function isValid(): bool
    {
        return $this->status
            && $this->used_at === null
            && (! $this->expire_date || ! $this->expire_date->isPast());
    }

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
