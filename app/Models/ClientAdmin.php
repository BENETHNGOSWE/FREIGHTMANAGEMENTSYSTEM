<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClientAdmin extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = [
        'firstname',
        'middlename',
        'lastname',
        'username',
        'email',
        'phone',
        'password',
        'user_id',
        'client_id'
    ];

    protected $searchableFields = ['*'];

    protected $table = 'client_admins';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function invoiceload()
    {
        return $this->belongsTo(InvoiceLoad::class);
    }

    public function kodapackage()
    {
        return $this->belongsTo(Kodapackage::class);
    }
}
