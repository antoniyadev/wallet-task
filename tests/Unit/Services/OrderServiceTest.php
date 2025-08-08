<?php

namespace Tests\Unit\Services;

use App\Events\OrderCompleted;
use App\Events\OrderRefunded;
use App\Models\Order;
use App\Models\User;
use App\Services\OrderService;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
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
    public function test_dispatches_event_when_order_is_completed()
    {
        Event::fake([OrderCompleted::class, OrderRefunded::class]);

        $user  = User::factory()->merchant()->create(['amount' => 0]);
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status'  => Order::STATUS_PENDING,
            'amount'  => 2000,
        ]);

        (new OrderService())->updateStatus($order, Order::STATUS_COMPLETED);

        Event::assertDispatched(OrderCompleted::class, fn ($e) => $e->order->is($order));
        Event::assertNotDispatched(OrderRefunded::class);
    }

    /** @test */
    public function test_dispatches_event_when_order_is_refunded()
    {
        Event::fake([OrderCompleted::class, OrderRefunded::class]);

        $user  = User::factory()->merchant()->create(['amount' => 5000]);
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status'  => Order::STATUS_PENDING,
            'amount'  => 3000,
        ]);

        (new OrderService())->updateStatus($order, Order::STATUS_REFUNDED);

        Event::assertDispatched(OrderRefunded::class, fn ($e) => $e->order->is($order));
        Event::assertNotDispatched(OrderCompleted::class);
    }

    /** @test */
    public function test_does_nothing_for_cancelled()
    {
        Event::fake([OrderCompleted::class, OrderRefunded::class]);

        $user  = User::factory()->merchant()->create();
        $order = Order::factory()->create(['user_id' => $user->id, 'status' => Order::STATUS_PENDING]);

        (new OrderService())->updateStatus($order, Order::STATUS_CANCELLED);

        Event::assertNotDispatched(OrderCompleted::class);
        Event::assertNotDispatched(OrderRefunded::class);
    }
}
