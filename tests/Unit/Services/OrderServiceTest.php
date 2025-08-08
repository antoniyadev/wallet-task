<?php

namespace Tests\Unit\Services;

use App\Models\Order;
use App\Models\Transaction;
use App\Models\User;
use App\Services\OrderService;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    /** @test */
    public function test_completed_order_creates_credit_transaction()
    {
        $user = User::factory()->merchant()->create(['amount' => 0]);

        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status'  => Order::STATUS_PENDING,
            'amount'  => 2000,
        ]);

        $service = new OrderService();
        $service->updateStatus($order, Order::STATUS_COMPLETED);

        $this->assertDatabaseHas('transactions', [
            'user_id'     => $user->id,
            'type'        => Transaction::TYPE_CREDIT,
            'amount'      => 2000,
            'description' => "Order Purchased funds #{$order->id}",
        ]);

        $this->assertEquals(2000, $user->fresh()->amount);
    }

    /** @test */
    public function test_refunded_order_creates_debit_transaction()
    {
        $user = User::factory()->merchant()->create(['amount' => 5000]);

        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status'  => Order::STATUS_PENDING,
            'amount'  => 3000,
        ]);

        $service = new OrderService();
        $service->updateStatus($order, Order::STATUS_REFUNDED);

        $this->assertDatabaseHas('transactions', [
            'user_id'     => $user->id,
            'type'        => Transaction::TYPE_DEBIT,
            'amount'      => 3000,
            'description' => "Order refunded #{$order->id}",
        ]);

        $this->assertEquals(2000, $user->fresh()->amount);
    }

    /** @test */
    public function test_cancelled_order_creates_no_transaction()
    {
        $user = User::factory()->merchant()->create(['amount' => 5000]);

        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status'  => Order::STATUS_PENDING,
            'amount'  => 3000,
        ]);

        $service = new OrderService();
        $service->updateStatus($order, Order::STATUS_CANCELLED);

        $this->assertDatabaseMissing('transactions', [
            'order_id' => $order->id,
        ]);

        $this->assertEquals(5000, $user->fresh()->amount);
    }
}
