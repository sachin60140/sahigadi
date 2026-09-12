<?php

namespace Tests\Feature\Billing;

use App\Exceptions\InsufficientBalanceException;
use App\Models\Customer;
use App\Models\CustomerWallet;
use App\Models\Dealer;
use App\Models\Setting;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Wallet balances were previously read into memory, compared, then written.
 * Two concurrent requests both passed the affordability check and spent the
 * same funds, driving the balance negative or losing one of the two updates.
 *
 * Each test here holds two model instances loaded before either writes, which
 * is what two simultaneous requests do.
 */
class WalletConcurrencyTest extends TestCase
{
    use DatabaseTransactions;

    public function test_a_second_concurrent_dealer_debit_is_refused(): void
    {
        $dealer = Dealer::query()->firstOrFail();
        $wallet = Wallet::firstOrCreate(['dealer_id' => $dealer->id], ['balance' => 0]);
        DB::table('wallets')->where('id', $wallet->id)->update(['balance' => 100]);

        $requestA = Dealer::find($dealer->id)->load('wallet');
        $requestB = Dealer::find($dealer->id)->load('wallet');

        $this->assertTrue($requestA->debitWallet(100, 'concurrency test A'));
        $this->assertFalse($requestB->debitWallet(100, 'concurrency test B'), 'second debit must be refused');

        $balance = (float) DB::table('wallets')->where('id', $wallet->id)->value('balance');
        $this->assertSame(0.0, $balance, 'balance must not go negative');
    }

    public function test_a_second_concurrent_customer_debit_is_refused(): void
    {
        $customer = Customer::query()->firstOrFail();
        $wallet = CustomerWallet::firstOrCreate(['customer_id' => $customer->id], ['balance' => 0]);
        DB::table('customer_wallets')->where('id', $wallet->id)->update(['balance' => 100]);

        $requestA = CustomerWallet::find($wallet->id);
        $requestB = CustomerWallet::find($wallet->id);

        $requestA->deductFunds(100, 'concurrency test A');

        $this->expectException(InsufficientBalanceException::class);

        try {
            $requestB->deductFunds(100, 'concurrency test B');
        } finally {
            $balance = (float) DB::table('customer_wallets')->where('id', $wallet->id)->value('balance');
            $this->assertGreaterThanOrEqual(0, $balance, 'balance must not go negative');
        }
    }

    public function test_a_debit_larger_than_the_balance_is_refused(): void
    {
        $customer = Customer::query()->firstOrFail();
        $wallet = CustomerWallet::firstOrCreate(['customer_id' => $customer->id], ['balance' => 0]);
        DB::table('customer_wallets')->where('id', $wallet->id)->update(['balance' => 10]);

        $this->expectException(InsufficientBalanceException::class);
        CustomerWallet::find($wallet->id)->deductFunds(50, 'over balance');
    }

    public function test_gst_is_derived_from_a_single_setting(): void
    {
        $original = Setting::getGstRate();

        try {
            Setting::setInvoiceSettings(['invoice_gst_rate' => 12]);

            // What checkout charges must survive the gateway's reverse split.
            $paid = round(1000 * Setting::gstMultiplier(), 2);
            $credited = round($paid / Setting::gstMultiplier(), 2);

            $this->assertSame(1120.0, $paid);
            $this->assertSame(1000.0, $credited);
            $this->assertSame(0.12, Setting::gstFraction());
            $this->assertSame(Setting::getGstRate(), Setting::getInvoiceGstRate());
        } finally {
            Setting::setInvoiceSettings(['invoice_gst_rate' => $original]);
        }
    }
}
