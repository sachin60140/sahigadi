<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChallanPdfSearch;
use App\Models\Setting;
use Illuminate\Http\Request;

class ChallanPdfController extends Controller
{
    public function index()
    {
        $settings = [
            'is_challan_pdf_active' => Setting::isChallanPdfActive(),
            'challan_pdf_charge' => Setting::getChallanPdfCharge(),
            'dealer_challan_pdf_charge' => Setting::getDealerChallanPdfCharge(),
        ];

        // Stats
        $totalSearches = ChallanPdfSearch::count();
        $totalRevenue = ChallanPdfSearch::where('is_success', true)->sum('charge_amount');
        $failedRequests = ChallanPdfSearch::where('is_success', false)->count();

        return view('admin.challan_pdf.index', compact('settings', 'totalSearches', 'totalRevenue', 'failedRequests'));
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'challan_pdf_charge' => 'required|numeric|min:0',
            'dealer_challan_pdf_charge' => 'required|numeric|min:0',
            'is_challan_pdf_active' => 'nullable|boolean',
        ]);

        Setting::setChallanPdfCharge($request->challan_pdf_charge);
        Setting::setDealerChallanPdfCharge($request->dealer_challan_pdf_charge);
        Setting::setIsChallanPdfActive($request->has('is_challan_pdf_active'));

        return back()->with('success', 'Challan PDF Service settings updated successfully.');
    }

    public function logs()
    {
        $logs = ChallanPdfSearch::with(['customer', 'dealer'])->latest()->paginate(20);
        return view('admin.challan_pdf.logs', compact('logs'));
    }

    public function exportLogs()
    {
        // Simple CSV Export
        $logs = ChallanPdfSearch::with(['customer', 'dealer'])->latest()->get();
        
        $filename = "challan_pdf_logs_" . date('Y-m-d') . ".csv";
        $handle = fopen('php://memory', 'w');
        
        fputcsv($handle, ['ID', 'Date', 'User Type', 'User Name', 'Vehicle Number', 'Status', 'Charge Amount', 'Error Message']);
        
        foreach ($logs as $log) {
            $userType = $log->customer_id ? 'Customer' : ($log->dealer_id ? 'Dealer' : 'Unknown');
            $userName = $log->customer ? $log->customer->name : ($log->dealer ? $log->dealer->name : 'N/A');
            $status = $log->is_success ? 'Success' : 'Failed';
            
            fputcsv($handle, [
                $log->id,
                $log->created_at->format('Y-m-d H:i:s'),
                $userType,
                $userName,
                $log->vehicle_number,
                $status,
                $log->charge_amount,
                $log->error_message
            ]);
        }
        
        fseek($handle, 0);
        
        return response()->stream(
            function () use ($handle) {
                fpassthru($handle);
            },
            200,
            [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]
        );
    }
}
