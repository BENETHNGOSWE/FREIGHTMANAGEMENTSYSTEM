<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Demorequest extends Model
{
    use HasFactory;
    protected $fillable = [
        'full_name',
        'email_address',
        'phone_number',
        'company_name',
        'city',
        'message',
    ];
}
