<?php

namespace App\Services\Attestr;

use App\Exceptions\AttestrRequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * The single place the Attestr RC request is built.
 *
 * VehicleSearchService (dealers) and CustomerVehicleSearchService (customers)
 * had near-identical copies of this call. They are merged here so the DPDP V3
 * consent change lands in one file rather than two.
 *
 * ON V3 AND CONSENT
 *
 * Attestr's console shows the mandatory transition to "DPDPA Compliant V3 APIs
 * by 01 Oct 2026", with a version selector offering exactly "v3" and "v2", and
 * a mandatory "Consent Details" field alongside the RC number. Register Consent
 * (POST /api/v3/public/consent/register) is fully documented and returns
 * {"_id": ..., "number": ...}, and the docs say the "_id" "must subsequently be
 * passed in all related verification and KYC API requests".
 *
 * What is NOT published anywhere is the name of the request field that carries
 * that id on a product endpoint. It is absent from the Vehicle RC Check page in
 * HTML, at the /v3/ path and in the canonical .md source, from the DPDPA
 * implementation guide, and from the Bank Account Verification page that the
 * migration guide itself uses as its worked example.
 *
 * So the field name is configuration, not a constant, and switching to v3
 * without supplying it fails loudly here rather than silently sending a request
 * Attestr will reject. Set VEHICLE_API_VERSION=v3 and VEHICLE_API_CONSENT_FIELD
 * together once Attestr confirms the name. See docs/attestr-dpdpa-v3.md.
 */
class AttestrRcClient
{
    public function __construct(
        private readonly string $url,
        private readonly string $apiKey,
        private readonly string $version,
        private readonly string $consentField,
        private readonly int $timeout = 60,
    ) {}

    public static function fromConfig(): self
    {
        return new self(
            url: (string) config('services.vehicle_api.url'),
            apiKey: (string) config('services.vehicle_api.key', ''),
            version: (string) config('services.vehicle_api.version', 'v2'),
            consentField: (string) config('services.vehicle_api.consent_field', ''),
        );
    }

    /** V3 is consent-driven; v2 is the legacy model Attestr is retiring. */
    public function requiresConsent(): bool
    {
        return strtolower($this->version) === 'v3';
    }

    /**
     * Perform the lookup and return Attestr's decoded payload.
     *
     * @param  string|null  $consentId  The "_id" from Register Consent. Required on v3.
     *
     * @throws AttestrRequestException on any non-2xx response
     * @throws RuntimeException if v3 is configured without the consent field name
     */
    public function lookup(string $registrationNumber, ?string $consentId = null): array
    {
        $response = Http::timeout($this->timeout)
            ->withHeaders([
                'Authorization' => $this->authorizationHeader(),
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])
            ->post($this->url, $this->payload($registrationNumber, $consentId));

        if ($response->successful()) {
            return $response->json() ?? [];
        }

        // The full provider payload stays in the log; callers show the user a
        // clean message of their own.
        Log::error('Attestr API Error', [
            'status' => $response->status(),
            'body' => $response->body(),
            'reg_no' => $registrationNumber,
            'version' => $this->version,
        ]);

        throw new AttestrRequestException($response->status(), $response->body());
    }

    /** @return array<string, string> */
    public function payload(string $registrationNumber, ?string $consentId = null): array
    {
        $payload = ['reg' => $registrationNumber];

        if (! $this->requiresConsent()) {
            return $payload;
        }

        if ($this->consentField === '') {
            throw new RuntimeException(
                'VEHICLE_API_VERSION is v3 but VEHICLE_API_CONSENT_FIELD is not set. '
                .'Attestr has not published the request field that carries the consent id; '
                .'confirm it with their support before enabling v3.'
            );
        }

        if ($consentId === null || $consentId === '') {
            throw new RuntimeException(
                'A registered consent id is required for a v3 RC lookup. '
                .'Register one first via POST /api/v3/public/consent/register.'
            );
        }

        $payload[$this->consentField] = $consentId;

        return $payload;
    }

    /**
     * Attestr documents "Basic {authToken}". The existing key is sent raw and
     * works in production, so it already carries the prefix - but a freshly
     * configured key usually will not, and the failure is an opaque 401.
     * Normalising here means either form works.
     */
    private function authorizationHeader(): string
    {
        $key = trim($this->apiKey);

        if ($key === '' || str_starts_with($key, 'Basic ') || str_starts_with($key, 'Bearer ')) {
            return $key;
        }

        return 'Basic '.$key;
    }
}
