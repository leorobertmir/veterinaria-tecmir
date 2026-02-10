<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Support\Facades\Hash;
use Src\Auth\Infrastructure\Models\UserEloquentModel; // <--- OJO CON ESTO

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Limpiar caché
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Crear Roles
        $roleAdmin = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Veterinario', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Cliente', 'guard_name' => 'web']);

        // 3. Crear Usuarios
        // Admin
        $admin = UserEloquentModel::firstOrCreate(
            ['email' => 'admin@veterinaria.com'],
            ['name' => 'Administrador Principal', 'password' => Hash::make('password')]
        );
        $admin->assignRole($roleAdmin);

        // Veterinario
        $vet = UserEloquentModel::firstOrCreate(
            ['email' => 'vet@veterinaria.com'],
            ['name' => 'Dr. Veterinario Prueba', 'password' => Hash::make('password')]
        );
        // $vet->assignRole('Veterinario'); // Descomentar si quieres asignar el rol aquí

        // Cliente
        $cliente = UserEloquentModel::firstOrCreate(
            ['email' => 'cliente@veterinaria.com'],
            ['name' => 'Cliente De Pruebas', 'password' => Hash::make('password')]
        );
        // $cliente->assignRole('Cliente'); // Descomentar si quieres asignar el rol aquí
    }
}
