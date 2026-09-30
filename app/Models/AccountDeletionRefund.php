<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * A wallet balance left behind when a customer deleted their account.
 *
 * A customer cannot withdraw their own balance - they can only spend it on a
 * featured listing, and the only refund path is admin-side - so deletion does
 * not block on an empty wallet and does not quietly absorb the money either.
 * The balance is recorded here and returned out of band.
 *
 * The phone number is kept on purpose, and only for as long as it takes to make
 * the refund. That is disclosed on the public account deletion page.
 */
class AccountDeletionRefund extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_REFUNDED = 'refunded';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'customer_id',
        'contact_phone',
        'contact_name',
        'balance',
        'status',
        'requested_at',
        'refunded_at',
        'reference',
        'notes',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'requested_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];

    /**
     * Contact details are only for making the refund, never for display on a
     * public surface.
     */
    protected $hidden = [
        'contact_phone',
        'contact_name',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class)->withTrashed();
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Mark the money as returned. The contact details go at the same time:
     * they were only ever kept in order to do this.
     */
    public function markRefunded(?string $reference = null): void
    {
        $this->forceFill([
            'status' => self::STATUS_REFUNDED,
            'refunded_at' => now(),
            'reference' => $reference,
            'contact_phone' => '',
            'contact_name' => null,
        ])->save();
    }
}
