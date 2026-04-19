<?php

namespace App\Services;

use App\Models\AdminChallanSearch;
use App\Models\Dealer;
use App\Models\Setting;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChallanSearchService
{
    protected string $apiUrl;

    protected string $secretKey;

    protected string $clientId;

    protected float $chargePerSearch;

    public function __construct()
    {
        $this->apiUrl = 'https://api.invincibleocean.com/invincible/vehicle-challan-detailed';
        $this->secretKey = config('services.service_history_api.secret_key', 'ONsoB7pAonpp1FYJ0Bf6sHFuGLYGEbytHxURPsEmK64gt3HR8yDtxwDafwMuCaonL');
        $this->clientId = config('services.service_history_api.client_id', '8f16f6344cbdcc74620cfdf7c87554f2:045cb470d3ae8da988c7e0982917e1ea');
        $this->chargePerSearch = Setting::getDealerChallanCharge();
    }

    public function getCharge(): float
    {
        return $this->chargePerSearch;
    }

    public function search(Dealer $dealer, string $vehicleNumber): array
    {
        $vehicleNum = strtoupper(preg_replace('/[^A-Z0-9]/', '', $vehicleNumber));

        $existing = AdminChallanSearch::where('dealer_id', $dealer->id)
            ->where('vehicle_number', $vehicleNum)
            ->where('is_success', true)
            ->where('created_at', '>=', now()->subHours(24))
            ->first();

        if ($existing) {
            return [
                'success' => true,
                'cached' => true,
                'data' => $existing,
                'message' => 'Retrieved from cache (last 24 hours)',
            ];
        }

        $walletBalance = $dealer->walletBalance();

        if ($walletBalance < $this->chargePerSearch) {
            return [
                'success' => false,
                'cached' => false,
                'data' => null,
                'message' => 'Insufficient wallet balance. Required: ₹'.number_format($this->chargePerSearch, 2),
            ];
        }

        try {
            $response = $this->callApi($vehicleNum);
            $result = $this->saveSearch($dealer, $vehicleNum, $response);

            if ($result->is_success) {
                $this->debitDealerWallet($dealer, $result, $vehicleNum);
            }

            return [
                'success' => $result->is_success,
                'cached' => false,
                'data' => $result,
                'message' => $result->is_success ? 'Challan details retrieved successfully' : ($result->error_message ?? 'Unknown error'),
            ];
        } catch (\Exception $e) {
            Log::error('Dealer Challan Search Error: '.$e->getMessage());

            $result = $this->saveSearch($dealer, $vehicleNum, [
                'code' => 500,
                'message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'cached' => false,
                'data' => $result,
                'message' => 'Error: '.$e->getMessage(),
            ];
        }
    }

    protected function debitDealerWallet(Dealer $dealer, AdminChallanSearch $challanSearch, string $vehicleNum): void
    {
        $wallet = $dealer->wallet;

        if ($wallet && $wallet->balance >= $this->chargePerSearch) {
            $wallet->balance -= $this->chargePerSearch;
            $wallet->save();

            WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'debit',
                'amount' => $this->chargePerSearch,
                'description' => 'E-Challan search for '.$vehicleNum,
                'reference_id' => $challanSearch->id,
                'reference_type' => 'challan_search',
            ]);
        }
    }

    protected function callApi(string $vehicleNumber): array
    {
        $response = Http::timeout(60)
            ->withHeaders([
                'Content-Type' => 'application/json',
                'secretKey' => $this->secretKey,
                'clientId' => $this->clientId,
            ])
            ->post($this->apiUrl, [
                'vehicleNumber' => $vehicleNumber,
            ]);

        if ($response->successful()) {
            return $response->json();
        }

        $statusCode = $response->status();
        $body = $response->body();

        Log::error('Challan API Error', [
            'status' => $statusCode,
            'body' => $body,
            'vehicleNumber' => $vehicleNumber,
        ]);

        throw new \Exception('API Error ('.$statusCode.'): '.substr($body, 0, 200));
    }

    protected function saveSearch(Dealer $dealer, string $vehicleNum, array $apiResponse): AdminChallanSearch
    {
        $isSuccess = ($apiResponse['code'] ?? 0) == 200;
        $challans = $isSuccess ? ($apiResponse['result'] ?? []) : [];

        $totalAmount = 0;
        $challanCount = count($challans);

        if ($challanCount > 0) {
            foreach ($challans as $challan) {
                $totalAmount += floatval($challan['amountChallan'] ?? 0);
            }
        }

        return AdminChallanSearch::create([
            'dealer_id' => $dealer->id,
            'vehicle_number' => $vehicleNum,
            'is_success' => $isSuccess,
            'charge_amount' => $isSuccess ? $this->chargePerSearch : null,
            'challan_data' => $isSuccess ? $challans : null,
            'total_amount' => $totalAmount,
            'challan_count' => $challanCount,
            'error_message' => $isSuccess ? null : ($apiResponse['message'] ?? 'Unknown error'),
        ]);
    }
}
