<?php

namespace Database\Factories;

use App\Models\Trip;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Itinerary>
 */
class ItineraryFactory extends Factory
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
            'title' => $this->faker->sentence,
            'description' => $this->faker->optional()->text,
            'day_number' => $this->faker->numberBetween(1, 10),
            'start_time' => $this->faker->optional()->time(),
            'end_time' => $this->faker->optional()->time(),
            'location' => $this->faker->optional()->city,
            'created_by' => User::factory(),
            'updated_by' => User::factory(),
        ];
    }
}
