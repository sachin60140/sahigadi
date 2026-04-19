<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'dealer_id',
        'plan_id',
        'listings_used',
        'starts_at',
        'expires_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'listings_used' => 'integer',
            'starts_at' => 'date',
            'expires_at' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function dealer(): BelongsTo
    {
        return $this->belongsTo(Dealer::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function isActive(): bool
    {
        return $this->is_active && $this->expires_at >= now();
    }

    public function remainingListings(): int
    {
        $activeCarsCount = $this->dealer->cars()->whereNull('deleted_at')->count();

        return max(0, $this->plan->listing_limit - $activeCarsCount);
    }

    public function getActiveListingsCount(): int
    {
        return $this->dealer->cars()->whereNull('deleted_at')->count();
    }

    public function incrementListings(): void
    {
        $this->increment('listings_used');
    }
}
