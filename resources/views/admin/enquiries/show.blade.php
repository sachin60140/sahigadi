@extends('layouts.admin')

@section('title', 'Enquiry Details - SAHI GADI Admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-chat-dots me-2"></i>Enquiry Details</h2>
    <a href="{{ route('admin.enquiries.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Back to Enquiries
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-person me-2"></i>Customer Information</h5>
                    @if($enquiry->status === 'new')
                        <span class="badge bg-danger">New</span>
                    @else
                        <span class="badge bg-secondary">Contacted</span>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Name</label>
                        <h5 class="mb-0">{{ $enquiry->customer_name }}</h5>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Email</label>
                        <h5 class="mb-0">{{ $enquiry->customer_email ?? 'N/A' }}</h5>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Phone</label>
                        <h5 class="mb-0">
                            <a href="tel:{{ $enquiry->customer_phone }}">{{ $enquiry->customer_phone }}</a>
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $enquiry->customer_phone) }}" target="_blank" class="btn btn-sm btn-success ms-2">
                                <i class="bi bi-whatsapp"></i> WhatsApp
                            </a>
                        </h5>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Enquiry Date</label>
                        <h5 class="mb-0">{{ $enquiry->created_at->format('d M Y, h:i A') }}</h5>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">IP Address</label>
                        <h5 class="mb-0">{{ $enquiry->ip_address ?? 'N/A' }}</h5>
                    </div>
                </div>
                @if($enquiry->message)
                <div class="mt-3">
                    <label class="text-muted small">Message</label>
                    <div class="p-3 bg-light rounded">
                        {{ $enquiry->message }}
                    </div>
                </div>
                @endif
            </div>
        </div>

        @if($enquiry->actual_car)
        <div class="card mb-4">
            <div class="card-header bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-car-front me-2"></i>Car Details</h5>
                    @if(!$enquiry->dealer_id)
                        <span class="badge bg-info"><i class="bi bi-person me-1"></i>Owner Sale</span>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <h5 class="mb-2">{{ $enquiry->actual_car->title }}</h5>
                        <p class="text-muted mb-2">
                            @if($enquiry->actual_car->brand)
                                {{ $enquiry->actual_car->brand->name }} |
                            @endif
                            {{ $enquiry->actual_car->year ?? 'N/A' }} |
                            {{ number_format($enquiry->actual_car->km_driven ?? 0) }} km |
                            {{ ucfirst($enquiry->actual_car->fuel_type ?? 'N/A') }}
                        </p>
                        <h4 class="text-accent mb-0">₹{{ number_format($enquiry->actual_car->price ?? 0) }}</h4>
                    </div>
                    <div class="col-md-4 text-end">
                        @if($enquiry->dealer_id)
                            <a href="{{ route('admin.cars.show', $enquiry->actual_car) }}" class="btn btn-outline-primary">
                                <i class="bi bi-eye me-2"></i>View Car
                            </a>
                        @endif
                        <a href="{{ route('car.detail', $enquiry->actual_car->slug) }}" target="_blank" class="btn btn-primary mt-2">
                            <i class="bi bi-box-arrow-up-right me-2"></i>View on Site
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="alert alert-danger mb-4">
            <i class="bi bi-exclamation-triangle me-2"></i>The associated car listing has been deleted or is no longer available.
        </div>
        @endif

        @if($enquiry->dealer_id)
            @if($enquiry->dealer)
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-shop me-2"></i>Dealer Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="mb-2">{{ $enquiry->dealer->name }}</h5>
                            <p class="text-muted mb-0">
                                @if($enquiry->dealer->phone)
                                    <i class="bi bi-telephone me-2"></i>{{ $enquiry->dealer->phone }}<br>
                                @endif
                                @if($enquiry->dealer->email)
                                    <i class="bi bi-envelope me-2"></i>{{ $enquiry->dealer->email }}
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6 text-end">
                            <a href="{{ route('admin.dealers.show', $enquiry->dealer) }}" class="btn btn-outline-primary">
                                <i class="bi bi-eye me-2"></i>View Dealer
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @else
            <div class="alert alert-danger mb-4">
                <i class="bi bi-exclamation-triangle me-2"></i>The associated dealer account has been deleted.
            </div>
            @endif
        @endif
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Actions</h5>
            </div>
            <div class="card-body">
                @if($enquiry->status === 'new')
                <a href="{{ route('admin.enquiries.contacted', $enquiry) }}" class="btn btn-success w-100 mb-3">
                    <i class="bi bi-check-lg me-2"></i>Mark as Contacted
                </a>
                @else
                <div class="alert alert-secondary mb-3">
                    <i class="bi bi-check-circle me-2"></i>Already contacted
                </div>
                @endif
                @php
                    $carDetails = $enquiry->actual_car ? " {$enquiry->actual_car->title} (" . ($enquiry->actual_car->year ?? '') . " " . ucfirst($enquiry->actual_car->fuel_type ?? '') . ")" : "";
                    $waText = urlencode("Hi {$enquiry->customer_name},\n\nThank you for your interest in the{$carDetails} listed on SAHI GADI!\n\nPlease let us know if you need any further details or would like to schedule a visit.");
                @endphp
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $enquiry->customer_phone) }}?text={{ $waText }}" target="_blank" class="btn btn-success w-100">
                    <i class="bi bi-whatsapp me-2"></i>Send WhatsApp Message
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
