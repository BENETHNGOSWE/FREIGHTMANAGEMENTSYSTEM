<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clientactivename extends Model
{
    use HasFactory;
    protected $fillable = ['client_id', 'sendername_id'];

    
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    
    public function sendername()
    {
        return $this->belongsTo(Sendername::class);
    }
}
