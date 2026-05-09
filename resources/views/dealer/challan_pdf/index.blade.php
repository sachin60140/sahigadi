@extends('layouts.dealer')

@section('title', 'Challan PDF Service')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Search Challan PDF</h5>
                    <a href="{{ route('dealer.challan-pdf.history') }}" class="btn btn-sm btn-light">View History</a>
                </div>
                <div class="card-body">

                    <div class="alert alert-info">
                        <strong>Note:</strong> Charge per search is ₹{{ \App\Models\Setting::getDealerChallanPdfCharge() }}. 
                        Amount will be deducted from your wallet only on successful PDF generation.
                    </div>

                    <form action="{{ route('dealer.challan-pdf.search') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="vehicle_number" class="form-label">Challan Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="vehicle_number" name="vehicle_number" placeholder="Enter Challan Number (e.g. HR464162310XXXXXXXX)" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Search & Generate PDF</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
