<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loadpackagetype extends Model
{
    use HasFactory;
    protected $fillable = [
        'load_type_name',
        'load_type_description',
        'client_id',
       
    ];

    protected $table = 'loadpackagetypes';

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function loadpackage()
    {
        return $this->belongsTo(Loadpackage::class);
    }
}
