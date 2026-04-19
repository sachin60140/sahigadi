<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminServiceHistory extends Model
{
    protected $table = 'admin_service_histories';

    protected $fillable = [
        'dealer_id',
        'vehicle_number',
        'service_count',
        'charge_amount',
        'is_success',
        'error_message',
        'raw_response',
    ];

    protected $casts = [
        'is_success' => 'boolean',
        'charge_amount' => 'decimal:2',
        'raw_response' => 'array',
    ];

    public function dealer(): BelongsTo
    {
        return $this->belongsTo(Dealer::class);
    }

    public static function createFromServiceHistory(ServiceHistory $sh, float $charge): self
    {
        return static::create([
            'dealer_id' => $sh->dealer_id,
            'vehicle_number' => $sh->vehicle_number,
            'service_count' => $sh->records()->count(),
            'charge_amount' => $sh->is_success ? $charge : 0,
            'is_success' => $sh->is_success,
            'error_message' => $sh->error_message,
            'raw_response' => $sh->raw_response,
        ]);
    }

    public static function createFromMarutiServiceHistory(MarutiServiceHistory $sh, float $charge): self
    {
        return static::create([
            'dealer_id' => $sh->dealer_id,
            'vehicle_number' => $sh->vehicle_number,
            'service_count' => $sh->records()->count(),
            'charge_amount' => $sh->is_success ? $charge : 0,
            'is_success' => $sh->is_success,
            'error_message' => $sh->error_message,
            'raw_response' => $sh->raw_response,
        ]);
    }
}
