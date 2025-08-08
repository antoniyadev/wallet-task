<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Transaction;
use App\Models\User;

/**
 * Service class responsible for handling Order-related business logic.
 */
class OrderService
{
    /**
     * Create a new order for a given user.
     *
     * @param  User  $user  The user creating the order.
     * @param  array $data  Order data: [
     *                      'title' => string,
     *                      'description' => string|null,
     *                      'amount' => int (stored in cents)
     *                      ]
     * @return Order        The newly created Order model instance.
     */
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

    /**
     * Update the status of an order and trigger corresponding wallet transactions.
     *
     * @param  Order  $order   The order to update.
     * @param  string $status  The new status (completed, refunded, etc.).
     * @return Order           The updated Order instance.
     */
    public function updateStatus(Order $order, string $status): Order
    {
        $order->update(['status' => $status]);
        $order->refresh();

        if ($status === Order::STATUS_COMPLETED) {
            $this->createTransaction($order, Transaction::TYPE_CREDIT, auth()->id());
        }

        if ($status === Order::STATUS_REFUNDED) {
            $this->createTransaction($order, Transaction::TYPE_DEBIT, auth()->id());
        }

        return $order;
    }

    /**
     * Create a wallet transaction for an order and update the user's balance.
     *
     * @param  Order    $order         The related order.
     * @param  string   $type          Transaction type: credit or debit.
     * @param  int|null $createdById   ID of the user who created the transaction (admin/merchant).
     * @return void
     */
    protected function createTransaction(Order $order, string $type, ?int $createdById = null): void
    {
        $description = $type === Transaction::TYPE_CREDIT
            ? "Order Purchased funds #{$order->id}"
            : "Order refunded #{$order->id}";

        Transaction::create([
            'user_id'     => $order->user_id,
            'type'        => $type,
            'amount'      => $order->amount,
            'order_id'    => $order->id,
            'description' => $description,
            'created_by'  => $createdById ?? null,
        ]);

        // Update user's wallet balance
        $order->user->increment('amount', $type === Transaction::TYPE_CREDIT ? $order->amount : -$order->amount);
    }

    /**
     * Get available order statuses with display labels and colors.
     *
     * @return array[] Each element contains:
     *                 [
     *                   'value' => string (status key),
     *                   'label' => string (human-readable name),
     *                   'color' => string (Bootstrap color class)
     *                 ]
     */
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
