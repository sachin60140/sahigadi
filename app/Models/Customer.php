<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
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
        // Encrypted at rest. $hidden keeps these out of JSON; this keeps them
        // out of the database in readable form. See App\Casts\EncryptedIdentity.
        'aadhaar_number' => \App\Casts\EncryptedIdentity::class,
        'pan_number' => \App\Casts\EncryptedIdentity::class,
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
     * Every table holding this customer's personal data that is reachable ONLY
     * by their denormalised phone number. None of them has a customer_id.
     *
     * cust_maruti_services is the real table name behind
     * CustomerMarutiServiceHistory, not the one the class name suggests.
     */
    private const PHONE_KEYED_LOOKUP_TABLES = [
        'customer_vehicle_searches',
        'customer_challan_searches',
        'customer_service_histories',
        'cust_maruti_services',
        'customer_mahindra_service_histories',
    ];

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
            // and nobody can manage them any more. Only the ones that were live
            // are marked, so a restore brings back exactly these and not one
            // the seller had already switched off themselves.
            $this->listings()
                ->where('is_active', true)
                ->update(['is_active' => false, 'deactivated_by_deletion_at' => now()]);

            // Every device is signed out; a token must not outlive its account.
            $this->tokens()->delete();

            $this->delete();
        });

        // An OTP issued seconds before this has a ten-minute life and would
        // otherwise still sign the account back in.
        Cache::forget('api_otp_customer_'.$this->phone);
    }

    /**
     * Stage two, on signing in again inside the grace period.
     */
    public function restoreAccount(): void
    {
        DB::transaction(function () {
            $this->restore();

            $this->listings()
                ->whereNotNull('deactivated_by_deletion_at')
                ->update(['is_active' => true, 'deactivated_by_deletion_at' => null]);

            $this->deletionRefund()
                ->where('status', 'pending')
                ->update(['status' => 'cancelled', 'notes' => 'Account restored within the grace period.']);
        });
    }

    /**
     * Stage three: the grace period has run out.
     *
     * The row survives because invoices, payments and customer_wallets all
     * reference customer_id and a GST series cannot lose its counterparty - but
     * nothing personal survives anywhere.
     *
     * Never call forceDelete() on a customer instead of this.
     * customer_wallets.customer_id cascades, and customer_wallet_transactions
     * cascades from it, so a hard delete destroys the entire money ledger while
     * the invoices pointing into it survive, because invoices.customer_id is a
     * bare indexed column with no foreign key to stop it.
     */
    public function anonymise(): void
    {
        // Captured before anything overwrites them: the lookup tables, the
        // enquiries and the unlock log are reachable ONLY by these strings.
        $phone = $this->phone;
        $placeholder = $this->placeholderPhone();

        // Files first, while the rows still name them. Done outside the
        // transaction because a storage failure must not roll back an erasure
        // that is a legal obligation; a missing file is not an error here.
        $this->deleteUploadedFiles();

        DB::transaction(function () use ($phone, $placeholder) {
            // Never $listing->delete(): CustomerCarListing::booted() has a
            // deleting hook that hard-destroys the enquiries pointed at it, and
            // those are the BUYERS' records - third-party data that is not this
            // seller's to erase. The model has no SoftDeletes trait either, so
            // it would be irreversible. Neutralise the row instead.
            $this->listings()->update([
                'owner_name' => 'Deleted user',
                'owner_phone' => $placeholder,
                'owner_email' => null,
                'whatsapp_number' => null,
                'registration_number' => null,
                'description' => null,
                'latitude' => null,
                'longitude' => null,
                'images' => '[]',
                'is_active' => false,
                'deactivated_by_deletion_at' => null,
                'status' => 'rejected',
                'rejection_reason' => 'Seller account deleted',
            ]);

            // The paid lookup history. The transaction trail stays - amounts,
            // gateway ids, the report itself - but it stops naming a person.
            foreach (self::PHONE_KEYED_LOOKUP_TABLES as $table) {
                DB::table($table)->where('customer_phone', $phone)->update([
                    'customer_name' => null,
                    'customer_email' => null,
                    'customer_phone' => null,
                ]);
            }

            // The one lookup table with a real foreign key. customer_id stays:
            // dropping it would sever the charge from the party who paid it.
            DB::table('challan_pdf_searches')->where('customer_id', $this->id)->update([
                'api_request' => null,
                'api_response' => null,
                'pdf_url' => null,
            ]);

            // Enquiries this customer SENT. The dealer keeps the lead and the
            // message; it just stops identifying anyone. customer_name and
            // customer_phone are NOT NULL, hence placeholders rather than nulls.
            // Enquiries OTHER people sent about this customer's listings are
            // left alone - those are buyers' details, not this seller's.
            DB::table('enquiries')->where('customer_phone', $phone)->update([
                'customer_name' => 'Deleted user',
                'customer_email' => null,
                'customer_phone' => $placeholder,
                'ip_address' => null,
            ]);

            // Contact unlocks this customer performed as a VIEWER. The
            // anti-abuse trail stays, no longer linkable to a person. Rows where
            // they are the subject belong to whoever unlocked them.
            // viewer_name and viewer_mobile are both NOT NULL, so these are
            // placeholders rather than nulls - nulling them throws and rolls
            // the whole erasure back.
            DB::table('contact_unlock_logs')->where('viewer_mobile', $phone)->update([
                'viewer_name' => 'Deleted user',
                'viewer_mobile' => $placeholder,
            ]);

            // An anonymised account must not carry a spendable balance. The
            // amount owed is already recorded on the refund row; this only moves
            // it off the wallet, through the row-locking trait so the ledger
            // stays self-consistent.
            $wallet = $this->wallet;
            if ($wallet && (float) $wallet->balance > 0) {
                $wallet->deductFunds(
                    $wallet->balance,
                    'Balance moved to the refund queue on account deletion'
                );
            }

            $this->forceFill([
                'name' => 'Deleted user',
                'email' => null,
                'phone' => $placeholder,
                'whatsapp_number' => null,
                'profile_image' => null,
                'address' => null,
                'city' => null,
                'state' => null,
                'pincode' => null,
                // No retention basis at all: these exist only to award 10% of a
                // profile-completion score and are never used for invoicing or
                // KYC. Hard-nulled, never anonymised.
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

            // Belt and braces: anything issued between stage one and now.
            $this->tokens()->delete();
        });
    }

    /**
     * The listing photos and profile picture, which are served publicly from
     * /storage and would otherwise stay fetchable forever after the account is
     * erased. Listing images are a json_encode'd string in a TEXT column, not
     * an array cast.
     */
    private function deleteUploadedFiles(): void
    {
        $paths = [];

        foreach ($this->listings()->get() as $listing) {
            $decoded = json_decode($listing->images ?? '[]', true);

            if (is_array($decoded)) {
                foreach ($decoded as $image) {
                    if (is_string($image) && $image !== '') {
                        $paths[] = $image;
                    }
                }
            }
        }

        if ($this->profile_image) {
            $paths[] = $this->profile_image;
        }

        foreach ($paths as $path) {
            try {
                Storage::disk('public')->delete(ltrim($path, '/'));
            } catch (\Throwable $e) {
                // A file that will not delete must not stop the erasure of the
                // database rows, which is the part with a legal deadline.
                Log::warning('Could not delete a file during account anonymisation', [
                    'customer_id' => $this->id,
                    'path' => $path,
                    'error' => $e->getMessage(),
                ]);
            }
        }
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
