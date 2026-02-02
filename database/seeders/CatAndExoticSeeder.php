<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatAndExoticSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = DB::table('categorias')->pluck('id')->toArray();

        if (empty($categorias)) {
            $this->command->error('No hay categorias.');
            return;
        }

        $productos = [
            ['codigo' => 'CAT001', 'nombre' => 'Baño y Cepillado - Gato', 'descripcion' => 'Servicio veterinario', 'precio' => 45.00, 'stock' => 0, 'tipo' => 'servicio'],
            ['codigo' => 'CAT002', 'nombre' => 'Corte de Pelo - Gato (Lion Cut)', 'descripcion' => 'Servicio veterinario', 'precio' => 65.00, 'stock' => 0, 'tipo' => 'servicio'],
            ['codigo' => 'CAT003', 'nombre' => 'Arena Sanitaria Aglomerante (5kg)', 'descripcion' => 'Producto veterinario', 'precio' => 25.00, 'stock' => 60, 'tipo' => 'bien'],
            ['codigo' => 'CAT004', 'nombre' => 'Alimento Húmedo Felix (Sobres)', 'descripcion' => 'Producto veterinario', 'precio' => 3.50, 'stock' => 200, 'tipo' => 'bien'],
            ['codigo' => 'CAT005', 'nombre' => 'Rascador de Cartón', 'descripcion' => 'Producto veterinario', 'precio' => 20.00, 'stock' => 15, 'tipo' => 'bien'],
            ['codigo' => 'CAT006', 'nombre' => 'Hierba Gatera (Catnip)', 'descripcion' => 'Producto veterinario', 'precio' => 12.00, 'stock' => 30, 'tipo' => 'bien'],
            ['codigo' => 'ROE001', 'nombre' => 'Consulta Animales Exóticos', 'descripcion' => 'Servicio veterinario', 'precio' => 50.00, 'stock' => 0, 'tipo' => 'servicio'],
            ['codigo' => 'ROE002', 'nombre' => 'Corte de Dientes (Incisivos)', 'descripcion' => 'Servicio veterinario', 'precio' => 40.00, 'stock' => 0, 'tipo' => 'servicio'],
            ['codigo' => 'ROE003', 'nombre' => 'Heno de Alfalfa (Bolsa 1kg)', 'descripcion' => 'Producto veterinario', 'precio' => 18.00, 'stock' => 40, 'tipo' => 'bien'],
            ['codigo' => 'ROE004', 'nombre' => 'Sustrato de Viruta Prensada', 'descripcion' => 'Producto veterinario', 'precio' => 15.00, 'stock' => 50, 'tipo' => 'bien'],
            ['codigo' => 'ROE005', 'nombre' => 'Alimento Conejina Premium', 'descripcion' => 'Producto veterinario', 'precio' => 14.00, 'stock' => 30, 'tipo' => 'bien'],
            ['codigo' => 'AVE001', 'nombre' => 'Corte de Pico y Uñas - Aves', 'descripcion' => 'Servicio veterinario', 'precio' => 35.00, 'stock' => 0, 'tipo' => 'servicio'],
            ['codigo' => 'AVE002', 'nombre' => 'Sexaje de Aves (ADN)', 'descripcion' => 'Servicio veterinario', 'precio' => 80.00, 'stock' => 0, 'tipo' => 'servicio'],
            ['codigo' => 'AVE003', 'nombre' => 'Mixtura de Semillas (1kg)', 'descripcion' => 'Producto veterinario', 'precio' => 10.00, 'stock' => 50, 'tipo' => 'bien'],
            ['codigo' => 'AVE004', 'nombre' => 'Piedra de Calcio para Aves', 'descripcion' => 'Producto veterinario', 'precio' => 5.00, 'stock' => 40, 'tipo' => 'bien'],
            ['codigo' => 'AVE005', 'nombre' => 'Juguete Colgante para Jaula', 'descripcion' => 'Producto veterinario', 'precio' => 12.00, 'stock' => 25, 'tipo' => 'bien'],
            ['codigo' => 'REP001', 'nombre' => 'Alimento Tortuga (Palitos)', 'descripcion' => 'Producto veterinario', 'precio' => 22.00, 'stock' => 20, 'tipo' => 'bien'],
            ['codigo' => 'REP002', 'nombre' => 'Acondicionador de Agua (Peces)', 'descripcion' => 'Producto veterinario', 'precio' => 15.00, 'stock' => 15, 'tipo' => 'bien'],
            ['codigo' => 'FAR001', 'nombre' => 'Desparasitante Interno (Jarabe)', 'descripcion' => 'Producto veterinario', 'precio' => 25.00, 'stock' => 50, 'tipo' => 'bien'],
            ['codigo' => 'FAR002', 'nombre' => 'Spray Antiséptico / Cicatrizante', 'descripcion' => 'Producto veterinario', 'precio' => 30.00, 'stock' => 30, 'tipo' => 'bien'],
            ['codigo' => 'FAR003', 'nombre' => 'Gasas Estériles (Paquete)', 'descripcion' => 'Producto veterinario', 'precio' => 5.00, 'stock' => 100, 'tipo' => 'bien'],
            ['codigo' => 'FAR004', 'nombre' => 'Jeringa de Alimentación', 'descripcion' => 'Producto veterinario', 'precio' => 3.00, 'stock' => 100, 'tipo' => 'bien'],
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
        $this->command->info('Productos extra cargados.');
    }
}