<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customerlead extends Model
{
    use HasFactory;
    protected $fillable = ['customer_no', 'customer_firstname', 'customer_middlename', 'customer_lastname', 'customer_address', 'customer_photo', 'customer_email', 'customer_city', 'customer_country', 'phone', 'customer_document', 'customer_bio', 'department_id', 'user_id', 'client_id', 'category_id'];

    public function customercategory()
    {
        return $this->belongsTo(Customercategory::class, 'category_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

 
}
