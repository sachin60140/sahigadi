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