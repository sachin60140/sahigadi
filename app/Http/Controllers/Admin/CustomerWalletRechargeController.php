<?php

namespace App\Http\Controllers\Admin;

use App\Exports\CustomerWalletRechargesExport;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\CustomerWalletTransaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Inertia\Inertia;

class CustomerWalletRechargeController extends Controller
{
    private function buildQuery(Request $request)
    {
        $query = CustomerWalletTransaction::with(['wallet.customer'])
            ->where(function($q) {
                $q->where(function($q1) {
                    $q1->where('type', 'credit')
                       ->where(function($q2) {
                           $q2->where('remark', 'LIKE', '%recharge%')
                             ->orWhere('reference_type', 'payment')
                             ->orWhere('reference_type', 'admin_credit');
                       });
                })->orWhere(function($q3) {
                    $q3->where('type', 'debit')
                       ->where('reference_type', 'admin_debit');
                });
            });

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        if ($request->filled('payment_gateway')) {
            $gateway = $request->payment_gateway;
            if ($gateway === 'direct_deposit') {
                $query->where('reference_type', 'admin_credit');
            } else {
                $query->whereExists(function($q) use ($gateway) {
                    $q->select(\Illuminate\Support\Facades\DB::raw(1))
                      ->from('payments')
                      ->where('payment_gateway', $gateway)
                      ->where(function($q2) {
                          $q2->whereColumn('payments.razorpay_payment_id', 'customer_wallet_transactions.reference_id')
                             ->orWhereColumn('payments.phonepe_transaction_id', 'customer_wallet_transactions.reference_id');
                      });
                });
            }
        }

        if ($request->filled('search')) {
            $query->whereHas('wallet.customer', function($q) use ($request) {
                $q->where('customer_unique_id', 'like', '%'.$request->search.'%')
                  ->orWhere('name', 'like', '%'.$request->search.'%')
                  ->orWhere('email', 'like', '%'.$request->search.'%')
                  ->orWhere('phone', 'like', '%'.$request->search.'%');
            });
        }

        return $query;
    }

    public function index(Request $request)
    {
        $transactions = $this->buildQuery($request)->orderBy('created_at', 'desc')->paginate(20)->withQueryString();
        $paymentsByReference = $this->paymentsByReference(collect($transactions->items())->pluck('reference_id')->filter()->values());
        $customers = Customer::with('wallet')
            ->orderBy('name')
            ->get()
            ->map(fn (Customer $customer) => [
                'id' => $customer->id,
                'label' => $customer->name.' ('.$customer->phone.') - Bal: Rs '.number_format((float) ($customer->wallet?->balance ?? 0), 2),
                'balance' => (float) ($customer->wallet?->balance ?? 0),
            ]);

        return Inertia::render('Admin/Finance/CustomerWalletRecharges', [
            'transactions' => $transactions->through(fn (CustomerWalletTransaction $transaction) => $this->mapTransaction($transaction, $paymentsByReference)),
            'customers' => $customers,
            'filters' => $request->only(['from_date', 'to_date', 'payment_gateway', 'search']),
            'exportUrls' => [
                'excel' => route('admin.customer-wallet-recharges.exportExcel', $request->query()),
                'pdf' => route('admin.customer-wallet-recharges.exportPdf', $request->query()),
            ],
        ]);
    }

    public function deductMoney(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'amount' => 'required|numeric|min:1',
            'remark' => 'required|string|max:255',
        ]);

        $customer = Customer::findOrFail($request->customer_id);
        $wallet = $customer->wallet()->firstOrCreate([]);

        if ($wallet->balance < $request->amount) {
            return back()->with('error', 'Insufficient balance in customer wallet. Current balance: ₹' . number_format($wallet->balance, 2));
        }

        $wallet->deductFunds(
            $request->amount,
            $request->remark,
            'admin_debit_' . time(),
            'admin_debit'
        );

        return back()->with('success', 'Amount deducted from customer wallet successfully');
    }

    public function exportExcel(Request $request)
    {
        // For now, don't implement Excel export as it requires creating CustomerWalletRechargesExport
        return redirect()->back()->with('error', 'Excel export not implemented yet.');
    }

    public function exportPdf(Request $request)
    {
        $transactions = $this->buildQuery($request)->orderBy('created_at', 'desc')->get();
        $pdf = Pdf::loadView('admin.customer-wallet-recharges.pdf', compact('transactions'));
        $pdf->setPaper('A4', 'landscape');
        return $pdf->download('customer-wallet-recharges-'.date('Y-m-d').'.pdf');
    }

    public function downloadReceipt($id)
    {
        $transaction = CustomerWalletTransaction::with('wallet.customer')->findOrFail($id);

        abort_if($transaction->type !== 'credit', 403, 'Only credit transactions have a receipt.');

        $data = [
            'transaction' => $transaction,
            'customer' => $transaction->wallet->customer,
            'baseAmount' => $transaction->amount,
            'gstAmount' => $transaction->amount * 0.18,
            'totalAmount' => $transaction->amount * 1.18,
            'date' => $transaction->created_at->format('d M Y')
        ];

        $pdf = Pdf::loadView('frontend.customer.wallet.receipt-pdf', $data);
        return $pdf->download('Admin-Customer-Wallet-Recharge-Receipt-'.$transaction->id.'.pdf');
    }

    private function paymentsByReference($references): \Illuminate\Support\Collection
    {
        $references = collect($references)->filter()->unique()->values();

        if ($references->isEmpty()) {
            return collect();
        }

        return Payment::whereIn('razorpay_payment_id', $references)
            ->orWhereIn('phonepe_transaction_id', $references)
            ->get()
            ->flatMap(fn (Payment $payment) => collect([
                $payment->razorpay_payment_id => $payment,
                $payment->phonepe_transaction_id => $payment,
            ])->filter())
            ->filter();
    }

    private function mapTransaction(CustomerWalletTransaction $transaction, \Illuminate\Support\Collection $paymentsByReference): array
    {
        $payment = $paymentsByReference->get($transaction->reference_id);
        $isDebit = $transaction->type === 'debit';
        $gateway = match (true) {
            $isDebit => 'Admin Deduction',
            $transaction->reference_type === 'admin_credit' => 'Direct Deposit',
            $payment?->payment_gateway !== null => ucfirst($payment->payment_gateway),
            str_starts_with((string) $transaction->reference_id, 'PP') => 'PhonePe',
            default => 'Razorpay',
        };
        $referenceLabel = str_starts_with((string) $transaction->reference_id, 'PP') ? 'UTR/Bank' : 'Order';

        return [
            'id' => $transaction->id,
            'date' => optional($transaction->created_at)->format('d M Y'),
            'time' => optional($transaction->created_at)->format('h:i A'),
            'receipt' => 'RCPT-'.optional($transaction->created_at)->format('Y').'-'.str_pad((string) $transaction->id, 5, '0', STR_PAD_LEFT),
            'type' => $transaction->type,
            'amount' => (float) $transaction->amount,
            'gst' => (float) $transaction->amount * 0.18,
            'total' => (float) $transaction->amount * 1.18,
            'gateway' => $gateway,
            'reference_id' => $transaction->reference_id,
            'secondary_reference' => $payment?->razorpay_order_id ?: $payment?->reference_id,
            'secondary_reference_label' => $referenceLabel,
            'reference_type' => $transaction->reference_type,
            'remark' => $transaction->remark,
            'customer' => [
                'name' => $transaction->wallet?->customer?->name ?: 'Unknown Customer',
                'company_name' => $transaction->wallet?->customer?->company_name ?: 'N/A',
                'phone' => $transaction->wallet?->customer?->phone,
                'unique_id' => $transaction->wallet?->customer?->customer_unique_id ?: 'N/A',
                'gst_number' => $transaction->wallet?->customer?->gst_number,
            ],
            'receipt_url' => $transaction->type === 'credit' ? route('admin.customer-wallet-recharges.receipt', $transaction->id) : null,
        ];
    }
}
