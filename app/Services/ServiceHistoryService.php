<?php

namespace App\Services;

use App\Models\AdminServiceHistory;
use App\Models\Dealer;
use App\Models\ServiceHistory;
use App\Models\ServiceHistoryRecord;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ServiceHistoryService
{
    protected string $apiUrl;

    protected string $secretKey;

    protected string $clientId;

    protected float $chargePerSearch;

    protected string $provider;

    public function __construct()
    {
        $this->apiUrl = config('services.service_history_api.url', 'https://api.invincibleocean.com/invincible/mahindra-service-history');
        $this->secretKey = config('services.service_history_api.secret_key', 'ONsoB7pAonpp1FYJ0Bf6sHFuGLYGEbytHxURPsEmK64gt3HR8yDtxwDafwMuCaonL');
        $this->clientId = config('services.service_history_api.client_id', '8f16f6344cbdcc74620cfdf7c87554f2:045cb470d3ae8da988c7e0982917e1ea');
        $this->chargePerSearch = Setting::getDealerServiceHistoryCharge();
        $this->provider = 'mahindra';
    }

    public function search(Dealer $dealer, string $vehicleNumber): array
    {
        $vehicleNum = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $vehicleNumber));

        $existing = ServiceHistory::where('dealer_id', $dealer->id)
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
            Log::error('Service History Search Error: '.$e->getMessage());

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

        Log::error('Service History API Error', [
            'status' => $statusCode,
            'body' => $body,
            'vehicleNumber' => $vehicleNumber,
        ]);

        throw new \Exception('API Error ('.$statusCode.'): '.substr($body, 0, 200));
    }

    protected function saveServiceHistory(Dealer $dealer, string $vehicleNum, array $apiResponse): ServiceHistory
    {
        $isSuccess = ($apiResponse['code'] ?? 0) == 200;
        $result = $apiResponse['result'] ?? null;
        $serviceRecords = $result['serviceHistoryDetails'] ?? [];

        $serviceHistory = ServiceHistory::updateOrCreate(
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
            ServiceHistoryRecord::create([
                'service_history_id' => $serviceHistory->id,
                'chassis_no' => $record['chassis_no'] ?? null,
                'location_code' => $record['location_code'] ?? null,
                'location_name' => $record['location_name'] ?? null,
                'mileage' => $record['mileage'] ?? null,
                'net_bill_amt' => $record['net_bill_amt'] ?? null,
                'online_payment_flag' => $record['online_payment_flag'] ?? null,
                'out_standing_amt' => $record['out_standing_amt'] ?? null,
                'paid_amt' => $record['paid_amt'] ?? null,
                'dealer_code' => $record['dealer_code'] ?? null,
                'dealer_name' => $record['dealer_name'] ?? null,
                'repair_order_bill_date' => $record['repair_order_bill_date'] ?? null,
                'repair_order_bill_no' => $record['repair_order_bill_no'] ?? null,
                'svc_date' => $record['svc_date'] ?? null,
                'repair_order_no' => $record['repair_order_no'] ?? null,
                'register_no' => $record['register_no'] ?? null,
                'service_assistant_no' => $record['service_assistant_no'] ?? null,
                'service_assistant_name' => $record['service_assistant_name'] ?? null,
                'work_type' => $record['work_type'] ?? null,
                'status' => $record['status'] ?? null,
                'service_cate' => $record['service_cate'] ?? null,
            ]);
        }

        AdminServiceHistory::createFromServiceHistory($serviceHistory, $this->chargePerSearch);

        return $serviceHistory;
    }

    protected function debitDealerWallet(Dealer $dealer, ServiceHistory $serviceHistory, string $vehicleNum): void
    {
        $dealer->debitWallet(
            $this->chargePerSearch,
            "Service History - {$vehicleNum} | Records: ".$serviceHistory->records()->count()
        );

        $serviceHistory->update(['debit_amount' => $this->chargePerSearch]);
    }

    public function getCharge(): float
    {
        return $this->chargePerSearch;
    }
}
