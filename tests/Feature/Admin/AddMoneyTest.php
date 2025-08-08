<?php

namespace Tests\Feature\Admin;

use App\Models\Role;
use App\Models\Transaction;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AddMoneyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    protected function admin(): User
    {
        $adminRoleId = Role::where('slug', 'admin')->value('id');

        return User::factory()->create([
            'role_id' => $adminRoleId,
        ]);
    }

    /** @test */
    public function test_admin_can_add_money_to_user()
    {
        $admin = $this->admin();
        $user  = User::factory()->create(['amount' => 0]);

        $this->actingAs($admin)->post('/api/admin/transactions', [
            'user_id'     => $user->id,
            'type'        => Transaction::TYPE_CREDIT,
            'amount'      => 1000,
            'description' => 'Manual top-up',
        ])->assertStatus(201);

        $this->assertEquals(1000, $user->fresh()->amount);

        $this->assertDatabaseHas('transactions', [
            'user_id'     => $user->id,
            'type'        => Transaction::TYPE_CREDIT,
            'amount'      => 1000,
            'description' => 'Manual top-up',
        ]);
    }

    /** @test */
    public function test_admin_add_money_requires_valid_amount_and_description()
    {
        $admin        = $this->admin();
        $merchantRole = Role::where('slug', 'merchant')->first();

        $user = User::factory()->create([
            'role_id' => $merchantRole->id,
        ]);

        Sanctum::actingAs($admin);

        // Missing description
        $this->postJson('/api/admin/transactions', [
            'user_id' => $user->id,
            'type'    => 'credit',
            'amount'  => 1000,
        ])->assertStatus(422);

        // Negative amount
        $this->postJson('/api/admin/transactions', [
            'user_id'     => $user->id,
            'type'        => 'credit',
            'amount'      => -50,
            'description' => 'Bad',
        ])->assertStatus(422);

        // Invalid type
        $this->postJson('/api/admin/transactions', [
            'user_id'     => $user->id,
            'type'        => 'topup',
            'amount'      => 1000,
            'description' => 'Bad',
        ])->assertStatus(422);
    }
}
