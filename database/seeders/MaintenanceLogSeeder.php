<?php

namespace Database\Seeders;

use App\Models\MaintenanceLog;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;

class MaintenanceLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vehicleIds = Vehicle::query()->pluck('id');
        $userIds = User::query()->where('role', 'supervisor')->pluck('id');

        if ($vehicleIds->isEmpty() || $userIds->isEmpty()) {
            return;
        }

        MaintenanceLog::factory()
            ->count(5)
            ->sequence(fn () => [
                'vehicle_id' => $vehicleIds->random(),
                'user_id' => $userIds->random(),
            ])
            ->create();
    }
}
