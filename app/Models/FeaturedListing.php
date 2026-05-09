<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeaturedListing extends Model
{
    use HasFactory;

    protected $fillable = [
        'listable_type',
        'listable_id',
        'user_type',
        'user_id',
        'featured_plan_id',
        'amount_paid',
        'started_at',
        'expires_at',
        'status',
        'transaction_id',
    ];

    protected $casts = [
        'amount_paid' => 'decimal:2',
        'started_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function listable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->morphTo();
    }

    public function plan()
    {
        return $this->belongsTo(FeaturedPlan::class, 'featured_plan_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                     ->where('expires_at', '>', now());
    }
}
