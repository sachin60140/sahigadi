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

    public function records(): HasMany
    {
        return $this->hasMany(CustomerServiceHistoryRecord::class, 'customer_service_history_id');
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
    public static function checkCache(string $vehicleNumber, string $customerPhone): ?self
    {
        if ($customerPhone === '') {
            return null;
        }

        return static::where('vehicle_number', strtoupper($vehicleNumber))
            ->where('customer_phone', $customerPhone)
            ->where('is_success', true)
            ->where('created_at', '>=', now()->subHours(24))
            ->first();
    }
}

class CustomerServiceHistoryRecord extends Model
{
    protected $table = 'csh_records';

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
