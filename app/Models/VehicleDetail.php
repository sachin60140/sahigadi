<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleDetail extends Model
{
    protected $fillable = [
        'dealer_id',
        'registration_number',
        'owner_name',
        'father_name',
        'address',
        'vehicle_class',
        'make',
        'model',
        'variant',
        'color',
        'fuel_type',
        'engine_number',
        'chassis_number',
        'registration_date',
        'fitness_date',
        'insurance_date',
        'insurance_policy_number',
        'insurance_provider',
        'puc_number',
        'puc_validity',
        'tax_amount',
        'tax_validity',
        'seats',
        'mobile_number',
        'vehicle_category',
        'rc_status',
        'blacklist_status',
        'financed',
        'lender_name',
        'rto_location',
        'norms_type',
        'cubic_capacity',
        'unladen_weight',
        'gross_weight',
        'cylinders',
        'is_commercial',
        'permit_number',
        'permit_type',
        'permit_validity',
        'manufactured_date',
        'debit_amount',
        'raw_response',
        'is_success',
        'error_message',
        'api_provider',
    ];

    protected $casts = [
        'registration_date' => 'date',
        'fitness_date' => 'date',
        'insurance_date' => 'date',
        'puc_validity' => 'date',
        'permit_validity' => 'date',
        'debit_amount' => 'decimal:2',
        'raw_response' => 'array',
        'is_success' => 'boolean',
        'financed' => 'boolean',
        'is_commercial' => 'boolean',
    ];

    public function dealer(): BelongsTo
    {
        return $this->belongsTo(Dealer::class);
    }
}
