<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class JournalEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id', 'entry_no', 'posting_date', 'entry_type', 'reference_doctype',
        'reference_id', 'reference_no', 'status', 'total_debit', 'total_credit',
        'remarks', 'is_opening', 'is_auto', 'reversal_of_id', 'created_by', 'posted_at',
    ];

    protected $casts = [
        'posting_date' => 'date',
        'posted_at' => 'datetime',
        'is_opening' => 'boolean',
        'is_auto' => 'boolean',
        'total_debit' => 'decimal:2',
        'total_credit' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $entry) {
            if (! $entry->created_by && Auth::check()) {
                $entry->created_by = Auth::id();
            }
            if (! $entry->posting_date) {
                $entry->posting_date = now()->toDateString();
            }
        });
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function lines(): HasMany
    {
        return $this->hasMany(JournalEntryAccount::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reversalOf(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class, 'reversal_of_id');
    }

    public function scopeForClient($query, int $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    public function scopePosted($query)
    {
        return $query->where('status', 'posted');
    }

    public function isEditable(): bool
    {
        return $this->status === 'posted' && ! $this->is_auto;
    }

    public function isBalanced(): bool
    {
        return abs((float) $this->total_debit - (float) $this->total_credit) < 0.005;
    }

    /** Human label of what created this entry. */
    public function sourceLabel(): string
    {
        return match ($this->entry_type) {
            'Sales Invoice' => 'Invoice ' . ($this->reference_no ?: '#'.$this->reference_id),
            'Payment Entry' => 'Payment ' . ($this->reference_no ?: '#'.$this->reference_id),
            'Expense Entry' => 'Expense ' . ($this->reference_no ?: '#'.$this->reference_id),
            'Opening Entry' => 'Opening balances',
            default => $this->entry_no ?: 'Journal entry',
        };
    }
}
