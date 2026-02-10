<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
// Imports necesarios
use Database\Seeders\RolesAndPermissionsSeeder;
use Database\Seeders\CategoriaSeeder;
use Database\Seeders\CatAndExoticSeeder;
use Database\Seeders\ProductSeeder; 

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seguridad (Roles y Permisos)
        // Usamos class_exists por seguridad, pero debería correr sin problemas
        if (class_exists(RolesAndPermissionsSeeder::class)) {
            $this->call(RolesAndPermissionsSeeder::class);
        }

        // 2. Categorías (Indispensable para facturar)
        $this->call(CategoriaSeeder::class);

        // 3. Productos VETERINARIA (El que te faltaba)
        $this->call(CatAndExoticSeeder::class);

        // 4. Productos Generales (En Inglés)
        $this->call(ProductSeeder::class);

        // NOTA: 'ProductoSeeder' (Español) NO se pone porque trae Laptops.
    }
}
