<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomerServiceHistory extends Model
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
        'is_refunded',
        'razorpay_refund_id',
    ];

    protected $casts = [
        'is_success' => 'boolean',
        'is_refunded' => 'boolean',
        'paid_amount' => 'decimal:2',
        'raw_response' => 'array',
    ];

    public function records(): HasMany
    {
        return $this->hasMany(CustomerServiceHistoryRecord::class, 'customer_service_history_id');
    }

    public static function checkCache(string $vehicleNumber): ?self
    {
        return static::where('vehicle_number', strtoupper($vehicleNumber))
            ->where('is_success', true)
            ->where('created_at', '>=', now()->subHours(24))
            ->first();
    }
}

class CustomerServiceHistoryRecord extends Model
{
    protected $table = 'customer_service_history_records';

    protected $fillable = [
        'customer_service_history_id',
        'chassis_no',
        'location_code',
        'location_name',
        'mileage',
        'net_bill_amt',
        'online_payment_flag',
        'out_standing_amt',
        'paid_amt',
        'dealer_code',
        'dealer_name',
        'repair_order_bill_date',
        'repair_order_bill_no',
        'svc_date',
        'repair_order_no',
        'register_no',
        'service_assistant_no',
        'service_assistant_name',
        'work_type',
        'status',
        'service_cate',
    ];

    protected $casts = [
        'net_bill_amt' => 'decimal:2',
        'out_standing_amt' => 'decimal:2',
        'paid_amt' => 'decimal:2',
        'repair_order_bill_date' => 'date',
        'svc_date' => 'date',
    ];

    public function serviceHistory(): BelongsTo
    {
        return $this->belongsTo(CustomerServiceHistory::class, 'customer_service_history_id');
    }
}
