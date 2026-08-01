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

        $raw = is_array($vehicleSearch->raw_response) ? $vehicleSearch->raw_response : [];
        $sections = $vehicleSearch->is_success ? ($raw ? $this->buildRcSections($raw) : $this->buildRcSectionsFromModel($vehicleSearch)) : [];
        $summary = $vehicleSearch->is_success ? array_values(array_filter([
            ['label' => 'Maker & model', 'value' => trim(($raw['makerDescription'] ?? '').' '.($raw['makerModel'] ?? ''))],
            ['label' => 'Fuel', 'value' => $raw['fuelType'] ?? ''],
            ['label' => 'Reg. date', 'value' => $raw['registered'] ?? ''],
            ['label' => 'RTO', 'value' => $raw['rto'] ?? ''],
        ], fn ($metric) => $metric['value'] !== '' && $metric['value'] !== null)) : [];

        return Inertia::render('Dealer/Services/Result', [
            'page' => ['title' => 'RC Details', 'eyebrow' => 'Vehicle verification'],
            'result' => ['vehicle_number' => $vehicleSearch->registration_number, 'is_success' => (bool) $vehicleSearch->is_success, 'error' => $vehicleSearch->error_message, 'charge' => (float) ($vehicleSearch->debit_amount ?? 0), 'created_at' => optional($vehicleSearch->created_at)->format('d M Y, h:i A')],
            'summary' => $summary,
            'sections' => $sections,
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

        $raw = is_array($vehicleSearch->raw_response) ? $vehicleSearch->raw_response : [];
        $sections = $vehicleSearch->is_success ? ($raw ? $this->buildRcSections($raw) : $this->buildRcSectionsFromModel($vehicleSearch)) : [];

        $pdf = Pdf::loadView('dealer.vehicle-search.pdf', compact('vehicleSearch', 'sections'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('rc-'.$vehicleSearch->registration_number.'.pdf');
    }

    /**
     * Build display sections from the complete API response so every field the
     * provider returns is shown, grouped, with any unmapped keys collected under
     * "Additional details" (nothing is dropped).
     */
    private function buildRcSections(array $raw): array
    {
        if (empty($raw)) {
            return [];
        }

        $labels = [
            'status' => 'RC status', 'registered' => 'Registration date', 'owner' => 'Owner name',
            'ownerNumber' => 'Owner serial', 'father' => 'Father / husband name', 'mobile' => 'Mobile number',
            'currentAddress' => 'Current address', 'permanentAddress' => 'Permanent address', 'rto' => 'RTO',
            'category' => 'Vehicle category', 'categoryDescription' => 'Category description',
            'makerDescription' => 'Maker', 'makerModel' => 'Model', 'makerVariant' => 'Variant',
            'bodyType' => 'Body type', 'colorType' => 'Colour', 'fuelType' => 'Fuel type',
            'normsType' => 'Emission norms', 'manufactured' => 'Manufactured', 'chassisNumber' => 'Chassis number',
            'engineNumber' => 'Engine / motor number', 'exShowroomPrice' => 'Ex-showroom price',
            'cubicCapacity' => 'Cubic capacity', 'cylinders' => 'Cylinders', 'seatingCapacity' => 'Seating capacity',
            'standingCapacity' => 'Standing capacity', 'sleepingCapacity' => 'Sleeper capacity',
            'unladenWeight' => 'Unladen weight', 'grossWeight' => 'Gross weight', 'wheelBase' => 'Wheel base',
            'insuranceProvider' => 'Insurance provider', 'insurancePolicyNumber' => 'Insurance policy number',
            'insuranceUpto' => 'Insurance valid till', 'fitnessUpto' => 'Fitness valid till',
            'pollutionCertificateNumber' => 'PUC number', 'pollutionCertificateUpto' => 'PUC valid till',
            'blacklistStatus' => 'Blacklist status', 'blacklistDetails' => 'Blacklist details',
            'financed' => 'Financed', 'lender' => 'Lender',
            'taxUpto' => 'Tax valid till', 'taxPaidUpto' => 'Tax paid up to',
            'permitNumber' => 'Permit number', 'permitType' => 'Permit type', 'permitIssued' => 'Permit issued',
            'permitFrom' => 'Permit from', 'permitUpto' => 'Permit valid till',
            'nationalPermitNumber' => 'National permit number', 'nationalPermitIssued' => 'National permit issued',
            'nationalPermitFrom' => 'National permit from', 'nationalPermitUpto' => 'National permit valid till',
            'nationalPermitIssuedBy' => 'National permit issued by',
            'nocDetails' => 'NOC details', 'challanDetails' => 'Challan details',
            'nonUseStatus' => 'Non-use status', 'nonUseFrom' => 'Non-use from', 'nonUseTo' => 'Non-use to',
            'commercial' => 'Commercial vehicle',
        ];

        $groups = [
            'Owner and registration' => ['status', 'registered', 'owner', 'ownerNumber', 'father', 'mobile', 'currentAddress', 'permanentAddress', 'rto'],
            'Vehicle' => ['category', 'categoryDescription', 'makerDescription', 'makerModel', 'makerVariant', 'bodyType', 'colorType', 'fuelType', 'normsType', 'manufactured', 'chassisNumber', 'engineNumber', 'exShowroomPrice', 'commercial'],
            'Engine and dimensions' => ['cubicCapacity', 'cylinders', 'seatingCapacity', 'standingCapacity', 'sleepingCapacity', 'unladenWeight', 'grossWeight', 'wheelBase'],
            'Insurance, fitness and PUC' => ['insuranceProvider', 'insurancePolicyNumber', 'insuranceUpto', 'fitnessUpto', 'pollutionCertificateNumber', 'pollutionCertificateUpto', 'blacklistStatus', 'blacklistDetails', 'financed', 'lender'],
            'Tax and permit' => ['taxUpto', 'taxPaidUpto', 'permitNumber', 'permitType', 'permitIssued', 'permitFrom', 'permitUpto', 'nationalPermitNumber', 'nationalPermitIssued', 'nationalPermitFrom', 'nationalPermitUpto', 'nationalPermitIssuedBy'],
            'Other status' => ['nocDetails', 'challanDetails', 'nonUseStatus', 'nonUseFrom', 'nonUseTo'],
        ];

        $booleanKeys = ['financed', 'commercial'];
        $used = ['valid', 'masked'];
        $sections = [];

        $format = function (string $key, $value) use ($booleanKeys) {
            if (is_array($value)) {
                return collect($value)->filter()->implode(', ');
            }
            if (in_array($key, $booleanKeys, true)) {
                return ((string) $value === '1' || $value === true) ? 'Yes' : 'No';
            }

            return $value;
        };

        $isEmpty = fn ($value) => $value === null || $value === '' || $value === [];

        foreach ($groups as $title => $keys) {
            $items = [];
            foreach ($keys as $key) {
                if (! array_key_exists($key, $raw)) {
                    continue;
                }
                $used[] = $key;
                $value = $format($key, $raw[$key]);
                if (! in_array($key, $booleanKeys, true) && $isEmpty($value)) {
                    continue;
                }
                $items[] = ['label' => $labels[$key] ?? $this->humanizeKey($key), 'value' => $value];
            }
            if ($items) {
                $sections[] = ['title' => $title, 'items' => $items];
            }
        }

        $extra = [];
        foreach ($raw as $key => $value) {
            if (in_array($key, $used, true)) {
                continue;
            }
            $value = $format($key, $value);
            if ($isEmpty($value)) {
                continue;
            }
            $extra[] = ['label' => $labels[$key] ?? $this->humanizeKey($key), 'value' => $value];
        }
        if ($extra) {
            $sections[] = ['title' => 'Additional details', 'items' => $extra];
        }

        return $sections;
    }

    /**
     * Fallback for legacy records saved without the full raw_response: build
     * sections from the stored columns so they still display their details.
     */
    private function buildRcSectionsFromModel(VehicleDetail $vehicle): array
    {
        $groups = [
            'Owner details' => ['owner_name' => 'Owner name', 'father_name' => 'Father name', 'address' => 'Address', 'mobile_number' => 'Mobile number', 'rto_location' => 'RTO location'],
            'Vehicle details' => ['vehicle_class' => 'Vehicle class', 'vehicle_category' => 'Category', 'make' => 'Make', 'model' => 'Model', 'variant' => 'Variant', 'color' => 'Colour', 'fuel_type' => 'Fuel type', 'norms_type' => 'Emission norms', 'engine_number' => 'Engine number', 'chassis_number' => 'Chassis number', 'registration_date' => 'Registration date', 'manufactured_date' => 'Manufactured', 'rc_status' => 'RC status'],
            'Engine and dimensions' => ['cubic_capacity' => 'Cubic capacity', 'cylinders' => 'Cylinders', 'seats' => 'Seating capacity', 'unladen_weight' => 'Unladen weight', 'gross_weight' => 'Gross weight'],
            'Insurance, fitness and PUC' => ['insurance_provider' => 'Insurance provider', 'insurance_policy_number' => 'Insurance policy number', 'insurance_date' => 'Insurance valid till', 'fitness_date' => 'Fitness valid till', 'puc_number' => 'PUC number', 'puc_validity' => 'PUC valid till', 'tax_validity' => 'Tax valid till', 'blacklist_status' => 'Blacklist status', 'lender_name' => 'Lender', 'permit_number' => 'Permit number'],
        ];

        $sections = [];
        foreach ($groups as $title => $fields) {
            $items = [];
            foreach ($fields as $field => $label) {
                $value = $vehicle->{$field};
                if ($value instanceof \Carbon\CarbonInterface) {
                    $value = $value->format('d M Y');
                }
                if ($value === null || $value === '') {
                    continue;
                }
                $items[] = ['label' => $label, 'value' => $value];
            }
            if ($items) {
                $sections[] = ['title' => $title, 'items' => $items];
            }
        }

        return $sections;
    }

    private function humanizeKey(string $key): string
    {
        return ucfirst(trim(preg_replace('/(?<!^)[A-Z]/', ' $0', $key)));
    }
}
