<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\User;

class TransactionService
{
    public function createTransaction(
        User $user,
        string $type,
        int $amount,
        ?string $description = null,
        ?int $orderId = null,
        ?User $creator = null
    ): Transaction {
        $isCredit = $type === Transaction::TYPE_CREDIT;

        $transaction = Transaction::create([
            'user_id'     => $user->id,
            'type'        => $type,
            'amount'      => $amount,
            'description' => $description ?? sprintf(
                'Manual %s by admin (%s)',
                $type,
                $creator ? $creator->email : 'system'
            ),
            'created_by' => $creator ? $creator->id : null,
            'order_id'   => $orderId,
        ]);

        // Wallet balance update
        $user->increment('amount', $isCredit ? $amount : -$amount);

        return $transaction;
    }

    public function createManualCredit(User $user, int $amount, ?string $description = null): Transaction
    {
        return $this->createTransaction(
            $user,
            Transaction::TYPE_CREDIT,
            $amount,
            $description,
            null,
            auth()->user()
        );
    }

    public function createManualDebit(User $user, int $amount, ?string $description = null): Transaction
    {
        return $this->createTransaction(
            $user,
            Transaction::TYPE_DEBIT,
            $amount,
            $description,
            null,
            auth()->user()
        );
    }
}
