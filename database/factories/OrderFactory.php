<?php

namespace Database\Factories;

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
        return [
            'user_id'     => User::factory(),
            'title'       => 'Top-up ' . $this->faker->numberBetween(10, 100) . ' BGN',
            'amount'      => $this->faker->numberBetween(1000, 10000),
            'status'      => $this->faker->randomElement(['pending_payment', 'completed', 'cancelled', 'refunded']),
            'description' => $this->faker->sentence,
        ];
    }
}
