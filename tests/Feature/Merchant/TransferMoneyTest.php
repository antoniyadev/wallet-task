<?php

namespace Tests\Feature\Merchant;

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TransferMoneyTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    protected function merchantRoleId(): int
    {
        return \App\Models\Role::where('slug', 'merchant')->value('id');
    }

    /** @test */
    public function test_user_can_transfer_money_to_another_user()
    {
        $sender = User::factory()->create([
            'role_id' => $this->merchantRoleId(),
            'amount'  => 5000,
        ]);

        $receiver = User::factory()->create([
            'role_id' => $this->merchantRoleId(),
            'amount'  => 0,
        ]);

        $this->followingRedirects()
            ->actingAs($sender)
            ->post('/api/transfer', [
                'to_user_email' => $receiver->email,
                'amount'        => 2000,
            ])
            ->assertStatus(200);

        $this->assertEquals(3000, $sender->fresh()->amount);
        $this->assertEquals(2000, $receiver->fresh()->amount);
    }

    /** @test */
    public function test_cannot_transfer_with_invalid_receiver_email()
    {
        $sender = User::factory()->create([
            'role_id' => $this->merchantRoleId(),
            'amount'  => 5000,
        ]);

        Sanctum::actingAs($sender);

        $this->postJson('/api/transfer', [
            'to_user_email' => 'not-an-email',
            'amount'        => 1000,
        ])->assertStatus(422); // expects validator to fail
    }

    /** @test */
    public function test_cannot_transfer_to_self()
    {
        $sender = User::factory()->create([
            'role_id' => $this->merchantRoleId(),
            'amount'  => 5000,
            'email'   => 'sender@example.com',
        ]);

        Sanctum::actingAs($sender);

        $this->postJson('/api/transfer', [
            'to_user_email' => $sender->email,
            'amount'        => 1000,
        ])->assertStatus(422);
    }

    /** @test */
    public function test_cannot_transfer_more_than_balance()
    {
        $sender = User::factory()->create([
            'role_id' => $this->merchantRoleId(),
            'amount'  => 500, // $5.00
        ]);

        $receiver = User::factory()->create([
            'role_id' => $this->merchantRoleId(),
            'amount'  => 0,
            'email'   => 'receiver@example.com',
        ]);

        Sanctum::actingAs($sender);

        $this->postJson('/api/transfer', [
            'to_user_email' => $receiver->email,
            'amount'        => 2000, // $20.00
        ])->assertStatus(422);
    }
}
