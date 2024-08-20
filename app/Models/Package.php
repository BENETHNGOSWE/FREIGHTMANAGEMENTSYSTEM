<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Package extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = [
        'name',
        'monthly_fee',
        'yearly_fee',
        'features',
        'client_id',
    ];

    protected $searchableFields = ['*'];

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function modules()
    {
        return $this->belongsToMany(Module::class);
    }
}
