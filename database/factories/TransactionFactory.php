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
        $type = $this->faker->randomElement(['credit', 'debit']);

        $description = $type === 'credit'
            ? 'Received funds from ' . $this->faker->email
            : 'Sent funds to ' . $this->faker->email;

        return [
            'user_id'     => User::factory(),
            'type'        => $type,
            'amount'      => $this->faker->numberBetween(100, 10000),
            'description' => $description,
            'created_by'  => User::factory(),
            'order_id'    => null,
        ];
    }
}
