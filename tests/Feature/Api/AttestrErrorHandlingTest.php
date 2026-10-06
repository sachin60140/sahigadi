<?php

namespace Tests\Feature\Api;

use App\Mail\AttestrAccountAlert;
use App\Models\Dealer;
use App\Services\Attestr\AttestrErrors;
use App\Services\CustomerVehicleSearchService;
use App\Services\VehicleSearchService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * The RC lookup used to pick its message from the HTTP status alone and ignore
 * Attestr's own error code, which hid the cases that matter:
 *
 * - 4005, low credit balance, comes back as a 400. Every lookup on the site
 *   would fail with "Please try again" and nobody would be told to top up.
 * - 4293/4294, the daily limits, were answered "wait a moment", though they do
 *   not clear until the next day.
 * - 5001 was called "usually a temporary issue". On live traffic the same
 *   vehicle fails on every retry, from production and from a developer machine
 *   alike, while other vehicles succeed - BR06PD4822, BR01FB8111, BR05PA3051.
 *
 * Problems only the operator can fix now email the operator, at most once an
 * hour per code.
 */
class AttestrErrorHandlingTest extends TestCase
{
    use DatabaseTransactions;

    private const URL = 'api.attestr.com/*';

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.vehicle_api.alert_email' => 'ops@example.test']);
        Mail::fake();
    }

    private function attestrFails(int $status, int $code, string $message = 'x'): void
    {
        Http::fake([self::URL => Http::response(
            json_encode(['httpStatusCode' => $status, 'code' => $code, 'message' => $message, 'appError' => true]),
            $status
        )]);
    }

    private function fundedDealer(): Dealer
    {
        $dealer = Dealer::query()->whereHas('wallet')->first();

        if (! $dealer) {
            $this->markTestSkipped('No dealer with a wallet to exercise.');
        }

        $dealer->wallet->addFunds('1000.00', 'test credit');

        return $dealer;
    }

    public static function documentedCodes(): array
    {
        return [
            '5001 cannot process this record' => [5001, "couldn't retrieve the record"],
            '4293 account daily limit' => [4293, 'try again tomorrow'],
            '4294 api daily limit' => [4294, 'try again tomorrow'],
            '4291 rate limit' => [4291, 'wait a moment'],
            '4005 low credit' => [4005, 'temporarily unavailable'],
            '4016 bad credentials' => [4016, 'temporarily unavailable'],
            '4039 ip not whitelisted' => [4039, 'temporarily unavailable'],
            '4035 not provisioned' => [4035, 'temporarily unavailable'],
        ];
    }

    #[DataProvider('documentedCodes')]
    public function test_each_documented_code_gets_an_accurate_message(int $code, string $expected): void
    {
        $this->assertStringContainsString($expected, AttestrErrors::userMessage($code));
    }

    public function test_an_unknown_code_falls_back_to_the_status_message(): void
    {
        $this->assertNull(AttestrErrors::userMessage(9999));
        $this->assertNull(AttestrErrors::userMessage(null));
    }

    public function test_a_5001_tells_the_dealer_honestly_and_charges_nothing(): void
    {
        $dealer = $this->fundedDealer();
        $before = $dealer->walletBalance();

        $this->attestrFails(500, 5001, 'Request could not be processed');

        $result = app(VehicleSearchService::class)->search($dealer, 'BR05PA3051');

        $this->assertFalse($result['success']);
        $this->assertStringContainsString("couldn't retrieve the record", $result['message']);
        $this->assertStringNotContainsString('temporary issue', $result['message'], 'It is not temporary: the same plate fails on every retry.');
        $this->assertStringContainsString('No amount has been charged', $result['message']);
        $this->assertSame($before, $dealer->fresh()->walletBalance());

        // A vehicle-specific failure is not the operator's to fix.
        Mail::assertNothingSent();
    }

    public function test_a_low_credit_balance_alerts_the_operator(): void
    {
        $dealer = $this->fundedDealer();
        $this->attestrFails(400, 4005, 'Operation could not be performed due to low credits balance');

        $result = app(VehicleSearchService::class)->search($dealer, 'BR01AB1234');

        $this->assertStringContainsString('temporarily unavailable', $result['message']);

        Mail::assertSent(AttestrAccountAlert::class, fn (AttestrAccountAlert $mail) => $mail->hasTo('ops@example.test')
            && $mail->code === 4005
            && str_contains($mail->summary, 'Top up'));
    }

    public function test_the_alert_is_sent_once_an_hour_not_once_per_failure(): void
    {
        $dealer = $this->fundedDealer();
        $this->attestrFails(400, 4005);

        foreach (['BR01AB1111', 'BR01AB2222', 'BR01AB3333'] as $plate) {
            app(VehicleSearchService::class)->search($dealer, $plate);
        }

        Mail::assertSent(AttestrAccountAlert::class, 1);

        $this->travel(61)->minutes();
        app(VehicleSearchService::class)->search($dealer, 'BR01AB4444');

        Mail::assertSent(AttestrAccountAlert::class, 2);
    }

    public function test_different_account_problems_alert_separately(): void
    {
        $dealer = $this->fundedDealer();

        // A second Http::fake() for the same URL does not replace the first, so
        // the two responses are queued as a sequence instead.
        Http::fake([self::URL => Http::sequence()
            ->push(['code' => 4005, 'message' => 'low credit', 'appError' => true], 400)
            ->push(['code' => 4039, 'message' => 'ip', 'appError' => true], 403)]);

        app(VehicleSearchService::class)->search($dealer, 'BR01AB1111');
        app(VehicleSearchService::class)->search($dealer, 'BR01AB2222');

        Mail::assertSent(AttestrAccountAlert::class, fn ($m) => $m->code === 4005);
        Mail::assertSent(AttestrAccountAlert::class, fn ($m) => $m->code === 4039);
    }

    public function test_a_rate_limit_does_not_page_anyone(): void
    {
        $dealer = $this->fundedDealer();
        $this->attestrFails(429, 4292);

        $result = app(VehicleSearchService::class)->search($dealer, 'BR01AB1234');

        $this->assertStringContainsString('wait a moment', $result['message']);
        Mail::assertNothingSent();
    }

    public function test_a_customer_sees_the_same_accuracy_with_the_refund_notice(): void
    {
        $this->attestrFails(400, 4005);

        $result = app(CustomerVehicleSearchService::class)->search('BR01AB1234', [
            'name' => 'Test', 'phone' => '9300000001', 'email' => null,
        ]);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('temporarily unavailable', $result['message']);
        $this->assertStringContainsString('refunded to your wallet', $result['message']);
    }

    public function test_a_failure_to_send_the_alert_never_breaks_the_lookup(): void
    {
        Mail::shouldReceive('to')->andThrow(new \RuntimeException('SMTP is down'));

        $dealer = $this->fundedDealer();
        $this->attestrFails(400, 4005);

        $result = app(VehicleSearchService::class)->search($dealer, 'BR01AB1234');

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('temporarily unavailable', $result['message']);
    }

    public function test_the_alert_carries_no_vehicle_or_personal_data(): void
    {
        $dealer = $this->fundedDealer();
        $this->attestrFails(400, 4005);

        app(VehicleSearchService::class)->search($dealer, 'BR09ZZ9999');

        Mail::assertSent(AttestrAccountAlert::class, function (AttestrAccountAlert $mail) {
            $rendered = $mail->render();

            return ! str_contains($rendered, 'BR09ZZ9999');
        });
    }
}
