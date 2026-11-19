<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Subscription extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = [
        'client_id',
        'start_date',
        'expire_date',
        'package_id',
        'duration_in_month',
        'status',
        'product',
        'sms_quota',
        'sms_used',
        'amount',
        'locked_at',
        'notes',
    ];

    protected $searchableFields = ['*'];

    protected $casts = [
        'start_date' => 'date',
        'expire_date' => 'date',
        'locked_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    /**
     * Does the database actually have the platform columns?
     * A half-migrated or older install must not take the owner screens down.
     */
    public static function platformColumnsReady(): bool
    {
        static $ready = null;

        if ($ready === null) {
            $ready = \Illuminate\Support\Facades\Schema::hasTable('subscriptions')
                && \Illuminate\Support\Facades\Schema::hasColumn('subscriptions', 'amount');
        }

        return $ready;
    }

    /** How the subscription reads on screen: active / grace / expired / locked. */
    public function health(): string
    {
        return app(\App\Services\Platform\SubscriptionService::class)->status($this->client);
    }

    public function smsLeft(): int
    {
        return max((int) $this->sms_quota - (int) \App\Models\Message::where('client_id', $this->client_id)->count(), 0);
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function kodapackages()
    {
        return $this->belongsTo(Kodapackage::class, 'package_id');
    }

    public function subscriptionInvoice()
    {
        return $this->hasOne(SubscriptionInvoice::class);
    }

    public function subscriptionTokens()
    {
        return $this->hasMany(SubscriptionToken::class);
    }
}
