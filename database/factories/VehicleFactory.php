<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Vehicle>
 */
class VehicleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'vehicle_type_id' => VehicleType::factory(),
            'plate_number' => strtoupper(fake()->bothify('???-####')),
            'brand' => fake()->randomElement(['Hino', 'Isuzu', 'JAC', 'Chevrolet', 'Toyota']),
            'model' => fake()->bothify('Model-##'),
            'year' => fake()->numberBetween(2016, (int) now()->format('Y')),
            'current_odometer' => fake()->numberBetween(10000, 250000),
            'next_maintenance_km' => fake()->numberBetween(12000, 270000),
            'status' => fake()->randomElement(['active', 'maintenance', 'inactive']),
            'assigned_driver_id' => User::factory()->state([
                'role' => 'motorista',
                'license_number' => strtoupper(fake()->bothify('LIC-#####')),
                'is_active' => true,
            ]),
        ];
    }
}
