<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Class TransferController
 *
 * Handles wallet-to-wallet transfers between users.
 * Allows an authenticated user to send funds to another user via email address.
 *
 * @package App\Http\Controllers\Api
 */
class TransferController extends Controller
{
    /**
     * Transfer funds from the authenticated user to another user.
     *
     * Validates:
     * - Recipient email must exist and not be the sender's own.
     * - Transfer amount must be a positive integer.
     * - Sender must have a sufficient balance.
     *
     * Performs the debit/credit operations for both accounts inside a DB transaction.
     *
     * @param  Request $request Incoming HTTP request containing transfer details.
     * @return JsonResponse JSON with a success or error message.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'to_user_email' => ['required', 'email', 'exists:users,email'],
            'amount'        => ['required', 'integer', 'min:1'],
            'description'   => ['nullable', 'string', 'max:255'],
        ]);

        $sender    = $request->user();
        $recipient = User::where('email', $validated['to_user_email'])->first();

        // Prevent sending to self
        if ($recipient->id === $sender->id) {
            return response()->json(['message' => 'Cannot transfer to yourself.'], 422);
        }

        // Check sender has enough balance
        if ($sender->amount < $validated['amount']) {
            return response()->json(['message' => 'Insufficient balance.'], 422);
        }

        DB::transaction(function () use ($sender, $recipient, $validated) {
            // Debit sender
            Transaction::create([
                'user_id'     => $sender->id,
                'type'        => Transaction::TYPE_DEBIT,
                'amount'      => $validated['amount'],
                'description' => "Sent funds to {$recipient->email}",
                'created_by'  => $sender->id,
            ]);
            $sender->decrement('amount', $validated['amount']);

            // Credit recipient
            Transaction::create([
                'user_id'     => $recipient->id,
                'type'        => Transaction::TYPE_CREDIT,
                'amount'      => $validated['amount'],
                'description' => "Received funds from {$sender->email}",
                'created_by'  => $sender->id,
            ]);
            $recipient->increment('amount', $validated['amount']);
        });

        return response()->json(['message' => 'Transfer successful.']);
    }
}
