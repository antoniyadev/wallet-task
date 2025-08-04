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
        $admin    = Role::where('slug', 'admin')->first();
        $merchant = Role::where('slug', 'merchant')->first();

        User::insert([
            [
                'name'        => 'Admin',
                'description' => 'Platform administrator',
                'email'       => 'admin@example.com',
                'password'    => Hash::make('secretpassword'),
                'role_id'     => $admin->id,
                'amount'      => 100000,
            ],
            [
                'name'        => 'Merchant',
                'description' => 'Test merchant account',
                'email'       => 'merchant@example.com',
                'password'    => Hash::make('merchantpassword'),
                'role_id'     => $merchant->id,
                'amount'      => 50000,
            ],
        ]);
    }
}
