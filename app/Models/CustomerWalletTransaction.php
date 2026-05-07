<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerWalletTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_wallet_id',
        'amount',
        'type',
        'remark',
        'reference_id',
        'reference_type',
        'is_duplicate_check',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_duplicate_check' => 'boolean',
    ];

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(CustomerWallet::class, 'customer_wallet_id');
    }
}
