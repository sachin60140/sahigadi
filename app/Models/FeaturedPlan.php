<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeaturedPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'duration_days',
        'price',
        'is_active',
    ];

    protected $casts = [
        'duration_days' => 'integer',
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
