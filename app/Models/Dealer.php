<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class Dealer extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'slug',
        'profile_image',
        'phone',
        'password',
        'company_name',
        'address',
        'city',
        'state',
        'pincode',
        'status',
        'rejection_reason',
        'gst_number',
        'kyc_document_type',
        'kyc_document_number',
        'kyc_document_path',
        'gst_document_path',
        'pan_number',
        'pan_document_path',
        'gst_verified',
        'gst_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected static function booted(): void
    {
        static::creating(function ($dealer) {
            if (empty($dealer->slug)) {
                $nameToSlug = !empty($dealer->company_name) ? $dealer->company_name : $dealer->name;
                $dealer->slug = static::generateUniqueSlug($nameToSlug);
            }
        });

        static::updating(function ($dealer) {
            if ($dealer->isDirty('company_name')) {
                $nameToSlug = !empty($dealer->company_name) ? $dealer->company_name : $dealer->name;
                $dealer->slug = static::generateUniqueSlug($nameToSlug);
            }
        });
    }

    public static function generateUniqueSlug(string $name): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        while (static::where('slug', $slug)->exists()) {
            $slug = $originalSlug.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'gst_verified_at' => 'datetime',
        ];
    }

    public function wallet(): HasOne
    {
        return $this->hasOne(Wallet::class);
    }

    public function cars(): HasMany
    {
        return $this->hasMany(Car::class);
    }

    public function approvedCars(): HasMany
    {
        return $this->hasMany(Car::class)->approved()->active();
    }

    public function enquiries(): HasMany
    {
        return $this->hasMany(Enquiry::class, 'dealer_id');
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function activeSubscription()
    {
        return $this->subscriptions()
            ->where('is_active', true)
            ->where('expires_at', '>=', now())
            ->first();
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isGstVerified(): bool
    {
        return $this->gst_verified === true;
    }

    public function canAddListing(): bool
    {
        $activeSubscription = $this->activeSubscription();
        if (! $activeSubscription) {
            return false;
        }

        return $activeSubscription->listings_used < $activeSubscription->plan->listing_limit;
    }

    public function getCatalogUrlAttribute(): string
    {
        return route('dealer.catalog', $this->slug);
    }

    public function walletBalance(): float
    {
        return $this->wallet?->balance ?? 0;
    }

    public function debitWallet(float $amount, string $description = ''): bool
    {
        $wallet = $this->wallet;

        if (! $wallet || $wallet->balance < $amount) {
            return false;
        }

        $wallet->balance -= $amount;
        $wallet->save();

        WalletTransaction::create([
            'wallet_id' => $wallet->id,
            'type' => 'debit',
            'amount' => $amount,
            'remark' => $description,
        ]);

        return true;
    }

    public function vehicleSearches(): HasMany
    {
        return $this->hasMany(VehicleDetail::class);
    }
}
