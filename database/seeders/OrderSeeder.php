<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $merchants = User::whereHas('role', fn ($q) => $q->where('slug', 'merchant'))->get();

        $merchants->each(function ($merchant) {
            Order::factory()->count(2)->create(['user_id' => $merchant->id]);
        });
    }
}
