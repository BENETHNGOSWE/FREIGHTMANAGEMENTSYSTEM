<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    protected $fillable = [
        'customer_no',
        'customer_firstname',
        'customer_middlename',
        'customer_lastname',
        'customer_address',
        'customer_photo',
        'customer_email',
        'customer_city',
        'customer_country',
        'phone',
        'customer_document',
        'customer_bio',
        'department_id',
        'customer_gender',
        'user_id',
        'client_id',
    ];
   

  

    public function users()
    {
        return $this->belongsTo( User::class, 'user_id');
    }
}
