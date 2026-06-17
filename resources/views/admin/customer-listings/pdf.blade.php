@extends('pdf.layout')

@section('doc-title', 'Customer Car Listings - SAHI GADI')
@section('brand-subtitle', 'Admin operations')
@section('report-kicker', 'Admin export')
@section('report-title', 'Customer Car Listings')
@section('report-meta', 'Generated '.now('Asia/Kolkata')->format('d M Y, h:i A'))
@section('footer-note', 'Admin export')

@section('content')
    <div class="section">
        <table class="section-heading"><tr><td class="section-title">Listing register</td><td class="section-caption">{{ $listings->count() }} entries</td></tr></table>
        <table class="data-table" style="table-layout: fixed; font-size: 7px; word-wrap: break-word;">
            <colgroup>
                <col style="width:2.5%"><col style="width:7%"><col style="width:5.5%"><col style="width:5.5%"><col style="width:3%">
                <col style="width:4%"><col style="width:4%"><col style="width:4.5%"><col style="width:5%"><col style="width:4.5%">
                <col style="width:5.5%"><col style="width:5.5%"><col style="width:3%"><col style="width:7%"><col style="width:5.5%">
                <col style="width:5.5%"><col style="width:5%"><col style="width:3%"><col style="width:3%"><col style="width:6%">
            </colgroup>
            <thead>
                <tr>
                    <td>ID</td><td>Title</td><td>Brand</td><td>Model</td><td>Year</td><td>Fuel</td><td>Trans</td><td class="num">KM</td>
                    <td class="num">Price</td><td>City</td><td>Lat/Lng</td><td>Reg no</td><td class="num">Owns</td><td>Owner / email</td>
                    <td>Phone</td><td>WhatsApp</td><td>Status</td><td>Actv</td><td>Feat</td><td>Date</td>
                </tr>
            </thead>
            <tbody>
                @forelse($listings as $listing)
                    <tr>
                        <td>{{ $listing->id }}</td>
                        <td>{{ Str::limit($listing->title, 20) }}</td>
                        <td>{{ Str::limit($listing->brand ? $listing->brand->name : '', 15) }}</td>
                        <td>{{ Str::limit($listing->model, 15) }}</td>
                        <td>{{ $listing->year }}</td>
                        <td>{{ $listing->fuel_type }}</td>
                        <td>{{ $listing->transmission }}</td>
                        <td class="num">{{ $listing->km_driven }}</td>
                        <td class="num">{{ $listing->price }}</td>
                        <td>{{ Str::limit($listing->city, 15) }}</td>
                        <td>{{ $listing->latitude }}<br>{{ $listing->longitude }}</td>
                        <td>{{ $listing->registration_number }}</td>
                        <td class="num">{{ $listing->owners }}</td>
                        <td>{{ Str::limit($listing->owner_name, 15) }}<br>{{ Str::limit($listing->owner_email, 15) }}</td>
                        <td>{{ $listing->owner_phone }}</td>
                        <td>{{ $listing->whatsapp_number }}</td>
                        <td>{{ ucfirst($listing->status) }}</td>
                        <td>{{ $listing->is_active ? 'Y' : 'N' }}</td>
                        <td>{{ $listing->is_featured ? 'Y' : 'N' }}</td>
                        <td>{{ $listing->created_at ? $listing->created_at->format('Y-m-d') : '' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="20" style="text-align: center; color: #7a8799;">No listings found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
