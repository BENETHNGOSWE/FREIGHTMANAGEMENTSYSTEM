<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Containerloading extends Model
{
    use HasFactory;
    protected $fillable = [
        'loadpackage_id',
        'container_id',
        'client_id',
    ];

    public function loadpackage()
    {
        return $this->belongsTo(Loadpackage::class, 'loadpackage_id');
    }

    public function container()
    {
        return $this->belongsTo(Container::class, 'container_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }
}
