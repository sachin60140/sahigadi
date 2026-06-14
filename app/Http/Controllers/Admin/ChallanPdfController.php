<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChallanPdfSearch;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;

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

        return Inertia::render('Admin/ChallanPdf/Settings', [
            'settings' => [
                'active' => (bool) $settings['is_challan_pdf_active'],
                'customerCharge' => (float) $settings['challan_pdf_charge'],
                'dealerCharge' => (float) $settings['dealer_challan_pdf_charge'],
            ],
            'stats' => [
                'total' => $totalSearches,
                'successful' => $totalSearches - $failedRequests,
                'failed' => $failedRequests,
                'revenue' => (float) $totalRevenue,
            ],
            'actions' => [
                'update' => route('admin.challan-pdf.settings'),
                'logs' => route('admin.challan-pdf.logs'),
            ],
        ]);
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
        Setting::setIsChallanPdfActive($request->boolean('is_challan_pdf_active'));

        return back()->with('success', 'Challan PDF Service settings updated successfully.');
    }

    public function logs(Request $request)
    {
        $filters = $this->filters($request);
        $logs = $this->applyFilters(ChallanPdfSearch::with(['customer', 'dealer']), $request)
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Admin/ChallanPdf/Logs', [
            'logs' => $logs->through(fn (ChallanPdfSearch $log) => $this->mapLog($log)),
            'filters' => $filters,
            'stats' => [
                'total' => ChallanPdfSearch::count(),
                'successful' => ChallanPdfSearch::where('is_success', true)->count(),
                'failed' => ChallanPdfSearch::where('is_success', false)->count(),
                'revenue' => (float) ChallanPdfSearch::where('is_success', true)->sum('charge_amount'),
            ],
            'actions' => [
                'settings' => route('admin.challan-pdf.index'),
                'export' => route('admin.challan-pdf.export-logs', array_filter($filters)),
            ],
        ]);
    }

    public function exportLogs(Request $request)
    {
        $logs = $this->applyFilters(ChallanPdfSearch::with(['customer', 'dealer']), $request)
            ->latest()
            ->get();
        
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

    private function applyFilters($query, Request $request)
    {
        if ($request->filled('search')) {
            $term = (string) $request->search;
            $query->where(function ($nestedQuery) use ($term) {
                $nestedQuery->where('vehicle_number', 'like', '%'.strtoupper($term).'%')
                    ->orWhereHas('customer', fn ($customerQuery) => $customerQuery
                        ->where('name', 'like', '%'.$term.'%')
                        ->orWhere('phone', 'like', '%'.$term.'%'))
                    ->orWhereHas('dealer', fn ($dealerQuery) => $dealerQuery
                        ->where('name', 'like', '%'.$term.'%')
                        ->orWhere('company_name', 'like', '%'.$term.'%')
                        ->orWhere('phone', 'like', '%'.$term.'%'));
            });
        }

        if ($request->filled('channel')) {
            if ($request->channel === 'customer') {
                $query->whereNotNull('customer_id');
            } elseif ($request->channel === 'dealer') {
                $query->whereNotNull('dealer_id');
            }
        }

        if ($request->filled('status')) {
            $query->where('is_success', $request->status === 'success');
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        return $query;
    }

    private function filters(Request $request): array
    {
        return [
            'search' => (string) $request->query('search', ''),
            'channel' => (string) $request->query('channel', ''),
            'status' => (string) $request->query('status', ''),
            'from_date' => (string) $request->query('from_date', ''),
            'to_date' => (string) $request->query('to_date', ''),
        ];
    }

    private function mapLog(ChallanPdfSearch $log): array
    {
        $channel = $log->customer_id ? 'customer' : ($log->dealer_id ? 'dealer' : 'unknown');
        $user = $log->customer ?: $log->dealer;

        return [
            'id' => $log->id,
            'vehicle_number' => $log->vehicle_number,
            'channel' => $channel,
            'user_name' => $channel === 'dealer'
                ? ($log->dealer?->company_name ?: $log->dealer?->name)
                : $log->customer?->name,
            'user_id' => $channel === 'dealer'
                ? $log->dealer?->dealer_unique_id
                : $log->customer?->customer_unique_id,
            'user_phone' => $user?->phone,
            'is_success' => (bool) $log->is_success,
            'charge_amount' => (float) ($log->charge_amount ?? 0),
            'error_message' => $log->error_message,
            'pdf_url' => $log->pdf_url,
            'api_request' => $log->api_request ?? [],
            'api_response' => $log->api_response ?? [],
            'created_at' => optional($log->created_at)->format('d M Y, h:i A'),
        ];
    }
}
