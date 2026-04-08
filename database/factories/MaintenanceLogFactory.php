<?php

namespace Database\Factories;

use App\Models\MaintenanceLog;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MaintenanceLog>
 */
class MaintenanceLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $serviceDate = fake()->dateTimeBetween('-6 months', '-3 days');
        $nextDueKm = fake()->numberBetween(25000, 300000);

        return [
            'vehicle_id' => Vehicle::factory(),
            'user_id' => User::factory()->state([
                'role' => 'supervisor',
                'license_number' => null,
                'is_active' => true,
            ]),
            'type' => fake()->randomElement(['preventive', 'corrective']),
            'description' => fake()->sentence(10),
            'cost' => fake()->randomFloat(2, 50, 2500),
            'service_date' => $serviceDate,
            'next_due_km' => $nextDueKm,
            'workshop_name' => fake()->company(),
            'status' => fake()->randomElement(['pending', 'completed']),
        ];
    }
}
