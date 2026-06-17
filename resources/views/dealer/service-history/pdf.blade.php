@include('pdf.mahindra-service-history-report', [
    'documentTitle' => 'Mahindra Service History',
    'vehicleNumber' => $serviceHistory->vehicle_number,
    'records' => $serviceHistory->records,
    'isSuccess' => $serviceHistory->is_success,
    'errorMessage' => $serviceHistory->error_message,
    'reportId' => $serviceHistory->id,
    'reportCreatedAt' => $serviceHistory->created_at,
    'chargeLabel' => 'Report charge',
    'chargeAmount' => $serviceHistory->debit_amount ?? 0,
    'subjectLabel' => 'Dealer account',
    'subjectName' => $serviceHistory->dealer->name ?? 'SAHI GADI dealer',
])
