<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * One node of the chart of accounts (same idea as ERPNext's Account doctype).
 *
 * Debit-normal: Asset, Expense. Credit-normal: Liability, Equity, Income.
 */
class Account extends Model
{
    use HasFactory;

    public const ROOT_TYPES = ['Asset', 'Liability', 'Equity', 'Income', 'Expense'];

    public const ACCOUNT_TYPES = [
        'Bank', 'Cash', 'Receivable', 'Payable', 'Tax', 'Equity',
        'Income Account', 'Expense Account', 'Cost of Goods Sold',
        'Fixed Asset', 'Current Asset', 'Current Liability', 'Fixed Liability', 'Stock',
    ];

    protected $fillable = [
        'client_id', 'parent_id', 'account_number', 'name', 'root_type', 'account_type',
        'is_group', 'currency', 'opening_balance', 'opening_date', 'disabled', 'description',
    ];

    protected $casts = [
        'is_group' => 'boolean',
        'disabled' => 'boolean',
        'opening_balance' => 'decimal:2',
        'opening_date' => 'date',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Account::class, 'parent_id')->orderBy('account_number')->orderBy('name');
    }

    public function lines(): HasMany
    {
        return $this->hasMany(JournalEntryAccount::class);
    }

    public function scopeForClient($query, int $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    public function scopeActive($query)
    {
        return $query->where('disabled', false);
    }

    public function scopeLeaves($query)
    {
        return $query->where('is_group', false);
    }

    public function scopeOfRoot($query, string $rootType)
    {
        return $query->where('root_type', $rootType);
    }

    /** Asset and Expense grow on the debit side. */
    public function isDebitNormal(): bool
    {
        return in_array($this->root_type, ['Asset', 'Expense'], true);
    }

    public function isBalanceSheet(): bool
    {
        return in_array($this->root_type, ['Asset', 'Liability', 'Equity'], true);
    }

    /** Full path, e.g. "Assets / Bank Accounts / CRDB Bank". */
    public function path(): string
    {
        $names = [$this->name];
        $parent = $this->parent;
        $guard = 0;

        while ($parent && $guard < 12) {
            array_unshift($names, $parent->name);
            $parent = $parent->parent;
            $guard++;
        }

        return implode(' / ', $names);
    }

    /** True when the account can receive postings. */
    public function isPostable(): bool
    {
        return ! $this->is_group && ! $this->disabled;
    }
}
