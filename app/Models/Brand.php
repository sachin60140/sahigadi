<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'logo',
        'is_active',
    ];

    protected static function booted(): void
    {
        static::creating(function ($brand) {
            if (empty($brand->slug)) {
                $brand->slug = Str::slug($brand->name);
            }
        });
    }

    public function cars(): HasMany
    {
        return $this->hasMany(Car::class);
    }

    public function customerListings(): HasMany
    {
        return $this->hasMany(CustomerCarListing::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
