<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseSeedTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

    public function test_roles_are_seeded()
    {
        $this->assertDatabaseHas('roles', ['slug' => 'admin']);
        $this->assertDatabaseHas('roles', ['slug' => 'merchant']);
    }

    public function test_users_have_roles_and_amount()
    {
        $admin = User::where('email', 'admin@example.com')->first();
        $this->assertNotNull($admin);
        $this->assertEquals('admin', $admin->role->slug);
        $this->assertGreaterThanOrEqual(0, $admin->amount);

        $merchant = User::where('email', 'merchant@example.com')->first();
        $this->assertNotNull($merchant);
        $this->assertEquals('merchant', $merchant->role->slug);
        $this->assertGreaterThan(0, $merchant->amount);
    }

    public function test_orders_are_seeded_for_merchant()
    {
        $merchant = User::where('email', 'merchant@example.com')->first();
        $this->assertTrue($merchant->orders()->exists());

        $this->assertDatabaseHas('orders', [
            'user_id' => $merchant->id,
            'status'  => 'completed',
        ]);
    }

    public function test_transactions_have_descriptions_and_types()
    {
        $merchant = User::where('email', 'merchant@example.com')->first();
        $this->assertTrue($merchant->transactions()->exists());

        $this->assertDatabaseHas('transactions', [
            'user_id' => $merchant->id,
            'type'    => 'credit',
        ]);

        $this->assertDatabaseMissing('transactions', [
            'description' => null,
        ]);
    }
}
