<?php

namespace Tests\Feature\Security;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

/**
 * sendOtp() wrote every code to the log next to the phone it was sent to, on
 * a line labelled "DEVELOPMENT" that shipped to production. Phone plus OTP is
 * the entire credential for /auth/verify-otp, so anyone who could read
 * storage/logs/laravel.log -- a support engineer, a backup, a log shipper, an
 * LFI bug -- could sign in as any user who had requested a code in the last
 * ten minutes.
 */
class OtpNotLoggedTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        // Never let the suite talk to the real SMS gateway.
        Http::fake(['pgapi.sparc.smartping.io/*' => Http::response('', 200)]);
    }

    public function test_the_otp_is_never_logged_outside_local(): void
    {
        $this->app['env'] = 'production';

        Log::spy();

        $phone = '9876500001';

        $this->postJson('/api/auth/send-otp', ['phone' => $phone, 'type' => 'customer'])
            ->assertOk()
            ->assertJson(['success' => true]);

        // The code really was issued, so this is not passing by doing nothing.
        $this->assertNotNull(
            Cache::get('api_otp_customer_'.$phone),
            'No OTP was issued, so the logging assertion would be vacuous.'
        );

        Log::shouldNotHaveReceived('info');
    }

    public function test_the_otp_is_still_logged_locally_for_development(): void
    {
        $this->app['env'] = 'local';

        Log::spy();

        $phone = '9876500002';

        $this->postJson('/api/auth/send-otp', ['phone' => $phone, 'type' => 'customer'])
            ->assertOk();

        $otp = Cache::get('api_otp_customer_'.$phone);
        $this->assertNotNull($otp);

        Log::shouldHaveReceived('info')
            ->withArgs(fn (string $message) => str_contains($message, (string) $otp))
            ->once();
    }
}
