<?php

namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use App\Models\AdminChallanSearch;
use App\Services\ChallanSearchService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ChallanSearchController extends Controller
{
    protected ChallanSearchService $challanSearchService;

    public function __construct(ChallanSearchService $challanSearchService)
    {
        $this->challanSearchService = $challanSearchService;
    }

    public function index(Request $request)
    {
        $dealer = auth('dealer')->user();
        $query = AdminChallanSearch::where('dealer_id', $dealer->id)
            ->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $query->where('vehicle_number', 'like', '%'.$request->search.'%');
        }

        if ($request->filled('status')) {
            $query->where('is_success', $request->status === 'success');
        }
        $challans = $query->paginate(15)->withQueryString();
        $charge = $this->challanSearchService->getCharge();

        return Inertia::render('Dealer/Services/Lookup', [
            'page' => ['title' => 'E-Challan Check', 'eyebrow' => 'Traffic fine records', 'description' => 'Review pending and paid traffic challans for a vehicle.', 'inputName' => 'vehicle_number', 'inputLabel' => 'Vehicle number', 'placeholder' => 'BR01AB1234', 'indexUrl' => route('dealer.challan-search.index')],
            'charge' => $charge, 'walletBalance' => $dealer->walletBalance(),
            'searches' => $challans->through(fn (AdminChallanSearch $item) => ['id' => $item->id, 'vehicle_number' => $item->vehicle_number, 'secondary' => null, 'record_count' => $item->challan_count, 'is_success' => (bool) $item->is_success, 'charge' => (float) ($item->charge_amount ?? 0), 'total_amount' => (float) ($item->total_amount ?? 0), 'created_at' => optional($item->created_at)->format('d M Y, h:i A'), 'error' => $item->error_message, 'actions' => ['show' => route('dealer.challan-search.show', $item), 'pdf' => $item->is_success ? route('dealer.challan-search.pdf', $item) : null]]),
            'filters' => ['search' => (string) $request->query('search', ''), 'status' => (string) $request->query('status', '')],
            'actions' => ['search' => route('dealer.challan-search.search'), 'wallet' => route('dealer.wallet.add')],
        ]);
    }

    public function search(Request $request)
    {
        $request->validate([
            'vehicle_number' => 'required|string|min:4|max:20',
        ]);

        $dealer = auth('dealer')->user();

        $result = $this->challanSearchService->search($dealer, $request->vehicle_number);

        if (! $result['success'] && str_contains($result['message'], 'Insufficient wallet')) {
            return redirect()->back()->with('error', $result['message']);
        }

        if ($result['success']) {
            $message = $result['cached']
                ? 'Challan details retrieved from cache.'
                : 'Challan details retrieved successfully. ₹'.number_format($this->challanSearchService->getCharge(), 2).' debited from wallet.';

            return redirect()->route('dealer.challan-search.show', $result['data'])->with('success', $message);
        }

        return redirect()->back()->with('error', $result['message'])->withInput();
    }

    public function show(AdminChallanSearch $challanSearch)
    {
        $dealer = auth('dealer')->user();

        if ($challanSearch->dealer_id !== $dealer->id) {
            abort(403);
        }

        $records = collect($challanSearch->challan_data ?? [])->sortByDesc('dateChallan')->values();
        $pending = $records->filter(fn ($item) => strtolower($item['status'] ?? '') !== 'paid')->sum(fn ($item) => (float) ($item['amountChallan'] ?? 0));
        $paid = $records->filter(fn ($item) => strtolower($item['status'] ?? '') === 'paid')->sum(fn ($item) => (float) ($item['amountChallan'] ?? 0));

        return Inertia::render('Dealer/Services/Result', [
            'page' => ['title' => 'E-Challan Details', 'eyebrow' => 'Traffic fine records'],
            'result' => ['vehicle_number' => $challanSearch->vehicle_number, 'is_success' => (bool) $challanSearch->is_success, 'error' => $challanSearch->error_message, 'charge' => (float) ($challanSearch->charge_amount ?? 0), 'created_at' => optional($challanSearch->created_at)->format('d M Y, h:i A')],
            'summary' => [['label' => 'Total challans', 'value' => $challanSearch->challan_count], ['label' => 'Pending amount', 'value' => 'Rs '.number_format($pending)], ['label' => 'Paid amount', 'value' => 'Rs '.number_format($paid)]],
            'sections' => [], 'columns' => [['key' => 'challan_no', 'label' => 'Challan number'], ['key' => 'date', 'label' => 'Date'], ['key' => 'location', 'label' => 'Location'], ['key' => 'offence', 'label' => 'Offence'], ['key' => 'amount', 'label' => 'Amount'], ['key' => 'status', 'label' => 'Status'], ['key' => 'court', 'label' => 'Court']],
            'records' => $records->map(fn ($item) => ['challan_no' => $item['challanNo'] ?? 'N/A', 'date' => $item['dateChallan'] ?? 'N/A', 'location' => $item['locationChallan'] ?? 'N/A', 'offence' => data_get($item, 'detailsViolation.0.offence', 'N/A'), 'amount' => 'Rs '.number_format((float) ($item['amountChallan'] ?? 0)), 'status' => $item['status'] ?? 'N/A', 'court' => $item['court_status_desc'] ?? 'N/A']),
            'actions' => ['index' => route('dealer.challan-search.index'), 'pdf' => $challanSearch->is_success ? route('dealer.challan-search.pdf', $challanSearch) : null],
        ]);
    }

    public function exportPdf(AdminChallanSearch $challanSearch)
    {
        $dealer = auth('dealer')->user();

        if ($challanSearch->dealer_id !== $dealer->id) {
            abort(403);
        }

        $pdf = Pdf::loadView('dealer.challan-searches.pdf', compact('challanSearch'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('challan-'.$challanSearch->vehicle_number.'.pdf');
    }
}
