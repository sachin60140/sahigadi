@include('pdf.mahindra-service-history-report', [
    'documentTitle' => 'Maruti Service History',
    'vehicleNumber' => $marutiServiceHistory->vehicle_number,
    'records' => $dealerSearch?->records ?? collect(),
    'isSuccess' => $marutiServiceHistory->is_success && (bool) $dealerSearch,
    'errorMessage' => $marutiServiceHistory->error_message,
    'reportId' => $marutiServiceHistory->id,
    'reportCreatedAt' => $marutiServiceHistory->created_at,
    'chargeLabel' => 'Report charge',
    'chargeAmount' => $marutiServiceHistory->charge_amount ?? 0,
    'subjectLabel' => 'Dealer account',
    'subjectName' => $marutiServiceHistory->dealer->name ?? 'SAHI GADI dealer',
])
