<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Enquiry extends Model
{
    use HasFactory;

    protected $fillable = [
        'car_id',
        'dealer_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'message',
        'status',
    ];

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }

    public function dealer(): BelongsTo
    {
        return $this->belongsTo(Dealer::class);
    }

    public function getWhatsAppUrlAttribute(): string
    {
        $phone = $this->car->dealer->phone ?? '';
        $message = "Hi, I'm interested in your car: ".$this->car->title."\n";
        $message .= 'Reference: '.route('car.detail', $this->car->slug);

        return 'https://wa.me/'.preg_replace('/[^0-9]/', '', $phone).'?text='.urlencode($message);
    }
}
