<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole    = Role::where('slug', 'admin')->firstOrFail();
        $merchantRole = Role::where('slug', 'merchant')->firstOrFail();

        User::factory()->create([
            'name'        => 'Admin',
            'email'       => 'admin@example.com',
            'password'    => Hash::make('secretpassword'),
            'role_id'     => $adminRole->id,
            'amount'      => 100_000,
            'description' => 'Main system administrator',
        ]);

        User::factory()->create([
            'name'        => 'Merchant',
            'email'       => 'merchant@example.com',
            'password'    => Hash::make('merchantpassword'),
            'role_id'     => $merchantRole->id,
            'amount'      => 50_000,
            'description' => 'Test merchant user',
        ]);

        // Generate 10 more merchants
        User::factory(10)->create([
            'role_id' => $merchantRole->id,
        ]);
    }
}
