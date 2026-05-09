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
        'whatsapp_number',
        'status',
        'rejection_reason',
        'images',
        'is_active',
        'is_featured',
        'featured_expires_at',
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
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
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
