@extends('layouts.admin')

@section('title', 'Payment Settings')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0"><i class="bi bi-credit-card me-2"></i>Razorpay Payment Settings</h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <form action="{{ route('admin.payment-settings.update') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-12 mb-4">
                                <h5 class="border-bottom pb-2">Gateway Activation</h5>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" id="is_razorpay_active" name="is_razorpay_active" {{ $isRazorpayActive ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_razorpay_active">Enable Razorpay</label>
                                </div>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" id="is_phonepe_active" name="is_phonepe_active" {{ $isPhonePeActive ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_phonepe_active">Enable PhonePe</label>
                                </div>
                            </div>
                            
                            <div class="col-12 mb-4">
                                <h5 class="border-bottom pb-2">Razorpay Settings</h5>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Razorpay Key ID</label>
                                <input type="text" name="razorpay_key_id" class="form-control" 
                                    value="{{ old('razorpay_key_id', $keyId) }}" required>
                                <small class="text-muted">Your Razorpay Key ID (e.g., rzp_test_xxxxx)</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Razorpay Key Secret</label>
                                <input type="password" name="razorpay_key_secret" class="form-control" 
                                    value="{{ old('razorpay_key_secret', $keySecret) }}" required>
                                <small class="text-muted">Your Razorpay Key Secret</small>
                            </div>

                            <div class="col-12 mb-4 mt-4">
                                <h5 class="border-bottom pb-2">PhonePe Settings</h5>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">PhonePe Merchant ID (Client ID)</label>
                                <input type="text" name="phonepe_merchant_id" class="form-control" 
                                    value="{{ old('phonepe_merchant_id', $phonePeMerchantId) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">PhonePe Salt Key (Client Secret Key)</label>
                                <input type="password" name="phonepe_salt_key" class="form-control" 
                                    value="{{ old('phonepe_salt_key', $phonePeSaltKey) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">PhonePe Salt Index</label>
                                <input type="text" name="phonepe_salt_index" class="form-control" 
                                    value="{{ old('phonepe_salt_index', $phonePeSaltIndex) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Environment</label>
                                <select name="phonepe_env" class="form-control" required>
                                    <option value="UAT" {{ old('phonepe_env', $phonePeEnv) === 'UAT' ? 'selected' : '' }}>UAT (Testing)</option>
                                    <option value="PRODUCTION" {{ old('phonepe_env', $phonePeEnv) === 'PRODUCTION' ? 'selected' : '' }}>PRODUCTION</option>
                                </select>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">PhonePe Custom Checkout API URL (Optional)</label>
                                <input type="url" name="phonepe_checkout_url" class="form-control" 
                                    value="{{ old('phonepe_checkout_url', $phonePeCheckoutUrl) }}" placeholder="e.g. https://api.phonepe.com/apis/pg/checkout/v2/pay">
                                <small class="text-muted">If provided, this exact URL will be used for checkout, and the system will automatically derive the Auth Token and Status URLs from it.</small>
                            </div>

                            <div class="col-12 mb-4 mt-4">
                                <h5 class="border-bottom pb-2">General Settings</h5>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Dealer Minimum Wallet Recharge Amount (₹)</label>
                                <input type="number" name="min_wallet_recharge_amount" class="form-control" 
                                    value="{{ old('min_wallet_recharge_amount', $minRechargeAmount) }}" min="1" step="0.01" required>
                                <small class="text-muted">Minimum amount a dealer must recharge</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Customer Minimum Wallet Recharge Amount (₹)</label>
                                <input type="number" name="customer_min_wallet_recharge_amount" class="form-control" 
                                    value="{{ old('customer_min_wallet_recharge_amount', $customerMinRechargeAmount) }}" min="1" step="0.01" required>
                                <small class="text-muted">Minimum amount a customer must recharge</small>
                            </div>
                        </div>
                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-2"></i>Save Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection