<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\User;

/**
 * Service responsible for creating and managing wallet transactions.
 *
 * Handles both manual (admin-initiated) and order-related credits/debits,
 * and ensures that user wallet balances are updated accordingly.
 */
class TransactionService
{
    /**
     * Create a transaction for a given user and update their wallet balance.
     *
     * @param  User         $user         The user whose wallet is affected.
     * @param  string       $type         Transaction type: Transaction::TYPE_CREDIT or Transaction::TYPE_DEBIT.
     * @param  int          $amount       Transaction amount in cents (e.g., 1000 = $10.00).
     * @param  string|null  $description  Optional transaction description.
     *                                    Defaults to "Manual credit/debit by admin".
     * @param  int|null     $orderId      Related order ID if applicable.
     * @param  User|null    $creator      The user (admin/merchant) who created this transaction, if any.
     *
     * @return Transaction                The newly created Transaction model.
     */
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

        // Update wallet balance
        $user->increment('amount', $isCredit ? $amount : -$amount);

        return $transaction;
    }

    /**
     * Create a manual credit transaction (adds funds to the user's wallet).
     *
     * @param  User         $user         The user to credit.
     * @param  int          $amount       Amount in cents.
     * @param  string|null  $description  Optional custom description.
     *
     * @return Transaction                The created credit transaction.
     */
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

    /**
     * Create a manual debit transaction (deducts funds from the user's wallet).
     *
     * @param  User         $user         The user to debit.
     * @param  int          $amount       Amount in cents.
     * @param  string|null  $description  Optional custom description.
     *
     * @return Transaction                The created debit transaction.
     */
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
