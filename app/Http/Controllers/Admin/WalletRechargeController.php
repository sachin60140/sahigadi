<?php

namespace App\Http\Controllers\Admin;

use App\Exports\WalletRechargesExport;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\WalletTransaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Inertia\Inertia;

class WalletRechargeController extends Controller
{
    private function buildQuery(Request $request)
    {
        $query = WalletTransaction::with(['wallet.dealer'])
            ->where('type', 'credit')
            ->where(function($q) {
                $q->where('remark', 'LIKE', '%recharge%')
                  ->orWhere('reference_type', 'payment')
                  ->orWhere('reference_type', 'admin_credit');
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
                          $q2->whereColumn('payments.razorpay_payment_id', 'wallet_transactions.reference_id')
                             ->orWhereColumn('payments.phonepe_transaction_id', 'wallet_transactions.reference_id');
                      });
                });
            }
        }

        if ($request->filled('search')) {
            $query->whereHas('wallet.dealer', function($q) use ($request) {
                $q->where('dealer_unique_id', 'like', '%'.$request->search.'%')
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

        return Inertia::render('Admin/Finance/WalletRecharges', [
            'transactions' => $transactions->through(fn (WalletTransaction $transaction) => $this->mapTransaction($transaction, $paymentsByReference)),
            'filters' => $request->only(['from_date', 'to_date', 'payment_gateway', 'search']),
            'exportUrls' => [
                'excel' => route('admin.wallet-recharges.exportExcel', $request->query()),
                'pdf' => route('admin.wallet-recharges.exportPdf', $request->query()),
            ],
        ]);
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

    private function mapTransaction(WalletTransaction $transaction, \Illuminate\Support\Collection $paymentsByReference): array
    {
        $payment = $paymentsByReference->get($transaction->reference_id);
        $gateway = $transaction->reference_type === 'admin_credit'
            ? 'Direct Deposit'
            : ($payment?->payment_gateway ? ucfirst($payment->payment_gateway) : (str_starts_with((string) $transaction->reference_id, 'PP_') ? 'PhonePe' : 'Razorpay'));
        $referenceLabel = str_starts_with((string) $transaction->reference_id, 'PP_') ? 'UTR/Bank' : 'Order';

        return [
            'id' => $transaction->id,
            'date' => optional($transaction->created_at)->format('d M Y'),
            'time' => optional($transaction->created_at)->format('h:i A'),
            'receipt' => 'RCPT-'.optional($transaction->created_at)->format('Y').'-'.str_pad((string) $transaction->id, 5, '0', STR_PAD_LEFT),
            'amount' => (float) $transaction->amount,
            'gst' => (float) $transaction->amount * 0.18,
            'total' => (float) $transaction->amount * 1.18,
            'gateway' => $gateway,
            'reference_id' => $transaction->reference_id,
            'secondary_reference' => $payment?->razorpay_order_id ?: $payment?->reference_id,
            'secondary_reference_label' => $referenceLabel,
            'reference_type' => $transaction->reference_type,
            'dealer' => [
                'name' => $transaction->wallet?->dealer?->name ?: 'Unknown Dealer',
                'company_name' => $transaction->wallet?->dealer?->company_name ?: 'N/A',
                'phone' => $transaction->wallet?->dealer?->phone,
                'unique_id' => $transaction->wallet?->dealer?->dealer_unique_id ?: 'N/A',
                'gst_number' => $transaction->wallet?->dealer?->gst_number,
            ],
            'receipt_url' => route('admin.wallet-recharges.receipt', $transaction->id),
        ];
    }

    public function exportExcel(Request $request)
    {
        $transactions = $this->buildQuery($request)->orderBy('created_at', 'desc')->get();
        return Excel::download(new WalletRechargesExport($transactions), 'wallet-recharges-'.date('Y-m-d').'.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $transactions = $this->buildQuery($request)->orderBy('created_at', 'desc')->get();
        $pdf = Pdf::loadView('admin.wallet-recharges.pdf', compact('transactions'));
        $pdf->setPaper('A4', 'landscape');
        return $pdf->download('wallet-recharges-'.date('Y-m-d').'.pdf');
    }

    public function downloadReceipt($id)
    {
        $transaction = WalletTransaction::with('wallet.dealer')->findOrFail($id);

        abort_if($transaction->type !== 'credit', 403, 'Only credit transactions have a receipt.');

        $data = [
            'transaction' => $transaction,
            'dealer' => $transaction->wallet->dealer,
            'baseAmount' => $transaction->amount,
            'gstAmount' => $transaction->amount * 0.18,
            'totalAmount' => $transaction->amount * 1.18,
            'date' => $transaction->created_at->format('d M Y')
        ];

        $pdf = Pdf::loadView('dealer.wallet.receipt-pdf', $data);
        return $pdf->download('Admin-Wallet-Recharge-Receipt-'.$transaction->id.'.pdf');
    }
}
