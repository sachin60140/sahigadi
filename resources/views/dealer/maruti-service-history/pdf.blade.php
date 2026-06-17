@include('pdf.mahindra-service-history-report', [
    'documentTitle' => 'Maruti Service History',
    'vehicleNumber' => $marutiServiceHistory->vehicle_number,
    'records' => $marutiServiceHistory->records,
    'isSuccess' => $marutiServiceHistory->is_success,
    'errorMessage' => $marutiServiceHistory->error_message,
    'reportId' => $marutiServiceHistory->id,
    'reportCreatedAt' => $marutiServiceHistory->created_at,
    'chargeLabel' => 'Report charge',
    'chargeAmount' => $marutiServiceHistory->debit_amount ?? 0,
    'subjectLabel' => 'Dealer account',
    'subjectName' => $marutiServiceHistory->dealer->name ?? 'SAHI GADI dealer',
])
