<?php

namespace Tests\Feature\Merchant;

use App\Models\Role;
use App\Models\Transaction;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionListTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    /** @test */
    public function test_user_can_see_transaction_list()
    {
        $merchantRole = Role::where('slug', 'merchant')->first();
        $this->assertNotNull($merchantRole, 'Missing role with slug: merchant');

        $user = User::factory()->create([
            'role_id' => $merchantRole->id,
        ]);

        Transaction::factory()->count(3)->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->get('/api/transactions');
        $response->assertStatus(200);

        // Assert JSON structure instead of HTML content
        $response->assertJsonStructure([
            'data' => [
                '*' => ['id', 'type', 'amount', 'amount_formatted', 'description', 'created_by', 'created_at']
            ]
        ]);

        // Optionally assert count = 3
        $json = $response->json();
        $this->assertArrayHasKey('data', $json);
        $this->assertCount(3, $json['data']);
    }
}
