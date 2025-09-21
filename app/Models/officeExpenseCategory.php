<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class officeExpenseCategory extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'client_id',
    ];

    public function client() {
        return $this->belongsTo(Client::class);
    }

    public function Expenses(){
        return $this->belongsTo(OfficeExpense::class);
    }
}
