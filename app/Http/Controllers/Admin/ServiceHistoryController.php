<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ServiceHistoryExport;
use App\Http\Controllers\Controller;
use App\Models\AdminServiceHistory;
use App\Models\Dealer;
use App\Models\ServiceHistory;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class ServiceHistoryController extends Controller
{
    public function index(Request $request)
    {
        $filters = $this->filters($request);
        $searches = $this->applyFilters(AdminServiceHistory::with('dealer'), $request)
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return Inertia::render('Admin/ServiceHistories/Index', [
            'searches' => $searches->through(fn (AdminServiceHistory $search) => $this->mapSearch($search)),
            'dealers' => Dealer::orderBy('name')->get()->map(fn (Dealer $dealer) => [
                'id' => $dealer->id,
                'name' => $dealer->company_name ?: $dealer->name,
            ])->values(),
            'filters' => $filters,
            'stats' => [
                'total' => AdminServiceHistory::count(),
                'successful' => AdminServiceHistory::where('is_success', true)->count(),
                'failed' => AdminServiceHistory::where('is_success', false)->count(),
                'revenue' => (float) AdminServiceHistory::where('is_success', true)->sum('charge_amount'),
                'charge' => (float) Setting::getDealerServiceHistoryCharge(),
            ],
            'actions' => [
                'settings' => route('admin.service-histories.settings'),
                'combinedLedger' => route('admin.service-tracking.service-history'),
                'exportExcel' => route('admin.service-histories.exportExcel', array_filter($filters)),
                'exportPdf' => route('admin.service-histories.exportPdf', array_filter($filters)),
            ],
        ]);
    }

    public function show(AdminServiceHistory $serviceHistory)
    {
        $serviceHistory->load('dealer');

        $dealerSearch = ServiceHistory::where('dealer_id', $serviceHistory->dealer_id)
            ->where('vehicle_number', $serviceHistory->vehicle_number)
            ->with('records')
            ->first();

        return Inertia::render('Admin/ServiceHistories/Show', [
            'search' => array_merge($this->mapSearch($serviceHistory), [
                'records' => $dealerSearch?->records?->map(fn ($record) => [
                    'date' => $this->formatDate($record->svc_date),
                    'dealer_name' => $record->dealer_name,
                    'location_name' => $record->location_name,
                    'work_type' => $record->work_type,
                    'service_category' => $record->service_cate,
                    'bill_amount' => (float) ($record->net_bill_amt ?? 0),
                    'paid_amount' => (float) ($record->paid_amt ?? 0),
                    'mileage' => $record->mileage,
                    'repair_order_no' => $record->repair_order_no,
                    'bill_no' => $record->repair_order_bill_no,
                ])->values() ?? [],
                'has_extended_record' => (bool) $dealerSearch,
                'actions' => [
                    'back' => route('admin.service-histories.index'),
                    'pdf' => route('admin.service-histories.downloadPdf', $serviceHistory),
                ],
            ]),
        ]);
    }

    public function settings(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'charge' => 'required|numeric|min:0',
                'dealer_charge' => 'required|numeric|min:0',
            ]);

            Setting::setServiceHistoryCharge($request->charge);
            Setting::setDealerServiceHistoryCharge($request->dealer_charge);

            return redirect()->back()->with('success', 'Service charges updated successfully!');
        }

        $charge = Setting::getServiceHistoryCharge();
        $dealerCharge = Setting::getDealerServiceHistoryCharge();
        $totalSearches = AdminServiceHistory::count();
        $successfulSearches = AdminServiceHistory::where('is_success', true)->count();
        $totalRevenue = AdminServiceHistory::where('is_success', true)->sum('charge_amount');

        return Inertia::render('Admin/ServiceHistories/Settings', [
            'serviceName' => 'General Service History',
            'charges' => [
                'customer' => (float) $charge,
                'dealer' => (float) $dealerCharge,
            ],
            'stats' => [
                'total' => $totalSearches,
                'successful' => $successfulSearches,
                'revenue' => (float) $totalRevenue,
            ],
            'actions' => [
                'update' => route('admin.service-histories.settings'),
                'back' => route('admin.service-histories.index'),
            ],
        ]);
    }

    public function exportExcel(Request $request)
    {
        $searches = $this->applyFilters(AdminServiceHistory::with('dealer'), $request)
            ->latest()
            ->get();

        return Excel::download(new ServiceHistoryExport($searches), 'service-histories-'.date('Y-m-d').'.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $searches = $this->applyFilters(AdminServiceHistory::with('dealer'), $request)
            ->latest()
            ->get();
        $totalRevenue = $searches->where('is_success', true)->sum('charge_amount');

        $pdf = Pdf::loadView('admin.service-histories.pdf', compact('searches', 'totalRevenue'));
        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('service-histories-'.date('Y-m-d').'.pdf');
    }

    public function downloadSinglePdf(AdminServiceHistory $serviceHistory)
    {
        $serviceHistory->load('dealer');

        $dealerSearch = ServiceHistory::where('dealer_id', $serviceHistory->dealer_id)
            ->where('vehicle_number', $serviceHistory->vehicle_number)
            ->with('records')
            ->first();

        $pdf = Pdf::loadView('admin.service-histories.single-pdf', compact('serviceHistory', 'dealerSearch'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('service-history-'.$serviceHistory->vehicle_number.'.pdf');
    }

    private function applyFilters($query, Request $request)
    {
        if ($request->filled('search')) {
            $query->where('vehicle_number', 'like', '%'.strtoupper((string) $request->search).'%');
        }
        if ($request->filled('dealer_id')) {
            $query->where('dealer_id', $request->dealer_id);
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
            'dealer_id' => (string) $request->query('dealer_id', ''),
            'status' => (string) $request->query('status', ''),
            'from_date' => (string) $request->query('from_date', ''),
            'to_date' => (string) $request->query('to_date', ''),
        ];
    }

    private function mapSearch(AdminServiceHistory $search): array
    {
        return [
            'id' => $search->id,
            'vehicle_number' => $search->vehicle_number,
            'service_count' => (int) ($search->service_count ?? 0),
            'charge_amount' => (float) ($search->charge_amount ?? 0),
            'is_success' => (bool) $search->is_success,
            'error_message' => $search->error_message,
            'created_at' => $this->formatDateTime($search->created_at),
            'dealer' => $search->dealer ? [
                'name' => $search->dealer->company_name ?: $search->dealer->name,
                'phone' => $search->dealer->phone,
                'show_url' => route('admin.dealers.show', $search->dealer),
            ] : null,
            'actions' => [
                'show' => route('admin.service-histories.show', $search),
                'pdf' => route('admin.service-histories.downloadPdf', $search),
            ],
        ];
    }

    private function formatDate($value): ?string
    {
        if (! $value) return null;
        try {
            return \Carbon\Carbon::parse($value)->format('d M Y');
        } catch (\Throwable) {
            return (string) $value;
        }
    }

    private function formatDateTime($value): ?string
    {
        if (! $value) return null;
        try {
            return \Carbon\Carbon::parse($value)->format('d M Y, h:i A');
        } catch (\Throwable) {
            return (string) $value;
        }
    }
}
