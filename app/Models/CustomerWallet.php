<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomerWallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'balance',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(CustomerWalletTransaction::class);
    }

    public function addFunds($amount, $remark = null, $referenceId = null, $referenceType = null)
    {
        $this->increment('balance', $amount);

        return $this->transactions()->create([
            'amount' => $amount,
            'type' => 'credit',
            'remark' => $remark,
            'reference_id' => $referenceId,
            'reference_type' => $referenceType,
        ]);
    }

    public function deductFunds($amount, $remark = null, $referenceId = null, $referenceType = null)
    {
        if ($this->balance < $amount) {
            throw new \Exception('Insufficient wallet balance');
        }

        $this->decrement('balance', $amount);

        return $this->transactions()->create([
            'amount' => $amount,
            'type' => 'debit',
            'remark' => $remark,
            'reference_id' => $referenceId,
            'reference_type' => $referenceType,
        ]);
    }
}
