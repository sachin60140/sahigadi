<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerMahindraServiceHistory extends Model
{
    protected $fillable = [
        'customer_name',
        'customer_email',
        'customer_phone',
        'vehicle_number',
        'is_success',
        'paid_amount',
        'razorpay_payment_id',
        'razorpay_order_id',
        'error_message',
        'raw_response',
    ];

    protected $casts = [
        'is_success' => 'boolean',
        'paid_amount' => 'decimal:2',
        'raw_response' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function records()
    {
        return $this->hasMany(CustomerMahindraServiceHistoryRecord::class, 'customer_mahindra_service_history_id');
    }

    /**
     * A previous result for this vehicle, belonging to THIS customer.
     *
     * The scope is the whole point. This used to match on the vehicle number
     * alone, so one customer's paid report was served to anyone else who typed
     * the same number within 24 hours - free, unauthenticated, and including
     * the registered owner's name, address and mobile. The phone is a required
     * argument rather than an optional one so the scope cannot be dropped by
     * accident again.
     */
    public static function checkCache(string $vehicleNumber, string $customerPhone)
    {
        if ($customerPhone === '') {
            return null;
        }

        return static::where('vehicle_number', $vehicleNumber)
            ->where('customer_phone', $customerPhone)
            ->where('is_success', true)
            ->where('created_at', '>=', now()->subHours(24))
            ->orderBy('created_at', 'desc')
            ->first();
    }
}
