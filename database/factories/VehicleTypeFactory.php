<?php

namespace Database\Factories;

use App\Models\VehicleType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<VehicleType>
 */
class VehicleTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->randomElement(['Camion Carga 3 Toneladas', 'Camion Liviano', 'Furgon', 'Pickup', 'Camioneta']);
        $codeMap = [
            'Camion Carga 3 Toneladas' => 'BOX_3T',
            'Camion Liviano' => 'BOX_LT',
            'Furgon' => 'VAN',
            'Pickup' => 'PICKUP',
            'Camioneta' => 'SUV',
        ];

        return [
            'name' => $name,
            'code' => $codeMap[$name],
            'maintenance_interval_km' => fake()->randomElement([5000, 7000, 10000, 12000]),
            'tank_capacity' => fake()->randomFloat(2, 35, 130),
            'payload_capacity' => fake()->randomFloat(2, 500, 3000),
        ];
    }
}
