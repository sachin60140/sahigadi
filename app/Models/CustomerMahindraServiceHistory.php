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

    public function records()
    {
        return $this->hasMany(CustomerMahindraServiceHistoryRecord::class, 'customer_mahindra_service_history_id');
    }

    public static function checkCache(string $vehicleNumber)
    {
        return static::where('vehicle_number', $vehicleNumber)
            ->where('is_success', true)
            ->where('created_at', '>=', now()->subHours(24))
            ->orderBy('created_at', 'desc')
            ->first();
    }
}
