<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomerChallanSearch;
use App\Models\CustomerMarutiServiceHistory;
use App\Models\CustomerServiceHistory;
use App\Models\CustomerVehicleSearch;
use App\Services\RazorpayService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CustomerTransactionController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->query('type', 'vahan');
        
        if ($type === 'challan') {
            $transactions = CustomerChallanSearch::orderBy('created_at', 'desc')->paginate(20);
        } elseif ($type === 'maruti') {
            $transactions = CustomerMarutiServiceHistory::orderBy('created_at', 'desc')->paginate(20);
        } elseif ($type === 'mahindra') {
            $transactions = CustomerServiceHistory::orderBy('created_at', 'desc')->paginate(20);
        } else {
            $type = 'vahan';
            $transactions = CustomerVehicleSearch::orderBy('created_at', 'desc')->paginate(20);
        }

        return Inertia::render('Admin/Finance/CustomerTransactions', [
            'transactions' => $transactions->through(fn ($transaction) => $this->mapTransactionRow($transaction, $type)),
            'type' => $type,
            'tabs' => $this->transactionTabs(),
        ]);
    }

    protected function getModelInstance(string $type, $id)
    {
        if ($type === 'challan') {
            return CustomerChallanSearch::findOrFail($id);
        } elseif ($type === 'maruti') {
            return CustomerMarutiServiceHistory::findOrFail($id);
        } elseif ($type === 'mahindra') {
            return CustomerServiceHistory::findOrFail($id);
        } else {
            return CustomerVehicleSearch::findOrFail($id);
        }
    }

    public function show(Request $request, $id)
    {
        $type = $request->query('type', 'vahan');
        $transaction = $this->getModelInstance($type, $id);

        if (method_exists($transaction, 'records')) {
            $transaction->load('records');
        }

        return Inertia::render('Admin/Finance/CustomerTransactionShow', [
            'transaction' => $this->mapTransactionDetail($transaction, $type),
            'type' => $type,
            'tabs' => $this->transactionTabs(),
        ]);
    }

    public function refund(Request $request, $id, RazorpayService $razorpayService)
    {
        $type = $request->query('type', 'vahan');
        $transaction = $this->getModelInstance($type, $id);

        if ($transaction->is_success) {
            // Uncomment next line if refunds should be strictly for failed only
            // return redirect()->back()->with('error', 'Cannot refund a successful API request.');
        }

        if ($transaction->is_refunded) {
            return redirect()->back()->with('error', 'Payment has already been refunded.');
        }

        if (!$transaction->razorpay_payment_id) {
            return redirect()->back()->with('error', 'No Razorpay payment ID found for this transaction.');
        }

        $result = $razorpayService->refundPayment($transaction->razorpay_payment_id);

        if ($result['success']) {
            $transaction->is_refunded = true;
            $transaction->razorpay_refund_id = $result['refund_id'];
            $transaction->save();

            return redirect()->back()->with('success', 'Refund initiated successfully (ID: ' . $result['refund_id'] . ').');
        } else {
            return redirect()->back()->with('error', 'Refund failed: ' . $result['message']);
        }
    }

    private function transactionTabs(): array
    {
        return [
            ['label' => 'Vahan Details', 'value' => 'vahan'],
            ['label' => 'E-Challan', 'value' => 'challan'],
            ['label' => 'Maruti SVC', 'value' => 'maruti'],
            ['label' => 'Mahindra SVC', 'value' => 'mahindra'],
        ];
    }

    private function mapTransactionRow($transaction, string $type): array
    {
        return [
            'id' => $transaction->id,
            'customer_name' => $transaction->customer_name,
            'customer_phone' => $transaction->customer_phone,
            'vehicle_number' => strtoupper($type === 'vahan' ? $transaction->registration_number : $transaction->vehicle_number),
            'paid_amount' => (float) ($transaction->paid_amount ?? 0),
            'is_success' => (bool) $transaction->is_success,
            'is_refunded' => (bool) $transaction->is_refunded,
            'razorpay_payment_id' => $transaction->razorpay_payment_id,
            'razorpay_refund_id' => $transaction->razorpay_refund_id,
            'created_at' => optional($transaction->created_at)->format('d M Y, h:i A'),
            'show_url' => route('admin.customer-transactions.show', ['id' => $transaction->id, 'type' => $type]),
            'refund_url' => route('admin.customer-transactions.refund', ['id' => $transaction->id, 'type' => $type]),
            'can_refund' => ! $transaction->is_success && ! $transaction->is_refunded && (bool) $transaction->razorpay_payment_id,
        ];
    }

    private function mapTransactionDetail($transaction, string $type): array
    {
        return [
            ...$this->mapTransactionRow($transaction, $type),
            'customer_email' => $transaction->customer_email,
            'date' => optional($transaction->created_at)->format('d M Y H:i'),
            'razorpay_order_id' => $transaction->razorpay_order_id,
            'error_message' => $transaction->error_message,
            'challan_count' => $transaction->challan_count ?? 0,
            'total_amount' => (float) ($transaction->total_amount ?? 0),
            'vehicle_data' => $transaction->vehicle_data ?? [],
            'challan_data' => $transaction->challan_data ?? [],
            'records' => method_exists($transaction, 'records')
                ? $transaction->records->map(fn ($record) => [
                    'svc_date' => $this->formatDateValue($record->svc_date),
                    'service_cate' => $record->service_cate ?? null,
                    'dealer_name' => $record->dealer_name ?? null,
                    'register_no' => $record->register_no ?? null,
                    'repair_order_no' => $record->repair_order_no ?? null,
                    'mileage' => $record->mileage ?? null,
                    'total_amount' => (float) ($record->total_amount ?? 0),
                    'work_type' => $record->work_type ?? null,
                    'net_bill_amt' => (float) ($record->net_bill_amt ?? 0),
                ])->values()
                : [],
        ];
    }

    private function formatDateValue($value): ?string
    {
        if (! $value) {
            return null;
        }

        if ($value instanceof \Carbon\CarbonInterface) {
            return $value->format('d M Y');
        }

        try {
            return \Carbon\Carbon::parse($value)->format('d M Y');
        } catch (\Throwable) {
            return (string) $value;
        }
    }
}
