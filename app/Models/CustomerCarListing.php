<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class CustomerCarListing extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'brand_id',
        'model',
        'year',
        'fuel_type',
        'transmission',
        'km_driven',
        'price',
        'city',
        'latitude',
        'longitude',
        'registration_number',
        'owners',
        'owner_name',
        'owner_phone',
        'owner_email',
        'whatsapp_number',
        'status',
        'rejection_reason',
        'images',
        'is_active',
        'is_featured',
        'featured_expires_at',
    ];

    /**
     * Never serialised. The public API returns these models directly, and the
     * website treats seller contact details as a gated asset: it shows only a
     * masked mobile and puts the real one behind the OTP contact unlock. The
     * registration number is the input to the paid RC lookup, and the
     * moderation fields are internal.
     *
     * $hidden affects serialisation only, so attribute access and queries
     * elsewhere (admin, the website, the enquiry flow) are unaffected.
     */
    protected $hidden = [
        'owner_phone',
        'owner_email',
        'whatsapp_number',
        'registration_number',
        'rejection_reason',
        'deleted_at',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'year' => 'integer',
            'km_driven' => 'integer',
            'owners' => 'integer',
            'latitude' => 'float',
            'longitude' => 'float',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'featured_expires_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function ($listing) {
            if (empty($listing->slug)) {
                $listing->slug = Str::slug($listing->title).'-'.Str::random(5);
            }
            while (static::where('slug', $listing->slug)->exists()) {
                $listing->slug = Str::slug($listing->title).'-'.Str::random(5);
            }
        });

        // Matches the Car model: a null dealer_id marks an enquiry as
        // belonging to a customer listing.
        static::deleting(function ($listing) {
            Enquiry::where('car_id', $listing->id)->whereNull('dealer_id')->delete();
        });
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'owner_phone', 'phone');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function isFeatured(): bool
    {
        if (! $this->is_featured) {
            return false;
        }
        if ($this->featured_expires_at && $this->featured_expires_at < now()) {
            return false;
        }

        return true;
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function featuredListings()
    {
        return $this->morphMany(FeaturedListing::class, 'listable');
    }
    public function getUniqueIdAttribute()
    {
        return 'CCAR' . (10000 + $this->id);
    }
}
