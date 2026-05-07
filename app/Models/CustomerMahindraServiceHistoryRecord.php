<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerMahindraServiceHistoryRecord extends Model
{
    protected $fillable = [
        'customer_mahindra_service_history_id',
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
        'repair_order_bill_date' => 'date',
        'svc_date' => 'date',
    ];

    public function serviceHistory()
    {
        return $this->belongsTo(CustomerMahindraServiceHistory::class, 'customer_mahindra_service_history_id');
    }
}
