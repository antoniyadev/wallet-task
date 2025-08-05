<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $adminRole    = Role::where('slug', Role::ADMIN)->first();
        $merchantRole = Role::where('slug', Role::MERCHANT)->first();

        User::insert([
            [
                'name'        => 'Admin',
                'description' => 'Platform administrator',
                'email'       => 'admin@example.com',
                'password'    => Hash::make('secretpassword'),
                'role_id'     => $adminRole->id,
                'amount'      => 100000,
            ],
            [
                'name'        => 'Merchant',
                'description' => 'Test merchant account',
                'email'       => 'merchant@example.com',
                'password'    => Hash::make('merchantpassword'),
                'role_id'     => $merchantRole->id,
                'amount'      => 50000,
            ],
        ]);

        // Generate 10 more merchants
        foreach (range(1, 10) as $i) {
            User::create([
                'name'        => "Merchant $i",
                'description' => "Auto-generated merchant $i",
                'email'       => "merchant$i@example.com",
                'password'    => Hash::make('merchantpassword'),
                'role_id'     => $merchantRole->id,
                'amount'      => rand(10000, 50000),
            ]);
        }
    }
}
