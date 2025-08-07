<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransferController extends Controller
{
    public function store(Request $request)
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
                'description' => "Received fund from {$sender->email}",
                'created_by'  => $sender->id,
            ]);
            $recipient->increment('amount', $validated['amount']);
        });

        return response()->json(['message' => 'Transfer successful.']);
    }
}
