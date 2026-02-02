<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mascotas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('cliente_id');
            $table->string('nombre');
            $table->string('especie'); // perro, gato, ave, roedor, reptil, otro
            $table->string('raza')->nullable();
            $table->string('color', 100)->nullable();
            $table->string('sexo', 10)->nullable(); // macho, hembra
            $table->date('fecha_nacimiento')->nullable();
            $table->decimal('peso', 5, 2)->nullable(); // en kg
            $table->string('microchip', 50)->nullable()->unique();
            $table->text('observaciones')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->foreign('cliente_id')
                ->references('id')
                ->on('clientes')
                ->onDelete('restrict');

            $table->index('cliente_id');
            $table->index('especie');
            $table->index('activo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mascotas');
    }
};
