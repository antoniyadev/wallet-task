<?php

namespace Tests\Feature\Admin;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserCrudTest extends TestCase
{
    use RefreshDatabase;

    protected int $merchantRoleId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        $this->merchantRoleId = (int) Role::where('slug', 'merchant')->value('id');
    }

    /** @test */
    public function test_admin_can_create_user_with_description()
    {
        $admin = User::factory()->admin()->create();

        $payload = [
            'name'        => 'Jane Doe',
            'email'       => 'jane@example.com',
            'description' => 'VIP merchant',
            'password'    => 'secretpassword',
            'role_id'     => $this->merchantRoleId,
        ];

        $this->actingAs($admin)
            ->postJson('/api/admin/users', $payload)
            ->assertCreated();

        $this->assertDatabaseHas('users', [
            'email'       => 'jane@example.com',
            'description' => 'VIP merchant',
        ]);
    }

    /** @test */
    public function test_admin_can_create_user_without_description_when_nullable()
    {
        $admin = User::factory()->admin()->create();

        $payload = [
            'name'     => 'John NoDesc',
            'email'    => 'john.nod@example.com',
            'password' => 'secretpassword',
            'role_id'  => $this->merchantRoleId,
        ];

        $this->actingAs($admin)
            ->postJson('/api/admin/users', $payload)
            ->assertCreated();

        $this->assertDatabaseHas('users', [
            'email'       => 'john.nod@example.com',
            'description' => null,
        ]);
    }

    /** @test */
    public function test_admin_can_update_user_description()
    {
        $admin = User::factory()->admin()->create();
        $user  = User::factory()->merchant()->create(['description' => null]);

        // Your Update request currently requires name (and maybe role_id) → include them
        $this->actingAs($admin)
            ->putJson("/api/admin/users/{$user->id}", [
                'name'        => $user->name,
                'role_id'     => $user->role_id,
                'description' => 'Updated note',
            ])
            ->assertOk();

        $this->assertSame('Updated note', $user->fresh()->description);
    }

    /** @test */
    public function test_email_must_be_unique_on_create_and_update()
    {
        $admin = User::factory()->admin()->create();
        $u1    = User::factory()->merchant()->create(['email' => 'taken@example.com']);
        $u2    = User::factory()->merchant()->create();

        // create (send required role_id)
        $this->actingAs($admin)
            ->postJson('/api/admin/users', [
                'name'     => 'Dup',
                'email'    => 'taken@example.com',
                'password' => 'secretpassword',
                'role_id'  => $this->merchantRoleId,
            ])->assertStatus(422);

        // update unique (also send required fields)
        $this->actingAs($admin)
            ->putJson("/api/admin/users/{$u2->id}", [
                'email'   => 'taken@example.com',
                'name'    => $u2->name,
                'role_id' => $u2->role_id,
            ])
            ->assertStatus(422);
    }

    /** @test */
    public function test_non_admin_cannot_manage_users()
    {
        $merchant = User::factory()->merchant()->create();

        $this->actingAs($merchant)
            ->postJson('/api/admin/users', [
                'name'     => 'Nope',
                'email'    => 'nope@example.com',
                'password' => 'secretpassword',
                'role_id'  => $this->merchantRoleId,
            ])->assertStatus(403);
    }

    /** @test */
    public function test_admin_can_delete_user()
    {
        $admin = User::factory()->admin()->create();
        $user  = User::factory()->merchant()->create();

        $this->actingAs($admin)
            ->deleteJson("/api/admin/users/{$user->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }
}
