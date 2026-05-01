@extends('layouts.app')

@section('title', 'Payment - SAHI GADI')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card border-0 shadow-lg">
                <div class="card-body p-5 text-center">
                    <i class="bi bi-credit-card" style="font-size: 4rem; color: var(--accent);"></i>
                    <h3 class="fw-bold mt-3">Complete Payment</h3>
                    <p class="text-muted mb-4">
                        Vehicle: <strong>{{ $vehicleNumber }}</strong>
                    </p>

                    <div class="alert alert-light border mb-4">
                        <h4 class="mb-0">₹{{ number_format($amount, 0) }}</h4>
                        <small class="text-muted">Maruti Service History Report</small>
                    </div>

                    <form id="payment-form" action="{{ route('maruti-service-history.callback') }}" method="POST">
                        @csrf
                        <input type="hidden" name="razorpay_order_id" value="{{ $orderId }}">
                        <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
                        <input type="hidden" name="razorpay_signature" id="razorpay_signature">
                        
                        <button type="button" id="pay-button" class="btn btn-accent w-100 py-3">
                            <i class="bi bi-lock me-2"></i>Pay Now ₹{{ number_format($amount, 0) }}
                        </button>
                    </form>

                    <div class="mt-4">
                        <small class="text-muted">
                            <i class="bi bi-shield-check me-1"></i>Secure payment via Razorpay
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
    const options = {
        "key": "{{ $keyId }}",
        "amount": "{{ $amount * 100 }}",
        "currency": "INR",
        "name": "SAHI GADI",
        "description": "Maruti Service History Report - {{ $vehicleNumber }}",
        "order_id": "{{ $orderId }}",
        "handler": function (response) {
            document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;
            document.getElementById('razorpay_signature').value = response.razorpay_signature;
            document.getElementById('payment-form').submit();
        },
        "theme": {
            "color": "#e94560"
        }
    };

    const rzp1 = new Razorpay(options);
    
    rzp1.on('payment.failed', function (response) {
        alert('Payment failed: ' + response.error.description);
    });
    
    document.getElementById('pay-button').onclick = function(e) {
        rzp1.open();
        e.preventDefault();
    }
</script>
@endpush
@endsection