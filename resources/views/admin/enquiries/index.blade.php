@extends('layouts.admin')

@section('title', 'Enquiries - SAHIGADI Admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-chat-dots me-2"></i>Enquiries</h2>
</div>

<div class="card">
    <div class="card-header bg-white">
        <form action="{{ route('admin.enquiries.index') }}" method="GET" class="row g-3">
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="new" {{ request('status') == 'new' ? 'selected' : '' }}>New</option>
                    <option value="contacted" {{ request('status') == 'contacted' ? 'selected' : '' }}>Contacted</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-secondary">Filter</button>
                <a href="{{ route('admin.enquiries.index') }}" class="btn btn-outline-secondary">Clear</a>
            </div>
        </form>
    </div>
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
                            @if($enquiry->car)
                                <a href="{{ route('admin.cars.show', $enquiry->car) }}">
                                    {{ Str::limit($enquiry->car->title, 25) }}
                                </a>
                                <br><small class="text-muted">₹{{ number_format($enquiry->car->price ?? 0) }}</small>
                            @else
                                <span class="text-danger">Car Deleted</span>
                            @endif
                        </td>
                        <td>
                            @if($enquiry->dealer)
                                <a href="{{ route('admin.dealers.show', $enquiry->dealer) }}">
                                    {{ $enquiry->dealer->name }}
                                </a>
                            @else
                                <span class="text-danger">Dealer Deleted</span>
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
                                <a href="{{ route('admin.enquiries.contacted', $enquiry) }}" class="btn btn-outline-success" title="Mark as Contacted">
                                    <i class="bi bi-check-lg"></i>
                                </a>
                                @endif
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $enquiry->customer_phone) }}" target="_blank" class="btn btn-outline-success">
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
