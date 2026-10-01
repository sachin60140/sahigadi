<?php

namespace Tests\Feature\Api;

use App\Exceptions\AttestrRequestException;
use App\Services\Attestr\AttestrRcClient;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Tests\TestCase;

/**
 * The RC request used to be built in two near-identical places. It is built
 * here now, so the DPDP V3 consent change lands once.
 *
 * Attestr mandates v3 from 01 Oct 2026 and requires a registered consent id on
 * every lookup, but has not published the name of the request field that
 * carries it - not on the Vehicle RC Check page in HTML, at the /v3/ path or in
 * the canonical .md source, nor in the DPDPA implementation guide, nor on the
 * Bank Account Verification page its own migration guide uses as the worked
 * example. So the field name is configuration, and enabling v3 without it must
 * fail loudly rather than send a request Attestr will reject.
 */
class AttestrRcClientTest extends TestCase
{
    private function client(string $version = 'v2', string $consentField = '', string $key = 'abc123'): AttestrRcClient
    {
        return new AttestrRcClient(
            url: 'https://api.attestr.com/api/'.$version.'/public/checkx/rc',
            apiKey: $key,
            version: $version,
            consentField: $consentField,
        );
    }

    public function test_v2_sends_only_the_registration_number(): void
    {
        $this->assertSame(['reg' => 'BR01AB1234'], $this->client()->payload('BR01AB1234'));
        $this->assertFalse($this->client()->requiresConsent());
    }

    public function test_v2_ignores_a_consent_id_it_has_no_use_for(): void
    {
        $this->assertSame(
            ['reg' => 'BR01AB1234'],
            $this->client()->payload('BR01AB1234', 'some-consent-id')
        );
    }

    public function test_v3_attaches_the_consent_id_under_the_configured_field(): void
    {
        $client = $this->client('v3', 'consentId');

        $this->assertTrue($client->requiresConsent());
        $this->assertSame(
            ['reg' => 'BR01AB1234', 'consentId' => 'CX3jpn-EDgaThaGkWO'],
            $client->payload('BR01AB1234', 'CX3jpn-EDgaThaGkWO')
        );
    }

    public function test_v3_without_the_field_name_refuses_to_build_a_request(): void
    {
        // The failure mode this guards against: someone flips the version to v3
        // and every lookup silently starts failing at the provider.
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches('/VEHICLE_API_CONSENT_FIELD/');

        $this->client('v3')->payload('BR01AB1234', 'CX3jpn-EDgaThaGkWO');
    }

    public function test_v3_without_a_consent_id_refuses_to_build_a_request(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches('/consent id is required/');

        $this->client('v3', 'consentId')->payload('BR01AB1234');
    }

    public function test_a_bare_key_is_sent_as_basic_and_a_prefixed_one_is_left_alone(): void
    {
        // Attestr documents "Basic {authToken}". Production's key already
        // carries the prefix, because the raw send works - but a freshly
        // configured one usually will not, and the failure is an opaque 401.
        Http::fake(['*' => Http::response(['valid' => true], 200)]);

        $this->client(key: 'rawtoken')->lookup('BR01AB1234');
        Http::assertSent(fn ($r) => $r->header('Authorization')[0] === 'Basic rawtoken');

        Http::fake(['*' => Http::response(['valid' => true], 200)]);

        $this->client(key: 'Basic alreadyprefixed')->lookup('BR01AB1234');
        Http::assertSent(fn ($r) => $r->header('Authorization')[0] === 'Basic alreadyprefixed');
    }

    public function test_a_provider_error_surfaces_the_status_rather_than_a_user_message(): void
    {
        // ProviderLookupException's contract is that its message is already safe
        // to show a user. This one is not, so it is a different type and each
        // service translates it into its own wording.
        Http::fake(['*' => Http::response('{"code":5001,"message":"Request could not be processed"}', 500)]);

        try {
            $this->client()->lookup('BR06PD4822');
            $this->fail('Expected AttestrRequestException.');
        } catch (AttestrRequestException $e) {
            $this->assertSame(500, $e->status);
            $this->assertStringContainsString('5001', $e->body);
        }
    }

    public function test_the_shipped_default_is_still_v2(): void
    {
        // v2 is confirmed still serving: successful lookups went through on
        // 01 Oct 2026, the stated transition date. The switch to v3 is a
        // deliberate env change once Attestr answers, not a default.
        $this->assertSame('v2', config('services.vehicle_api.version'));
        $this->assertSame('', config('services.vehicle_api.consent_field'));
    }
}
