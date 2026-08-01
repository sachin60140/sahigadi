<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'type', 'group', 'description'];

    public static function get($key, $default = null)
    {
        $setting = static::where('key', $key)->first();

        return $setting ? $setting->value : $default;
    }

    public static function set($key, $value, $type = 'string', $group = 'general', $description = null): void
    {
        static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'type' => $type,
                'group' => $group,
                'description' => $description,
            ]
        );
    }

    public static function getVehicleSearchCharge(): float
    {
        return (float) static::get('vehicle_search_charge', config('services.vehicle_api.charge', 50));
    }

    public static function setVehicleSearchCharge(float $amount): void
    {
        static::set('vehicle_search_charge', $amount, 'number', 'services', 'Charge per vehicle search in rupees');
    }

    public static function getDealerVehicleSearchCharge(): float
    {
        return (float) static::get('dealer_vehicle_search_charge', config('services.vehicle_api.dealer_charge', 20));
    }

    public static function setDealerVehicleSearchCharge(float $amount): void
    {
        static::set('dealer_vehicle_search_charge', $amount, 'number', 'services', 'Charge per vehicle search for dealers');
    }

    // ---- GST tax invoice settings -------------------------------------------

    public static function getInvoicePrefix(): string
    {
        return (string) static::get('invoice_prefix', 'AW/SG');
    }

    public static function getInvoiceSupplierName(): string
    {
        return (string) static::get('invoice_supplier_name', 'Awani Enterprises');
    }

    public static function getInvoiceSupplierGstin(): string
    {
        return (string) static::get('invoice_supplier_gstin', '09CEKPS2342H1Z8');
    }

    public static function getInvoiceSupplierAddress(): string
    {
        return (string) static::get(
            'invoice_supplier_address',
            'UGF-4-5, Parsvanath Majestic Arcade, Vaibhav Khand, Indirapuram, Ghaziabad, UP - 201014'
        );
    }

    public static function getInvoiceSupplierState(): string
    {
        return (string) static::get('invoice_supplier_state', 'Uttar Pradesh');
    }

    public static function getInvoiceSacCode(): string
    {
        return (string) static::get('invoice_sac_code', '998399');
    }

    public static function getInvoiceGstRate(): float
    {
        return (float) static::get('invoice_gst_rate', 18);
    }

    public static function setInvoiceSettings(array $values): void
    {
        $map = [
            'invoice_prefix' => 'string',
            'invoice_supplier_name' => 'string',
            'invoice_supplier_gstin' => 'string',
            'invoice_supplier_address' => 'string',
            'invoice_supplier_state' => 'string',
            'invoice_sac_code' => 'string',
            'invoice_gst_rate' => 'number',
        ];

        foreach ($map as $key => $type) {
            if (array_key_exists($key, $values) && $values[$key] !== null) {
                static::set($key, $values[$key], $type, 'invoicing', 'GST invoice setting');
            }
        }
    }

    public static function getDealerApiVehicleSearchCharge(): float
    {
        return (float) static::get('dealer_api_vehicle_search_charge', static::getDealerVehicleSearchCharge());
    }

    public static function setDealerApiVehicleSearchCharge(float $amount): void
    {
        static::set('dealer_api_vehicle_search_charge', $amount, 'number', 'services', 'Charge per dealer API vehicle search in rupees');
    }

    public static function isDealerApiEnabled(): bool
    {
        return (bool) static::get('dealer_api_enabled', false);
    }

    public static function setDealerApiEnabled(bool $enabled): void
    {
        static::set('dealer_api_enabled', $enabled ? '1' : '0', 'boolean', 'services', 'Master switch for the dealer vehicle-search API');
    }

    public static function getChallanCharge(): float
    {
        return (float) static::get('challan_charge', 50);
    }

    public static function setChallanCharge(float $amount): void
    {
        static::set('challan_charge', $amount, 'number', 'services', 'Charge per e-challan search');
    }

    public static function getDealerChallanCharge(): float
    {
        return (float) static::get('dealer_challan_charge', 25);
    }

    public static function setDealerChallanCharge(float $amount): void
    {
        static::set('dealer_challan_charge', $amount, 'number', 'services', 'Charge per e-challan search for dealers');
    }

    public static function isChallanPdfActive(): bool
    {
        return (bool) static::get('is_challan_pdf_active', true);
    }

    public static function setIsChallanPdfActive(bool $isActive): void
    {
        static::set('is_challan_pdf_active', $isActive ? '1' : '0', 'boolean', 'services', 'Is Challan PDF Service Active');
    }

    public static function getChallanPdfCharge(): float
    {
        return (float) static::get('challan_pdf_charge', 49);
    }

    public static function setChallanPdfCharge(float $amount): void
    {
        static::set('challan_pdf_charge', $amount, 'number', 'services', 'Charge per Challan PDF search for customers');
    }

    public static function getDealerChallanPdfCharge(): float
    {
        return (float) static::get('dealer_challan_pdf_charge', 29);
    }

    public static function setDealerChallanPdfCharge(float $amount): void
    {
        static::set('dealer_challan_pdf_charge', $amount, 'number', 'services', 'Charge per Challan PDF search for dealers');
    }

    public static function getServiceHistoryCharge(): float
    {
        return (float) static::get('service_history_charge', config('services.service_history_api.charge', 500));
    }

    public static function setServiceHistoryCharge(float $amount): void
    {
        static::set('service_history_charge', $amount, 'number', 'services', 'Charge per service history search in rupees');
    }

    public static function getDealerServiceHistoryCharge(): float
    {
        return (float) static::get('dealer_service_history_charge', config('services.service_history_api.dealer_charge', 250));
    }

    public static function setDealerServiceHistoryCharge(float $amount): void
    {
        static::set('dealer_service_history_charge', $amount, 'number', 'services', 'Charge per service history for dealers');
    }

    public static function getMarutiServiceHistoryCharge(): float
    {
        return (float) static::get('maruti_service_history_charge', 500);
    }

    public static function setMarutiServiceHistoryCharge(float $amount): void
    {
        static::set('maruti_service_history_charge', $amount, 'number', 'services', 'Charge per maruti service history search in rupees');
    }

    public static function getDealerMarutiServiceHistoryCharge(): float
    {
        return (float) static::get('dealer_maruti_service_history_charge', 250);
    }

    public static function setDealerMarutiServiceHistoryCharge(float $amount): void
    {
        static::set('dealer_maruti_service_history_charge', $amount, 'number', 'services', 'Charge per maruti service history for dealers');
    }

    public static function getMahindraServiceHistoryCharge(): float
    {
        return (float) static::get('mahindra_service_history_charge', 500);
    }

    public static function setMahindraServiceHistoryCharge(float $amount): void
    {
        static::set('mahindra_service_history_charge', $amount, 'number', 'services', 'Charge per mahindra service history search in rupees');
    }

    public static function getDealerMahindraServiceHistoryCharge(): float
    {
        return (float) static::get('dealer_mahindra_service_history_charge', 250);
    }

    public static function setDealerMahindraServiceHistoryCharge(float $amount): void
    {
        static::set('dealer_mahindra_service_history_charge', $amount, 'number', 'services', 'Charge per mahindra service history for dealers');
    }

    public static function getRazorpayKeyId(): ?string
    {
        return static::get('razorpay_key_id', config('services.razorpay.key'));
    }

    public static function setRazorpayKeyId(string $keyId): void
    {
        static::set('razorpay_key_id', $keyId, 'string', 'payment', 'Razorpay Key ID');
    }

    public static function getRazorpayKeySecret(): ?string
    {
        return static::get('razorpay_key_secret', config('services.razorpay.secret'));
    }

    public static function setRazorpayKeySecret(string $keySecret): void
    {
        static::set('razorpay_key_secret', $keySecret, 'string', 'payment', 'Razorpay Key Secret');
    }

    public static function getMinimumWalletRechargeAmount(): float
    {
        return (float) static::get('min_wallet_recharge_amount', 100);
    }

    public static function setMinimumWalletRechargeAmount(float $amount): void
    {
        static::set('min_wallet_recharge_amount', $amount, 'number', 'payment', 'Minimum dealer wallet recharge amount');
    }

    public static function getCustomerMinimumWalletRechargeAmount(): float
    {
        return (float) static::get('customer_min_wallet_recharge_amount', 100);
    }

    public static function setCustomerMinimumWalletRechargeAmount(float $amount): void
    {
        static::set('customer_min_wallet_recharge_amount', $amount, 'number', 'payment', 'Minimum customer wallet recharge amount');
    }

    public static function getPhonePeMerchantId(): ?string
    {
        return static::get('phonepe_merchant_id', config('services.phonepe.merchant_id'));
    }

    public static function setPhonePeMerchantId(string $merchantId): void
    {
        static::set('phonepe_merchant_id', $merchantId, 'string', 'payment', 'PhonePe Client ID');
    }

    public static function getPhonePeSaltKey(): ?string
    {
        return static::get('phonepe_salt_key', config('services.phonepe.salt_key'));
    }

    public static function setPhonePeSaltKey(string $saltKey): void
    {
        static::set('phonepe_salt_key', $saltKey, 'string', 'payment', 'PhonePe Client Secret');
    }

    public static function getPhonePeSaltIndex(): string
    {
        return static::get('phonepe_salt_index', config('services.phonepe.salt_index', '1'));
    }

    public static function setPhonePeSaltIndex(string $saltIndex): void
    {
        static::set('phonepe_salt_index', $saltIndex, 'string', 'payment', 'PhonePe Client Version');
    }

    public static function getPhonePeEnvironment(): string
    {
        return static::get('phonepe_env', config('services.phonepe.env', 'UAT'));
    }

    public static function setPhonePeEnvironment(string $env): void
    {
        static::set('phonepe_env', $env, 'string', 'payment', 'PhonePe Environment (UAT or PRODUCTION)');
    }

    public static function getPhonePeCheckoutUrl(): ?string
    {
        return static::get('phonepe_checkout_url', config('services.phonepe.checkout_url'));
    }

    public static function setPhonePeCheckoutUrl(?string $url): void
    {
        static::set('phonepe_checkout_url', $url, 'string', 'payment', 'PhonePe Custom Checkout API URL');
    }

    public static function isRazorpayActive(): bool
    {
        return (bool) static::get('is_razorpay_active', true);
    }

    public static function setIsRazorpayActive(bool $isActive): void
    {
        static::set('is_razorpay_active', $isActive ? '1' : '0', 'boolean', 'payment', 'Is Razorpay Active');
    }

    public static function isPhonePeActive(): bool
    {
        return (bool) static::get('is_phonepe_active', false);
    }

    public static function setIsPhonePeActive(bool $isActive): void
    {
        static::set('is_phonepe_active', $isActive ? '1' : '0', 'boolean', 'payment', 'Is PhonePe Active');
    }
}
