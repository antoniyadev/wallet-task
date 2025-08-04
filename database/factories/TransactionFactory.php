<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
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
            'type'        => $this->faker->randomElement(['credit', 'debit']),
            'amount'      => $this->faker->numberBetween(100, 10000),
            'description' => $this->faker->sentence,
            'created_by'  => User::factory(),
            'order_id'    => null,
        ];
    }
}
