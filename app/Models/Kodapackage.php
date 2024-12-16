<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kodapackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'monthly_fee',
        'yearly_fee',
        'staff_type_id',
        'client_admin_id',
    ];

    protected $searchableFields = ['*'];

    
    public function clientadmin()
    {
        return $this->belongsTo(ClientAdmin::class);
    }

    public function stafftype()
    {
        return $this->belongsTo(StaffType::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class, 'package_id');
    }
}
