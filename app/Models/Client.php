<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Client extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = [
        'client_name',
        'client_logo',
        'client_location',
        'client_address_one',
        'client_address_two',
        'client_tinnumber',
        'client_phonenumber',
        'client_email',
        'client_city',
        'client_country',
        'client_zipcode',
        'client_fax',
        'client_website_link',
        'client_type_id',
    ];
    

    protected $searchableFields = ['*'];

    // public function country()
    // {
    //     return $this->belongsTo(Country::class);
    // }

    public function clientType()
    {
        return $this->belongsTo(ClientType::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function branches()
    {
        return $this->hasMany(Branch::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function subscriptionTokens()
    {
        return $this->hasMany(SubscriptionToken::class);
    }

    public function clientAdmins()
    {
        return $this->hasMany(ClientAdmin::class);
    }

    public function modules()
    {
        return $this->hasMany(Module::class);
    }

    public function packages()
    {
        return $this->hasMany(Package::class);
    }

    public function subscriptionInvoices()
    {
        return $this->hasMany(SubscriptionInvoice::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}
