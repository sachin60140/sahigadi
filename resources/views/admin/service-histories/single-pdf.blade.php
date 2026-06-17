@include('pdf.mahindra-service-history-report', [
    'documentTitle' => 'Mahindra Service History',
    'vehicleNumber' => $serviceHistory->vehicle_number,
    'records' => $dealerSearch?->records ?? collect(),
    'isSuccess' => $serviceHistory->is_success && (bool) $dealerSearch,
    'errorMessage' => $serviceHistory->error_message,
    'reportId' => $serviceHistory->id,
    'reportCreatedAt' => $serviceHistory->created_at,
    'chargeLabel' => 'Report charge',
    'chargeAmount' => $serviceHistory->charge_amount ?? 0,
    'subjectLabel' => 'Dealer account',
    'subjectName' => $serviceHistory->dealer->name ?? 'SAHI GADI dealer',
])
