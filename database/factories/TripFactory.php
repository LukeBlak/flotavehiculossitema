<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Vehicle;
use App\Models\Trip;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Trip>
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
        $start = fake()->numberBetween(15000, 200000);
        $distance = fake()->numberBetween(20, 650);
        $startTime = fake()->dateTimeBetween('-6 months', '-2 days');
        $endTime = (clone $startTime)->modify('+' . fake()->numberBetween(1, 12) . ' hours');

        return [
            'vehicle_id' => Vehicle::factory(),
            'driver_id' => User::factory()->state([
                'role' => 'motorista',
                'license_number' => strtoupper(fake()->bothify('LIC-#####')),
                'is_active' => true,
            ]),
            'start_odometer' => $start,
            'end_odometer' => $start + $distance,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'status' => fake()->randomElement(['scheduled', 'in_progress', 'completed']),
            'route_description' => fake()->city() . ' -> ' . fake()->city(),
        ];
    }
}
