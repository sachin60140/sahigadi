<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\CustomerMarutiServiceHistory;
use App\Services\CustomerMarutiServiceHistoryService;
use App\Services\RazorpayService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MarutiServiceHistoryController extends Controller
{
    protected CustomerMarutiServiceHistoryService $serviceHistoryService;

    protected RazorpayService $razorpayService;

    public function __construct(
        CustomerMarutiServiceHistoryService $serviceHistoryService,
        RazorpayService $razorpayService
    ) {
        $this->serviceHistoryService = $serviceHistoryService;
        $this->razorpayService = $razorpayService;
    }

    public function index()
    {
        $charge = $this->serviceHistoryService->getCharge();
        $history = collect();

        if (auth('customer')->check()) {
            $history = CustomerMarutiServiceHistory::where('customer_phone', auth('customer')->user()->phone)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return Inertia::render('Public/Services/Lookup', [
            'service' => [
                'key' => 'maruti-service-history',
                'eyebrow' => 'Authorized service records',
                'title' => 'Maruti service history',
                'description' => 'Review authorized Maruti visits, dealer details, mileage and bill history in one report.',
                'numberField' => 'vehicle_number',
                'numberLabel' => 'Vehicle registration number',
                'placeholder' => 'e.g. DL01AB1234',
                'submitLabel' => 'Search Maruti history',
                'actionUrl' => route('maruti-service-history.search'),
                'charge' => (float) $charge,
                'requiresGuestDetails' => ! auth('customer')->check(),
                'customerAuthenticated' => auth('customer')->check(),
                'seoTitle' => 'Maruti Service History - SAHI GADI',
                'seoDescription' => 'Search Maruti service history online with SAHI GADI. Review authorized service visits, bill details and mileage checkpoints.',
                'canonical' => route('maruti-service-history.index'),
                'features' => [
                    'Authorized Maruti service visits',
                    'Parts, labour and total bill amounts',
                    'Dealer and mileage checkpoints',
                ],
            ],
            'history' => $history->map(fn (CustomerMarutiServiceHistory $record) => [
                'id' => $record->id,
                'number' => $record->vehicle_number,
                'is_success' => $record->is_success,
                'created_at' => $record->created_at?->toIso8601String(),
                'view_url' => $record->is_success ? route('maruti-service-history.show', $record) : null,
                'pdf_url' => $record->is_success ? route('maruti-service-history.pdf', $record) : null,
            ])->values(),
        ]);
    }

    public function search(Request $request)
    {
        $rules = [
            'vehicle_number' => 'required|string|min:4|max:20',
        ];

        if (!auth('customer')->check()) {
            $rules['customer_name'] = 'required|string|max:255';
            $rules['customer_phone'] = 'required|string|max:20';
            $rules['customer_email'] = 'nullable|email|max:255';
        }

        $request->validate($rules);

        $vehicleNumber = $request->vehicle_number;
        
        if (auth('customer')->check()) {
            $customerInfo = [
                'name' => auth('customer')->user()->name,
                'phone' => auth('customer')->user()->phone,
                'email' => auth('customer')->user()->email,
            ];
        } else {
            $customerInfo = [
                'name' => $request->customer_name,
                'phone' => $request->customer_phone,
                'email' => $request->customer_email,
            ];
        }

        // Cache disabled per user request: "search always fresh no old record required"

        $charge = $this->serviceHistoryService->getCharge();

        if (auth('customer')->check()) {
            $customer = auth('customer')->user();
            $wallet = \App\Models\CustomerWallet::firstOrCreate(
                ['customer_id' => $customer->id],
                ['balance' => 0]
            );

            if ($wallet->balance < $charge) {
                return redirect()->route('customer.wallet.add')
                    ->with('error', 'Low Balance! Please recharge your wallet to continue.');
            }

            // Deduct funds
            $transaction = $wallet->deductFunds($charge, "Maruti Service History Search for {$vehicleNumber}");

            // Perform API search directly
            $result = $this->serviceHistoryService->search($vehicleNumber, $customerInfo);

            if (isset($result['data']) && $result['data']) {
                $result['data']->update([
                    'paid_amount' => $charge,
                ]);

                if ($result['success']) {
                    $result['data']->load('records');
                }
            }

            // If API fails, refund the wallet
            if (!$result['success']) {
                $wallet->addFunds($charge, "Refund: Failed Maruti Service History Search for {$vehicleNumber}");
                $transaction->update(['remark' => $transaction->remark . ' (Refunded)']);
            }

            return $this->renderResult(
                $result['data'] ?? null,
                $result['cached'] ?? false,
                $result['success'],
                $result['message']
            );
        }

        // For non-logged-in users, continue with Razorpay
        $order = $this->razorpayService->createOrder(
            $charge,
            'msh_'.time(),
            [
                'vehicle_number' => $vehicleNumber,
                'customer_name' => $customerInfo['name'],
                'customer_phone' => $customerInfo['phone'],
            ]
        );

        session([
            'maruti_service_history_pending' => [
                'vehicle_number' => $vehicleNumber,
                'customer_info' => $customerInfo,
                'order_id' => $order['order_id'],
            ],
        ]);

        return Inertia::render('Public/Services/Payment', [
            'orderId' => $order['order_id'],
            'amount' => (float) $order['amount'],
            'keyId' => $this->razorpayService->getKeyId(),
            'vehicleNumber' => $vehicleNumber,
            'customerName' => $customerInfo['name'],
            'reportLabel' => 'Maruti Service History Report',
            'description' => 'Maruti Service History Report - '.$vehicleNumber,
            'callbackUrl' => route('maruti-service-history.callback'),
            'cancelUrl' => route('maruti-service-history.index'),
        ]);
    }

    public function paymentCallback(Request $request)
    {
        $pending = session('maruti_service_history_pending');

        if (! $pending) {
            return redirect()->route('maruti-service-history.index')->with('error', 'No pending payment found');
        }

        $razorpayOrderId = $request->razorpay_order_id ?? $request->query('razorpay_order_id');
        $razorpayPaymentId = $request->razorpay_payment_id ?? $request->query('razorpay_payment_id');
        $razorpaySignature = $request->razorpay_signature ?? $request->query('razorpay_signature');

        if (empty($razorpayOrderId) || empty($razorpayPaymentId) || empty($razorpaySignature)) {
            return redirect()->route('maruti-service-history.index')->with('error', 'Payment was not completed');
        }

        if (! hash_equals((string) $pending['order_id'], (string) $razorpayOrderId)) {
            return redirect()->route('maruti-service-history.index')->with('error', 'Payment order does not match this search');
        }

        if (! $this->razorpayService->verifySignature($razorpayOrderId, $razorpayPaymentId, $razorpaySignature)) {
            return redirect()->route('maruti-service-history.index')->with('error', 'Payment verification failed');
        }

        $result = $this->serviceHistoryService->search(
            $pending['vehicle_number'],
            $pending['customer_info']
        );

        if (isset($result['data']) && $result['data']) {
            $result['data']->update([
                'razorpay_order_id' => $razorpayOrderId,
                'razorpay_payment_id' => $razorpayPaymentId,
            ]);

            if ($result['success']) {
                $result['data']->load('records');
            }
        }

        session()->forget('maruti_service_history_pending');

        return $this->renderResult(
            $result['data'] ?? null,
            $result['cached'] ?? false,
            $result['success'],
            $result['message']
        );
    }

    public function show(CustomerMarutiServiceHistory $marutiServiceHistory)
    {
        $marutiServiceHistory->load('records');

        return $this->renderResult(
            $marutiServiceHistory,
            false,
            $marutiServiceHistory->is_success,
            $marutiServiceHistory->error_message
        );
    }

    public function downloadPdf(CustomerMarutiServiceHistory $marutiServiceHistory)
    {
        $marutiServiceHistory->load('records');

        $pdf = Pdf::loadView('frontend.maruti-service-history.pdf', compact('marutiServiceHistory'));
        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('maruti-service-history-'.$marutiServiceHistory->vehicle_number.'.pdf');
    }

    private function renderResult(
        ?CustomerMarutiServiceHistory $serviceHistory,
        bool $cached,
        ?bool $success = null,
        ?string $message = null
    ) {
        return Inertia::render('Public/Services/ServiceResult', [
            'report' => $serviceHistory,
            'cached' => $cached,
            'success' => $success ?? (bool) $serviceHistory?->is_success,
            'message' => $message,
            'variant' => 'maruti',
            'serviceTitle' => 'Maruti Service History',
            'indexUrl' => route('maruti-service-history.index'),
            'pdfUrl' => $serviceHistory?->is_success
                ? route('maruti-service-history.pdf', $serviceHistory)
                : null,
            'freshSearchUrl' => route('maruti-service-history.search'),
        ]);
    }
}
