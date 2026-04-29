@extends('layouts.frontend')

@section('title', 'Link Expired - ' . config('app.name'))

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card border-0 shadow-sm text-center py-5">
                <div class="card-body">
                    <div class="mb-4">
                        @if($payment_link->status === 'paid')
                            <i class="bi bi-check-circle text-success" style="font-size: 5rem;"></i>
                            <h3 class="fw-bold mt-3 text-dark">Already Paid</h3>
                            <p class="text-muted">This payment link has already been successfully processed.</p>
                        @else
                            <i class="bi bi-clock-history text-danger" style="font-size: 5rem;"></i>
                            <h3 class="fw-bold mt-3 text-dark">Link Expired</h3>
                            <p class="text-muted">This payment link is no longer valid. Please contact the administrator to generate a new link.</p>
                        @endif
                    </div>
                    <a href="{{ url('/') }}" class="btn btn-outline-primary px-4 rounded-pill">Return to Home</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
