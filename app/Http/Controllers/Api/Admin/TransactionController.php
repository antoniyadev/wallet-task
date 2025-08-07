<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTransactionRequest;
use App\Models\User;
use App\Services\TransactionService;
use Illuminate\Http\JsonResponse;

class TransactionController extends Controller
{
    protected $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function store(StoreTransactionRequest $request): JsonResponse
    {
        $this->authorize('create', User::class); // Optional

        $data = $request->validated();
        $user = User::findOrFail($data['user_id']);

        $transaction = $data['type'] === 'credit'
            ? $this->transactionService->createManualCredit($user, $data['amount'], $data['description'])
            : $this->transactionService->createManualDebit($user, $data['amount'], $data['description']);

        return response()->json([
            'message'     => 'Transaction completed successfully',
            'transaction' => $transaction,
        ]);
    }
}
