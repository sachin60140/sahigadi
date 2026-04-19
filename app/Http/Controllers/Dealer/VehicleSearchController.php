<?php

namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use App\Models\VehicleDetail;
use App\Services\VehicleSearchService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

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

        $searches = $query->paginate(20);
        $charge = $this->vehicleSearchService->getCharge();
        $walletBalance = $dealer->walletBalance();

        return view('dealer.vehicle-search.index', compact('searches', 'charge', 'walletBalance'));
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

        return view('dealer.vehicle-search.show', compact('vehicleSearch'));
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
}
