@include('pdf.mahindra-service-history-report', [
    'documentTitle' => 'Mahindra Service History',
    'vehicleNumber' => $serviceHistory->vehicle_number,
    'records' => $serviceHistory->records,
    'isSuccess' => $serviceHistory->is_success,
    'errorMessage' => $serviceHistory->error_message,
    'reportId' => $serviceHistory->id,
    'reportCreatedAt' => $serviceHistory->created_at,
    'chargeLabel' => 'Amount paid',
    'chargeAmount' => $serviceHistory->paid_amount ?? 0,
    'subjectLabel' => 'Customer',
    'subjectName' => $serviceHistory->customer_name ?? 'Guest customer',
])
