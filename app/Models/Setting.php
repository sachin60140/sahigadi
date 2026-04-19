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
}
