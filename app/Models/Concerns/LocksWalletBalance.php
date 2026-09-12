<?php

namespace App\Models\Concerns;

use App\Exceptions\InsufficientBalanceException;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use RuntimeException;

/**
 * Safe balance mutation for wallet models.
 *
 * The balance MUST be re-read under a row lock inside a transaction. Reading it
 * into memory, comparing, then writing lets two concurrent requests both pass
 * the affordability check and spend the same funds twice, which drives the
 * balance negative or silently loses one of the two updates.
 *
 * Amounts are handled with bcmath so repeated float arithmetic cannot drift.
 */
trait LocksWalletBalance
{
    public function addFunds($amount, $remark = null, $referenceId = null, $referenceType = null)
    {
        return $this->mutateBalance('credit', $amount, $remark, $referenceId, $referenceType);
    }

    public function deductFunds($amount, $remark = null, $referenceId = null, $referenceType = null)
    {
        return $this->mutateBalance('debit', $amount, $remark, $referenceId, $referenceType);
    }

    protected function mutateBalance(string $type, $amount, $remark, $referenceId, $referenceType)
    {
        $amount = number_format((float) $amount, 2, '.', '');

        if (bccomp($amount, '0', 2) <= 0) {
            throw new InvalidArgumentException('Wallet amount must be positive.');
        }

        return DB::transaction(function () use ($type, $amount, $remark, $referenceId, $referenceType) {
            // Re-read under a row lock: concurrent requests serialise here, so
            // the check below always sees the committed balance.
            $locked = static::query()->whereKey($this->getKey())->lockForUpdate()->first();

            if (! $locked) {
                throw new RuntimeException('Wallet not found.');
            }

            $balance = number_format((float) $locked->balance, 2, '.', '');

            if ($type === 'debit' && bccomp($balance, $amount, 2) < 0) {
                throw new InsufficientBalanceException('Insufficient wallet balance');
            }

            $locked->balance = $type === 'debit'
                ? bcsub($balance, $amount, 2)
                : bcadd($balance, $amount, 2);

            $locked->save();

            // Keep the instance the caller holds consistent with the database.
            $this->setAttribute('balance', $locked->balance);
            $this->syncOriginalAttribute('balance');

            return $this->transactions()->create([
                'amount' => $amount,
                'type' => $type,
                'remark' => $remark,
                'reference_id' => $referenceId,
                'reference_type' => $referenceType,
            ]);
        });
    }
}
