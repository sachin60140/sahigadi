<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChallanPdfSearch extends Model
{
    protected $fillable = [
        'customer_id',
        'dealer_id',
        'vehicle_number',
        'is_success',
        'charge_amount',
        'api_request',
        'api_response',
        'pdf_url',
        'error_message',
    ];

    protected $casts = [
        'is_success' => 'boolean',
        'api_request' => 'array',
        'api_response' => 'array',
        'charge_amount' => 'decimal:2',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function dealer(): BelongsTo
    {
        return $this->belongsTo(Dealer::class);
    }
}
