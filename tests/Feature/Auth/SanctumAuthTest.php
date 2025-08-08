<?php

namespace Tests\Feature\Auth;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SanctumAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    /** @test */
    public function test_guest_cannot_access_api_user()
    {
        $this->get('/api/user')->assertStatus(401);
    }

    /** @test */
    public function test_merchant_cannot_call_admin_add_money_endpoint()
    {
        $merchant = User::factory()->create([
            'role_id' => Role::where('slug', 'merchant')->value('id'),
        ]);

        Sanctum::actingAs($merchant);

        $this->postJson('/api/admin/transactions', [
            'user_id'     => $merchant->id,
            'type'        => 'credit',
            'amount'      => 1000,
            'description' => 'Nope',
        ])->assertStatus(403);
    }
}
