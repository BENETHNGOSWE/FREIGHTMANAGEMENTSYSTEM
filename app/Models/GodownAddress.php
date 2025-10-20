<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GodownAddress extends Model
{
    use HasFactory;
    protected $fillable = [
        'godown_location',
        'godown_address_one',
        'godown_address_two',
        'godown_phonenumber',
        'client_id',
    ];

    public function clients(){
        return $this->belongsTo(Client::class);
    }
}
