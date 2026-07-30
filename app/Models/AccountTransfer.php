<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountTransfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'from_account_id',
        'to_account_id',
        'amount',
        'admin_fee',
        'date',
        'notes',
        'from_transaction_id',
        'to_transaction_id',
        'fee_transaction_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'admin_fee' => 'decimal:2',
        'date' => 'date',
    ];

    public function fromAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'from_account_id');
    }

    public function toAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'to_account_id');
    }

    public function fromTransaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'from_transaction_id');
    }

    public function toTransaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'to_transaction_id');
    }

    public function feeTransaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'fee_transaction_id');
    }
}
