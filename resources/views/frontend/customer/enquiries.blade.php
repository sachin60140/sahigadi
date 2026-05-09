@extends('layouts.app')

@section('title', 'My Enquiries - SAHI GADI')

@section('content')
<div class="container py-5">
    <div class="row mb-4 align-items-center">
        <div class="col-md-8 d-flex align-items-center">
            <button class="btn btn-light rounded-circle me-3 d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#customerSidebar" aria-controls="customerSidebar">
                <i class="bi bi-list fs-5"></i>
            </button>
            <div>
                <h2 class="fw-bold mb-1">My Enquiries</h2>
                <p class="text-muted mb-0">View all customer enquiries for your listed cars</p>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3 mb-4 mb-lg-0">
            @include('frontend.customer.partials.sidebar')
        </div>

        <!-- Main Content -->
        <div class="col-lg-9">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    @if($enquiries->isEmpty())
                        <div class="text-center py-5">
                            <i class="bi bi-chat-square-text text-muted mb-3" style="font-size: 3rem;"></i>
                            <h5 class="fw-bold">No Enquiries Yet</h5>
                            <p class="text-muted">When customers unlock contact details for your cars, they will appear here.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Car</th>
                                        <th>Customer Details</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($enquiries as $enquiry)
                                        <tr>
                                            <td class="text-muted small">
                                                {{ $enquiry->created_at->format('d M Y') }}<br>
                                                {{ $enquiry->created_at->format('h:i A') }}
                                            </td>
                                            <td>
                                                @if($enquiry->customerCar)
                                                    <a href="{{ route('car.detail', $enquiry->customerCar->slug) }}" target="_blank" class="text-decoration-none fw-bold text-dark">
                                                        {{ \Illuminate\Support\Str::limit($enquiry->customerCar->title, 30) }}
                                                    </a>
                                                    <br>
                                                    <span class="badge bg-light text-secondary border small mb-1">#{{ $enquiry->customerCar->unique_id }}</span>
                                                    <div class="small text-muted mt-1">
                                                        <i class="bi bi-calendar-event"></i> {{ $enquiry->customerCar->year }} | 
                                                        <span class="text-uppercase"><i class="bi bi-card-text"></i> {{ $enquiry->customerCar->registration_number }}</span>
                                                    </div>
                                                @else
                                                    <span class="text-danger">Car Deleted</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="fw-bold">{{ $enquiry->customer_name ?? 'Customer' }}</div>
                                                <div><a href="tel:{{ $enquiry->customer_phone }}" class="text-decoration-none"><i class="bi bi-telephone-fill small me-1 text-muted"></i>{{ $enquiry->customer_phone }}</a></div>
                                            </td>
                                            <td>
                                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-2">
                                                    <i class="bi bi-check-circle-fill me-1"></i>Contact Unlocked
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if($enquiries->hasPages())
                            <div class="mt-4 d-flex justify-content-center">
                                {{ $enquiries->links('pagination::bootstrap-5') }}
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
