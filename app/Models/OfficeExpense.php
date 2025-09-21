<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfficeExpense extends Model
{
    use HasFactory;
    protected $fillable=[
        'expenses_description',
        'expenses_cost',
        'status',
        'client_id',
        'office_expense_categories_id'
    ];

    public function client(){
        return $this->belongsTo(Client::class);
    }

    public function categoryexpense() {
        return $this->belongsTo(officeExpenseCategory::class, 'office_expense_categories_id');
    }
}
