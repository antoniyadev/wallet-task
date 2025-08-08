<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTransactionRequest;
use App\Models\User;
use App\Services\TransactionService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;

/**
 * Class TransactionController
 *
 * Admin endpoint for creating internal wallet transactions (credit/debit)
 * directly against a user's balance (no orders involved).
 *
 * @package App\Http\Controllers\Api\Admin
 */
class TransactionController extends Controller
{
    /**
     * Domain service for transaction-related operations.
     *
     * @var TransactionService
     */
    protected TransactionService $transactionService;

    /**
     * @param TransactionService $transactionService Handles credit/debit operations.
     */
    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    /**
     * Create a credit or debit transaction for a user.
     *
     * Authorizes the action, validates input, locates the target user, and
     * delegates the operation to the TransactionService.
     *
     * Request payload (validated by StoreTransactionRequest):
     * - user_id: int
     * - type: 'credit'|'debit'
     * - amount: int (cents)
     * - description?: string|null
     *
     * @param  StoreTransactionRequest $request
     * @return JsonResponse JSON containing a success message and the created transaction.
     *
     * @throws AuthorizationException       If the current user is not allowed to create transactions.
     * @throws ModelNotFoundException       If the target user does not exist.
     */
    public function store(StoreTransactionRequest $request): JsonResponse
    {
        $this->authorize('create', User::class); // Optional policy gate

        $data = $request->validated();

        $user = User::findOrFail($data['user_id']);

        $transaction = $data['type'] === 'credit'
            ? $this->transactionService->createManualCredit($user, (int) $data['amount'], $data['description'] ?? null)
            : $this->transactionService->createManualDebit($user, (int) $data['amount'], $data['description'] ?? null);

        return response()->json([
            'message'     => 'Transaction completed successfully',
            'transaction' => $transaction,
        ], 201);
    }
}
