<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ChallanPdfSearch;
use App\Models\Setting;
use App\Services\ChallanPdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ChallanPdfController extends Controller
{
    protected $challanPdfService;

    public function __construct(ChallanPdfService $challanPdfService)
    {
        $this->challanPdfService = $challanPdfService;
    }

    public function index()
    {
        return $this->renderPage();
    }

    public function search(Request $request)
    {
        $request->validate([
            'vehicle_number' => 'required|string|max:20'
        ]);

        $customer = Auth::guard('customer')->user();
        
        $result = $this->challanPdfService->fetchChallanPdf($request->vehicle_number, $customer, 'customer');

        if (isset($result['redirect_to_recharge']) && $result['redirect_to_recharge']) {
            return redirect()->route('customer.wallet.add')->with('error', $result['message']);
        }

        if (!$result['success']) {
            return back()->with('error', $result['message']);
        }

        return redirect()->route('customer.challan-pdf.history')->with('success', 'Challan PDF generated successfully.');
    }

    public function history()
    {
        return $this->renderPage(true);
    }

    private function renderPage(bool $showHistory = false)
    {
        $customer = Auth::guard('customer')->user();
        $history = ChallanPdfSearch::where('customer_id', $customer->id)
            ->latest()
            ->paginate(15);

        return Inertia::render('Customer/ChallanPdf/Index', [
            'showHistory' => $showHistory,
            'service' => [
                'active' => Setting::isChallanPdfActive(),
                'charge' => Setting::getChallanPdfCharge(),
                'wallet_balance' => (float) ($customer->wallet?->balance ?? 0),
            ],
            'history' => $history->through(fn (ChallanPdfSearch $record) => [
                'id' => $record->id,
                'vehicle_number' => $record->vehicle_number,
                'is_success' => (bool) $record->is_success,
                'charge_amount' => (float) ($record->charge_amount ?? 0),
                'pdf_url' => $record->pdf_url,
                'error_message' => $record->error_message,
                'created_at' => optional($record->created_at)->format('d M Y, h:i A'),
            ]),
            'actions' => [
                'search' => route('customer.challan-pdf.search'),
                'index' => route('customer.challan-pdf.index'),
                'history' => route('customer.challan-pdf.history'),
                'wallet' => route('customer.wallet.add'),
            ],
        ]);
    }
}
