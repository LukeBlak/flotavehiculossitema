<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleType;
use Illuminate\Database\Seeder;

class VehicleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $typeIds = VehicleType::query()->pluck('id');

        if ($typeIds->isEmpty()) {
            VehicleType::factory()->count(3)->create();
            $typeIds = VehicleType::query()->pluck('id');
        }

        $driverIds = User::query()->where('role', 'motorista')->pluck('id');
        if ($driverIds->isEmpty()) {
            User::factory()->count(5)->create([
                'role' => 'motorista',
                'license_number' => null,
            ]);
            $driverIds = User::query()->where('role', 'motorista')->pluck('id');
        }

        Vehicle::factory()
            ->count(10)
            ->sequence(fn () => [
                'vehicle_type_id' => $typeIds->random(),
                'assigned_driver_id' => $driverIds->random(),
                'status' => fake()->randomElement(['active', 'maintenance', 'inactive']),
            ])
            ->create();
    }
}
