@extends('layouts.admin')

@section('title', 'Challan PDF Service Settings')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Stats -->
        <div class="col-md-4">
            <div class="card mb-4 bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Searches</h5>
                    <h3>{{ $totalSearches }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card mb-4 bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Revenue</h5>
                    <h3>₹{{ number_format($totalRevenue, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card mb-4 bg-danger text-white">
                <div class="card-body">
                    <h5 class="card-title">Failed Requests</h5>
                    <h3>{{ $failedRequests }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Settings Form -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Service Settings</h6>
            <a href="{{ route('admin.challan-pdf.logs') }}" class="btn btn-sm btn-info">View Logs</a>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form action="{{ route('admin.challan-pdf.settings') }}" method="POST">
                @csrf
                <div class="form-group mb-3">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="is_challan_pdf_active" name="is_challan_pdf_active" value="1" {{ $settings['is_challan_pdf_active'] ? 'checked' : '' }}>
                        <label class="custom-control-label" for="is_challan_pdf_active">Enable Challan PDF Service</label>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Customer Price (₹)</label>
                        <input type="number" step="0.01" class="form-control" name="challan_pdf_charge" value="{{ old('challan_pdf_charge', $settings['challan_pdf_charge']) }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Dealer Price (₹)</label>
                        <input type="number" step="0.01" class="form-control" name="dealer_challan_pdf_charge" value="{{ old('dealer_challan_pdf_charge', $settings['dealer_challan_pdf_charge']) }}" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Save Settings</button>
            </form>
        </div>
    </div>
</div>
@endsection
