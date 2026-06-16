<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerVehicleSearch extends Model
{
    protected $fillable = [
        'customer_name',
        'customer_email',
        'customer_phone',
        'registration_number',
        'is_success',
        'paid_amount',
        'razorpay_payment_id',
        'razorpay_order_id',
        'vehicle_data',
        'error_message',
        'is_refunded',
        'razorpay_refund_id',
    ];

    protected $casts = [
        'is_success' => 'boolean',
        'is_refunded' => 'boolean',
        'paid_amount' => 'decimal:2',
        'vehicle_data' => 'array',
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

    public static function checkCache(string $registrationNumber): ?self
    {
        return static::where('registration_number', strtoupper($registrationNumber))
            ->where('is_success', true)
            ->where('created_at', '>=', now()->subHours(24))
            ->first();
    }
}
