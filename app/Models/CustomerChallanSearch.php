<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerChallanSearch extends Model
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
        'challan_data',
        'total_amount',
        'challan_count',
        'error_message',
        'is_refunded',
        'razorpay_refund_id',
    ];

    protected $casts = [
        'is_success' => 'boolean',
        'is_refunded' => 'boolean',
        'paid_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'challan_data' => 'array',
    ];

    public static function checkCache(string $vehicleNumber): ?self
    {
        return static::where('vehicle_number', strtoupper($vehicleNumber))
            ->where('is_success', true)
            ->where('created_at', '>=', now()->subHours(24))
            ->first();
    }
}
