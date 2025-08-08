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
    public function run(): void
    {
        $merchant = User::where('email', 'merchant@example.com')->first();

        // Add at least one completed order
        Order::create([
            'user_id'     => $merchant->id,
            'title'       => 'Top-up',
            'amount'      => 5693,
            'status'      => Order::STATUS_COMPLETED,
            'description' => 'Top-up $56.93',
        ]);

        // Add additional optional orders
        Order::factory()->count(2)->create([
            'user_id' => $merchant->id,
            'status'  => Order::STATUS_REFUNDED,
        ]);
    }
}
