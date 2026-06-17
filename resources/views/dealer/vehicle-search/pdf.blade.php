@extends('pdf.layout')

@php
    $reference = 'SG-RC-'.str_pad((string) ($vehicleSearch->id ?? 0), 6, '0', STR_PAD_LEFT);
    $reportDate = optional($vehicleSearch->created_at)->format('d M Y, h:i A') ?? now('Asia/Kolkata')->format('d M Y, h:i A');
@endphp

@section('doc-title', 'RC Details - '.$vehicleSearch->registration_number.' - SAHI GADI')
@section('brand-subtitle', 'Verified vehicle intelligence')
@section('report-kicker', 'Vehicle RC dossier')
@section('report-title', 'RC Verification Report')
@section('report-meta', 'Reference '.$reference.' | Report date '.$reportDate)
@section('reference', $reference)
@section('footer-note', 'Vehicle RC report')

@section('content')
    @include('pdf.partials.rc-structured', [
        'reg' => $vehicleSearch->registration_number,
        'isSuccess' => $vehicleSearch->is_success,
        'errorMessage' => $vehicleSearch->error_message,
        'detail' => $vehicleSearch,
        'reference' => $reference,
        'summaryRows' => [
            'Search date' => $reportDate,
            'Amount charged' => 'Rs. '.number_format((float) ($vehicleSearch->debit_amount ?? 0), 2),
        ],
    ])
@endsection
