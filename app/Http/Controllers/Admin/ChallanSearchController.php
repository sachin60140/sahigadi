<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminChallanSearch;
use App\Models\Dealer;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ChallanSearchController extends Controller
{
    public function index(Request $request)
    {
        $filters = $this->filters($request);
        $searches = $this->applyFilters(AdminChallanSearch::with('dealer'), $request)
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Admin/ChallanSearches/Index', [
            'searches' => $searches->through(fn (AdminChallanSearch $search) => $this->mapSearch($search)),
            'dealers' => Dealer::orderBy('name')->get()->map(fn (Dealer $dealer) => [
                'id' => $dealer->id,
                'name' => $dealer->company_name ?: $dealer->name,
            ])->values(),
            'filters' => $filters,
            'stats' => [
                'total' => AdminChallanSearch::count(),
                'successful' => AdminChallanSearch::where('is_success', true)->count(),
                'failed' => AdminChallanSearch::where('is_success', false)->count(),
                'revenue' => (float) AdminChallanSearch::where('is_success', true)->sum('charge_amount'),
                'fine_amount' => (float) AdminChallanSearch::where('is_success', true)->sum('total_amount'),
                'charge' => (float) Setting::getDealerChallanCharge(),
            ],
            'actions' => [
                'settings' => route('admin.challan-searches.settings'),
                'combinedLedger' => route('admin.service-tracking.challan-search'),
                'exportExcel' => route('admin.challan-searches.exportExcel', array_filter($filters)),
                'exportPdf' => route('admin.challan-searches.exportPdf', array_filter($filters)),
            ],
        ]);
    }

    public function show(AdminChallanSearch $challanSearch)
    {
        $challanSearch->load('dealer');

        return Inertia::render('Admin/ChallanSearches/Show', [
            'search' => array_merge($this->mapSearch($challanSearch), [
                'challans' => $challanSearch->challan_data ?? [],
                'actions' => [
                    'back' => route('admin.challan-searches.index'),
                    'pdf' => route('admin.challan-searches.download-pdf', $challanSearch),
                ],
            ]),
        ]);
    }

    public function downloadPdf(AdminChallanSearch $challanSearch)
    {
        $pdf = Pdf::loadView('dealer.challan-searches.pdf', compact('challanSearch'));

        return $pdf->download('e-challan-'.$challanSearch->vehicle_number.'.pdf');
    }

    public function settings(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'charge' => 'required|numeric|min:0',
                'dealer_charge' => 'required|numeric|min:0',
            ]);

            Setting::setChallanCharge($request->charge);
            Setting::setDealerChallanCharge($request->dealer_charge);

            return redirect()->back()->with('success', 'Service charges updated successfully!');
        }

        $charge = Setting::getChallanCharge();
        $dealerCharge = Setting::getDealerChallanCharge();
        $totalSearches = AdminChallanSearch::count();
        $successfulSearches = AdminChallanSearch::where('is_success', true)->count();
        $totalRevenue = AdminChallanSearch::where('is_success', true)->sum('charge_amount');

        return Inertia::render('Admin/ChallanSearches/Settings', [
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
                'update' => route('admin.challan-searches.settings'),
                'back' => route('admin.challan-searches.index'),
            ],
        ]);
    }

    public function exportExcel(Request $request)
    {
        $searches = $this->applyFilters(AdminChallanSearch::with('dealer'), $request)
            ->latest()
            ->get();

        $filename = 'e-challan-searches-'.now()->format('Y-m-d').'.csv';
        $headers = ['Content-Type' => 'text/csv', 'Content-Disposition' => 'attachment; filename="'.$filename.'"'];

        $callback = function () use ($searches) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Dealer', 'Vehicle Number', 'Challans', 'Total Amount', 'Status', 'Date']);
            foreach ($searches as $s) {
                fputcsv($handle, [
                    $s->id,
                    $s->dealer->name ?? 'N/A',
                    $s->vehicle_number,
                    $s->challan_count,
                    $s->total_amount ?? 0,
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
        $searches = $this->applyFilters(AdminChallanSearch::with('dealer'), $request)
            ->latest()
            ->get();

        $totalRevenue = $searches->where('is_success', true)->sum('total_amount');

        $pdf = Pdf::loadView('admin.challan-searches.pdf', compact('searches', 'totalRevenue'));

        return $pdf->download('e-challan-searches-'.now()->format('Y-m-d').'.pdf');
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

    private function mapSearch(AdminChallanSearch $search): array
    {
        return [
            'id' => $search->id,
            'vehicle_number' => $search->vehicle_number,
            'challan_count' => (int) ($search->challan_count ?? 0),
            'total_amount' => (float) ($search->total_amount ?? 0),
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
                'show' => route('admin.challan-searches.show', $search),
                'pdf' => route('admin.challan-searches.download-pdf', $search),
            ],
        ];
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
