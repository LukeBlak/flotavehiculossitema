<?php

namespace Database\Seeders;

use App\Models\FuelLog;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Database\Seeder;

class FuelLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tripIds = Trip::query()->pluck('id');
        $userIds = User::query()->pluck('id');

        if ($tripIds->isEmpty() || $userIds->isEmpty()) {
            return;
        }

        foreach ($tripIds as $tripId) {
            $trip = Trip::query()->find($tripId);

            if (! $trip) {
                continue;
            }

            FuelLog::factory()->create([
                'trip_id' => $trip->id,
                'vehicle_id' => $trip->vehicle_id,
                'user_id' => $userIds->random(),
            ]);
        }
    }
}
