<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@flota.local'],
            [
                'name' => 'Administrador General',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'license_number' => null,
                'phone' => '0990000001',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        User::firstOrCreate(
            ['email' => 'supervisor@flota.local'],
            [
                'name' => 'Supervisor de Operaciones',
                'password' => bcrypt('password'),
                'role' => 'supervisor',
                'license_number' => null,
                'phone' => '0990000002',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        User::firstOrCreate(
            ['email' => 'conductor@flota.local'],
            [
                'name' => 'Conductor Principal',
                'password' => bcrypt('password'),
                'role' => 'driver',
                'license_number' => 'LIC-00001',
                'phone' => '0990000003',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        $remaining = max(0, 20 - User::count());
        if ($remaining > 0) {
            User::factory()->count($remaining)->create();
        }
    }
}
