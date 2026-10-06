<?php

namespace App\Services;

use App\Models\CustomerVehicleSearch;
use App\Models\Setting;
use App\Exceptions\AttestrRequestException;
use App\Services\Attestr\AttestrErrors;
use App\Services\Attestr\AttestrRcClient;
use Illuminate\Support\Facades\Log;

class CustomerVehicleSearchService
{
    use \App\Services\Concerns\MapsProviderErrors;

    protected string $provider;

    protected float $chargePerSearch;

    public function __construct()
    {
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

        $cached = CustomerVehicleSearch::checkCache($regNumber, (string) ($customerInfo['phone'] ?? ''));
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
            Log::error('Customer Vehicle Search Error', ['reg_no' => $regNumber, 'error' => $e->getMessage()]);

            $userMessage = match (true) {
                $e instanceof \App\Exceptions\ProviderLookupException => $e->getMessage(),
                $e instanceof \Illuminate\Http\Client\ConnectionException => $this->providerConnectionMessage('vehicle').$this->refundNotice(),
                default => 'We could not complete this vehicle lookup.'.$this->refundNotice(),
            };

            $result = $this->saveSearch($regNumber, [
                'success' => false,
                'message' => $userMessage,
            ], $customerInfo);

            return [
                'success' => false,
                'cached' => false,
                'data' => $result,
                'message' => $userMessage,
            ];
        }
    }

    protected function callApi(string $registrationNumber): array
    {
        try {
            $data = AttestrRcClient::fromConfig()->lookup($registrationNumber);

            return [
                'success' => $data['valid'] ?? false,
                'data' => $data,
            ];
        } catch (AttestrRequestException $e) {
            // The client has already logged the provider status and body.
            return [
                'success' => false,
                'message' => (AttestrErrors::userMessage($e->attestrCode()) ?? $this->providerFailureMessage($e->status, 'vehicle'))
                    .$this->refundNotice(),
            ];
        }
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
