<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Message extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'body',
        'status',
        'opened',
        'client_id',
    ];

    protected $searchableFields = ['*'];

    protected $casts = [
        'opened' => 'boolean',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
