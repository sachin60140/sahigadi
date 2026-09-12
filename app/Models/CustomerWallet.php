<?php

namespace App\Models;

use App\Models\Concerns\LocksWalletBalance;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomerWallet extends Model
{
    // addFunds() / deductFunds() come from the trait, which mutates the balance
    // under a row lock so concurrent requests cannot spend the same funds twice.
    use HasFactory, LocksWalletBalance;

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
}
