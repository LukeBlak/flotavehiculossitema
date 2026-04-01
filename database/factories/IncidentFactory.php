<?php

namespace Database\Factories;

use App\Models\Incident;
use App\Models\Trip;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Incident>
 */
class IncidentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = fake()->randomElement(['reported', 'in_progress', 'resolved']);

        return [
            'trip_id' => Trip::factory(),
            'vehicle_id' => Vehicle::factory(),
            'user_id' => User::factory(),
            'description' => fake()->sentence(12),
            'reported_at' => fake()->dateTimeBetween('-4 months', 'now'),
            'status' => $status,
            'resolution' => $status === 'resolved' ? fake()->sentence(10) : null,
        ];
    }
}
