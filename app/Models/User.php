<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use App\Models\Scopes\Searchable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasRoles;
    use Notifiable;
    use HasFactory;
    use Searchable;
    use HasApiTokens;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'photo',
        'password',
        'client_id',
        'user_type',
    ];
    

    protected $searchableFields = ['*'];
    protected $guard_name = 'web';

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getRoleNameAttribute()
    {
        return $this->roles->pluck('name')->first();
    }

  
 
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function customers()
    {
        return $this->hasMany(Customer::class, 'user_id'); // Assuming 'user_id' exists in 'customers' table
    }
    
    public function customercategories()
    {
        return $this->hasMany(Customercategory::class, 'user_id');
    }

    public function customerleads()
    {
        return $this->hasMany(Customerlead::class, 'user_id');
    }

    public function patients()
    {
        return $this->hasMany(Patient::class);
    }

    public function clientAdmins()
    {
        return $this->hasMany(ClientAdmin::class);
    }

    public function userRoles()
    {
        return $this->hasMany(UserRole::class);
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super-admin');
    }

    public function loadpackages()
    {
        return $this->hasMany(Loadpackage::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

     public function staff()
    {
        return $this->hasOne(Staff::class);
    }

    
}
