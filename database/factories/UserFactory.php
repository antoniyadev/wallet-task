<?php

namespace Database\Factories;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $adminRole    = Role::where('slug', 'admin')->first();
        $merchantRole = Role::where('slug', 'merchant')->first();

        // One admin
        User::factory()->create([
            'name'              => 'Admin',
            'email'             => 'admin@example.com',
            'email_verified_at' => now(),
            'password'          => bcrypt('secretpassword'),
            'remember_token'    => Str::random(10),
            'role_id'           => $adminRole->id,
            'amount'            => 100000,
            'description'       => 'Main system administrator',
        ]);

        // One merchant (static)
        User::factory()->create([
            'name'              => 'Merchant',
            'email'             => 'merchant@example.com',
            'email_verified_at' => now(),
            'password'          => bcrypt('merchantpassword'),
            'remember_token'    => Str::random(10),
            'role_id'           => $merchantRole->id,
            'amount'            => 50000,
            'description'       => 'Test merchant user',
        ]);

        // 10 random merchants
        User::factory(10)->create(['role_id' => $merchantRole->id]);
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }
}
