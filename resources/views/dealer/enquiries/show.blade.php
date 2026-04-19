@extends('layouts.dealer')

@section('title', 'Enquiry Details')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Enquiry Details</h2>
    <div>
        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $enquiry->customer_phone) }}" target="_blank" class="btn btn-success">
            <i class="bi bi-whatsapp"></i> WhatsApp
        </a>
        <a href="tel:{{ $enquiry->customer_phone }}" class="btn btn-primary">
            <i class="bi bi-telephone"></i> Call
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Customer Information</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <td class="text-muted">Name</td>
                        <td>{{ $enquiry->customer_name }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Email</td>
                        <td>{{ $enquiry->customer_email ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Phone</td>
                        <td><a href="tel:{{ $enquiry->customer_phone }}">{{ $enquiry->customer_phone }}</a></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Status</td>
                        <td>
                            @if($enquiry->status === 'new')
                                <span class="badge bg-danger">New</span>
                            @else
                                <span class="badge bg-secondary">Contacted</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Received On</td>
                        <td>{{ $enquiry->created_at->format('d M Y, h:i A') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Car Details</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <td class="text-muted">Car</td>
                        <td><a href="{{ route('car.detail', $enquiry->car->slug ?? '#') }}">{{ $enquiry->car->title ?? 'N/A' }}</a></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Price</td>
                        <td>₹{{ number_format($enquiry->car->price ?? 0) }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

@if($enquiry->message)
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">Customer Message</h5>
    </div>
    <div class="card-body">
        <p class="mb-0">{{ $enquiry->message }}</p>
    </div>
</div>
@endif

<div class="card">
    <div class="card-body">
        <form action="{{ route('dealer.enquiries.contacted', $enquiry) }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-success" {{ $enquiry->status === 'contacted' ? 'disabled' : '' }}>
                <i class="bi bi-check-circle"></i> Mark as Contacted
            </button>
        </form>
        <a href="{{ route('dealer.enquiries.index') }}" class="btn btn-secondary">Back</a>
    </div>
</div>
@endsection
