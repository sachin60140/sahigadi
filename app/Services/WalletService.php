<?php

namespace App\Services;

use App\Models\Wallet;
use App\Models\WalletTransaction;
use Exception;
use Illuminate\Support\Facades\DB;

class WalletService
{
    public function getOrCreateWallet(int $dealerId): Wallet
    {
        $wallet = Wallet::firstOrCreate(
            ['dealer_id' => $dealerId],
            ['balance' => 0]
        );

        return $wallet;
    }

    public function credit(
        int $dealerId,
        float $amount,
        string $remark = '',
        ?string $referenceId = null,
        ?string $referenceType = null
    ): WalletTransaction {
        if ($amount <= 0) {
            throw new Exception('Credit amount must be positive');
        }

        if ($referenceId && $this->isDuplicateTransaction($referenceId, $referenceType)) {
            throw new Exception('Duplicate transaction detected');
        }

        return DB::transaction(function () use ($dealerId, $amount, $remark, $referenceId, $referenceType) {
            $wallet = Wallet::where('dealer_id', $dealerId)->lockForUpdate()->first();

            if (! $wallet) {
                $wallet = $this->getOrCreateWallet($dealerId);
            }

            $wallet->balance = bcadd($wallet->balance, $amount, 2);
            $wallet->save();

            $transaction = WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'amount' => $amount,
                'type' => 'credit',
                'remark' => $remark,
                'reference_id' => $referenceId,
                'reference_type' => $referenceType,
                'is_duplicate_check' => (bool) $referenceId,
            ]);

            return $transaction;
        });
    }

    public function debit(
        int $dealerId,
        float $amount,
        string $remark = '',
        ?string $referenceId = null,
        ?string $referenceType = null
    ): WalletTransaction {
        if ($amount <= 0) {
            throw new Exception('Debit amount must be positive');
        }

        if ($referenceId && $this->isDuplicateTransaction($referenceId, $referenceType)) {
            throw new Exception('Duplicate transaction detected');
        }

        return DB::transaction(function () use ($dealerId, $amount, $remark, $referenceId, $referenceType) {
            $wallet = Wallet::where('dealer_id', $dealerId)->lockForUpdate()->first();

            if (! $wallet) {
                throw new Exception('Wallet not found');
            }

            if (bccomp($wallet->balance, $amount, 2) < 0) {
                throw new Exception('Insufficient balance');
            }

            $wallet->balance = bcsub($wallet->balance, $amount, 2);
            $wallet->save();

            $transaction = WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'amount' => $amount,
                'type' => 'debit',
                'remark' => $remark,
                'reference_id' => $referenceId,
                'reference_type' => $referenceType,
                'is_duplicate_check' => (bool) $referenceId,
            ]);

            return $transaction;
        });
    }

    public function isDuplicateTransaction(string $referenceId, ?string $referenceType = null): bool
    {
        $query = WalletTransaction::where('reference_id', $referenceId)
            ->where('is_duplicate_check', true);

        if ($referenceType) {
            $query->where('reference_type', $referenceType);
        }

        return $query->exists();
    }

    public function getBalance(int $dealerId): float
    {
        $wallet = Wallet::where('dealer_id', $dealerId)->first();

        return $wallet ? (float) $wallet->balance : 0;
    }

    public function getTransactions(int $dealerId, int $perPage = 20)
    {
        $wallet = Wallet::where('dealer_id', $dealerId)->first();

        if (! $wallet) {
            return collect();
        }

        return WalletTransaction::where('wallet_id', $wallet->id)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
}
