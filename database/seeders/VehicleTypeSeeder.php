<?php

namespace Database\Seeders;

use App\Models\VehicleType;
use Illuminate\Database\Seeder;

class VehicleTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            [
                'name' => 'Camion Carga 3 Toneladas',
                'code' => 'BOX_3T',
                'maintenance_interval_km' => 5000,
                'tank_capacity' => 120,
                'payload_capacity' => 3000,
            ],
            [
                'name' => 'Furgon',
                'code' => 'VAN',
                'maintenance_interval_km' => 7000,
                'tank_capacity' => 80,
                'payload_capacity' => 1800,
            ],
            [
                'name' => 'Pickup',
                'code' => 'PICKUP',
                'maintenance_interval_km' => 10000,
                'tank_capacity' => 70,
                'payload_capacity' => 900,
            ],
        ];

        foreach ($types as $type) {
            VehicleType::updateOrCreate(['code' => $type['code']], $type);
        }
    }
}
