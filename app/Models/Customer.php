<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Customer extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'profile_image',
        'phone',
        'whatsapp_number',
        'address',
        'city',
        'state',
        'pincode',
        'gst_number',
        'company_name',
    ];

    protected static function booted()
    {
        static::created(function ($customer) {
            $customer->wallet()->create(['balance' => 0]);
        });
    }

    public function wallet()
    {
        return $this->hasOne(CustomerWallet::class);
    }

    public function listings()
    {
        return $this->hasMany(CustomerCarListing::class, 'owner_phone', 'phone');
    }
}
