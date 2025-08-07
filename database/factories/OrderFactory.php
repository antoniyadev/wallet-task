<?php

namespace Database\Factories;

use App\Helpers\CurrencyHelper;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $amount = $this->faker->numberBetween(1000, 10000); // cents

        return [
            'user_id'     => User::factory(),
            'title'       => 'Top-up',
            'amount'      => $amount,
            'status'      => $this->faker->randomElement(['pending_payment', 'completed', 'cancelled', 'refunded']),
            'description' => 'Top-up ' . CurrencyHelper::format($amount),
        ];
    }
}
