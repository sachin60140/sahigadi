<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\CustomerServiceHistory;
use App\Services\CustomerServiceHistoryService;
use App\Services\RazorpayService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ServiceHistoryController extends Controller
{
    protected CustomerServiceHistoryService $serviceHistoryService;

    protected RazorpayService $razorpayService;

    public function __construct(
        CustomerServiceHistoryService $serviceHistoryService,
        RazorpayService $razorpayService
    ) {
        $this->serviceHistoryService = $serviceHistoryService;
        $this->razorpayService = $razorpayService;
    }

    public function index()
    {
        $charge = $this->serviceHistoryService->getCharge();

        return Inertia::render('Public/Services/Lookup', [
            'service' => [
                'key' => 'service-history',
                'eyebrow' => 'Maintenance intelligence',
                'title' => 'Vehicle service history report',
                'description' => 'Review authorized service visits, mileage checkpoints and billed work before making a decision.',
                'numberField' => 'vehicle_number',
                'numberLabel' => 'Vehicle registration number',
                'placeholder' => 'e.g. BR01AB1234',
                'submitLabel' => 'Search service history',
                'actionUrl' => route('service-history.search'),
                'charge' => (float) $charge,
                'requiresGuestDetails' => true,
                'customerAuthenticated' => false,
                'seoTitle' => 'Vehicle Service History Report Online - SAHI GADI',
                'seoDescription' => 'Search vehicle service history online with SAHI GADI. Review service records, mileage and report details before buying a used car.',
                'canonical' => route('service-history.index'),
                'features' => [
                    'Authorized service-center visits',
                    'Mileage and work-type checkpoints',
                    'Bill amounts and downloadable report',
                ],
            ],
            'history' => [],
        ]);
    }

    public function search(Request $request)
    {
        $request->validate([
            'vehicle_number' => 'required|string|min:4|max:20',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'nullable|email|max:255',
        ]);

        $vehicleNumber = $request->vehicle_number;
        $customerInfo = [
            'name' => $request->customer_name,
            'phone' => $request->customer_phone,
            'email' => $request->customer_email,
        ];

        $forceFresh = $request->has('force_fresh');
        $cached = $forceFresh ? null : CustomerServiceHistory::checkCache($vehicleNumber);
        if ($cached) {
            $cached->load('records');

            return $this->renderResult($cached, true);
        }

        $charge = $this->serviceHistoryService->getCharge();

        $order = $this->razorpayService->createOrder(
            $charge,
            'sh_'.time(),
            [
                'vehicle_number' => $vehicleNumber,
                'customer_name' => $customerInfo['name'],
                'customer_phone' => $customerInfo['phone'],
            ]
        );

        session([
            'service_history_pending' => [
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
            'reportLabel' => 'Service History Report',
            'description' => 'Service History Report - '.$vehicleNumber,
            'callbackUrl' => route('service-history.callback'),
            'cancelUrl' => route('service-history.index'),
        ]);
    }

    public function paymentCallback(Request $request)
    {
        $pending = session('service_history_pending');

        if (! $pending) {
            return redirect()->route('service-history.index')->with('error', 'No pending payment found');
        }

        $razorpayOrderId = $request->razorpay_order_id;
        $razorpayPaymentId = $request->razorpay_payment_id;
        $razorpaySignature = $request->razorpay_signature;

        if (empty($razorpayOrderId) || empty($razorpayPaymentId) || empty($razorpaySignature)) {
            return redirect()->route('service-history.index')->with('error', 'Payment was not completed');
        }

        if (! hash_equals((string) $pending['order_id'], (string) $razorpayOrderId)) {
            return redirect()->route('service-history.index')->with('error', 'Payment order does not match this search');
        }

        if (! $this->razorpayService->verifySignature($razorpayOrderId, $razorpayPaymentId, $razorpaySignature)) {
            return redirect()->route('service-history.index')->with('error', 'Payment verification failed');
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

        session()->forget('service_history_pending');

        return $this->renderResult(
            $result['data'] ?? null,
            $result['cached'] ?? false,
            $result['success'],
            $result['message']
        );
    }

    public function show(CustomerServiceHistory $serviceHistory)
    {
        $serviceHistory->load('records');

        return $this->renderResult(
            $serviceHistory,
            false,
            $serviceHistory->is_success,
            $serviceHistory->error_message
        );
    }

    public function downloadPdf(CustomerServiceHistory $serviceHistory)
    {
        $serviceHistory->load('records');

        $pdf = Pdf::loadView('frontend.service-history.pdf', compact('serviceHistory'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('service-history-'.$serviceHistory->vehicle_number.'.pdf');
    }

    private function renderResult(
        ?CustomerServiceHistory $serviceHistory,
        bool $cached,
        ?bool $success = null,
        ?string $message = null
    ) {
        return Inertia::render('Public/Services/ServiceResult', [
            'report' => $serviceHistory,
            'cached' => $cached,
            'success' => $success ?? (bool) $serviceHistory?->is_success,
            'message' => $message,
            'variant' => 'generic',
            'serviceTitle' => 'Vehicle Service History',
            'indexUrl' => route('service-history.index'),
            'pdfUrl' => $serviceHistory?->is_success
                ? route('service-history.download-pdf', $serviceHistory)
                : null,
            'freshSearchUrl' => route('service-history.search'),
        ]);
    }
}
