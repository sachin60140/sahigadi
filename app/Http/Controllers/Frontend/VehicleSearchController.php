<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\CustomerVehicleSearch;
use App\Models\CustomerWallet;
use App\Services\CustomerVehicleSearchService;
use App\Services\RazorpayService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Inertia\Inertia;

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
        $history = collect();

        if (auth('customer')->check()) {
            $history = CustomerVehicleSearch::where('customer_phone', auth('customer')->user()->phone)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return Inertia::render('Public/Services/Lookup', [
            'service' => [
                'key' => 'vehicle-search',
                'eyebrow' => 'Vehicle verification',
                'title' => 'Vahan details and RC search',
                'description' => 'Check registration, ownership, insurance and vehicle details before you buy.',
                'numberField' => 'registration_number',
                'numberLabel' => 'Vehicle registration number',
                'placeholder' => 'e.g. BR01AB1234',
                'submitLabel' => 'Search vehicle details',
                'actionUrl' => route('vehicle-search.search'),
                'charge' => (float) $charge,
                'requiresGuestDetails' => ! auth('customer')->check(),
                'customerAuthenticated' => auth('customer')->check(),
                'seoTitle' => 'Vahan Details (RC Search) - SAHI GADI',
                'seoDescription' => 'Check Vahan RC details online with SAHI GADI. Search vehicle registration, owner, fitness and insurance information before buying a used car.',
                'canonical' => route('vehicle-search.index'),
                'features' => [
                    'Registration and ownership information',
                    'Insurance and fitness checkpoints',
                    'Downloadable report history for customers',
                ],
            ],
            'history' => $history->map(fn (CustomerVehicleSearch $record) => [
                'id' => $record->id,
                'number' => $record->registration_number,
                'is_success' => $record->is_success,
                'created_at' => $record->created_at?->toIso8601String(),
                'view_url' => $record->is_success ? route('vehicle-search.show', $record) : null,
                'pdf_url' => $record->is_success ? route('vehicle-search.pdf', $record) : null,
            ])->values(),
        ]);
    }

    public function search(Request $request)
    {
        $rules = [
            'registration_number' => 'required|string|min:4|max:20',
        ];

        if (!auth('customer')->check()) {
            $rules['customer_name'] = 'required|string|max:255';
            $rules['customer_phone'] = 'required|string|max:20';
            $rules['customer_email'] = 'nullable|email|max:255';
        }

        $request->validate($rules);

        $registrationNumber = $request->registration_number;
        
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

        $cached = CustomerVehicleSearch::checkCache($registrationNumber);
        if ($cached) {
            return Inertia::render('Public/Services/VehicleResult', [
                'vehicleSearch' => $cached,
                'cached' => true,
                'success' => true,
                'message' => 'Found in cache',
                'indexUrl' => route('vehicle-search.index'),
                'pdfUrl' => route('vehicle-search.pdf', $cached),
            ]);
        }

        $charge = $this->vehicleSearchService->getCharge();

        // If customer is logged in, try to deduct from wallet
        if (auth('customer')->check()) {
            $customer = auth('customer')->user();
            
            // Get or create wallet
            $wallet = CustomerWallet::firstOrCreate(
                ['customer_id' => $customer->id],
                ['balance' => 0]
            );

            if ($wallet->balance < $charge) {
                return redirect()->route('customer.wallet.add')
                    ->with('error', 'Low Balance! Please recharge your wallet to continue.');
            }

            // Deduct funds
            $transaction = $wallet->deductFunds($charge, "RC Search for {$registrationNumber}");

            // Perform API search directly
            $result = $this->vehicleSearchService->search($registrationNumber, $customerInfo);

            if (isset($result['data']) && $result['data']) {
                $result['data']->update([
                    'paid_amount' => $charge,
                ]);
            }

            // If API fails, refund the wallet
            if (!$result['success']) {
                $wallet->addFunds($charge, "Refund: Failed RC Search for {$registrationNumber}");
                $transaction->update(['remark' => $transaction->remark . ' (Refunded)']);
            }

            return Inertia::render('Public/Services/VehicleResult', [
                'vehicleSearch' => $result['data'] ?? null,
                'cached' => $result['cached'] ?? false,
                'success' => $result['success'],
                'message' => $result['message'],
                'indexUrl' => route('vehicle-search.index'),
                'pdfUrl' => isset($result['data']) && $result['data']
                    ? route('vehicle-search.pdf', $result['data'])
                    : null,
            ]);
        }

        // For non-logged-in users, continue with Razorpay
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

        return Inertia::render('Public/Services/Payment', [
            'orderId' => $order['order_id'],
            'amount' => (float) $order['amount'],
            'keyId' => $this->razorpayService->getKeyId(),
            'vehicleNumber' => $registrationNumber,
            'customerName' => $customerInfo['name'],
            'reportLabel' => 'RC Search Report',
            'description' => 'RC Search - '.$registrationNumber,
            'callbackUrl' => route('vehicle-search.callback'),
            'cancelUrl' => route('vehicle-search.index'),
        ]);
    }

    public function paymentCallback(Request $request)
    {
        $pending = session('vehicle_search_pending');

        if (! $pending) {
            return redirect()->route('vehicle-search.index')->with('error', 'No pending payment found');
        }

        $razorpayOrderId = $request->razorpay_order_id ?? $request->query('razorpay_order_id');
        $razorpayPaymentId = $request->razorpay_payment_id ?? $request->query('razorpay_payment_id');
        $razorpaySignature = $request->razorpay_signature ?? $request->query('razorpay_signature');

        if (empty($razorpayOrderId) || empty($razorpayPaymentId) || empty($razorpaySignature)) {
            return redirect()->route('vehicle-search.index')->with('error', 'Payment was not completed');
        }

        if (! hash_equals((string) $pending['order_id'], (string) $razorpayOrderId)) {
            return redirect()->route('vehicle-search.index')->with('error', 'Payment order does not match this search');
        }

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

        return Inertia::render('Public/Services/VehicleResult', [
            'vehicleSearch' => $result['data'] ?? null,
            'cached' => $result['cached'] ?? false,
            'success' => $result['success'],
            'message' => $result['message'],
            'indexUrl' => route('vehicle-search.index'),
            'pdfUrl' => isset($result['data']) && $result['data']
                ? route('vehicle-search.pdf', $result['data'])
                : null,
        ]);
    }

    public function show(CustomerVehicleSearch $vehicleSearch)
    {
        return Inertia::render('Public/Services/VehicleResult', [
            'vehicleSearch' => $vehicleSearch,
            'cached' => false,
            'success' => $vehicleSearch->is_success,
            'message' => $vehicleSearch->error_message,
            'indexUrl' => route('vehicle-search.index'),
            'pdfUrl' => $vehicleSearch->is_success ? route('vehicle-search.pdf', $vehicleSearch) : null,
        ]);
    }

    public function downloadPdf(CustomerVehicleSearch $vehicleSearch)
    {
        $pdf = Pdf::loadView('frontend.vehicle-search.pdf', compact('vehicleSearch'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('vehicle-search-'.$vehicleSearch->registration_number.'.pdf');
    }
}
