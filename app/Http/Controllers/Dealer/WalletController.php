<?php

namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use App\Services\WalletService;
use App\Models\WalletTransaction;
use Barryvdh\DomPDF\Facade\Pdf;

class WalletController extends Controller
{
    public function __construct(
        protected WalletService $walletService
    ) {}

    public function index()
    {
        $dealer = auth('dealer')->user();
        $balance = $this->walletService->getBalance($dealer->id);
        $transactions = $this->walletService->getTransactions($dealer->id);

        return view('dealer.wallet.index', compact('balance', 'transactions'));
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
