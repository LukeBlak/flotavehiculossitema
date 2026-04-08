<?php

namespace Database\Factories;

use App\Models\FuelLog;
use App\Models\Trip;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FuelLog>
 */
class FuelLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $liters = fake()->randomFloat(2, 8, 80);
        $costPerLiter = fake()->randomFloat(2, 1.2, 2.2);

        return [
            'trip_id' => Trip::factory(),
            'vehicle_id' => Vehicle::factory(),
            'user_id' => User::factory()->state([
                'role' => 'motorista',
                'license_number' => strtoupper(fake()->bothify('LIC-#####')),
                'is_active' => true,
            ]),
            'liters' => $liters,
            'cost_per_liter' => $costPerLiter,
            'total_cost' => round($liters * $costPerLiter, 2),
            'station_name' => fake()->randomElement(['Primax', 'Petroecuador', 'Terpel', 'Mobil']),
            'receipt_image' => fake()->boolean(70) ? 'receipts/' . fake()->uuid() . '.jpg' : null,
            'logged_at' => fake()->dateTimeBetween('-6 months', 'now'),
        ];
    }
}
