<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MarutiServiceHistory extends Model
{
    protected $table = 'maruti_service_histories';

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
        return $this->hasMany(MarutiServiceHistoryRecord::class, 'maruti_service_history_id');
    }
}

class MarutiServiceHistoryRecord extends Model
{
    protected $table = 'maruti_service_records';

    protected $fillable = [
        'maruti_service_history_id',
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
    ];

    public function serviceHistory(): BelongsTo
    {
        return $this->belongsTo(MarutiServiceHistory::class, 'maruti_service_history_id');
    }
}
