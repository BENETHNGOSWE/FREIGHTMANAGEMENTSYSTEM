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

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

 
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
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
}
