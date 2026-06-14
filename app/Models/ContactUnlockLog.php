<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ContactUnlockLog extends Model
{
    protected $fillable = [
        'unlockable_type',
        'unlockable_id',
        'viewer_name',
        'viewer_mobile',
        'ip_address',
        'user_agent',
        'source_page',
        'otp_sent_at',
        'otp_verified_at',
        'contact_revealed_at',
        'otp_attempts',
        'resend_count',
        'status',
        'revealed_contact_last4',
    ];

    protected $casts = [
        'otp_sent_at' => 'datetime',
        'otp_verified_at' => 'datetime',
        'contact_revealed_at' => 'datetime',
    ];

    public function unlockable(): MorphTo
    {
        return $this->morphTo();
    }
}
