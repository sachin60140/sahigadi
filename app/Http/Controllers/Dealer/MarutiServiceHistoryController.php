<?php

namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use App\Models\MarutiServiceHistory;
use App\Services\MarutiServiceHistoryService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MarutiServiceHistoryController extends Controller
{
    protected MarutiServiceHistoryService $serviceHistoryService;

    public function __construct(MarutiServiceHistoryService $serviceHistoryService)
    {
        $this->serviceHistoryService = $serviceHistoryService;
    }

    public function index(Request $request)
    {
        $dealer = auth('dealer')->user();
        $query = MarutiServiceHistory::where('dealer_id', $dealer->id)->with('records')
            ->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $query->where('vehicle_number', 'like', '%'.strtoupper($request->search).'%');
        }

        if ($request->filled('status')) {
            $query->where('is_success', $request->status === 'success');
        }
        $searches = $query->paginate(20)->withQueryString();
        $charge = $this->serviceHistoryService->getCharge();

        return Inertia::render('Dealer/Services/Lookup', [
            'page' => ['title' => 'Maruti Service History', 'eyebrow' => 'Workshop records', 'description' => 'Check available Maruti workshop visits, mileage and billing records.', 'inputName' => 'vehicle_number', 'inputLabel' => 'Registration number', 'placeholder' => 'BR01AB1234', 'indexUrl' => route('dealer.maruti-service-history.index')],
            'charge' => $charge, 'walletBalance' => $dealer->walletBalance(),
            'searches' => $searches->through(fn (MarutiServiceHistory $item) => ['id' => $item->id, 'vehicle_number' => $item->vehicle_number, 'secondary' => null, 'record_count' => $item->records->count(), 'is_success' => (bool) $item->is_success, 'charge' => (float) ($item->debit_amount ?? 0), 'total_amount' => null, 'created_at' => optional($item->created_at)->format('d M Y, h:i A'), 'error' => $item->error_message, 'actions' => ['show' => route('dealer.maruti-service-history.show', $item), 'pdf' => $item->is_success ? route('dealer.maruti-service-history.pdf', $item) : null]]),
            'filters' => ['search' => (string) $request->query('search', ''), 'status' => (string) $request->query('status', '')],
            'actions' => ['search' => route('dealer.maruti-service-history.search'), 'wallet' => route('dealer.wallet.add')],
        ]);
    }

    public function search(Request $request)
    {
        $request->validate([
            'vehicle_number' => 'required|string|min:5|max:20',
        ]);

        $dealer = auth('dealer')->user();
        $result = $this->serviceHistoryService->search($dealer, $request->vehicle_number);

        if ($result['success']) {
            return redirect()->route('dealer.maruti-service-history.show', $result['data'])
                ->with('success', $result['message']);
        }

        return redirect()->back()->with('error', $result['message']);
    }

    public function show(MarutiServiceHistory $marutiServiceHistory)
    {
        if ($marutiServiceHistory->dealer_id !== auth('dealer')->id()) {
            abort(403);
        }
        $marutiServiceHistory->load('records');

        return Inertia::render('Dealer/Services/Result', [
            'page' => ['title' => 'Maruti Service History', 'eyebrow' => 'Workshop records'],
            'result' => ['vehicle_number' => $marutiServiceHistory->vehicle_number, 'is_success' => (bool) $marutiServiceHistory->is_success, 'error' => $marutiServiceHistory->error_message, 'charge' => (float) ($marutiServiceHistory->debit_amount ?? 0), 'created_at' => optional($marutiServiceHistory->created_at)->format('d M Y, h:i A')],
            'summary' => [['label' => 'Service records', 'value' => $marutiServiceHistory->records->count()], ['label' => 'Total billed', 'value' => 'Rs '.number_format((float) $marutiServiceHistory->records->sum('total_amount'), 2)]],
            'sections' => [], 'columns' => [['key' => 'svc_date', 'label' => 'Service date'], ['key' => 'service_cate', 'label' => 'Category'], ['key' => 'dealer_name', 'label' => 'Dealer'], ['key' => 'repair_order_no', 'label' => 'RO number'], ['key' => 'mileage', 'label' => 'Mileage'], ['key' => 'total_amount', 'label' => 'Total amount']],
            'records' => $marutiServiceHistory->records->map->only(['svc_date', 'service_cate', 'dealer_name', 'repair_order_no', 'mileage', 'total_amount']),
            'actions' => ['index' => route('dealer.maruti-service-history.index'), 'pdf' => $marutiServiceHistory->is_success ? route('dealer.maruti-service-history.pdf', $marutiServiceHistory) : null],
        ]);
    }

    public function downloadPdf(MarutiServiceHistory $marutiServiceHistory)
    {
        if ($marutiServiceHistory->dealer_id !== auth('dealer')->id()) {
            abort(403);
        }
        $marutiServiceHistory->load('records');

        $pdf = Pdf::loadView('dealer.maruti-service-history.pdf', compact('marutiServiceHistory'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('maruti-service-history-'.$marutiServiceHistory->vehicle_number.'.pdf');
    }
}
