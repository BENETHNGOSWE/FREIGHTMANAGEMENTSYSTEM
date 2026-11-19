<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountingSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id', 'receivable_account_id', 'income_account_id', 'transport_income_account_id',
        'storage_income_account_id',
        'cash_account_id', 'bank_account_id', 'mobile_money_account_id', 'expense_account_id',
        'payable_account_id', 'equity_account_id', 'fiscal_year_start_month',
        'auto_post_invoices', 'auto_post_payments', 'auto_post_expenses',
    ];

    protected $casts = [
        'auto_post_invoices' => 'boolean',
        'auto_post_payments' => 'boolean',
        'auto_post_expenses' => 'boolean',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function receivable(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'receivable_account_id');
    }

    public function income(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'income_account_id');
    }

    public function transportIncome(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'transport_income_account_id');
    }

    public function cash(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'cash_account_id');
    }

    public function storageIncome(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'storage_income_account_id');
    }

    public function bank(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'bank_account_id');
    }

    public function mobileMoney(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'mobile_money_account_id');
    }

    public function expense(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'expense_account_id');
    }

    public function payable(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'payable_account_id');
    }

    public function equity(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'equity_account_id');
    }
}
