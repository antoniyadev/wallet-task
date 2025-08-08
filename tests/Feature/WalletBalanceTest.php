<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class WalletBalanceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    /** @test */
    public function test_user_can_see_wallet_balance_via_sanctum_api_user()
    {
        $merchantRoleId = Role::where('slug', 'merchant')->value('id');
        $this->assertNotNull($merchantRoleId, 'Missing role with slug: merchant');

        $user = User::factory()->create([
            'role_id' => $merchantRoleId,
            'amount'  => 12345, // cents
        ]);

        Sanctum::actingAs($user);

        $resp = $this->get('/api/user')->assertStatus(200);

        $resp->assertJsonFragment([
            'id'     => $user->id,
            'name'   => $user->name,
            'email'  => $user->email,
            'role'   => 'merchant',
            'amount' => 12345,
        ]);
    }
}
