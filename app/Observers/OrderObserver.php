<?php

namespace App\Observers;

use App\Events\OrderCompleted;
use App\Events\OrderRefunded;
use App\Models\Order;

class OrderObserver
{
    public function updated(Order $order): void
    {
        if (! $order->wasChanged('status')) {
            return;
        }

        switch ($order->status) {
            case Order::STATUS_COMPLETED:
                event(new OrderCompleted($order));
                break;

            case Order::STATUS_REFUNDED:
                event(new OrderRefunded($order));
                break;

            default:
                // pending payment/cancelled -> no side effects
                break;
        }
    }
}
