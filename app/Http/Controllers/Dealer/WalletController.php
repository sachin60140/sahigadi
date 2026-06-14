<?php

namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use App\Services\WalletService;
use App\Models\WalletTransaction;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Inertia\Inertia;

class WalletController extends Controller
{
    public function __construct(
        protected WalletService $walletService
    ) {}

    public function index()
    {
        $dealer = auth('dealer')->user();
        $this->walletService->getOrCreateWallet($dealer->id);
        $balance = $this->walletService->getBalance($dealer->id);
        $transactions = $this->walletService->getTransactions($dealer->id);
        $minRechargeAmount = Setting::getMinimumWalletRechargeAmount();

        return Inertia::render('Dealer/Wallet/Index', [
            'balance' => $balance,
            'minRechargeAmount' => (float) $minRechargeAmount,
            'openRecharge' => (bool) session('open_recharge_modal', false),
            'transactions' => $transactions->through(function (WalletTransaction $transaction) {
                $remark = (string) ($transaction->remark ?? '');
                $hasReceipt = $transaction->type === 'credit'
                    && str_contains(strtolower($remark), 'recharge via');

                return [
                    'id' => $transaction->id,
                    'type' => $transaction->type,
                    'amount' => (float) $transaction->amount,
                    'remark' => $transaction->remark,
                    'reference_id' => $transaction->reference_id,
                    'created_at' => optional($transaction->created_at)->format('d M Y, h:i A'),
                    'receipt_url' => $hasReceipt ? route('dealer.wallet.receipt', $transaction->id) : null,
                ];
            }),
            'actions' => [
                'checkout' => route('dealer.payments.checkout'),
            ],
        ]);
    }

    public function add()
    {
        return redirect()->route('dealer.wallet.index')->with('open_recharge_modal', true);
    }

    public function downloadReceipt($id)
    {
        $dealer = auth('dealer')->user();
        $transaction = WalletTransaction::where('id', $id)
            ->whereHas('wallet', function ($q) use ($dealer) {
                $q->where('dealer_id', $dealer->id);
            })
            ->firstOrFail();

        abort_if($transaction->type !== 'credit', 403, 'Only credit transactions have a receipt.');

        $data = [
            'transaction' => $transaction,
            'dealer' => $dealer,
            'baseAmount' => $transaction->amount,
            'gstAmount' => $transaction->amount * 0.18,
            'totalAmount' => $transaction->amount * 1.18,
            'date' => $transaction->created_at->format('d M Y')
        ];

        $pdf = Pdf::loadView('dealer.wallet.receipt-pdf', $data);

        return $pdf->download('Wallet-Recharge-Receipt-'.$transaction->id.'.pdf');
    }
}
