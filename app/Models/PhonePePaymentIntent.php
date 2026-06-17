<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhonePePaymentIntent extends Model
{
    protected $table = 'phonepe_payment_intents';

    protected $fillable = [
        'transaction_id',
        'dealer_id',
        'type',
        'plan_id',
        'car_id',
        'days',
        'amount',
        'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'days' => 'integer',
    ];
}
