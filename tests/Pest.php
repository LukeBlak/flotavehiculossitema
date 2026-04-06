<?php
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

uses(TestCase::class, RefreshDatabase::class)->in('Feature');
uses(TestCase::class, RefreshDatabase::class)->in('Unit');

function crearRol(string $nombre): Role {
    return Role::firstOrCreate(['name' => $nombre, 'guard_name' => 'web']);
}

function crearPermiso(string $nombre): Permission {
    return Permission::firstOrCreate(['name' => $nombre, 'guard_name' => 'web']);
}

function gerente(array $attrs = []): \App\Models\User {
    return \App\Models\User::factory()->create($attrs)->assignRole(crearRol('gerente'));
}

function supervisor(array $attrs = []): \App\Models\User {
    return \App\Models\User::factory()->create($attrs)->assignRole(crearRol('supervisor'));
}

function motorista(array $attrs = []): \App\Models\User {
    return \App\Models\User::factory()->create($attrs)->assignRole(crearRol('motorista'));
}

function vehiculoActivo(): \App\Models\Vehicle {
    return \App\Models\Vehicle::factory()->create(['status' => 'active']);
}

beforeEach(function () { app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions(); });