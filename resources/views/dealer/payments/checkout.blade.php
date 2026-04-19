@extends('layouts.dealer')

@section('title', 'Payment Checkout')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Payment - {{ $typeLabel }}</h2>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Order Summary</h5>
            </div>
            <div class="card-body">
                <table class="table">
                    <tr>
                        <td>Type</td>
                        <td><strong>{{ $typeLabel }}</strong></td>
                    </tr>
                    <tr>
                        <td>Amount</td>
                        <td><strong class="text-success">₹{{ number_format($amount, 2) }}</strong></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Payment Details</h5>
            </div>
            <div class="card-body">
                <form id="razorpay-form" action="{{ route('dealer.payments.success') }}" method="POST">
                    @csrf
                    <input type="hidden" name="razorpay_order_id" id="razorpay_order_id" value="{{ $order['order_id'] }}">
                    <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
                    <input type="hidden" name="razorpay_signature" id="razorpay_signature">
                    <input type="hidden" name="amount" value="{{ $order['amount'] * 100 }}">
                    <input type="hidden" name="type" value="{{ $type }}">
                    <input type="hidden" name="plan_id" value="{{ $planId ?? '' }}">
                    <input type="hidden" name="car_id" value="{{ $carId ?? '' }}">
                    <input type="hidden" name="days" value="{{ $days ?? '' }}">

                    <button type="button" id="rzp-button" class="btn btn-primary btn-lg w-100">
                        <i class="bi bi-credit-card"></i> Pay ₹{{ number_format($amount, 2) }} with Razorpay
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
var options = {
    "key": "{{ $keyId }}",
    "amount": {{ $order['amount'] * 100 }},
    "currency": "INR",
    "name": "CarMarket",
    "description": "{{ $typeLabel }}",
    "order_id": "{{ $order['order_id'] }}",
    "handler": function (response){
        document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;
        document.getElementById('razorpay_signature').value = response.razorpay_signature;
        document.getElementById('razorpay-form').submit();
    },
    "prefill": {
        "name": "{{ auth('dealer')->user()->name }}",
        "email": "{{ auth('dealer')->user()->email }}"
    },
    "theme": {
        "color": "#0d6efd"
    }
};
var rzp1 = new Razorpay(options);
document.getElementById('rzp-button').onclick = function(e){
    rzp1.open();
    e.preventDefault();
}
</script>
@endpush
