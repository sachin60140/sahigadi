<?php

namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use App\Models\ServiceHistory;
use App\Services\ServiceHistoryService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

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

        $searches = $query->paginate(20);
        $charge = $this->serviceHistoryService->getCharge();
        $walletBalance = $dealer->walletBalance();

        return view('dealer.service-history.index', compact('searches', 'charge', 'walletBalance'));
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

        return view('dealer.service-history.show', compact('serviceHistory'));
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
