<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminVehicleSearch extends Model
{
    protected $table = 'admin_vehicle_searches';

    protected $fillable = [
        'dealer_id',
        'registration_number',
        'owner_name',
        'address',
        'make',
        'model',
        'fuel_type',
        'registration_date',
        'rc_status',
        'insurance_date',
        'insurance_policy_number',
        'puc_validity',
        'chassis_number',
        'engine_number',
        'charge_amount',
        'is_success',
        'error_message',
        'raw_response',
    ];

    protected $casts = [
        'charge_amount' => 'decimal:2',
        'is_success' => 'boolean',
        'raw_response' => 'array',
    ];

    public function dealer(): BelongsTo
    {
        return $this->belongsTo(Dealer::class);
    }

    public static function createFromVehicleDetail(VehicleDetail $vd, float $charge): self
    {
        return static::create([
            'dealer_id' => $vd->dealer_id,
            'registration_number' => $vd->registration_number,
            'owner_name' => $vd->owner_name,
            'address' => $vd->address,
            'make' => $vd->make,
            'model' => $vd->model,
            'fuel_type' => $vd->fuel_type,
            'registration_date' => $vd->registration_date,
            'rc_status' => $vd->rc_status,
            'insurance_date' => $vd->insurance_date,
            'insurance_policy_number' => $vd->insurance_policy_number,
            'puc_validity' => $vd->puc_validity,
            'chassis_number' => $vd->chassis_number,
            'engine_number' => $vd->engine_number,
            'charge_amount' => $charge,
            'is_success' => $vd->is_success,
            'error_message' => $vd->error_message,
            'raw_response' => $vd->raw_response,
        ]);
    }
}
