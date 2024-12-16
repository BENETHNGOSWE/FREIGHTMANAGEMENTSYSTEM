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
    ];

    protected $searchableFields = ['*'];

    protected $casts = [
        'start_date' => 'date',
        'expire_date' => 'date',
    ];

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
