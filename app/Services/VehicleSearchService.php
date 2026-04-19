<?php

namespace App\Services;

use App\Models\AdminVehicleSearch;
use App\Models\Dealer;
use App\Models\Setting;
use App\Models\VehicleDetail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VehicleSearchService
{
    protected string $apiUrl;

    protected string $apiKey;

    protected float $chargePerSearch;

    protected string $provider;

    public function __construct()
    {
        $this->apiUrl = config('services.vehicle_api.url', 'https://api.attestr.com/api/v2/public/checkx/rc');
        $this->apiKey = config('services.vehicle_api.key', '');
        $this->provider = config('services.vehicle_api.provider', 'attestr');
        $this->chargePerSearch = Setting::getDealerVehicleSearchCharge();
    }

    public function search(Dealer $dealer, string $registrationNumber): array
    {
        $regNumber = strtoupper(preg_replace('/[^A-Z0-9]/', '', $registrationNumber));

        $existing = VehicleDetail::where('dealer_id', $dealer->id)
            ->where('registration_number', $regNumber)
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
            $response = $this->callApi($regNumber);

            $vehicleDetail = $this->saveVehicleDetail($dealer, $regNumber, $response);

            if ($vehicleDetail->is_success) {
                $this->debitDealerWallet($dealer, $vehicleDetail, $regNumber);
            }

            return [
                'success' => $vehicleDetail->is_success,
                'cached' => false,
                'data' => $vehicleDetail,
                'message' => $vehicleDetail->is_success ? 'Vehicle details retrieved successfully' : ($vehicleDetail->error_message ?? 'Unknown error'),
            ];
        } catch (\Exception $e) {
            Log::error('Vehicle Search Error: '.$e->getMessage());

            $vehicleDetail = $this->saveVehicleDetail($dealer, $regNumber, [
                'valid' => false,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'cached' => false,
                'data' => $vehicleDetail,
                'message' => 'Error: '.$e->getMessage(),
            ];
        }
    }

    protected function callApi(string $registrationNumber): array
    {
        $response = Http::timeout(60)
            ->withHeaders([
                'Authorization' => $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])
            ->post($this->apiUrl, [
                'reg' => $registrationNumber,
            ]);

        if ($response->successful()) {
            return $response->json();
        }

        $statusCode = $response->status();
        $body = $response->body();

        Log::error('Attestr API Error', [
            'status' => $statusCode,
            'body' => $body,
            'reg_no' => $registrationNumber,
        ]);

        throw new \Exception('API Error ('.$statusCode.'): '.substr($body, 0, 200));
    }

    protected function saveVehicleDetail(Dealer $dealer, string $regNumber, array $apiResponse): VehicleDetail
    {
        $isSuccess = $apiResponse['valid'] ?? false;

        $detailData = [
            'dealer_id' => $dealer->id,
            'registration_number' => $regNumber,
            'raw_response' => $apiResponse,
            'is_success' => $isSuccess,
            'api_provider' => $this->provider,
            'error_message' => $isSuccess ? null : ($apiResponse['error'] ?? $apiResponse['message'] ?? 'Unknown error'),
        ];

        if ($isSuccess) {
            $detailData['owner_name'] = $apiResponse['owner'] ?? null;
            $detailData['father_name'] = $apiResponse['father'] ?? null;
            $detailData['address'] = $apiResponse['currentAddress'] ?? $apiResponse['permanentAddress'] ?? null;
            $detailData['mobile_number'] = $apiResponse['mobile'] ?? null;
            $detailData['vehicle_category'] = $apiResponse['category'] ?? null;
            $detailData['vehicle_class'] = $apiResponse['bodyType'] ?? null;
            $detailData['make'] = $apiResponse['makerDescription'] ?? null;
            $detailData['model'] = $apiResponse['makerModel'] ?? null;
            $detailData['variant'] = $apiResponse['makerVariant'] ?? null;
            $detailData['chassis_number'] = $apiResponse['chassisNumber'] ?? null;
            $detailData['engine_number'] = $apiResponse['engineNumber'] ?? null;
            $detailData['color'] = $apiResponse['colorType'] ?? null;
            $detailData['fuel_type'] = $apiResponse['fuelType'] ?? null;
            $detailData['seats'] = $apiResponse['seatingCapacity'] ?? null;
            $detailData['rc_status'] = $apiResponse['status'] ?? null;
            $detailData['insurance_policy_number'] = $apiResponse['insurancePolicyNumber'] ?? null;
            $detailData['insurance_provider'] = $apiResponse['insuranceProvider'] ?? null;
            $detailData['puc_number'] = $apiResponse['pollutionCertificateNumber'] ?? null;
            $detailData['tax_validity'] = $this->parseDate($apiResponse['taxUpto'] ?? null);
            $detailData['tax_amount'] = $apiResponse['taxPaidUpto'] ?? null;
            $detailData['blacklist_status'] = $apiResponse['blacklistStatus'] ?? null;
            $detailData['financed'] = $apiResponse['financed'] ?? false;
            $detailData['lender_name'] = $apiResponse['lender'] ?? null;
            $detailData['rto_location'] = $apiResponse['rto'] ?? null;
            $detailData['norms_type'] = $apiResponse['normsType'] ?? null;
            $detailData['cubic_capacity'] = $apiResponse['cubicCapacity'] ?? null;
            $detailData['unladen_weight'] = $apiResponse['unladenWeight'] ?? null;
            $detailData['gross_weight'] = $apiResponse['grossWeight'] ?? null;
            $detailData['cylinders'] = $apiResponse['cylinders'] ?? null;
            $detailData['is_commercial'] = $apiResponse['commercial'] ?? false;
            $detailData['permit_number'] = $apiResponse['permitNumber'] ?? null;
            $detailData['permit_type'] = $apiResponse['permitType'] ?? null;
            $detailData['manufactured_date'] = $apiResponse['manufactured'] ?? null;
            $detailData['registration_date'] = $this->parseDate($apiResponse['registered'] ?? null);
            $detailData['fitness_date'] = $this->parseDate($apiResponse['fitnessUpto'] ?? null);
            $detailData['insurance_date'] = $this->parseDate($apiResponse['insuranceUpto'] ?? null);
            $detailData['puc_validity'] = $this->parseDate($apiResponse['pollutionCertificateUpto'] ?? null);
            $detailData['permit_validity'] = $this->parseDate($apiResponse['permitUpto'] ?? null);
        }

        $vehicleDetail = VehicleDetail::updateOrCreate(
            [
                'dealer_id' => $dealer->id,
                'registration_number' => $regNumber,
            ],
            $detailData
        );

        AdminVehicleSearch::createFromVehicleDetail($vehicleDetail, $this->chargePerSearch);

        return $vehicleDetail;
    }

    protected function parseDate($date): ?string
    {
        if (empty($date)) {
            return null;
        }

        try {
            return date('Y-m-d', strtotime($date));
        } catch (\Exception $e) {
            return null;
        }
    }

    protected function debitDealerWallet(Dealer $dealer, VehicleDetail $vehicleDetail, string $regNumber): void
    {
        $dealer->debitWallet(
            $this->chargePerSearch,
            "RC Search - {$regNumber} | Owner: ".($vehicleDetail->owner_name ?? 'N/A').' | '.($vehicleDetail->make ?? '').' '.($vehicleDetail->model ?? '')
        );

        $vehicleDetail->update(['debit_amount' => $this->chargePerSearch]);
    }

    public function getCharge(): float
    {
        return $this->chargePerSearch;
    }
}
