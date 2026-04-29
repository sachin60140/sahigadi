@extends('layouts.app')

@section('title', 'Secure Payment - ' . config('app.name'))

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card border-0 shadow-lg" style="border-radius: 1rem;">
                <div class="card-body p-4 p-md-5">
                    @if(session('success'))
                        <div class="text-center mb-4">
                            <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                            <h3 class="mt-3 fw-bold text-dark">Payment Successful!</h3>
                            <p class="text-muted">Thank you for your payment.</p>
                            <a href="{{ url('/') }}" class="btn btn-outline-primary mt-3 rounded-pill px-4">Return Home</a>
                        </div>
                    @else
                        @if(session('error'))
                            <div class="alert alert-danger mb-4">
                                {{ session('error') }}
                            </div>
                        @endif

                        <div class="text-center mb-4">
                            <h3 class="fw-bold text-dark mb-1">Secure Checkout</h3>
                            <p class="text-muted small">Complete your payment below</p>
                        </div>

                        <div class="bg-light p-4 rounded-3 mb-4 text-center">
                            <div class="text-muted small fw-medium text-uppercase mb-2">Amount to Pay</div>
                            <div class="display-5 fw-bold text-dark">₹{{ number_format($link->amount, 2) }}</div>
                            <hr class="my-3 border-secondary border-opacity-25">
                            <div class="d-flex justify-content-between text-start small">
                                <span class="text-muted">Purpose:</span>
                                <span class="fw-medium text-dark">{{ $link->purpose }}</span>
                            </div>
                            <div class="d-flex justify-content-between text-start small mt-2">
                                <span class="text-muted">Payee:</span>
                                <span class="fw-medium text-dark">{{ $link->dealer ? ($link->dealer->company_name ?? $link->dealer->name) : $link->customer_name }}</span>
                            </div>
                        </div>

                        <div class="d-grid gap-3">
                            @if(in_array($link->gateway, ['any', 'phonepe']) && $isPhonePeActive)
                                <form action="{{ route('pay.link.checkout', $link->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="gateway" value="phonepe">
                                    <button type="submit" class="btn btn-lg w-100 rounded-pill text-white fw-bold d-flex align-items-center justify-content-center gap-2" style="background: #5f259f; border: none; padding: 12px;">
                                        <i class="bi bi-phone"></i> Pay with PhonePe
                                    </button>
                                </form>
                            @endif

                            @if(in_array($link->gateway, ['any', 'razorpay']) && $isRazorpayActive && $order)
                                <form action="{{ route('pay.link.checkout', $link->id) }}" method="POST" id="razorpay-form">
                                    @csrf
                                    <input type="hidden" name="gateway" value="razorpay">
                                    <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
                                    <input type="hidden" name="razorpay_order_id" id="razorpay_order_id" value="{{ $order['order_id'] }}">
                                    <input type="hidden" name="razorpay_signature" id="razorpay_signature">
                                    
                                    <button type="button" id="rzp-button" class="btn btn-lg w-100 rounded-pill text-white fw-bold d-flex align-items-center justify-content-center gap-2" style="background: #3395ff; border: none; padding: 12px;">
                                        <i class="bi bi-credit-card"></i> Pay with Razorpay
                                    </button>
                                </form>

                                <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
                                <script>
                                    var options = {
                                        "key": "{{ $keyId }}",
                                        "amount": "{{ $order['amount'] * 100 }}",
                                        "currency": "INR",
                                        "name": "{{ config('app.name') }}",
                                        "description": "{{ $link->purpose }}",
                                        "order_id": "{{ $order['order_id'] }}",
                                        "handler": function (response){
                                            document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;
                                            document.getElementById('razorpay_signature').value = response.razorpay_signature;
                                            document.getElementById('razorpay-form').submit();
                                        },
                                        "prefill": {
                                            "name": "{{ $link->dealer ? $link->dealer->name : $link->customer_name }}",
                                            "email": "{{ $link->dealer ? $link->dealer->email : $link->customer_email }}",
                                            "contact": "{{ $link->dealer ? $link->dealer->phone : $link->customer_mobile }}"
                                        },
                                        "theme": {
                                            "color": "#3395ff"
                                        }
                                    };
                                    var rzp1 = new Razorpay(options);
                                    document.getElementById('rzp-button').onclick = function(e){
                                        rzp1.open();
                                        e.preventDefault();
                                    }
                                </script>
                            @endif

                            @if(!$isPhonePeActive && !$isRazorpayActive)
                                <div class="alert alert-warning text-center">
                                    Payments are currently disabled.
                                </div>
                            @endif
                        </div>
                        
                        <p class="text-center text-muted small mt-4 mb-0">
                            <i class="bi bi-shield-lock-fill text-success me-1"></i> Secured by 256-bit encryption
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
