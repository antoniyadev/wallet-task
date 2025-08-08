<?php

namespace App\Listeners;

use App\Events\OrderRefunded;
use App\Services\TransactionService;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreateDebitTransactionForRefundedOrder implements ShouldQueue
{
    public $afterCommit = true;

    /** @var \App\Services\TransactionService */
    public $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function handle(OrderRefunded $event): void
    {
        $order = $event->order;

        $this->transactionService->createManualDebit(
            $order->user,
            $order->amount,
            "Order refund for #{$order->id}"
        );
    }
}
