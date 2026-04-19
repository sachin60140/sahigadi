<?php

namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use App\Models\AdminChallanSearch;
use App\Services\ChallanSearchService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ChallanSearchController extends Controller
{
    protected ChallanSearchService $challanSearchService;

    public function __construct(ChallanSearchService $challanSearchService)
    {
        $this->challanSearchService = $challanSearchService;
    }

    public function index()
    {
        $dealer = auth('dealer')->user();
        $query = AdminChallanSearch::where('dealer_id', $dealer->id)
            ->orderBy('created_at', 'desc');

        if (request('search')) {
            $query->where('vehicle_number', 'like', '%'.request('search').'%');
        }

        $challans = $query->paginate(15);
        $charge = $this->challanSearchService->getCharge();

        return view('dealer.challan-searches.index', compact('challans', 'charge'));
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

        $cached = false;

        return view('dealer.challan-searches.result', [
            'challan' => $challanSearch,
            'success' => $challanSearch->is_success,
            'message' => $challanSearch->is_success ? 'Challan details retrieved' : ($challanSearch->error_message ?? 'Error'),
            'cached' => $cached,
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
