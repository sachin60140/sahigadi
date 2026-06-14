<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\WalletTransaction;
use App\Models\Payment;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PhonePeService
{
    protected string $clientId;
    protected string $clientSecret;
    protected string $clientVersion;
    protected string $env;

    public function __construct()
    {
        // For V2, the Merchant ID field in our settings is used as Client ID
        $this->clientId = trim(Setting::getPhonePeMerchantId() ?? '');
        // Salt Key is used as Client Secret
        $this->clientSecret = trim(Setting::getPhonePeSaltKey() ?? '');
        // Salt Index is used as Client Version
        $this->clientVersion = trim(Setting::getPhonePeSaltIndex() ?? '1');
        $this->env = strtoupper(trim(Setting::getPhonePeEnvironment() ?? 'UAT'));
    }

    protected function getTokenUrl(): string
    {
        $customUrl = Setting::getPhonePeCheckoutUrl();
        if ($customUrl) {
            $this->assertSupportedCheckoutUrl($customUrl);

            if (str_contains($customUrl, 'api-preprod.phonepe.com')) {
                return str_replace('/checkout/v2/pay', '/v1/oauth/token', $customUrl);
            } else {
                $tokenUrl = str_replace('/pg/checkout/v2/pay', '/identity-manager/v1/oauth/token', $customUrl);
                if ($tokenUrl !== $customUrl) {
                    return $tokenUrl;
                }
                
                return str_replace('/checkout/v2/pay', '/v1/oauth/token', $customUrl);
            }
        }

        return $this->env === 'PRODUCTION'
            ? 'https://api.phonepe.com/apis/identity-manager/v1/oauth/token'
            : 'https://api-preprod.phonepe.com/apis/pg-sandbox/v1/oauth/token';
    }

    protected function getPaymentUrl(): string
    {
        $customUrl = Setting::getPhonePeCheckoutUrl();
        if ($customUrl) {
            $this->assertSupportedCheckoutUrl($customUrl);

            return $customUrl;
        }

        return $this->env === 'PRODUCTION'
            ? 'https://api.phonepe.com/apis/pg/checkout/v2/pay'
            : 'https://api-preprod.phonepe.com/apis/pg-sandbox/checkout/v2/pay';
    }

    protected function getStatusUrl(string $transactionId): string
    {
        $paymentUrl = $this->getPaymentUrl();
        return str_replace('/pay', "/order/{$transactionId}/status", $paymentUrl);
    }

    protected function getAccessToken(): string
    {
        $this->assertConfiguration();

        $cacheKey = 'phonepe_access_token_'.hash(
            'sha256',
            implode('|', [$this->env, $this->clientId, $this->clientVersion, $this->clientSecret])
        );

        $cachedToken = Cache::get($cacheKey);
        if (is_string($cachedToken) && $cachedToken !== '') {
            return $cachedToken;
        }

        $tokenUrl = $this->getTokenUrl();

        try {
            $response = Http::asForm()
                ->acceptJson()
                ->connectTimeout(8)
                ->timeout(20)
                ->post($tokenUrl, [
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'client_version' => $this->clientVersion,
                    'grant_type' => 'client_credentials',
                ]);
        } catch (\Throwable $exception) {
            Log::error('PhonePe authentication request failed', [
                'environment' => $this->env,
                'endpoint_host' => parse_url($tokenUrl, PHP_URL_HOST),
                'error' => $exception->getMessage(),
            ]);

            throw new Exception('Unable to connect to PhonePe. Please verify that outbound HTTPS requests are allowed by the server.');
        }

        $token = $response->json('access_token');
        if ($response->successful() && is_string($token) && $token !== '') {
            $expiresAt = (int) $response->json('expires_at', time() + 3000);
            $ttl = max(60, $expiresAt - time() - 60);
            Cache::put($cacheKey, $token, $ttl);

            return $token;
        }

        $this->logGatewayFailure('authentication', $response->status(), $response->json());

        throw new Exception($this->gatewayErrorMessage(
            'PhonePe authentication failed',
            $response->status(),
            $response->json()
        ));
    }

    public function testConnection(): array
    {
        $this->getAccessToken();

        return [
            'success' => true,
            'environment' => $this->env,
            'payment_endpoint' => $this->getPaymentUrl(),
        ];
    }

    public function createPayment(float $amount, string $transactionId, array $callbackParams = []): array
    {
        $this->assertPaymentRequest($amount, $transactionId);

        $redirectUrl = $callbackParams['redirectUrl'] ?? route('dealer.payments.phonepe.callback');
        $this->assertRedirectUrl($redirectUrl);

        $payload = [
            'merchantOrderId' => $transactionId,
            'amount' => (int) round($amount * 100),
            'expireAfter' => 1200,
            'paymentFlow' => [
                'type' => 'PG_CHECKOUT',
                'merchantUrls' => [
                    'redirectUrl' => $redirectUrl,
                ],
            ],
        ];

        $paymentUrl = $this->getPaymentUrl();

        $token = $this->getAccessToken();

        try {
            $response = Http::acceptJson()
                ->asJson()
                ->withHeaders(['Authorization' => 'O-Bearer '.$token])
                ->connectTimeout(8)
                ->timeout(20)
                ->post($paymentUrl, $payload);
        } catch (\Throwable $exception) {
            Log::error('PhonePe payment initiation request failed', [
                'environment' => $this->env,
                'merchant_order_id' => $transactionId,
                'endpoint_host' => parse_url($paymentUrl, PHP_URL_HOST),
                'error' => $exception->getMessage(),
            ]);

            throw new Exception('Unable to connect to PhonePe checkout. Please try again shortly.');
        }

        if ($response->successful()) {
            $redirectUrl = $response->json('redirectUrl')
                ?? $response->json('data.redirectUrl');

            if (! is_string($redirectUrl) || ! filter_var($redirectUrl, FILTER_VALIDATE_URL)) {
                $this->logGatewayFailure('missing_redirect_url', $response->status(), $response->json());

                throw new Exception('PhonePe created the order but did not return a valid checkout URL.');
            }

            return [
                'success' => true,
                'redirect_url' => $redirectUrl,
                'order_id' => $response->json('orderId') ?? $response->json('data.orderId'),
                'state' => $response->json('state') ?? $response->json('data.state'),
            ];
        }

        $this->logGatewayFailure('payment_initiation', $response->status(), $response->json());

        throw new Exception($this->gatewayErrorMessage(
            'PhonePe payment initiation failed',
            $response->status(),
            $response->json()
        ));
    }

    public function verifyPaymentStatus(string $transactionId): array
    {
        $this->assertTransactionId($transactionId);

        $statusUrl = $this->getStatusUrl($transactionId);
        $token = $this->getAccessToken();

        try {
            $response = Http::acceptJson()
                ->withHeaders(['Authorization' => 'O-Bearer '.$token])
                ->connectTimeout(8)
                ->timeout(20)
                ->get($statusUrl, [
                    'details' => 'false',
                    'errorContext' => 'true',
                ]);
        } catch (\Throwable $exception) {
            Log::error('PhonePe order status request failed', [
                'environment' => $this->env,
                'merchant_order_id' => $transactionId,
                'endpoint_host' => parse_url($statusUrl, PHP_URL_HOST),
                'error' => $exception->getMessage(),
            ]);

            throw new Exception('Unable to verify the PhonePe payment status. Please try again shortly.');
        }

        if ($response->successful()) {
            $data = $response->json();
            $state = $data['state'] ?? ($data['data']['state'] ?? 'UNKNOWN');
            $amount = $data['amount'] ?? ($data['data']['amount'] ?? 0);
            $providerRef = $data['paymentDetails'][0]['transactionId']
                ?? $data['transactionId']
                ?? $data['providerReferenceId']
                ?? $data['data']['paymentDetails'][0]['transactionId']
                ?? $data['data']['transactionId']
                ?? $data['data']['providerReferenceId']
                ?? null;
            
            return [
                'success' => $state === 'COMPLETED',
                'status' => $state,
                'amount' => (float) ($amount / 100),
                'transaction_id' => $transactionId,
                'provider_reference_id' => $providerRef,
                'error_code' => $data['errorCode']
                    ?? $data['errorContext']['errorCode']
                    ?? $data['data']['errorCode']
                    ?? null,
                'error_description' => $data['errorContext']['description']
                    ?? $data['data']['errorContext']['description']
                    ?? null,
            ];
        }

        $this->logGatewayFailure('order_status', $response->status(), $response->json());

        throw new Exception($this->gatewayErrorMessage(
            'PhonePe payment status could not be verified',
            $response->status(),
            $response->json()
        ));
    }

    public function processPayment($dealer, string $transactionId, float $expectedAmount, string $type)
    {
        $status = $this->verifyPaymentStatus($transactionId);

        if (!$status['success']) {
            throw new Exception("PhonePe payment was not successful. Status: " . $status['status']);
        }

        if ((int) round($status['amount'] * 100) !== (int) round($expectedAmount * 100)) {
            throw new Exception("Payment amount mismatch. Expected: {$expectedAmount}, Received: {$status['amount']}");
        }

        DB::beginTransaction();
        try {
            // Prevent duplicate processing - this logic was previously dealer specific using WalletTransaction
            // But we don't strictly enforce reference_id unique across both wallet transaction types.
            // A better way is checking the Payment model.
            $existingPayment = Payment::where('phonepe_transaction_id', $transactionId)
                ->where('status', 'completed')
                ->lockForUpdate()
                ->first();

            if ($existingPayment) {
                DB::commit();
                return $existingPayment;
            }

            $payment = Payment::updateOrCreate(
                ['phonepe_transaction_id' => $transactionId],
                [
                    'dealer_id' => ($dealer instanceof \App\Models\Dealer) ? $dealer->id : null,
                    'customer_id' => ($dealer instanceof \App\Models\Customer) ? $dealer->id : null,
                    'amount' => $status['amount'],
                    'status' => 'completed',
                    'payment_gateway' => 'phonepe',
                    'type' => $type,
                    'reference_id' => $status['provider_reference_id'] ?? null,
                ]
            );

            if ($type === 'wallet_recharge') {
                $walletCreditAmount = round($status['amount'] / 1.18, 2);
                
                if ($dealer instanceof \App\Models\Dealer) {
                    $walletService = app(\App\Services\WalletService::class);
                    $walletService->credit($dealer->id, $walletCreditAmount, 'Wallet recharge via PhonePe', $transactionId, 'payment');
                } elseif ($dealer instanceof \App\Models\Customer) {
                    $dealer->wallet()->firstOrCreate([])->addFunds(
                        $walletCreditAmount,
                        'Wallet recharge via PhonePe',
                        $transactionId,
                        'payment'
                    );
                }
            }

            DB::commit();
            return $payment;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    protected function assertConfiguration(): void
    {
        if ($this->clientId === '' || $this->clientSecret === '' || $this->clientVersion === '') {
            throw new Exception('PhonePe v2 credentials are incomplete. Configure Client ID, Client Secret, and Client Version.');
        }

        if (! in_array($this->env, ['UAT', 'PRODUCTION'], true)) {
            throw new Exception('PhonePe environment must be UAT or PRODUCTION.');
        }
    }

    protected function assertPaymentRequest(float $amount, string $transactionId): void
    {
        if ((int) round($amount * 100) < 100) {
            throw new Exception('PhonePe requires a minimum payment amount of Rs 1.00.');
        }

        $this->assertTransactionId($transactionId);
    }

    protected function assertTransactionId(string $transactionId): void
    {
        if (! preg_match('/^[A-Za-z0-9_-]{1,63}$/', $transactionId)) {
            throw new Exception('PhonePe merchant order ID is invalid.');
        }
    }

    protected function assertSupportedCheckoutUrl(string $url): void
    {
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        $path = rtrim((string) parse_url($url, PHP_URL_PATH), '/');
        $allowedHosts = ['api.phonepe.com', 'api-preprod.phonepe.com'];

        if (! in_array($host, $allowedHosts, true) || ! str_ends_with($path, '/checkout/v2/pay')) {
            throw new Exception('The custom PhonePe URL must be an official Checkout v2 /checkout/v2/pay endpoint.');
        }
    }

    protected function assertRedirectUrl(string $url): void
    {
        if (! filter_var($url, FILTER_VALIDATE_URL)) {
            throw new Exception('PhonePe callback URL is invalid. Check the live APP_URL configuration.');
        }

        if ($this->env === 'PRODUCTION' && parse_url($url, PHP_URL_SCHEME) !== 'https') {
            throw new Exception('PhonePe production callback URL must use HTTPS. Check APP_URL and proxy configuration.');
        }
    }

    protected function gatewayErrorMessage(string $prefix, int $status, mixed $payload): string
    {
        $code = is_array($payload) ? ($payload['code'] ?? null) : null;
        $message = is_array($payload) ? ($payload['message'] ?? null) : null;
        $details = collect([$code, $message])->filter()->implode(': ');

        return $prefix.' (HTTP '.$status.')'.($details !== '' ? ' '.$details : '');
    }

    protected function logGatewayFailure(string $stage, int $status, mixed $payload): void
    {
        Log::error('PhonePe gateway request failed', [
            'stage' => $stage,
            'environment' => $this->env,
            'http_status' => $status,
            'code' => is_array($payload) ? ($payload['code'] ?? null) : null,
            'message' => is_array($payload) ? ($payload['message'] ?? null) : null,
        ]);
    }
}
