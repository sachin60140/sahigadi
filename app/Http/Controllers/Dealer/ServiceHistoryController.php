<?php

namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use App\Models\ServiceHistory;
use App\Services\ServiceHistoryService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ServiceHistoryController extends Controller
{
    protected ServiceHistoryService $serviceHistoryService;

    public function __construct(ServiceHistoryService $serviceHistoryService)
    {
        $this->serviceHistoryService = $serviceHistoryService;
    }

    public function index(Request $request)
    {
        $dealer = auth('dealer')->user();

        $query = ServiceHistory::where('dealer_id', $dealer->id)
            ->with('records')
            ->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $query->where('vehicle_number', 'like', '%'.strtoupper($request->search).'%');
        }

        if ($request->filled('status')) {
            if ($request->status === 'success') {
                $query->where('is_success', true);
            } elseif ($request->status === 'failed') {
                $query->where('is_success', false);
            }
        }

        $searches = $query->paginate(20)->withQueryString();
        $charge = $this->serviceHistoryService->getCharge();
        $walletBalance = $dealer->walletBalance();

        return Inertia::render('Dealer/Services/Lookup', [
            'page' => ['title' => 'Mahindra Service History', 'eyebrow' => 'Workshop records', 'description' => 'Review available service visits and dealer workshop history.', 'inputName' => 'vehicle_number', 'inputLabel' => 'Vehicle number', 'placeholder' => 'BR01AB1234', 'indexUrl' => route('dealer.service-history.index')],
            'charge' => $charge, 'walletBalance' => $walletBalance,
            'searches' => $searches->through(fn (ServiceHistory $item) => ['id' => $item->id, 'vehicle_number' => $item->vehicle_number, 'secondary' => null, 'record_count' => $item->records->count(), 'is_success' => (bool) $item->is_success, 'charge' => (float) ($item->debit_amount ?? 0), 'total_amount' => null, 'created_at' => optional($item->created_at)->format('d M Y, h:i A'), 'error' => $item->error_message, 'actions' => ['show' => route('dealer.service-history.show', $item), 'pdf' => $item->is_success ? route('dealer.service-history.pdf', $item) : null]]),
            'filters' => ['search' => (string) $request->query('search', ''), 'status' => (string) $request->query('status', '')],
            'actions' => ['search' => route('dealer.service-history.search'), 'wallet' => route('dealer.wallet.add')],
        ]);
    }

    public function search(Request $request)
    {
        $request->validate([
            'vehicle_number' => 'required|string|min:4|max:20',
        ]);

        $dealer = auth('dealer')->user();
        $result = $this->serviceHistoryService->search($dealer, $request->vehicle_number);

        if (! $result['success'] && str_contains($result['message'], 'Insufficient wallet')) {
            return redirect()->back()->with('error', $result['message']);
        }

        if ($result['success']) {
            $message = $result['cached']
                ? 'Service history retrieved from cache.'
                : 'Service history retrieved successfully. ₹'.number_format($this->serviceHistoryService->getCharge(), 2).' debited from wallet.';

            return redirect()->route('dealer.service-history.show', $result['data'])->with('success', $message);
        }

        return redirect()->back()->with('error', $result['message'])->withInput();
    }

    public function show(ServiceHistory $serviceHistory)
    {
        $dealer = auth('dealer')->user();

        if ($serviceHistory->dealer_id !== $dealer->id) {
            abort(403);
        }

        $serviceHistory->load('records');

        return Inertia::render('Dealer/Services/Result', [
            'page' => ['title' => 'Mahindra Service History', 'eyebrow' => 'Workshop records'],
            'result' => ['vehicle_number' => $serviceHistory->vehicle_number, 'is_success' => (bool) $serviceHistory->is_success, 'error' => $serviceHistory->error_message, 'charge' => (float) ($serviceHistory->debit_amount ?? 0), 'created_at' => optional($serviceHistory->created_at)->format('d M Y, h:i A')],
            'summary' => [['label' => 'Service records', 'value' => $serviceHistory->records->count()]],
            'sections' => [], 'columns' => [['key' => 'svc_date', 'label' => 'Service date'], ['key' => 'service_cate', 'label' => 'Category'], ['key' => 'dealer_name', 'label' => 'Dealer'], ['key' => 'mileage', 'label' => 'Mileage'], ['key' => 'net_bill_amt', 'label' => 'Bill amount'], ['key' => 'status', 'label' => 'Status']],
            'records' => $serviceHistory->records->map->only(['svc_date', 'service_cate', 'dealer_name', 'mileage', 'net_bill_amt', 'status']),
            'actions' => ['index' => route('dealer.service-history.index'), 'pdf' => $serviceHistory->is_success ? route('dealer.service-history.pdf', $serviceHistory) : null],
        ]);
    }

    public function downloadPdf(ServiceHistory $serviceHistory)
    {
        $dealer = auth('dealer')->user();

        if ($serviceHistory->dealer_id !== $dealer->id) {
            abort(403);
        }

        $serviceHistory->load('records');

        $pdf = Pdf::loadView('dealer.service-history.pdf', compact('serviceHistory'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('service-history-'.$serviceHistory->vehicle_number.'.pdf');
    }
}
