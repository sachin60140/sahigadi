<?php

namespace App\Http\Controllers\Admin;

use App\Exports\VehicleSearchesExport;
use App\Http\Controllers\Controller;
use App\Models\AdminVehicleSearch;
use App\Models\Dealer;
use App\Models\Setting;
use App\Models\VehicleDetail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class VehicleSearchController extends Controller
{
    public function index(Request $request)
    {
        $filters = $this->filters($request);
        $searches = $this->applyFilters(AdminVehicleSearch::with('dealer'), $request)
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return Inertia::render('Admin/VehicleSearches/Index', [
            'searches' => $searches->through(fn (AdminVehicleSearch $search) => $this->mapSearch($search)),
            'dealers' => Dealer::orderBy('name')->get()->map(fn (Dealer $dealer) => [
                'id' => $dealer->id,
                'name' => $dealer->company_name ?: $dealer->name,
            ])->values(),
            'filters' => $filters,
            'stats' => [
                'total' => AdminVehicleSearch::count(),
                'successful' => AdminVehicleSearch::where('is_success', true)->count(),
                'failed' => AdminVehicleSearch::where('is_success', false)->count(),
                'revenue' => (float) AdminVehicleSearch::where('is_success', true)->sum('charge_amount'),
                'charge' => (float) Setting::getDealerVehicleSearchCharge(),
            ],
            'actions' => [
                'customerSearches' => route('admin.customer-vehicle-searches.index'),
                'combinedLedger' => route('admin.service-tracking.vehicle-search'),
                'settings' => route('admin.vehicle-searches.settings'),
                'exportExcel' => route('admin.vehicle-searches.exportExcel', array_filter($filters)),
                'exportPdf' => route('admin.vehicle-searches.exportPdf', array_filter($filters)),
            ],
        ]);
    }

    public function show(AdminVehicleSearch $vehicleSearch)
    {
        $vehicleSearch->load('dealer');

        $dealerSearch = VehicleDetail::where('dealer_id', $vehicleSearch->dealer_id)
            ->where('registration_number', $vehicleSearch->registration_number)
            ->first();

        return Inertia::render('Admin/VehicleSearches/Show', [
            'search' => $this->mapSearchDetail($vehicleSearch, $dealerSearch),
        ]);
    }

    public function settings(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'charge' => 'required|numeric|min:0',
                'dealer_charge' => 'required|numeric|min:0',
            ]);

            Setting::setVehicleSearchCharge($request->charge);
            Setting::setDealerVehicleSearchCharge($request->dealer_charge);

            return redirect()->back()->with('success', 'Service charges updated successfully!');
        }

        $charge = Setting::getVehicleSearchCharge();
        $dealerCharge = Setting::getDealerVehicleSearchCharge();
        $totalSearches = AdminVehicleSearch::count();
        $successfulSearches = AdminVehicleSearch::where('is_success', true)->count();
        $totalRevenue = AdminVehicleSearch::where('is_success', true)->sum('charge_amount');

        return Inertia::render('Admin/VehicleSearches/Settings', [
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
                'update' => route('admin.vehicle-searches.settings'),
                'dealerSearches' => route('admin.vehicle-searches.index'),
                'customerSearches' => route('admin.customer-vehicle-searches.index'),
                'combinedLedger' => route('admin.service-tracking.vehicle-search'),
            ],
        ]);
    }

    public function exportExcel(Request $request)
    {
        $searches = $this->applyFilters(AdminVehicleSearch::with('dealer'), $request)
            ->latest()
            ->get();

        return Excel::download(new VehicleSearchesExport($searches), 'vehicle-searches-'.date('Y-m-d').'.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $searches = $this->applyFilters(AdminVehicleSearch::with('dealer'), $request)
            ->latest()
            ->get();
        $totalRevenue = $searches->where('is_success', true)->sum('charge_amount');

        $pdf = Pdf::loadView('admin.vehicle-searches.pdf', compact('searches', 'totalRevenue'));
        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('vehicle-searches-'.date('Y-m-d').'.pdf');
    }

    public function downloadSinglePdf(AdminVehicleSearch $vehicleSearch)
    {
        $vehicleSearch->load('dealer');

        $dealerSearch = VehicleDetail::where('dealer_id', $vehicleSearch->dealer_id)
            ->where('registration_number', $vehicleSearch->registration_number)
            ->first();

        $pdf = Pdf::loadView('admin.vehicle-searches.single-pdf', compact('vehicleSearch', 'dealerSearch'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('rc-'.$vehicleSearch->registration_number.'.pdf');
    }

    private function applyFilters($query, Request $request)
    {
        if ($request->filled('search')) {
            $query->where('registration_number', 'like', '%'.strtoupper((string) $request->search).'%');
        }

        if ($request->filled('dealer_id')) {
            $query->where('dealer_id', $request->dealer_id);
        }

        if ($request->filled('status')) {
            if ($request->status === 'success') {
                $query->where('is_success', true);
            } elseif ($request->status === 'failed') {
                $query->where('is_success', false);
            }
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

    private function mapSearch(AdminVehicleSearch $search): array
    {
        return [
            'id' => $search->id,
            'registration_number' => $search->registration_number,
            'owner_name' => $search->owner_name,
            'vehicle' => trim(implode(' ', array_filter([$search->make, $search->model]))) ?: null,
            'fuel_type' => $search->fuel_type,
            'rc_status' => $search->rc_status,
            'is_success' => (bool) $search->is_success,
            'charge_amount' => (float) ($search->charge_amount ?? 0),
            'error_message' => $search->error_message,
            'created_at' => $this->formatDateTime($search->created_at),
            'dealer' => $search->dealer ? [
                'id' => $search->dealer->id,
                'name' => $search->dealer->company_name ?: $search->dealer->name,
                'phone' => $search->dealer->phone,
                'show_url' => route('admin.dealers.show', $search->dealer),
            ] : null,
            'actions' => [
                'show' => route('admin.vehicle-searches.show', $search),
                'pdf' => route('admin.vehicle-searches.downloadPdf', $search),
            ],
        ];
    }

    private function mapSearchDetail(AdminVehicleSearch $search, ?VehicleDetail $detail): array
    {
        $value = fn (string $field) => $detail?->{$field} ?? $search->{$field} ?? null;

        return array_merge($this->mapSearch($search), [
            'address' => $value('address'),
            'registration_date' => $this->formatDate($value('registration_date')),
            'insurance_date' => $this->formatDate($value('insurance_date')),
            'insurance_policy_number' => $value('insurance_policy_number'),
            'puc_validity' => $this->formatDate($value('puc_validity')),
            'chassis_number' => $value('chassis_number'),
            'engine_number' => $value('engine_number'),
            'has_extended_record' => (bool) $detail,
            'extended' => $detail ? [
                'father_name' => $detail->father_name,
                'mobile_number' => $detail->mobile_number,
                'rto_location' => $detail->rto_location,
                'vehicle_class' => $detail->vehicle_class,
                'vehicle_category' => $detail->vehicle_category,
                'variant' => $detail->variant,
                'color' => $detail->color,
                'seats' => $detail->seats,
                'fitness_date' => $this->formatDate($detail->fitness_date),
                'insurance_provider' => $detail->insurance_provider,
                'puc_number' => $detail->puc_number,
                'blacklist_status' => $detail->blacklist_status,
                'financed' => $detail->financed,
                'lender_name' => $detail->lender_name,
                'norms_type' => $detail->norms_type,
                'cubic_capacity' => $detail->cubic_capacity,
                'is_commercial' => $detail->is_commercial,
                'permit_number' => $detail->permit_number,
                'permit_type' => $detail->permit_type,
                'permit_validity' => $this->formatDate($detail->permit_validity),
            ] : null,
            'actions' => [
                'back' => route('admin.vehicle-searches.index'),
                'pdf' => route('admin.vehicle-searches.downloadPdf', $search),
            ],
        ]);
    }

    private function formatDate($value): ?string
    {
        if (! $value) {
            return null;
        }

        try {
            return \Carbon\Carbon::parse($value)->format('d M Y');
        } catch (\Throwable) {
            return (string) $value;
        }
    }

    private function formatDateTime($value): ?string
    {
        if (! $value) {
            return null;
        }

        try {
            return \Carbon\Carbon::parse($value)->format('d M Y, h:i A');
        } catch (\Throwable) {
            return (string) $value;
        }
    }
}
