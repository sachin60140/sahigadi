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
        'ip_address',
    ];

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }

    public function customerCar(): BelongsTo
    {
        return $this->belongsTo(CustomerCarListing::class, 'car_id');
    }

    public function getActualCarAttribute()
    {
        return $this->dealer_id ? $this->car : $this->customerCar;
    }

    public function dealer(): BelongsTo
    {
        return $this->belongsTo(Dealer::class);
    }

    public function getWhatsAppUrlAttribute(): string
    {
        $phone = $this->actual_car->dealer->phone ?? ($this->actual_car->owner_phone ?? '');
        $message = "Hi, I'm interested in your car: ".($this->actual_car->title ?? 'Unknown')."\n";
        $message .= 'Reference: '.route('car.detail', $this->actual_car->slug ?? '');

        return 'https://wa.me/'.preg_replace('/[^0-9]/', '', $phone).'?text='.urlencode($message);
    }
}
