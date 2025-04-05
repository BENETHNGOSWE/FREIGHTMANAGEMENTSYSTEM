<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Companyinvoicedetail extends Model
{
    use HasFactory;
    protected $fillable = [
        'company_name',
        'office_phone',
        'tin_number',
        'tagline',
        'logo',
        'client_id',
    ];

    protected $table = 'companyinvoicedetails';

    public function client(){
        return $this->belongsTo(Client::class);
    }
}
