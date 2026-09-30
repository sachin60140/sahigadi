<?php

namespace Tests\Feature\Api;

use App\Models\AccountDeletionRefund;
use App\Models\Customer;
use App\Models\CustomerCarListing;
use App\Models\Dealer;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Google Play requires any app that creates accounts to offer an in-app route
 * to delete one and a public web URL for the same, and the Data safety form
 * cannot be completed truthfully without them.
 *
 * Deletion here is two-stage. The account is hidden and every token revoked at
 * once; a scheduled pass anonymises for good once the grace period expires.
 * The row itself survives because invoices carry customer_id and a GST series
 * must stay unbroken.
 */
class AccountDeletionTest extends TestCase
{
    use DatabaseTransactions;

    private function makeCustomer(string $phone = '9800000001'): Customer
    {
        return Customer::create(['phone' => $phone, 'name' => 'Test Seller']);
    }

    private function makeListing(Customer $customer, string $key = 'a'): CustomerCarListing
    {
        return CustomerCarListing::create([
            'title' => 'Listing '.$key,
            'slug' => 'deletion-test-'.$key.'-'.substr(md5($customer->phone.$key), 0, 8),
            'owner_phone' => $customer->phone,
            'owner_name' => $customer->name,
            'price' => 100000,
            'status' => 'approved',
            'is_active' => true,
        ]);
    }

    public function test_a_customer_can_delete_their_own_account(): void
    {
        $customer = $this->makeCustomer();
        Sanctum::actingAs($customer, ['role:customer']);

        $this->deleteJson('/api/account', ['confirm' => 'DELETE'])
            ->assertOk()
            ->assertJson(['success' => true, 'grace_days' => Customer::GRACE_DAYS]);

        $this->assertNull(Customer::find($customer->id), 'The account is still visible to normal queries.');
        $this->assertNotNull(Customer::withTrashed()->find($customer->id), 'The row must survive for retained invoices.');
    }

    public function test_deleting_requires_an_explicit_confirmation(): void
    {
        $customer = $this->makeCustomer('9800000002');
        Sanctum::actingAs($customer, ['role:customer']);

        $this->deleteJson('/api/account')->assertStatus(422);
        $this->deleteJson('/api/account', ['confirm' => 'yes'])->assertStatus(422);

        $this->assertNotNull(Customer::find($customer->id), 'A rejected request must not delete anything.');
    }

    public function test_a_dealer_cannot_delete_through_the_customer_endpoint(): void
    {
        $dealer = Dealer::first();

        if (! $dealer) {
            $this->markTestSkipped('No dealer to exercise the guard with.');
        }

        Sanctum::actingAs($dealer, ['role:dealer']);

        $this->deleteJson('/api/account', ['confirm' => 'DELETE'])->assertStatus(403);
    }

    public function test_deleting_takes_the_listings_down(): void
    {
        $customer = $this->makeCustomer('9800000003');
        $listing = $this->makeListing($customer);

        Sanctum::actingAs($customer, ['role:customer']);
        $this->deleteJson('/api/account', ['confirm' => 'DELETE'])->assertOk();

        $this->assertFalse(
            (bool) $listing->fresh()->is_active,
            'A deleted seller\'s listing still carries their phone number and cannot be managed.'
        );
    }

    public function test_a_leftover_wallet_balance_is_recorded_for_refund(): void
    {
        $customer = $this->makeCustomer('9800000004');
        $customer->wallet->addFunds('500.00', 'test credit');

        Sanctum::actingAs($customer, ['role:customer']);
        $this->deleteJson('/api/account', ['confirm' => 'DELETE'])->assertOk();

        $refund = AccountDeletionRefund::where('customer_id', $customer->id)->first();

        $this->assertNotNull($refund, 'Deletion must never silently absorb a balance.');
        $this->assertSame('500.00', (string) $refund->balance);
        $this->assertSame(AccountDeletionRefund::STATUS_PENDING, $refund->status);
        $this->assertSame('9800000004', $refund->contact_phone);
    }

    public function test_an_empty_wallet_leaves_nothing_behind(): void
    {
        $customer = $this->makeCustomer('9800000005');

        Sanctum::actingAs($customer, ['role:customer']);
        $this->deleteJson('/api/account', ['confirm' => 'DELETE'])->assertOk();

        $this->assertNull(
            AccountDeletionRefund::where('customer_id', $customer->id)->first(),
            'With no balance there is nothing to keep a phone number for.'
        );
    }

    public function test_signing_in_within_the_grace_period_restores_the_account(): void
    {
        Http::fake(['pgapi.sparc.smartping.io/*' => Http::response('', 200)]);

        $phone = '9800000006';
        $customer = $this->makeCustomer($phone);
        $listing = $this->makeListing($customer, 'restore');

        Sanctum::actingAs($customer, ['role:customer']);
        $this->deleteJson('/api/account', ['confirm' => 'DELETE'])->assertOk();

        // OTP login is the only route back in.
        Cache::put('api_otp_customer_'.$phone, 123456, now()->addMinutes(10));

        $response = $this->postJson('/api/auth/verify-otp', [
            'phone' => $phone,
            'otp' => '123456',
            'type' => 'customer',
        ])->assertOk();

        $restored = Customer::find($customer->id);

        $this->assertNotNull($restored, 'The account should be back.');
        $this->assertSame($customer->id, $response->json('user.id'), 'A second row was created instead of restoring.');
        $this->assertTrue((bool) $listing->fresh()->is_active, 'An approved listing should come back live.');
        $this->assertSame(1, Customer::withTrashed()->where('phone', $phone)->count(), 'Duplicate row on the same phone.');
    }

    public function test_restoring_cancels_a_pending_refund(): void
    {
        Http::fake(['pgapi.sparc.smartping.io/*' => Http::response('', 200)]);

        $phone = '9800000007';
        $customer = $this->makeCustomer($phone);
        $customer->wallet->addFunds('250.00', 'test credit');

        Sanctum::actingAs($customer, ['role:customer']);
        $this->deleteJson('/api/account', ['confirm' => 'DELETE'])->assertOk();

        Cache::put('api_otp_customer_'.$phone, 123456, now()->addMinutes(10));
        $this->postJson('/api/auth/verify-otp', ['phone' => $phone, 'otp' => '123456', 'type' => 'customer'])->assertOk();

        $this->assertSame(
            AccountDeletionRefund::STATUS_CANCELLED,
            AccountDeletionRefund::where('customer_id', $customer->id)->value('status'),
            'The balance is back in the restored account, so no refund is owed.'
        );
    }

    public function test_the_scheduled_pass_leaves_an_account_still_in_its_grace_period_alone(): void
    {
        $customer = $this->makeCustomer('9800000008');

        Sanctum::actingAs($customer, ['role:customer']);
        $this->deleteJson('/api/account', ['confirm' => 'DELETE'])->assertOk();

        $this->artisan('customers:anonymise-deleted')->assertSuccessful();

        $this->assertSame(
            '9800000008',
            Customer::withTrashed()->find($customer->id)->phone,
            'An account inside the grace period must stay recoverable.'
        );
    }

    public function test_the_scheduled_pass_anonymises_once_the_grace_period_expires(): void
    {
        $phone = '9800000009';
        $customer = $this->makeCustomer($phone);
        $listing = $this->makeListing($customer, 'anon');

        Sanctum::actingAs($customer, ['role:customer']);
        $this->deleteJson('/api/account', ['confirm' => 'DELETE'])->assertOk();

        // Age the deletion past the window.
        Customer::withTrashed()->whereKey($customer->id)->update([
            'deleted_at' => now()->subDays(Customer::GRACE_DAYS + 1),
        ]);

        $this->artisan('customers:anonymise-deleted')->assertSuccessful();

        $scrubbed = Customer::withTrashed()->find($customer->id);

        $this->assertNotNull($scrubbed, 'The row must survive for retained invoices.');
        $this->assertNotNull($scrubbed->anonymised_at);
        $this->assertSame('Deleted user', $scrubbed->name);
        $this->assertNull($scrubbed->email);
        $this->assertNotSame($phone, $scrubbed->phone, 'The mobile number must not survive.');

        // Indian mobile numbers are recycled. The listings keep their own copy
        // of the seller's phone and CustomerCarListing::customer() joins on it,
        // so leaving it would hand these listings to whoever gets the number
        // next.
        $this->assertNotSame(
            $phone,
            $listing->fresh()->owner_phone,
            'A recycled number would inherit this seller\'s listings.'
        );
        $this->assertSame(0, Customer::withTrashed()->where('phone', $phone)->count());
    }

    public function test_an_anonymised_account_cannot_be_restored_by_signing_in(): void
    {
        Http::fake(['pgapi.sparc.smartping.io/*' => Http::response('', 200)]);

        $phone = '9800000010';
        $customer = $this->makeCustomer($phone);

        Sanctum::actingAs($customer, ['role:customer']);
        $this->deleteJson('/api/account', ['confirm' => 'DELETE'])->assertOk();

        Customer::withTrashed()->whereKey($customer->id)->update([
            'deleted_at' => now()->subDays(Customer::GRACE_DAYS + 1),
        ]);
        $this->artisan('customers:anonymise-deleted')->assertSuccessful();

        Cache::put('api_otp_customer_'.$phone, 123456, now()->addMinutes(10));
        $response = $this->postJson('/api/auth/verify-otp', ['phone' => $phone, 'otp' => '123456', 'type' => 'customer'])->assertOk();

        $this->assertNotSame(
            $customer->id,
            $response->json('user.id'),
            'Signing in after anonymisation must start a fresh account, not resurrect an emptied one.'
        );
    }

    public function test_the_public_deletion_page_is_reachable_without_signing_in(): void
    {
        // Google Play requires a URL a reviewer can open without installing the
        // app or holding an account.
        $this->get('/account-deletion')
            ->assertOk()
            ->assertSee('Delete Your Account', false);
    }

    public function test_anonymising_clears_the_customer_from_every_phone_keyed_lookup_table(): void
    {
        $phone = '9800000011';
        $customer = $this->makeCustomer($phone);

        // These five hold the customer's paid lookup history and are reachable
        // ONLY by the denormalised phone: none of them has a customer_id.
        $tables = [
            'customer_vehicle_searches' => ['registration_number' => 'BR01AB1234'],
            'customer_challan_searches' => ['vehicle_number' => 'BR01AB1234'],
            'customer_service_histories' => ['vehicle_number' => 'BR01AB1234'],
            'cust_maruti_services' => ['vehicle_number' => 'BR01AB1234'],
            'customer_mahindra_service_histories' => ['vehicle_number' => 'BR01AB1234'],
        ];

        foreach ($tables as $table => $extra) {
            DB::table($table)->insert($extra + [
                'customer_name' => 'Test Seller',
                'customer_email' => 'seller@example.com',
                'customer_phone' => $phone,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->deleteAndAnonymise($customer);

        foreach (array_keys($tables) as $table) {
            $rows = DB::table($table)->where('customer_phone', $phone)->count();
            $this->assertSame(0, $rows, "$table still names the deleted customer by phone");
        }

        $this->assertSame(
            0,
            DB::table('customer_vehicle_searches')->where('customer_email', 'seller@example.com')->count(),
            'The email survived in the lookup history'
        );
    }

    public function test_anonymising_deletes_the_uploaded_photos(): void
    {
        Storage::fake('public');

        $customer = $this->makeCustomer('9800000012');
        $customer->forceFill(['profile_image' => 'customer-profiles/face.jpg'])->saveQuietly();

        Storage::disk('public')->put('customer-profiles/face.jpg', 'x');
        Storage::disk('public')->put('customer_cars/front.jpg', 'x');
        Storage::disk('public')->put('customer_cars/rear.jpg', 'x');

        $listing = $this->makeListing($customer, 'photos');
        $listing->forceFill([
            'images' => json_encode(['customer_cars/front.jpg', 'customer_cars/rear.jpg']),
        ])->save();

        $this->deleteAndAnonymise($customer);

        // These are served publicly from /storage and would otherwise stay
        // fetchable forever after the account was erased.
        Storage::disk('public')->assertMissing('customer_cars/front.jpg');
        Storage::disk('public')->assertMissing('customer_cars/rear.jpg');
        Storage::disk('public')->assertMissing('customer-profiles/face.jpg');
    }

    public function test_anonymising_clears_enquiries_the_customer_sent_but_not_ones_they_received(): void
    {
        $phone = '9800000013';
        $customer = $this->makeCustomer($phone);
        $listing = $this->makeListing($customer, 'enq');

        // Sent by the customer, to a dealer's car.
        $sent = DB::table('enquiries')->insertGetId([
            'car_id' => 9999, 'dealer_id' => 1,
            'customer_name' => 'Test Seller', 'customer_email' => 'seller@example.com',
            'customer_phone' => $phone, 'ip_address' => '1.2.3.4',
            'created_at' => now(), 'updated_at' => now(),
        ]);

        // Sent BY A BUYER about this customer's listing. Third-party data, and
        // not the seller's to erase.
        $received = DB::table('enquiries')->insertGetId([
            'car_id' => $listing->id, 'dealer_id' => null,
            'customer_name' => 'Interested Buyer', 'customer_email' => 'buyer@example.com',
            'customer_phone' => '9777000000', 'ip_address' => '5.6.7.8',
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $this->deleteAndAnonymise($customer);

        $sentRow = DB::table('enquiries')->find($sent);
        $this->assertSame('Deleted user', $sentRow->customer_name);
        $this->assertNull($sentRow->customer_email);
        $this->assertNotSame($phone, $sentRow->customer_phone);
        $this->assertNull($sentRow->ip_address);
        $this->assertSame(1, (int) $sentRow->dealer_id, 'The dealer keeps the lead, just not the identity.');

        $receivedRow = DB::table('enquiries')->find($received);
        $this->assertNotNull($receivedRow, 'A buyer\'s enquiry must survive the seller deleting their account.');
        $this->assertSame('Interested Buyer', $receivedRow->customer_name);
        $this->assertSame('9777000000', $receivedRow->customer_phone);
    }

    public function test_anonymising_clears_the_contact_unlocks_the_customer_performed(): void
    {
        $phone = '9800000014';
        $customer = $this->makeCustomer($phone);

        $asViewer = DB::table('contact_unlock_logs')->insertGetId([
            'unlockable_type' => 'App\\Models\\Car', 'unlockable_id' => 1,
            'viewer_name' => 'Test Seller', 'viewer_mobile' => $phone,
            'ip_address' => '1.2.3.4', 'created_at' => now(), 'updated_at' => now(),
        ]);

        $someoneElse = DB::table('contact_unlock_logs')->insertGetId([
            'unlockable_type' => 'App\\Models\\Car', 'unlockable_id' => 1,
            'viewer_name' => 'Another Person', 'viewer_mobile' => '9777000001',
            'ip_address' => '5.6.7.8', 'created_at' => now(), 'updated_at' => now(),
        ]);

        $this->deleteAndAnonymise($customer);

        $mine = DB::table('contact_unlock_logs')->find($asViewer);
        $this->assertSame('Deleted user', $mine->viewer_name);
        $this->assertNotSame($phone, $mine->viewer_mobile);
        $this->assertSame('1.2.3.4', $mine->ip_address, 'The anti-abuse trail stays, just unlinked from a person.');

        $theirs = DB::table('contact_unlock_logs')->find($someoneElse);
        $this->assertSame('Another Person', $theirs->viewer_name, 'Another viewer\'s row must not be touched.');
    }

    public function test_anonymising_strips_the_free_text_and_location_from_the_listings(): void
    {
        $customer = $this->makeCustomer('9800000015');
        $listing = $this->makeListing($customer, 'strip');
        $listing->forceFill([
            'description' => 'Call me on my personal number, I live near the temple',
            'registration_number' => 'BR01XY9999',
            'latitude' => 25.5941,
            'longitude' => 85.1376,
        ])->save();

        $this->deleteAndAnonymise($customer);

        $fresh = $listing->fresh();

        $this->assertNotNull($fresh, 'The listing row must survive: deleting it would destroy buyers\' enquiries.');
        $this->assertNull($fresh->description);
        $this->assertNull($fresh->registration_number);
        $this->assertNull($fresh->latitude);
        $this->assertNull($fresh->longitude);
        $this->assertSame('[]', $fresh->images);
    }

    public function test_an_anonymised_account_carries_no_spendable_balance(): void
    {
        $customer = $this->makeCustomer('9800000016');
        $customer->wallet->addFunds('300.00', 'test credit');

        $this->deleteAndAnonymise($customer);

        $this->assertSame(
            '0.00',
            (string) $customer->wallet()->first()->balance,
            'The amount owed is on the refund row; it must not stay spendable.'
        );
        $this->assertSame(
            '300.00',
            (string) AccountDeletionRefund::where('customer_id', $customer->id)->value('balance'),
            'The debt to the customer must still be recorded.'
        );
    }

    public function test_restoring_only_brings_back_listings_the_deletion_took_down(): void
    {
        Http::fake(['pgapi.sparc.smartping.io/*' => Http::response('', 200)]);

        $phone = '9800000017';
        $customer = $this->makeCustomer($phone);

        $live = $this->makeListing($customer, 'live');
        $alreadyOff = $this->makeListing($customer, 'off');
        $alreadyOff->forceFill(['is_active' => false])->save();

        Sanctum::actingAs($customer, ['role:customer']);
        $this->deleteJson('/api/account', ['confirm' => 'DELETE'])->assertOk();

        Cache::put('api_otp_customer_'.$phone, 123456, now()->addMinutes(10));
        $this->postJson('/api/auth/verify-otp', ['phone' => $phone, 'otp' => '123456', 'type' => 'customer'])->assertOk();

        $this->assertTrue((bool) $live->fresh()->is_active, 'A listing the deletion hid should come back.');
        $this->assertFalse(
            (bool) $alreadyOff->fresh()->is_active,
            'A listing the seller had switched off themselves must stay off.'
        );
    }

    public function test_deleting_invalidates_an_otp_issued_just_before(): void
    {
        $phone = '9800000018';
        $customer = $this->makeCustomer($phone);

        Cache::put('api_otp_customer_'.$phone, 123456, now()->addMinutes(10));

        Sanctum::actingAs($customer, ['role:customer']);
        $this->deleteJson('/api/account', ['confirm' => 'DELETE'])->assertOk();

        $this->assertNull(
            Cache::get('api_otp_customer_'.$phone),
            'A live OTP would sign the account straight back in.'
        );
    }

    /** Delete, then age past the grace period and run the scheduled pass. */
    private function deleteAndAnonymise(Customer $customer): void
    {
        Sanctum::actingAs($customer, ['role:customer']);
        $this->deleteJson('/api/account', ['confirm' => 'DELETE'])->assertOk();

        Customer::withTrashed()->whereKey($customer->id)->update([
            'deleted_at' => now()->subDays(Customer::GRACE_DAYS + 1),
        ]);

        $this->artisan('customers:anonymise-deleted')->assertSuccessful();
    }
}
