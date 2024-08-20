<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SubscriptionInvoice extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = [
        'amount',
        'balance',
        'bill_date',
        'due_date',
        'status',
        'subscription_id',
        'client_id',
    ];

    protected $searchableFields = ['*'];

    protected $table = 'subscription_invoices';

    protected $casts = [
        'bill_date' => 'date',
        'due_date' => 'date',
    ];

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function subscriptionTokens()
    {
        return $this->hasMany(SubscriptionToken::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
