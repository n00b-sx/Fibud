<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'account_number',
        'initial_balance',
    ];

    protected $casts = [
        'initial_balance' => 'decimal:2',
    ];

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function transfersFrom(): HasMany
    {
        return $this->hasMany(AccountTransfer::class, 'from_account_id');
    }

    public function transfersTo(): HasMany
    {
        return $this->hasMany(AccountTransfer::class, 'to_account_id');
    }

    public function getBalanceAttribute(): float
    {
        $income = $this->transactions()->whereHas('category', fn ($q) => $q->where('type', 'income'))->sum('amount');
        $expense = $this->transactions()->whereHas('category', fn ($q) => $q->where('type', 'expense'))->sum('amount');

        return (float) $this->initial_balance + $income - $expense;
    }
}
