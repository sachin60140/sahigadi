<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminMarutiServiceHistory;
use App\Models\Dealer;
use App\Models\MarutiServiceHistory;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MarutiServiceHistoryController extends Controller
{
    public function index(Request $request)
    {
        $filters = $this->filters($request);
        $searches = $this->applyFilters(AdminMarutiServiceHistory::with('dealer'), $request)
            ->latest()->paginate(20)->withQueryString();

        return Inertia::render('Admin/ProviderServiceHistories/Index', [
            'page' => [
                'title' => 'Maruti Dealer Service History',
                'heading' => 'Track Maruti workshop searches from dealer accounts.',
                'description' => 'Review dealer usage, returned service counts, lookup status and wallet charges.',
                'channel' => 'dealer',
                'filterUrl' => route('admin.maruti-service-histories.index'),
            ],
            'searches' => $searches->through(fn (AdminMarutiServiceHistory $search) => $this->mapSearch($search)),
            'dealers' => Dealer::orderBy('name')->get()->map(fn (Dealer $dealer) => [
                'id' => $dealer->id,
                'name' => $dealer->company_name ?: $dealer->name,
            ])->values(),
            'filters' => $filters,
            'stats' => [
                'total' => AdminMarutiServiceHistory::count(),
                'successful' => AdminMarutiServiceHistory::where('is_success', true)->count(),
                'failed' => AdminMarutiServiceHistory::where('is_success', false)->count(),
                'revenue' => (float) AdminMarutiServiceHistory::where('is_success', true)->sum('charge_amount'),
                'charge' => (float) Setting::getDealerMarutiServiceHistoryCharge(),
            ],
            'actions' => [
                'exportExcel' => route('admin.maruti-service-histories.exportExcel', array_filter($filters)),
                'exportPdf' => route('admin.maruti-service-histories.exportPdf', array_filter($filters)),
            ],
        ]);
    }

    public function show(AdminMarutiServiceHistory $marutiServiceHistory)
    {
        $marutiServiceHistory->load('dealer');
        $records = data_get($marutiServiceHistory->raw_response, 'result.serviceHistoryDetails', []);

        return Inertia::render('Admin/ProviderServiceHistories/Show', [
            'page' => ['title' => 'Maruti Dealer Service Details', 'provider' => 'Maruti', 'channel' => 'dealer'],
            'search' => array_merge($this->mapSearch($marutiServiceHistory), [
                'records' => collect(is_array($records) ? $records : [])->map(fn ($record) => [
                    'date' => $record['dateOfSVC'] ?? null,
                    'category' => $record['serviceType'] ?? null,
                    'dealer_name' => $record['dealerName'] ?? null,
                    'job_card' => $record['noOfJobCard'] ?? null,
                    'ro_no' => $record['noOfRO'] ?? null,
                    'part_amount' => (float) ($record['partAmmount'] ?? 0),
                    'labour_amount' => (float) ($record['labourAmmount'] ?? 0),
                    'total_amount' => (float) ($record['totalAmount'] ?? 0),
                    'mileage' => $record['mileage'] ?? null,
                ])->values(),
                'actions' => [
                    'back' => route('admin.maruti-service-histories.index'),
                    'pdf' => route('admin.maruti-service-histories.downloadPdf', $marutiServiceHistory),
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

            Setting::setMarutiServiceHistoryCharge($request->charge);
            Setting::setDealerMarutiServiceHistoryCharge($request->dealer_charge);

            return redirect()->back()->with('success', 'Service charges updated successfully!');
        }

        $charge = Setting::getMarutiServiceHistoryCharge();
        $dealerCharge = Setting::getDealerMarutiServiceHistoryCharge();
        $totalSearches = AdminMarutiServiceHistory::count();
        $successfulSearches = AdminMarutiServiceHistory::where('is_success', true)->count();
        $totalRevenue = AdminMarutiServiceHistory::where('is_success', true)->sum('charge_amount');

        return Inertia::render('Admin/ServiceHistories/Settings', [
            'serviceName' => 'Maruti Service History',
            'charges' => ['customer' => (float) $charge, 'dealer' => (float) $dealerCharge],
            'stats' => ['total' => $totalSearches, 'successful' => $successfulSearches, 'revenue' => (float) $totalRevenue],
            'actions' => ['update' => route('admin.maruti-service-histories.settings')],
        ]);
    }

    public function exportExcel(Request $request)
    {
        $query = AdminMarutiServiceHistory::with('dealer');

        if ($request->filled('search')) {
            $query->where('vehicle_number', 'like', '%'.strtoupper($request->search).'%');
        }
        if ($request->filled('dealer_id')) {
            $query->where('dealer_id', $request->dealer_id);
        }
        if ($request->filled('status')) {
            $query->where('is_success', $request->status == 'success');
        }
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $searches = $query->orderBy('created_at', 'desc')->get();

        $filename = 'maruti-service-histories-'.now()->format('Y-m-d').'.csv';
        $headers = ['Content-Type' => 'text/csv', 'Content-Disposition' => 'attachment; filename="'.$filename.'"'];

        $callback = function () use ($searches) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Dealer', 'Vehicle Number', 'Services', 'Charge Amount', 'Status', 'Date']);
            foreach ($searches as $s) {
                fputcsv($handle, [
                    $s->id,
                    $s->dealer->name ?? 'N/A',
                    $s->vehicle_number,
                    $s->service_count,
                    $s->charge_amount ?? 0,
                    $s->is_success ? 'Success' : 'Failed',
                    $s->created_at->format('d M Y'),
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf(Request $request)
    {
        $query = AdminMarutiServiceHistory::with('dealer');

        if ($request->filled('search')) {
            $query->where('vehicle_number', 'like', '%'.strtoupper($request->search).'%');
        }
        if ($request->filled('dealer_id')) {
            $query->where('dealer_id', $request->dealer_id);
        }
        if ($request->filled('status')) {
            $query->where('is_success', $request->status == 'success');
        }
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $searches = $query->orderBy('created_at', 'desc')->get();

        $totalRevenue = $searches->where('is_success', true)->sum('charge_amount');

        $pdf = Pdf::loadView('admin.maruti-service-histories.pdf', compact('searches', 'totalRevenue'));

        return $pdf->download('maruti-service-histories-'.now()->format('Y-m-d').'.pdf');
    }

    public function downloadPdf(AdminMarutiServiceHistory $marutiServiceHistory)
    {
        $dealerSearch = MarutiServiceHistory::where('dealer_id', $marutiServiceHistory->dealer_id)
            ->where('vehicle_number', $marutiServiceHistory->vehicle_number)
            ->with('records')
            ->first();

        $pdf = Pdf::loadView('admin.maruti-service-histories.single-pdf', compact('marutiServiceHistory', 'dealerSearch'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('maruti-service-history-'.$marutiServiceHistory->vehicle_number.'.pdf');
    }

    private function applyFilters($query, Request $request)
    {
        if ($request->filled('search')) $query->where('vehicle_number', 'like', '%'.strtoupper((string) $request->search).'%');
        if ($request->filled('dealer_id')) $query->where('dealer_id', $request->dealer_id);
        if ($request->filled('status')) $query->where('is_success', $request->status === 'success');
        if ($request->filled('from_date')) $query->whereDate('created_at', '>=', $request->from_date);
        if ($request->filled('to_date')) $query->whereDate('created_at', '<=', $request->to_date);
        return $query;
    }

    private function filters(Request $request): array
    {
        return ['search' => (string) $request->query('search', ''), 'dealer_id' => (string) $request->query('dealer_id', ''), 'status' => (string) $request->query('status', ''), 'from_date' => (string) $request->query('from_date', ''), 'to_date' => (string) $request->query('to_date', '')];
    }

    private function mapSearch(AdminMarutiServiceHistory $search): array
    {
        return [
            'id' => $search->id, 'vehicle_number' => $search->vehicle_number, 'service_count' => (int) ($search->service_count ?? 0),
            'amount' => (float) ($search->charge_amount ?? 0), 'is_success' => (bool) $search->is_success, 'error_message' => $search->error_message,
            'created_at' => optional($search->created_at)->format('d M Y, h:i A'), 'subject_name' => $search->dealer?->company_name ?: ($search->dealer?->name ?? 'N/A'),
            'subject_phone' => $search->dealer?->phone,
            'actions' => ['show' => route('admin.maruti-service-histories.show', $search), 'pdf' => route('admin.maruti-service-histories.downloadPdf', $search)],
        ];
    }
}
