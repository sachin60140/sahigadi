<?php

namespace App\Services;

use App\Models\AdminMarutiServiceHistory;
use App\Models\Dealer;
use App\Models\MarutiServiceHistory;
use App\Models\MarutiServiceHistoryRecord;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MarutiServiceHistoryService
{
    protected string $apiUrl;

    protected string $secretKey;

    protected string $clientId;

    protected float $chargePerSearch;

    public function __construct()
    {
        $this->apiUrl = 'https://api.invincibleocean.com/invincible/maruti-service-history';
        $this->secretKey = config('services.service_history_api.secret_key', 'ONsoB7pAonpp1FYJ0Bf6sHFuGLYGEbytHxURPsEmK64gt3HR8yDtxwDafwMuCaonL');
        $this->clientId = config('services.service_history_api.client_id', '8f16f6344cbdcc74620cfdf7c87554f2:045cb470d3ae8da988c7e0982917e1ea');
        $this->chargePerSearch = Setting::getDealerMarutiServiceHistoryCharge();
    }

    public function getCharge(): float
    {
        return $this->chargePerSearch;
    }

    public function search(Dealer $dealer, string $vehicleNumber): array
    {
        $vehicleNum = strtoupper(preg_replace('/[^A-Z0-9]/', '', $vehicleNumber));

        $existing = MarutiServiceHistory::where('dealer_id', $dealer->id)
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
                'message' => 'Insufficient wallet balance. Required: Rs.'.number_format($this->chargePerSearch, 2),
            ];
        }

        try {
            $response = $this->callApi($vehicleNum);

            $serviceHistory = $this->saveServiceHistory($dealer, $vehicleNum, $response);

            if ($serviceHistory->is_success) {
                $this->debitDealerWallet($dealer, $serviceHistory, $vehicleNum);
            }

            return [
                'success' => $serviceHistory->is_success,
                'cached' => false,
                'data' => $serviceHistory,
                'message' => $serviceHistory->is_success ? 'Service history retrieved successfully' : ($serviceHistory->error_message ?? 'Unknown error'),
            ];
        } catch (\Exception $e) {
            Log::error('Maruti Service History Search Error: '.$e->getMessage());

            $serviceHistory = $this->saveServiceHistory($dealer, $vehicleNum, [
                'code' => 500,
                'message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'cached' => false,
                'data' => $serviceHistory,
                'message' => 'Error: '.$e->getMessage(),
            ];
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

        Log::error('Maruti Service History API Error', [
            'status' => $statusCode,
            'body' => $body,
            'vehicleNumber' => $vehicleNumber,
        ]);

        throw new \Exception('API Error ('.$statusCode.'): '.substr($body, 0, 200));
    }

    protected function saveServiceHistory(Dealer $dealer, string $vehicleNum, array $apiResponse): MarutiServiceHistory
    {
        $isSuccess = ($apiResponse['code'] ?? 0) == 200;
        $result = $apiResponse['result'] ?? null;
        $serviceRecords = $result['serviceHistoryDetails'] ?? [];

        $serviceHistory = MarutiServiceHistory::updateOrCreate(
            [
                'dealer_id' => $dealer->id,
                'vehicle_number' => $vehicleNum,
            ],
            [
                'is_success' => $isSuccess,
                'error_message' => $isSuccess ? null : ($apiResponse['message'] ?? 'Unknown error'),
                'raw_response' => $apiResponse,
            ]
        );

        $serviceHistory->records()->delete();

        foreach ($serviceRecords as $record) {
            MarutiServiceHistoryRecord::create([
                'maruti_service_history_id' => $serviceHistory->id,
                'mileage' => $record['mileage'] ?? null,
                'total_amount' => $record['totalAmount'] ?? null,
                'dealer_code' => $record['dealerNo'] ?? null,
                'dealer_name' => $record['dealerName'] ?? null,
                'dealer_address' => $record['dealerAddress'] ?? null,
                'repair_order_bill_date' => $record['dateOfBill'] ?? null,
                'repair_order_bill_no' => $record['noOfRO'] ?? null,
                'svc_date' => $record['dateOfSVC'] ?? null,
                'repair_order_no' => $record['noOfRO'] ?? null,
                'register_no' => $record['noOfJobCard'] ?? null,
                'service_assistant_name' => $record['nameOfSA'] ?? null,
                'work_type' => $record['serviceType'] ?? null,
                'status' => $record['typOfPayment'] ?? null,
                'service_cate' => $record['serviceType'] ?? null,
                'chassis_no' => $record['srVehicleCd'] ?? null,
                'location_code' => $record['cdLoc'] ?? null,
                'part_amount' => $record['partAmmount'] ?? null,
                'labour_amount' => $record['labourAmmount'] ?? null,
            ]);
        }

        AdminMarutiServiceHistory::createFromMarutiServiceHistory($serviceHistory, $this->chargePerSearch);

        return $serviceHistory;
    }

    protected function debitDealerWallet(Dealer $dealer, MarutiServiceHistory $serviceHistory, string $vehicleNum): void
    {
        $dealer->debitWallet(
            $this->chargePerSearch,
            "Maruti Service History - {$vehicleNum} | Records: ".$serviceHistory->records()->count()
        );

        $serviceHistory->update(['debit_amount' => $this->chargePerSearch]);
    }
}
