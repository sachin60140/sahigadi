<?php

namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use App\Models\MarutiServiceHistory;
use App\Services\MarutiServiceHistoryService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

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
        $query = MarutiServiceHistory::where('dealer_id', $dealer->id)
            ->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $query->where('vehicle_number', 'like', '%'.strtoupper($request->search).'%');
        }

        $searches = $query->paginate(20);
        $charge = $this->serviceHistoryService->getCharge();

        return view('dealer.maruti-service-history.index', compact('searches', 'charge'));
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

        return view('dealer.maruti-service-history.show', compact('marutiServiceHistory'));
    }

    public function exportPdf(MarutiServiceHistory $marutiServiceHistory)
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
