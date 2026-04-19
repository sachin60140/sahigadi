<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Car extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'dealer_id',
        'brand_id',
        'title',
        'slug',
        'model',
        'year',
        'fuel_type',
        'transmission',
        'km_driven',
        'price',
        'description',
        'city',
        'latitude',
        'longitude',
        'registration_number',
        'owners',
        'status',
        'rejection_reason',
        'is_featured',
        'featured_expires_at',
        'is_active',
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
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'featured_expires_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function ($car) {
            if (empty($car->slug)) {
                $car->slug = Str::slug($car->title).'-'.Str::random(5);
            }
        });
    }

    public function dealer(): BelongsTo
    {
        return $this->belongsTo(Dealer::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(CarImage::class)->orderBy('is_primary', 'desc')->orderBy('sort_order');
    }

    public function enquiries(): HasMany
    {
        return $this->hasMany(Enquiry::class);
    }

    public function primaryImage()
    {
        return $this->images()->where('is_primary', true)->first() ?? $this->images()->first();
    }

    public function getImageUrlAttribute(): ?string
    {
        $primary = $this->primaryImage();
        if (! $primary) {
            return null;
        }

        if (filter_var($primary->image_path, FILTER_VALIDATE_URL)) {
            return $primary->image_path;
        }

        return asset('storage/'.$primary->image_path);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true)
            ->where(function ($q) {
                $q->whereNull('featured_expires_at')
                    ->orWhere('featured_expires_at', '>', now());
            });
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
}
