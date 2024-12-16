<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use App\Models\ClientAdmin;
use App\Models\User;
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


    public function admin()
    {
        $admin = ClientAdmin::where('client_id', $this->id)->first();
        if ($admin) {
            $user = User::where('id', $admin->user_id)->first();

            return $user;
        } else {
            return null;
        }

    }


    // public function country()
    // {
    //     return $this->belongsTo(Country::class);
    // }

    public function clientType()
    {
        return $this->belongsTo(ClientType::class)->value('name');
    
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function branches()
    {
        return $this->hasMany(Branch::class);
    }

    public function containers()
    {
        return $this->hasMany(Container::class);
    }

    public function customercategory()
    {
        return $this->hasMany(Customercategory::class);
    }

    public function customerlead()
    {
        return $this->hasMany(Customerlead::class);
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

    public function loadpackage()
    {
        return $this->hasMany(Loadpackage::class);
    }

    public function modules()
    {
        return $this->hasMany(Module::class);
    }

    public function packages()
    {
        return $this->hasMany(Package::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function sendernames()
    {
        return $this->belongsToMany(Sendername::class, 'client_sendername');
    }

    public function subscriptionInvoices()
    {
        return $this->hasMany(SubscriptionInvoice::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function invoiceloads()
    {
        return $this->hasMany(InvoiceLoad::class);
    }
}
