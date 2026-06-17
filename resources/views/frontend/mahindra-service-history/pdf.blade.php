@include('pdf.mahindra-service-history-report', [
    'documentTitle' => 'Mahindra Service History',
    'vehicleNumber' => $mahindraServiceHistory->vehicle_number,
    'records' => $mahindraServiceHistory->records,
    'isSuccess' => $mahindraServiceHistory->is_success,
    'errorMessage' => $mahindraServiceHistory->error_message,
    'reportId' => $mahindraServiceHistory->id,
    'reportCreatedAt' => $mahindraServiceHistory->created_at,
    'chargeLabel' => 'Amount paid',
    'chargeAmount' => $mahindraServiceHistory->paid_amount ?? 0,
    'subjectLabel' => 'Customer',
    'subjectName' => $mahindraServiceHistory->customer_name ?? 'Guest customer',
])
