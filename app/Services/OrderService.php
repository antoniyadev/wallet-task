<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Transaction;
use App\Models\User;

class OrderService
{
    public function create(User $user, array $data): Order
    {
        return Order::create([
            'title'       => $data['title'],
            'description' => $data['description'] ?? null,
            'amount'      => $data['amount'],
            'status'      => Order::STATUS_PENDING,
            'user_id'     => $user->id,
        ]);
    }

    public function updateStatus(Order $order, string $status): Order
    {
        $order->status = $status;
        $order->save();

        if ($status === Order::STATUS_COMPLETED) {
            $this->createTransaction($order, Transaction::TYPE_CREDIT);
        }

        if ($status === Order::STATUS_REFUNDED) {
            $this->createTransaction($order, Transaction::TYPE_DEBIT);
        }

        return $order;
    }

    protected function createTransaction(Order $order, string $type)
    {
        Transaction::create([
            'user_id'     => $order->user_id,
            'type'        => $type,
            'amount'      => $order->amount,
            'order_id'    => $order->id,
            'description' => "Order Purchased funds #{$order->id}",
            'created_by'  => auth()->id(), // Admin
        ]);

        // Update wallet
        $order->user->increment('amount', $type === 'credit' ? $order->amount : -$order->amount);
    }

    public function getStatusOptions(): array
    {
        $statuses = [];

        foreach (Order::STATUSES as $key => $label) {
            $statuses[] = [
                'value' => $key,
                'label' => $label,
                'color' => Order::STATUS_COLORS[$key] ?? 'danger',
            ];
        }

        return $statuses;
    }
}
