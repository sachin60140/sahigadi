<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WalletTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'wallet_id',
        'amount',
        'type',
        'remark',
        'reference_id',
        'reference_type',
        'is_duplicate_check',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
        ];
    }

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class, 'razorpay_payment_id', 'reference_id')
            ->orWhere('phonepe_transaction_id', $this->reference_id);
    }
}
