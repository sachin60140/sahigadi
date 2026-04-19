<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\CustomerVehicleSearch;
use App\Services\CustomerVehicleSearchService;
use App\Services\RazorpayService;
use Illuminate\Http\Request;

class VehicleSearchController extends Controller
{
    protected CustomerVehicleSearchService $vehicleSearchService;

    protected RazorpayService $razorpayService;

    public function __construct(
        CustomerVehicleSearchService $vehicleSearchService,
        RazorpayService $razorpayService
    ) {
        $this->vehicleSearchService = $vehicleSearchService;
        $this->razorpayService = $razorpayService;
    }

    public function index()
    {
        $charge = $this->vehicleSearchService->getCharge();

        return view('frontend.vehicle-search.index', compact('charge'));
    }

    public function search(Request $request)
    {
        $request->validate([
            'registration_number' => 'required|string|min:4|max:20',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'nullable|email|max:255',
        ]);

        $registrationNumber = $request->registration_number;
        $customerInfo = [
            'name' => $request->customer_name,
            'phone' => $request->customer_phone,
            'email' => $request->customer_email,
        ];

        $cached = CustomerVehicleSearch::checkCache($registrationNumber);
        if ($cached) {
            return view('frontend.vehicle-search.result', [
                'vehicleSearch' => $cached,
                'cached' => true,
            ]);
        }

        $charge = $this->vehicleSearchService->getCharge();

        $order = $this->razorpayService->createOrder(
            $charge,
            'rc_'.time(),
            [
                'registration_number' => $registrationNumber,
                'customer_name' => $customerInfo['name'],
                'customer_phone' => $customerInfo['phone'],
            ]
        );

        session([
            'vehicle_search_pending' => [
                'registration_number' => $registrationNumber,
                'customer_info' => $customerInfo,
                'order_id' => $order['order_id'],
            ],
        ]);

        return view('frontend.vehicle-search.payment', [
            'orderId' => $order['order_id'],
            'amount' => $order['amount'],
            'keyId' => $this->razorpayService->getKeyId(),
            'registrationNumber' => $registrationNumber,
            'customerName' => $customerInfo['name'],
        ]);
    }

    public function paymentCallback(Request $request)
    {
        $pending = session('vehicle_search_pending');

        if (! $pending) {
            return redirect()->route('vehicle-search.index')->with('error', 'No pending payment found');
        }

        $razorpayOrderId = $request->razorpay_order_id;
        $razorpayPaymentId = $request->razorpay_payment_id;
        $razorpaySignature = $request->razorpay_signature;

        if (! $this->razorpayService->verifySignature($razorpayOrderId, $razorpayPaymentId, $razorpaySignature)) {
            return redirect()->route('vehicle-search.index')->with('error', 'Payment verification failed');
        }

        $result = $this->vehicleSearchService->search(
            $pending['registration_number'],
            $pending['customer_info']
        );

        if (isset($result['data']) && $result['data']) {
            $result['data']->update([
                'razorpay_order_id' => $razorpayOrderId,
                'razorpay_payment_id' => $razorpayPaymentId,
            ]);
        }

        session()->forget('vehicle_search_pending');

        return view('frontend.vehicle-search.result', [
            'vehicleSearch' => $result['data'],
            'cached' => $result['cached'] ?? false,
            'success' => $result['success'],
            'message' => $result['message'],
        ]);
    }
}
