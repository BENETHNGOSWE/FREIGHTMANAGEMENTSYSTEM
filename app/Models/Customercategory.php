<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customercategory extends Model
{
    use HasFactory;
    protected $fillable = [
        'categoryname',
        'categorydescription',
        'client_id',
       
    ];

    protected $table = 'customercategories';

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function customerleads()
    {
        return $this->belongsToMany(Customerlead::class, 'customerlead_category', 'category_id', 'customerlead_id')
                    ->withTimestamps();
    }

    
}
