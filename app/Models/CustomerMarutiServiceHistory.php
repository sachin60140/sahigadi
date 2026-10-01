<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomerMarutiServiceHistory extends Model
{
    protected $table = 'cust_maruti_services';

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
        return $this->hasMany(CustomerMarutiServiceHistoryRecord::class, 'cust_maruti_service_id');
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

class CustomerMarutiServiceHistoryRecord extends Model
{
    protected $table = 'cust_maruti_records';

    protected $fillable = [
        'cust_maruti_service_id',
        'chassis_no',
        'location_code',
        'dealer_code',
        'dealer_name',
        'dealer_address',
        'mileage',
        'total_amount',
        'part_amount',
        'labour_amount',
        'repair_order_bill_date',
        'repair_order_bill_no',
        'svc_date',
        'repair_order_no',
        'register_no',
        'service_assistant_name',
        'work_type',
        'status',
        'service_cate',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'part_amount' => 'decimal:2',
        'labour_amount' => 'decimal:2',
        'svc_date' => 'date',
        'repair_order_bill_date' => 'date',
    ];

    public function serviceHistory(): BelongsTo
    {
        return $this->belongsTo(CustomerMarutiServiceHistory::class, 'cust_maruti_service_id');
    }
}
