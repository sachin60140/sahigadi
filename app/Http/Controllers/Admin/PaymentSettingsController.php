<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

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

        return view('admin.payment-settings.index', compact(
            'keyId', 'keySecret', 'minRechargeAmount', 'customerMinRechargeAmount',
            'phonePeMerchantId', 'phonePeSaltKey', 'phonePeSaltIndex', 'phonePeEnv', 'phonePeCheckoutUrl',
            'isRazorpayActive', 'isPhonePeActive'
        ));
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
}
