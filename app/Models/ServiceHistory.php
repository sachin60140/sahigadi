<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceHistory extends Model
{
    protected $fillable = [
        'dealer_id',
        'vehicle_number',
        'is_success',
        'debit_amount',
        'error_message',
        'raw_response',
    ];

    protected $casts = [
        'is_success' => 'boolean',
        'debit_amount' => 'decimal:2',
        'raw_response' => 'array',
    ];

    public function dealer(): BelongsTo
    {
        return $this->belongsTo(Dealer::class);
    }

    public function records(): HasMany
    {
        return $this->hasMany(ServiceHistoryRecord::class);
    }
}

class ServiceHistoryRecord extends Model
{
    protected $fillable = [
        'service_history_id',
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

    public function serviceHistory(): BelongsTo
    {
        return $this->belongsTo(ServiceHistory::class);
    }
}
