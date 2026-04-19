@extends('layouts.dealer')

@section('title', 'Enquiries')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Enquiries</h2>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('dealer.enquiries.index') }}" method="GET" class="row g-3 mb-3">
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="all">All Status</option>
                    <option value="new" {{ request('status') == 'new' ? 'selected' : '' }}>New</option>
                    <option value="contacted" {{ request('status') == 'contacted' ? 'selected' : '' }}>Contacted</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-secondary">Filter</button>
                <a href="{{ route('dealer.enquiries.index') }}" class="btn btn-outline-secondary">Clear</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Phone</th>
                        <th>Car</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($enquiries as $enquiry)
                    <tr>
                        <td>{{ $enquiry->created_at->format('d M Y') }}</td>
                        <td>{{ $enquiry->customer_name }}</td>
                        <td>
                            <a href="tel:{{ $enquiry->customer_phone }}">{{ $enquiry->customer_phone }}</a>
                            @if($enquiry->customer_email)
                            <br><small class="text-muted">{{ $enquiry->customer_email }}</small>
                            @endif
                        </td>
                        <td><a href="{{ route('car.detail', $enquiry->car->slug ?? '#') }}">{{ Str::limit($enquiry->car->title ?? 'N/A', 25) }}</a></td>
                        <td>
                            @if($enquiry->status === 'new')
                                <span class="badge bg-danger">New</span>
                            @else
                                <span class="badge bg-secondary">Contacted</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('dealer.enquiries.show', $enquiry) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $enquiry->customer_phone) }}" target="_blank" class="btn btn-sm btn-outline-success">
                                <i class="bi bi-whatsapp"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4">No enquiries found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $enquiries->withQueryString()->links() }}
    </div>
</div>
@endsection
