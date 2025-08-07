<?php

namespace Database\Seeders;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin     = User::where('email', 'admin@example.com')->first();
        $merchants = User::whereHas('role', fn ($q) => $q->where('slug', 'merchant'))->get();

        foreach ($merchants as $merchant) {
            // Internal credit by admin
            Transaction::factory()->create([
                'user_id'     => $merchant->id,
                'type'        => 'credit',
                'amount'      => 3000,
                'description' => "Received fund from admin ({$admin->email})",
                'created_by'  => $admin->id,
                'order_id'    => null,
            ]);

            // Linked to completed order
            $completedOrder = $merchant->orders()->where('status', 'completed')->first();
            if ($completedOrder) {
                Transaction::factory()->create([
                    'user_id'     => $merchant->id,
                    'type'        => 'credit',
                    'amount'      => $completedOrder->amount,
                    'description' => "Order Purchased funds #{$completedOrder->id}",
                    'created_by'  => $admin->id,
                    'order_id'    => $completedOrder->id,
                ]);
            }
        }
    }
}
