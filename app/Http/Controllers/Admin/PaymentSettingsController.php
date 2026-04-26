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

        return view('admin.payment-settings.index', compact('keyId', 'keySecret', 'minRechargeAmount'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'razorpay_key_id' => 'required|string',
            'razorpay_key_secret' => 'required|string',
            'min_wallet_recharge_amount' => 'required|numeric|min:1',
        ]);

        Setting::setRazorpayKeyId($request->razorpay_key_id);
        Setting::setRazorpayKeySecret($request->razorpay_key_secret);
        Setting::setMinimumWalletRechargeAmount($request->min_wallet_recharge_amount);

        return redirect()->back()->with('success', 'Razorpay settings updated successfully!');
    }
}
