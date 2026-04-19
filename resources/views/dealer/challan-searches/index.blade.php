@extends('layouts.dealer')

@section('title', 'E-Challan Search - Dealer Panel')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h4><i class="bi bi-receipt me-2"></i>E-Challan Check</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <strong>Price: ₹{{ number_format($charge, 0) }}</strong> per search
                    </div>

                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <form action="{{ route('dealer.challan-search.search') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Vehicle Number</label>
                            <input type="text" name="vehicle_number" class="form-control" 
                                placeholder="e.g. BR01AB1234" required>
                            <small class="text-muted">Enter vehicle registration number</small>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search me-2"></i>Search Challan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Search History</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Vehicle Number</th>
                                    <th>Date</th>
                                    <th>Challans</th>
                                    <th>Total Amount</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($challans as $challan)
                                <tr>
                                    <td>{{ $challan->vehicle_number }}</td>
                                    <td>{{ $challan->created_at->format('d M Y') }}</td>
                                    <td>{{ $challan->challan_count }}</td>
                                    <td>₹{{ number_format($challan->total_amount ?? 0) }}</td>
                                    <td>
                                        <a href="{{ route('dealer.challan-search.show', $challan) }}" class="btn btn-sm btn-primary">
                                            View
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No searches yet</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ $challans->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection