<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categorias = DB::table('categorias')->pluck('id')->toArray();

        if (empty($categorias)) {
            $this->command->error('No hay categorias. Ejecuta primero CategoriaSeeder.');
            return;
        }

        $productos = [
            ['codigo' => 'SERV001', 'nombre' => 'Consulta General', 'descripcion' => 'Servicio veterinario', 'precio' => 30.00, 'stock' => 0, 'tipo' => 'servicio'],
            ['codigo' => 'SERV002', 'nombre' => 'Consulta de Emergencia', 'descripcion' => 'Servicio veterinario', 'precio' => 60.00, 'stock' => 0, 'tipo' => 'servicio'],
            ['codigo' => 'SERV003', 'nombre' => 'Limpieza Dental (Profilaxis)', 'descripcion' => 'Servicio veterinario', 'precio' => 120.00, 'stock' => 0, 'tipo' => 'servicio'],
            ['codigo' => 'SERV004', 'nombre' => 'Rayos X - Placa Simple', 'descripcion' => 'Servicio veterinario', 'precio' => 50.00, 'stock' => 0, 'tipo' => 'servicio'],
            ['codigo' => 'SERV005', 'nombre' => 'Rayos X - Estudio Completo', 'descripcion' => 'Servicio veterinario', 'precio' => 90.00, 'stock' => 0, 'tipo' => 'servicio'],
            ['codigo' => 'SERV006', 'nombre' => 'Baño y Corte - Perro Pequeño', 'descripcion' => 'Servicio veterinario', 'precio' => 35.00, 'stock' => 0, 'tipo' => 'servicio'],
            ['codigo' => 'SERV007', 'nombre' => 'Baño y Corte - Perro Grande', 'descripcion' => 'Servicio veterinario', 'precio' => 55.00, 'stock' => 0, 'tipo' => 'servicio'],
            ['codigo' => 'SERV008', 'nombre' => 'Esterilización Canina', 'descripcion' => 'Servicio veterinario', 'precio' => 150.00, 'stock' => 0, 'tipo' => 'servicio'],
            ['codigo' => 'SERV009', 'nombre' => 'Ecografía Abdominal', 'descripcion' => 'Servicio veterinario', 'precio' => 70.00, 'stock' => 0, 'tipo' => 'servicio'],
            ['codigo' => 'PROD001', 'nombre' => 'Vacuna Sextuple (Perro)', 'descripcion' => 'Producto veterinario', 'precio' => 45.00, 'stock' => 100, 'tipo' => 'bien'],
            ['codigo' => 'PROD002', 'nombre' => 'Vacuna Triple Felina', 'descripcion' => 'Producto veterinario', 'precio' => 40.00, 'stock' => 100, 'tipo' => 'bien'],
            ['codigo' => 'PROD003', 'nombre' => 'Vacuna Antirrábica', 'descripcion' => 'Producto veterinario', 'precio' => 35.00, 'stock' => 150, 'tipo' => 'bien'],
            ['codigo' => 'PROD004', 'nombre' => 'Pastilla Simparica Trio (10-20kg)', 'descripcion' => 'Producto veterinario', 'precio' => 55.00, 'stock' => 200, 'tipo' => 'bien'],
            ['codigo' => 'PROD005', 'nombre' => 'Pipeta Frontline Plus (Gatos)', 'descripcion' => 'Producto veterinario', 'precio' => 25.00, 'stock' => 80, 'tipo' => 'bien'],
            ['codigo' => 'PROD006', 'nombre' => 'Alimento ProPlan Adulto (3kg)', 'descripcion' => 'Producto veterinario', 'precio' => 95.00, 'stock' => 20, 'tipo' => 'bien'],
            ['codigo' => 'PROD007', 'nombre' => 'Alimento Ricocan Cordero (15kg)', 'descripcion' => 'Producto veterinario', 'precio' => 140.00, 'stock' => 15, 'tipo' => 'bien'],
            ['codigo' => 'PROD008', 'nombre' => 'Collar Isabelino (Talla M)', 'descripcion' => 'Producto veterinario', 'precio' => 15.00, 'stock' => 30, 'tipo' => 'bien'],
        ];

        foreach ($productos as $producto) {
            DB::table('productos')->insert([
                'id' => DB::raw('gen_random_uuid()'),
                'categoria_id' => $categorias[array_rand($categorias)],
                'codigo' => $producto['codigo'],
                'nombre' => $producto['nombre'],
                'descripcion' => $producto['descripcion'],
                'precio_unitario' => $producto['precio'],
                'stock' => $producto['stock'],
                'tipo' => $producto['tipo'],
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('Productos veterinarios creados exitosamente.');
    }
}
