<?php

namespace Tests\Feature\Api;

use App\Models\AccountDeletionRefund;
use App\Models\Customer;
use App\Models\CustomerCarListing;
use App\Models\Dealer;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
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
}
