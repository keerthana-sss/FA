<?php

namespace Database\Factories;

use App\Models\Trip;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Expense>
 */
class ExpenseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'trip_id' => Trip::factory(),
            'payer_id' => User::factory(),
            'payee_id' => User::factory(),
            'amount' => $this->faker->numberBetween(1000, 10000),
            'currency' => 'INR',
            'description' => $this->faker->optional()->sentence,
            'is_settled' => $this->faker->boolean,
        ];
    }
}
