<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\CustomerChallanSearch;
use App\Services\CustomerChallanSearchService;
use App\Services\RazorpayService;
use Illuminate\Http\Request;

class ChallanSearchController extends Controller
{
    protected CustomerChallanSearchService $challanService;

    protected RazorpayService $razorpayService;

    public function __construct(
        CustomerChallanSearchService $challanService,
        RazorpayService $razorpayService
    ) {
        $this->challanService = $challanService;
        $this->razorpayService = $razorpayService;
    }

    public function index()
    {
        $charge = $this->challanService->getCharge();

        return view('frontend.challan-search.index', compact('charge'));
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

        $cached = CustomerChallanSearch::checkCache($vehicleNumber);
        if ($cached) {
            return view('frontend.challan-search.result', [
                'challanSearch' => $cached,
                'cached' => true,
            ]);
        }

        $charge = $this->challanService->getCharge();

        $order = $this->razorpayService->createOrder(
            $charge,
            'ch_'.time(),
            [
                'vehicle_number' => $vehicleNumber,
                'customer_name' => $customerInfo['name'],
                'customer_phone' => $customerInfo['phone'],
            ]
        );

        session([
            'challan_search_pending' => [
                'vehicle_number' => $vehicleNumber,
                'customer_info' => $customerInfo,
                'order_id' => $order['order_id'],
            ],
        ]);

        return view('frontend.challan-search.payment', [
            'orderId' => $order['order_id'],
            'amount' => $order['amount'],
            'keyId' => $this->razorpayService->getKeyId(),
            'vehicleNumber' => $vehicleNumber,
            'customerName' => $customerInfo['name'],
        ]);
    }

    public function paymentCallback(Request $request)
    {
        $pending = session('challan_search_pending');

        if (! $pending) {
            return redirect()->route('challan-search.index')->with('error', 'No pending payment found');
        }

        $razorpayOrderId = $request->razorpay_order_id;
        $razorpayPaymentId = $request->razorpay_payment_id;
        $razorpaySignature = $request->razorpay_signature;

        if (! $this->razorpayService->verifySignature($razorpayOrderId, $razorpayPaymentId, $razorpaySignature)) {
            return redirect()->route('challan-search.index')->with('error', 'Payment verification failed');
        }

        $result = $this->challanService->search(
            $pending['vehicle_number'],
            $pending['customer_info']
        );

        if (isset($result['data']) && $result['data']) {
            $result['data']->update([
                'razorpay_order_id' => $razorpayOrderId,
                'razorpay_payment_id' => $razorpayPaymentId,
            ]);
        }

        session()->forget('challan_search_pending');

        return view('frontend.challan-search.result', [
            'challanSearch' => $result['data'],
            'cached' => $result['cached'] ?? false,
            'success' => $result['success'],
            'message' => $result['message'],
        ]);
    }
}
