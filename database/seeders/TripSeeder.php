<?php

namespace Database\Seeders;

use App\Models\Trip;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;

class TripSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vehicleIds = Vehicle::query()->pluck('id');
        $driverIds = User::query()->where('role', 'motorista')->pluck('id');

        if ($vehicleIds->isEmpty()) {
            return;
        }

        if ($driverIds->isEmpty()) {
            return;
        }

        Trip::factory()
            ->count(20)
            ->sequence(fn () => [
                'vehicle_id' => $vehicleIds->random(),
                'driver_id' => $driverIds->random(),
                'status' => fake()->randomElement(['scheduled', 'in_progress', 'completed']),
            ])
            ->create();
    }
}
