<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\CustomerServiceHistory;
use App\Services\CustomerServiceHistoryService;
use App\Services\RazorpayService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

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

        return view('frontend.service-history.index', compact('charge'));
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

        $cached = CustomerServiceHistory::checkCache($vehicleNumber);
        if ($cached) {
            $cached->load('records');

            return view('frontend.service-history.result', [
                'serviceHistory' => $cached,
                'cached' => true,
            ]);
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

        return view('frontend.service-history.payment', [
            'orderId' => $order['order_id'],
            'amount' => $order['amount'],
            'keyId' => $this->razorpayService->getKeyId(),
            'vehicleNumber' => $vehicleNumber,
            'customerName' => $customerInfo['name'],
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

        return view('frontend.service-history.result', [
            'serviceHistory' => $result['data'],
            'cached' => $result['cached'] ?? false,
            'success' => $result['success'],
            'message' => $result['message'],
        ]);
    }

    public function show(CustomerServiceHistory $serviceHistory)
    {
        $serviceHistory->load('records');

        return view('frontend.service-history.show', compact('serviceHistory'));
    }

    public function downloadPdf(CustomerServiceHistory $serviceHistory)
    {
        $serviceHistory->load('records');

        $pdf = Pdf::loadView('frontend.service-history.pdf', compact('serviceHistory'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('service-history-'.$serviceHistory->vehicle_number.'.pdf');
    }
}
