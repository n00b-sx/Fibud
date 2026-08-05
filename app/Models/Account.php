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
        'type',
        'account_number',
        'initial_balance',
    ];

    protected $casts = [
        'initial_balance' => 'decimal:2',
    ];

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'ewallet' => 'E-Wallet',
            'cash' => 'Tunai',
            default => 'Bank',
        };
    }

    public function getOpenmojiIconUrlAttribute(): string
    {
        return match($this->type) {
            'ewallet' => 'https://cdn.jsdelivr.net/npm/openmoji@15.1.0/color/svg/1F4F1.svg', // Smartphone 📱
            'cash' => 'https://cdn.jsdelivr.net/npm/openmoji@15.1.0/color/svg/1F4B5.svg', // Banknote 💵
            default => 'https://cdn.jsdelivr.net/npm/openmoji@15.1.0/color/svg/1F3E6.svg', // Bank 🏦
        };
    }

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
