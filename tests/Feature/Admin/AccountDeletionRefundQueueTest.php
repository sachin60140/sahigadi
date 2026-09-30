<?php

namespace Tests\Feature\Admin;

use App\Models\AccountDeletionRefund;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

/**
 * Deleting an account is never blocked by a wallet balance, because a customer
 * cannot withdraw one themselves. The money is recorded instead of absorbed -
 * and the public page at /account-deletion promises it will be returned. That
 * promise depends entirely on someone seeing these rows, so the queue needs a
 * screen.
 */
class AccountDeletionRefundQueueTest extends TestCase
{
    use DatabaseTransactions;

    private function admin(): User
    {
        $admin = User::first();

        if (! $admin) {
            $this->markTestSkipped('No admin account to sign in as.');
        }

        return $admin;
    }

    private function makeRefund(array $overrides = []): AccountDeletionRefund
    {
        $customer = Customer::create(['phone' => $overrides['phone'] ?? '9700000001', 'name' => 'Gone Seller']);

        return AccountDeletionRefund::create(array_merge([
            'customer_id' => $customer->id,
            'contact_phone' => $customer->phone,
            'contact_name' => $customer->name,
            'balance' => '450.00',
            'status' => AccountDeletionRefund::STATUS_PENDING,
            'requested_at' => now()->subDay(),
        ], array_diff_key($overrides, ['phone' => null])));
    }

    public function test_the_queue_requires_an_admin(): void
    {
        $this->get('/admin/account-deletion-refunds')->assertRedirect();
    }

    public function test_an_admin_sees_a_pending_refund_with_the_number_to_pay(): void
    {
        $refund = $this->makeRefund();

        $this->actingAs($this->admin(), 'admin')
            ->get('/admin/account-deletion-refunds')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/AccountDeletionRefunds/Index')
                ->where('totals.pendingCount', 1)
                ->where('totals.pendingTotal', 450)
                ->has('refunds.data', 1, fn (Assert $row) => $row
                    // contact_phone is in the model's $hidden, so it only
                    // reaches this screen because the controller maps it
                    // explicitly - and this is the one place it is needed.
                    ->where('contact_phone', '9700000001')
                    ->where('balance', 450)
                    ->where('is_pending', true)
                    ->etc()
                )
            );
    }

    public function test_marking_a_refund_closes_it_and_drops_the_contact_details(): void
    {
        $refund = $this->makeRefund(['phone' => '9700000002']);

        $this->actingAs($this->admin(), 'admin')
            ->post("/admin/account-deletion-refunds/{$refund->id}/refunded", ['reference' => 'UTR12345'])
            ->assertRedirect();

        $fresh = $refund->fresh();

        $this->assertSame(AccountDeletionRefund::STATUS_REFUNDED, $fresh->status);
        $this->assertSame('UTR12345', $fresh->reference);
        $this->assertNotNull($fresh->refunded_at);

        // The phone was kept only in order to make this payment.
        $this->assertSame('', $fresh->contact_phone);
        $this->assertNull($fresh->contact_name);

        // The amount stays: it is the record that the money was returned.
        $this->assertSame('450.00', (string) $fresh->balance);
    }

    public function test_a_refund_cannot_be_marked_twice(): void
    {
        $refund = $this->makeRefund(['phone' => '9700000003']);
        $admin = $this->admin();

        $this->actingAs($admin, 'admin')
            ->post("/admin/account-deletion-refunds/{$refund->id}/refunded", ['reference' => 'FIRST'])
            ->assertRedirect();

        $this->actingAs($admin, 'admin')
            ->post("/admin/account-deletion-refunds/{$refund->id}/refunded", ['reference' => 'SECOND'])
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertSame('FIRST', $refund->fresh()->reference, 'A second confirmation must not overwrite the first.');
    }

    public function test_the_queue_can_be_filtered_by_status(): void
    {
        $this->makeRefund(['phone' => '9700000004']);
        $this->makeRefund(['phone' => '9700000005', 'status' => AccountDeletionRefund::STATUS_REFUNDED, 'refunded_at' => now()]);

        $admin = $this->admin();

        $this->actingAs($admin, 'admin')
            ->get('/admin/account-deletion-refunds?status=refunded')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->has('refunds.data', 1, fn (Assert $row) => $row
                ->where('status', AccountDeletionRefund::STATUS_REFUNDED)
                ->etc()
            ));
    }
}
