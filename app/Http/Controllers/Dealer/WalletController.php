<?php

namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use App\Services\WalletService;

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
}
