<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminChallanSearch extends Model
{
    protected $fillable = [
        'dealer_id',
        'vehicle_number',
        'is_success',
        'charge_amount',
        'challan_data',
        'total_amount',
        'challan_count',
        'error_message',
    ];

    protected $casts = [
        'is_success' => 'boolean',
        'charge_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'challan_data' => 'array',
    ];

    public function dealer(): BelongsTo
    {
        return $this->belongsTo(Dealer::class);
    }
}
