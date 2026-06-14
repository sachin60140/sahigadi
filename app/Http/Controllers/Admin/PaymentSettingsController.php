<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\PhonePeService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class PaymentSettingsController extends Controller
{
    public function index()
    {
        $keyId = Setting::getRazorpayKeyId();
        $keySecret = Setting::getRazorpayKeySecret();
        $minRechargeAmount = Setting::getMinimumWalletRechargeAmount();
        $customerMinRechargeAmount = Setting::getCustomerMinimumWalletRechargeAmount();

        $phonePeMerchantId = Setting::getPhonePeMerchantId();
        $phonePeSaltKey = Setting::getPhonePeSaltKey();
        $phonePeSaltIndex = Setting::getPhonePeSaltIndex();
        $phonePeEnv = Setting::getPhonePeEnvironment();
        $phonePeCheckoutUrl = Setting::getPhonePeCheckoutUrl();

        $isRazorpayActive = Setting::isRazorpayActive();
        $isPhonePeActive = Setting::isPhonePeActive();

        return Inertia::render('Admin/Finance/PaymentSettings', [
            'settings' => [
                'razorpay_key_id' => $keyId,
                'razorpay_key_secret' => $keySecret,
                'min_wallet_recharge_amount' => $minRechargeAmount,
                'customer_min_wallet_recharge_amount' => $customerMinRechargeAmount,
                'phonepe_merchant_id' => $phonePeMerchantId,
                'phonepe_salt_key' => $phonePeSaltKey,
                'phonepe_salt_index' => $phonePeSaltIndex,
                'phonepe_env' => $phonePeEnv,
                'phonepe_checkout_url' => $phonePeCheckoutUrl,
                'is_razorpay_active' => $isRazorpayActive,
                'is_phonepe_active' => $isPhonePeActive,
            ],
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'razorpay_key_id' => 'required|string',
            'razorpay_key_secret' => 'required|string',
            'min_wallet_recharge_amount' => 'required|numeric|min:1',
            'customer_min_wallet_recharge_amount' => 'required|numeric|min:1',
            'phonepe_merchant_id' => 'required|string',
            'phonepe_salt_key' => 'required|string',
            'phonepe_salt_index' => 'required|string',
            'phonepe_env' => 'required|in:UAT,PRODUCTION',
            'phonepe_checkout_url' => 'nullable|url',
        ]);

        Setting::setRazorpayKeyId($request->razorpay_key_id);
        Setting::setRazorpayKeySecret($request->razorpay_key_secret);
        Setting::setMinimumWalletRechargeAmount($request->min_wallet_recharge_amount);
        Setting::setCustomerMinimumWalletRechargeAmount($request->customer_min_wallet_recharge_amount);

        Setting::setPhonePeMerchantId($request->phonepe_merchant_id);
        Setting::setPhonePeSaltKey($request->phonepe_salt_key);
        Setting::setPhonePeSaltIndex($request->phonepe_salt_index);
        Setting::setPhonePeEnvironment($request->phonepe_env);
        Setting::setPhonePeCheckoutUrl($request->phonepe_checkout_url);

        Setting::setIsRazorpayActive($request->has('is_razorpay_active'));
        Setting::setIsPhonePeActive($request->has('is_phonepe_active'));

        return redirect()->back()->with('success', 'Payment Gateway settings updated successfully!');
    }

    public function testPhonePe(PhonePeService $phonePeService)
    {
        try {
            $result = $phonePeService->testConnection();

            return redirect()->back()->with(
                'success',
                "PhonePe {$result['environment']} authentication succeeded."
            );
        } catch (Exception $exception) {
            Log::warning('PhonePe connection test failed', [
                'environment' => Setting::getPhonePeEnvironment(),
                'error' => $exception->getMessage(),
            ]);

            return redirect()->back()->with('error', $exception->getMessage());
        }
    }
}
