<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Customer extends Authenticatable
{
    use HasApiTokens, HasFactory;

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
        'aadhaar_number',
        'pan_number',
        'gender',
        'dob',
        'profile_completion_percentage',
        'profile_completed_at',
    ];

    protected $casts = [
        'dob' => 'date',
    ];

    protected static function booted()
    {
        static::created(function ($customer) {
            $customer->customer_unique_id = 'CUS' . (10000 + $customer->id);
            $customer->saveQuietly();
            
            $customer->wallet()->create(['balance' => 0]);
        });
    }

    public function calculateProfileCompletion()
    {
        $weights = [
            'name' => 10,
            'phone' => 10,
            'email' => 10,
            'profile_image' => 10,
            'city' => 10,
            'state' => 10,
            'address' => 10,
            'pincode' => 10,
            'gender' => 5,
            'dob' => 5,
        ];

        $percentage = 0;

        foreach ($weights as $field => $weight) {
            if (!empty($this->$field)) {
                $percentage += $weight;
            }
        }

        // Aadhaar or PAN gives the remaining 10%
        if (!empty($this->aadhaar_number) || !empty($this->pan_number)) {
            $percentage += 10;
        }

        $this->profile_completion_percentage = $percentage;
        
        if ($percentage == 100 && is_null($this->profile_completed_at)) {
            $this->profile_completed_at = now();
        } elseif ($percentage < 100) {
            $this->profile_completed_at = null;
        }

        $this->saveQuietly(); // save without triggering events that might cause loop

        return $percentage;
    }

    public function getMissingProfileFields()
    {
        $fields = [
            'name' => 'Full Name',
            'phone' => 'Mobile Number',
            'email' => 'Email Address',
            'profile_image' => 'Profile Photo',
            'city' => 'City',
            'state' => 'State',
            'address' => 'Full Address',
            'pincode' => 'Pincode',
            'gender' => 'Gender',
            'dob' => 'Date of Birth',
        ];

        $missing = [];
        foreach ($fields as $key => $label) {
            if (empty($this->$key)) {
                $missing[] = $label;
            }
        }

        if (empty($this->aadhaar_number) && empty($this->pan_number)) {
            $missing[] = 'Aadhaar Number or PAN Number';
        }

        return $missing;
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
