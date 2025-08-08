<?php

namespace App\Listeners;

use App\Events\OrderCompleted;
use App\Services\TransactionService;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreateCreditTransactionForCompletedOrder implements ShouldQueue
{
    public $afterCommit = true;

    /** @var TransactionService */
    public $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function handle(OrderCompleted $event): void
    {
        $order = $event->order;

        $this->transactionService->createManualCredit(
            $order->user,
            $order->amount,
            "Order completed #{$order->id}"
        );
    }
}
