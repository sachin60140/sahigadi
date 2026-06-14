<?php

namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use App\Models\VehicleDetail;
use App\Services\VehicleSearchService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Inertia\Inertia;

class VehicleSearchController extends Controller
{
    protected VehicleSearchService $vehicleSearchService;

    public function __construct(VehicleSearchService $vehicleSearchService)
    {
        $this->vehicleSearchService = $vehicleSearchService;
    }

    public function index(Request $request)
    {
        $dealer = auth('dealer')->user();

        $query = VehicleDetail::where('dealer_id', $dealer->id)
            ->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $query->where('registration_number', 'like', '%'.strtoupper($request->search).'%');
        }

        if ($request->filled('status')) {
            if ($request->status === 'success') {
                $query->where('is_success', true);
            } elseif ($request->status === 'failed') {
                $query->where('is_success', false);
            }
        }

        $searches = $query->paginate(20)->withQueryString();
        $charge = $this->vehicleSearchService->getCharge();
        $walletBalance = $dealer->walletBalance();

        return Inertia::render('Dealer/Services/Lookup', [
            'page' => ['title' => 'Vahan RC Check', 'eyebrow' => 'Vehicle verification', 'description' => 'Fetch official registration, ownership and compliance details.', 'inputName' => 'registration_number', 'inputLabel' => 'Registration number', 'placeholder' => 'BR01AB1234', 'indexUrl' => route('dealer.vehicle-search.index')],
            'charge' => $charge, 'walletBalance' => $walletBalance,
            'searches' => $searches->through(fn (VehicleDetail $item) => [
                'id' => $item->id, 'vehicle_number' => $item->registration_number, 'secondary' => trim(($item->owner_name ?? '').' / '.($item->make ?? '').' '.($item->model ?? ''), ' /'), 'record_count' => null, 'is_success' => (bool) $item->is_success, 'charge' => (float) ($item->debit_amount ?? 0), 'total_amount' => null, 'created_at' => optional($item->created_at)->format('d M Y, h:i A'), 'error' => $item->error_message,
                'actions' => ['show' => route('dealer.vehicle-search.show', $item), 'pdf' => $item->is_success ? route('dealer.vehicle-search.pdf', $item) : null],
            ]),
            'filters' => ['search' => (string) $request->query('search', ''), 'status' => (string) $request->query('status', '')],
            'actions' => ['search' => route('dealer.vehicle-search.search'), 'wallet' => route('dealer.wallet.add')],
        ]);
    }

    public function search(Request $request)
    {
        $request->validate([
            'registration_number' => 'required|string|min:4|max:20',
        ]);

        $dealer = auth('dealer')->user();
        $result = $this->vehicleSearchService->search($dealer, $request->registration_number);

        if (! $result['success'] && $result['message'] === 'Insufficient wallet balance. Required: ₹'.number_format($result['data'] ? 0 : $this->vehicleSearchService->getCharge(), 2)) {
            return redirect()->back()->with('error', $result['message']);
        }

        if ($result['success']) {
            $message = $result['cached']
                ? 'Vehicle details retrieved from cache.'
                : 'Vehicle details retrieved successfully. ₹'.number_format($this->vehicleSearchService->getCharge(), 2).' debited from wallet.';

            return redirect()->route('dealer.vehicle-search.show', $result['data'])->with('success', $message);
        }

        return redirect()->back()->with('error', $result['message'])->withInput();
    }

    public function show(VehicleDetail $vehicleSearch)
    {
        $dealer = auth('dealer')->user();

        if ($vehicleSearch->dealer_id !== $dealer->id) {
            abort(403);
        }

        return Inertia::render('Dealer/Services/Result', [
            'page' => ['title' => 'RC Details', 'eyebrow' => 'Vehicle verification'],
            'result' => ['vehicle_number' => $vehicleSearch->registration_number, 'is_success' => (bool) $vehicleSearch->is_success, 'error' => $vehicleSearch->error_message, 'charge' => (float) ($vehicleSearch->debit_amount ?? 0), 'created_at' => optional($vehicleSearch->created_at)->format('d M Y, h:i A')],
            'summary' => [],
            'sections' => [
                ['title' => 'Owner details', 'items' => $this->items($vehicleSearch, ['owner_name' => 'Owner name', 'father_name' => 'Father name', 'address' => 'Address', 'mobile_number' => 'Mobile number', 'rto_location' => 'RTO location'])],
                ['title' => 'Vehicle details', 'items' => $this->items($vehicleSearch, ['vehicle_class' => 'Vehicle class', 'vehicle_category' => 'Category', 'make' => 'Make', 'model' => 'Model', 'variant' => 'Variant', 'color' => 'Color', 'fuel_type' => 'Fuel type', 'engine_number' => 'Engine number', 'chassis_number' => 'Chassis number', 'registration_date' => 'Registration date', 'manufactured_date' => 'Manufactured date', 'rc_status' => 'RC status'])],
                ['title' => 'Documents and compliance', 'items' => $this->items($vehicleSearch, ['insurance_provider' => 'Insurance provider', 'insurance_policy_number' => 'Insurance policy', 'insurance_date' => 'Insurance valid till', 'fitness_date' => 'Fitness valid till', 'puc_number' => 'PUC number', 'puc_validity' => 'PUC valid till', 'tax_validity' => 'Tax valid till', 'blacklist_status' => 'Blacklist status', 'lender_name' => 'Lender', 'permit_number' => 'Permit number'])],
            ],
            'columns' => [], 'records' => [],
            'actions' => ['index' => route('dealer.vehicle-search.index'), 'pdf' => $vehicleSearch->is_success ? route('dealer.vehicle-search.pdf', $vehicleSearch) : null],
        ]);
    }

    public function exportPdf(VehicleDetail $vehicleSearch)
    {
        $dealer = auth('dealer')->user();

        if ($vehicleSearch->dealer_id !== $dealer->id) {
            abort(403);
        }

        $pdf = Pdf::loadView('dealer.vehicle-search.pdf', compact('vehicleSearch'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('rc-'.$vehicleSearch->registration_number.'.pdf');
    }

    private function items(VehicleDetail $vehicle, array $fields): array
    {
        return collect($fields)->map(fn ($label, $field) => [
            'label' => $label,
            'value' => $vehicle->{$field} instanceof \Carbon\CarbonInterface ? $vehicle->{$field}->format('d M Y') : ($vehicle->{$field} ?? 'N/A'),
        ])->values()->all();
    }
}
