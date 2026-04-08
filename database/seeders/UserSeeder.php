<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'trips.assign',
            'trips.validate',
            'incidents.report',
            'incidents.manage',
            'fuel.report',
            'fuel.validate',
        ];

        foreach (['gerente', 'supervisor', 'motorista'] as $roleName) {
            Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'web',
            ]);
        }

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => 'web',
            ]);
        }

        Role::findByName('gerente', 'web')->syncPermissions($permissions);
        Role::findByName('supervisor', 'web')->syncPermissions([
            'trips.assign',
            'trips.validate',
            'incidents.manage',
            'fuel.validate',
        ]);
        Role::findByName('motorista', 'web')->syncPermissions([
            'incidents.report',
            'fuel.report',
        ]);

        $users = [
            [
                'email' => 'admin@flota.local',
                'name' => 'Administrador General',
                'password' => bcrypt('password'),
                'role' => 'gerente',
                'license_number' => null,
                'phone' => '0990000001',
                'is_active' => true,
                'email_verified_at' => now(),
            ],
            [
                'email' => 'supervisor@flota.local',
                'name' => 'Supervisor de Operaciones',
                'password' => bcrypt('password'),
                'role' => 'supervisor',
                'license_number' => null,
                'phone' => '0990000002',
                'is_active' => true,
                'email_verified_at' => now(),
            ],
            [
                'email' => 'conductor@flota.local',
                'name' => 'Conductor Principal',
                'password' => bcrypt('password'),
                'role' => 'motorista',
                'license_number' => 'LIC-00001',
                'phone' => '0990000003',
                'is_active' => true,
                'email_verified_at' => now(),
            ],
        ];

        foreach ($users as $userData) {
            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );

            $user->syncRoles([$userData['role']]);
        }

        $remaining = max(0, 20 - User::count());
        if ($remaining > 0) {
            User::factory()->count($remaining)->create();
        }
    }
}
