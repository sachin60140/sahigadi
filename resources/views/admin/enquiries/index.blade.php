@extends('layouts.admin')

@section('title', 'Enquiries - SAHI GADI Admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-chat-dots me-2"></i>Enquiries</h2>
</div>

<div class="card mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Filter Enquiries</h5>
        <a href="{{ route('admin.enquiries.exportExcel', request()->all()) }}" class="btn btn-success btn-sm">
            <i class="bi bi-file-earmark-excel me-1"></i>Export Excel
        </a>
    </div>
    <div class="card-body bg-light">
        <form action="{{ route('admin.enquiries.index') }}" method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label text-muted small">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Name or Phone..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label text-muted small">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="new" {{ request('status') == 'new' ? 'selected' : '' }}>New</option>
                    <option value="contacted" {{ request('status') == 'contacted' ? 'selected' : '' }}>Contacted</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label text-muted small">Dealer</label>
                <select name="dealer_id" class="form-select">
                    <option value="">All Dealers</option>
                    @foreach($dealers ?? [] as $dealer)
                    <option value="{{ $dealer->id }}" {{ request('dealer_id') == $dealer->id ? 'selected' : '' }}>{{ Str::limit($dealer->name, 20) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label text-muted small">Date From</label>
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label text-muted small">Date To</label>
                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100" title="Filter"><i class="bi bi-search"></i></button>
                <a href="{{ route('admin.enquiries.index') }}" class="btn btn-outline-secondary w-100 ms-2" title="Clear"><i class="bi bi-x-circle"></i></a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Phone</th>
                        <th>Car</th>
                        <th>Dealer</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($enquiries as $enquiry)
                    <tr>
                        <td>{{ $enquiry->created_at->format('d M Y, h:i A') }}</td>
                        <td>
                            <strong>{{ $enquiry->customer_name }}</strong>
                            @if($enquiry->customer_email)
                            <br><small class="text-muted">{{ $enquiry->customer_email }}</small>
                            @endif
                        </td>
                        <td>
                            <a href="tel:{{ $enquiry->customer_phone }}">{{ $enquiry->customer_phone }}</a>
                        </td>
                        <td>
                            @if($enquiry->actual_car)
                                <a href="{{ route('admin.cars.show', $enquiry->actual_car) }}">
                                    {{ Str::limit($enquiry->actual_car->title, 25) }}
                                </a>
                                <br><small class="text-muted">₹{{ number_format($enquiry->actual_car->price ?? 0) }}</small>
                            @else
                                <span class="text-danger">Car Deleted</span>
                            @endif
                        </td>
                        <td>
                            @if($enquiry->dealer_id)
                                @if($enquiry->dealer)
                                    <a href="{{ route('admin.dealers.show', $enquiry->dealer) }}">
                                        {{ $enquiry->dealer->name }}
                                    </a>
                                @else
                                    <span class="text-danger">Dealer Deleted</span>
                                @endif
                            @else
                                <span class="badge bg-info">Owner Sale</span>
                            @endif
                        </td>
                        <td>
                            @if($enquiry->status === 'new')
                                <span class="badge bg-danger">New</span>
                            @else
                                <span class="badge bg-secondary">Contacted</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.enquiries.show', $enquiry) }}" class="btn btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if($enquiry->status === 'new')
                                <form action="{{ route('admin.enquiries.contacted', $enquiry) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-success" title="Mark as Contacted">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                </form>
                                @endif
                                @php
                                    $carDetails = $enquiry->actual_car ? " {$enquiry->actual_car->title} (" . ($enquiry->actual_car->year ?? '') . " " . ucfirst($enquiry->actual_car->fuel_type ?? '') . ")" : "";
                                    $waText = urlencode("Hi {$enquiry->customer_name},\n\nThank you for your interest in the{$carDetails} listed on SAHI GADI!\n\nPlease let us know if you need any further details or would like to schedule a visit.");
                                @endphp
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $enquiry->customer_phone) }}?text={{ $waText }}" target="_blank" class="btn btn-outline-success" title="Send WhatsApp Message">
                                    <i class="bi bi-whatsapp"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">No enquiries found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $enquiries->withQueryString()->links() }}
    </div>
</div>
@endsection
