<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customermessage extends Model
{
    use HasFactory;
    protected $fillable = ['client_id', 'messageamount'];

    protected $searchableFields = ['*'];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
