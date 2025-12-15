<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Trip>
 */
class TripFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
        'title' => $this->faker->unique()->sentence,
        'destination' => $this->faker->city,
        'owner_id' => User::factory(),
        'start_date' => now(),
        'end_date' => now()->addDays(3),
    ];
    }
}
