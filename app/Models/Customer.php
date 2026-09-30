<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;

class Customer extends Authenticatable
{
    use HasApiTokens, HasFactory, SoftDeletes;

    /** How long a deleted account can still be recovered by signing in again. */
    public const GRACE_DAYS = 30;

    /**
     * Never serialise identity documents.
     *
     * /api/auth/user returns this model straight to the mobile app, which
     * does not need them. Admin screens read the attributes directly, and
     * $hidden only affects toArray()/toJson(), so those are unaffected.
     */
    protected $hidden = [
        'aadhaar_number',
        'pan_number',
    ];

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
        'deleted_at' => 'datetime',
        'anonymised_at' => 'datetime',
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

    public function deletionRefund()
    {
        return $this->hasOne(AccountDeletionRefund::class);
    }

    /**
     * Still inside the window where signing in again brings the account back.
     */
    public function isRecoverable(): bool
    {
        return $this->trashed()
            && $this->anonymised_at === null
            && $this->deleted_at !== null
            && $this->deleted_at->gt(now()->subDays(self::GRACE_DAYS));
    }

    /**
     * Stage one: the account disappears from every surface and every session
     * ends, but nothing is destroyed yet.
     */
    public function requestDeletion(): void
    {
        DB::transaction(function () {
            // Any balance is recorded before the account goes, because a
            // customer has no way to withdraw it themselves: the only refund
            // path is admin-side. It stays pending until someone returns it.
            $balance = (float) ($this->wallet?->balance ?? 0);

            if ($balance > 0) {
                AccountDeletionRefund::updateOrCreate(
                    ['customer_id' => $this->id],
                    [
                        'contact_phone' => $this->phone,
                        'contact_name' => $this->name,
                        'balance' => $balance,
                        'status' => 'pending',
                        'requested_at' => now(),
                    ]
                );
            }

            // Take the listings down. They carry the seller's contact details
            // and nobody can manage them any more.
            $this->listings()->update(['is_active' => false]);

            // Every device is signed out; a token must not outlive its account.
            $this->tokens()->delete();

            $this->delete();
        });
    }

    /**
     * Stage two, on signing in again inside the grace period.
     */
    public function restoreAccount(): void
    {
        DB::transaction(function () {
            $this->restore();

            // Listings come back live if they are still approved. One the user
            // had deactivated themselves before deleting comes back too; that
            // is the safer direction to be wrong in.
            $this->listings()->where('status', 'approved')->update(['is_active' => true]);

            $this->deletionRefund()
                ->where('status', 'pending')
                ->update(['status' => 'cancelled', 'notes' => 'Account restored within the grace period.']);
        });
    }

    /**
     * Stage three: the grace period has run out. The row survives because
     * invoices reference customer_id and a GST series cannot lose its
     * counterparty, but nothing personal survives on it.
     */
    public function anonymise(): void
    {
        DB::transaction(function () {
            // The listings hold their own copy of the seller's phone, and that
            // is how CustomerCarListing::customer() joins. Indian mobile
            // numbers get recycled, so leaving the old number on them would
            // hand this person's listings to whoever is issued it next.
            $this->listings()->update([
                'owner_name' => 'Deleted user',
                'owner_phone' => $this->placeholderPhone(),
                'owner_email' => null,
                'whatsapp_number' => null,
                'is_active' => false,
            ]);

            $this->forceFill([
                'name' => 'Deleted user',
                'email' => null,
                'phone' => $this->placeholderPhone(),
                'whatsapp_number' => null,
                'profile_image' => null,
                'address' => null,
                'city' => null,
                'state' => null,
                'pincode' => null,
                'aadhaar_number' => null,
                'pan_number' => null,
                'gst_number' => null,
                'company_name' => null,
                'gender' => null,
                'dob' => null,
                'profile_completion_percentage' => 0,
                'profile_completed_at' => null,
                'anonymised_at' => now(),
            ])->saveQuietly();
        });
    }

    /**
     * A value that fits the column, cannot collide with a real Indian mobile
     * number, and stays unique so a phone lookup never matches it.
     */
    private function placeholderPhone(): string
    {
        return 'deleted-'.$this->id;
    }
}
