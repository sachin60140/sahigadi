<?php

namespace App\Services;

use App\Models\CustomerVehicleSearch;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CustomerVehicleSearchService
{
    protected string $apiUrl;

    protected string $apiKey;

    protected string $provider;

    protected float $chargePerSearch;

    public function __construct()
    {
        $this->apiUrl = config('services.vehicle_api.url', 'https://api.attestr.com/api/v2/public/checkx/rc');
        $this->apiKey = config('services.vehicle_api.key', '');
        $this->provider = config('services.vehicle_api.provider', 'attestr');
        $this->chargePerSearch = Setting::getVehicleSearchCharge();
    }

    public function getCharge(): float
    {
        return $this->chargePerSearch;
    }

    public function search(string $registrationNumber, array $customerInfo): array
    {
        $regNumber = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $registrationNumber));

        $cached = CustomerVehicleSearch::checkCache($regNumber);
        if ($cached) {
            return [
                'success' => true,
                'cached' => true,
                'data' => $cached,
                'message' => 'Retrieved from cache (last 24 hours)',
            ];
        }

        try {
            $response = $this->callApi($regNumber);
            $result = $this->saveSearch($regNumber, $response, $customerInfo);

            return [
                'success' => $result->is_success,
                'cached' => false,
                'data' => $result,
                'message' => $result->is_success ? 'Vehicle details retrieved successfully' : ($result->error_message ?? 'Unknown error'),
            ];
        } catch (\Exception $e) {
            Log::error('Customer Vehicle Search Error: '.$e->getMessage());

            $result = $this->saveSearch($regNumber, [
                'success' => false,
                'message' => $e->getMessage(),
            ], $customerInfo);

            return [
                'success' => false,
                'cached' => false,
                'data' => $result,
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
            $data = $response->json();
            
            return [
                'success' => $data['valid'] ?? false,
                'data' => $data,
            ];
        }

        $statusCode = $response->status();
        $body = $response->body();
        
        Log::error('Attestr API Error (Customer RC)', [
            'status' => $statusCode,
            'body' => $body,
            'reg_no' => $registrationNumber,
        ]);

        return [
            'success' => false,
            'message' => 'API Error ('.$statusCode.'): '.substr($body, 0, 200),
        ];
    }

    protected function saveSearch(string $regNumber, array $apiResponse, array $customerInfo): CustomerVehicleSearch
    {
        $isSuccess = $apiResponse['success'] ?? false;
        $rawData = $apiResponse['data'] ?? [];
        
        $vehicleData = null;
        if ($isSuccess && !empty($rawData)) {
            $vehicleData = [
                'rc_status' => $rawData['status'] ?? null,
                'registration_date' => $this->parseDate($rawData['registered'] ?? null),
                'owner_name' => $rawData['owner'] ?? null,
                'owner_number' => $rawData['ownerNumber'] ?? null,
                'father_name' => $rawData['father'] ?? null,
                'current_address' => $rawData['currentAddress'] ?? null,
                'permanent_address' => $rawData['permanentAddress'] ?? null,
                'mobile_number' => $rawData['mobile'] ?? null,
                'vehicle_category' => $rawData['category'] ?? null,
                'category_description' => $rawData['categoryDescription'] ?? null,
                'chassis_number' => $rawData['chassisNumber'] ?? null,
                'engine_number' => $rawData['engineNumber'] ?? null,
                'make' => $rawData['makerDescription'] ?? null,
                'model' => $rawData['makerModel'] ?? null,
                'variant' => $rawData['makerVariant'] ?? null,
                'body_type' => $rawData['bodyType'] ?? null,
                'fuel_type' => $rawData['fuelType'] ?? null,
                'color' => $rawData['colorType'] ?? null,
                'norms_type' => $rawData['normsType'] ?? null,
                'fitness_valid_till' => $this->parseDate($rawData['fitnessUpto'] ?? null),
                'financed' => isset($rawData['financed']) ? ($rawData['financed'] ? 'Yes' : 'No') : null,
                'lender_name' => $rawData['lender'] ?? null,
                'insurance_provider' => $rawData['insuranceProvider'] ?? null,
                'insurance_policy_number' => $rawData['insurancePolicyNumber'] ?? null,
                'insurance_valid_till' => $this->parseDate($rawData['insuranceUpto'] ?? null),
                'manufactured_month_year' => $rawData['manufactured'] ?? null,
                'rto_location' => $rawData['rto'] ?? null,
                'cubic_capacity' => $rawData['cubicCapacity'] ?? null,
                'gross_weight' => $rawData['grossWeight'] ?? null,
                'wheel_base' => $rawData['wheelBase'] ?? null,
                'unladen_weight' => $rawData['unladenWeight'] ?? null,
                'cylinders' => $rawData['cylinders'] ?? null,
                'seating_capacity' => $rawData['seatingCapacity'] ?? null,
                'sleeping_capacity' => $rawData['sleepingCapacity'] ?? null,
                'standing_capacity' => $rawData['standingCapacity'] ?? null,
                'puc_number' => $rawData['pollutionCertificateNumber'] ?? null,
                'puc_valid_till' => $this->parseDate($rawData['pollutionCertificateUpto'] ?? null),
                'permit_number' => $rawData['permitNumber'] ?? null,
                'permit_issued' => $this->parseDate($rawData['permitIssued'] ?? null),
                'permit_from' => $this->parseDate($rawData['permitFrom'] ?? null),
                'permit_upto' => $this->parseDate($rawData['permitUpto'] ?? null),
                'permit_type' => $rawData['permitType'] ?? null,
                'tax_valid_till' => $this->parseDate($rawData['taxUpto'] ?? null),
                'tax_paid_upto' => $rawData['taxPaidUpto'] ?? null,
                'national_permit_number' => $rawData['nationalPermitNumber'] ?? null,
                'national_permit_issued' => $this->parseDate($rawData['nationalPermitIssued'] ?? null),
                'national_permit_from' => $this->parseDate($rawData['nationalPermitFrom'] ?? null),
                'national_permit_upto' => $this->parseDate($rawData['nationalPermitUpto'] ?? null),
                'national_permit_issued_by' => $rawData['nationalPermitIssuedBy'] ?? null,
                'is_commercial' => isset($rawData['commercial']) ? ($rawData['commercial'] ? 'Yes' : 'No') : null,
                'blacklist_status' => $rawData['blacklistStatus'] ?? null,
                'noc_details' => $rawData['nocDetails'] ?? null,
                'ex_showroom_price' => $rawData['exShowroomPrice'] ?? null,
                'non_use_status' => $rawData['nonUseStatus'] ?? null,
                'non_use_from' => $this->parseDate($rawData['nonUseFrom'] ?? null),
                'non_use_to' => $this->parseDate($rawData['nonUseTo'] ?? null),
            ];
            
            // Remove null values and empty strings to keep JSON clean
            $vehicleData = array_filter($vehicleData, function($value) {
                return $value !== null && $value !== '';
            });
        }

        return CustomerVehicleSearch::create([
            'customer_name' => $customerInfo['name'] ?? null,
            'customer_email' => $customerInfo['email'] ?? null,
            'customer_phone' => $customerInfo['phone'] ?? null,
            'registration_number' => $regNumber,
            'is_success' => $isSuccess,
            'paid_amount' => $this->chargePerSearch,
            'vehicle_data' => $vehicleData,
            'error_message' => $isSuccess ? null : ($apiResponse['message'] ?? $rawData['error'] ?? $rawData['message'] ?? 'Unknown error'),
        ]);
    }

    protected function parseDate($date): ?string
    {
        if (empty($date)) {
            return null;
        }

        try {
            return date('d M Y', strtotime($date));
        } catch (\Exception $e) {
            return null;
        }
    }

    public function updatePaymentInfo(CustomerVehicleSearch $search, string $orderId, string $paymentId, float $amount): void
    {
        $search->update([
            'razorpay_order_id' => $orderId,
            'razorpay_payment_id' => $paymentId,
            'paid_amount' => $amount,
        ]);
    }
}
