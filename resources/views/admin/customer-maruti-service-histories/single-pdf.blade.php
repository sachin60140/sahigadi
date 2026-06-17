@include('pdf.mahindra-service-history-report', [
    'documentTitle' => 'Maruti Service History',
    'vehicleNumber' => $marutiServiceHistory->vehicle_number,
    'records' => $marutiServiceHistory->records,
    'isSuccess' => $marutiServiceHistory->is_success,
    'errorMessage' => $marutiServiceHistory->error_message,
    'reportId' => $marutiServiceHistory->id,
    'reportCreatedAt' => $marutiServiceHistory->created_at,
    'chargeLabel' => 'Amount paid',
    'chargeAmount' => $marutiServiceHistory->paid_amount ?? 0,
    'subjectLabel' => 'Customer',
    'subjectName' => $marutiServiceHistory->customer_name ?? 'Guest customer',
])
