<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('citas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('mascota_id');
            $table->uuid('veterinario_id');
            $table->dateTime('fecha_hora');
            $table->dateTime('fecha_hora_fin')->nullable();
            $table->string('motivo', 500);
            $table->enum('estado', [
                'programada',
                'confirmada',
                'en_curso',
                'completada',
                'cancelada',
                'no_asistio'
            ])->default('programada');
            $table->text('observaciones')->nullable();
            $table->enum('tipo_consulta', [
                'consulta_general',
                'vacunacion',
                'cirugia',
                'emergencia',
                'control',
                'peluqueria',
                'desparasitacion'
            ])->default('consulta_general');
            $table->timestamps();

            $table->foreign('mascota_id')
                ->references('id')
                ->on('mascotas')
                ->onDelete('restrict');

            $table->foreign('veterinario_id')
                ->references('id')
                ->on('users')
                ->onDelete('restrict');

            $table->index('mascota_id');
            $table->index('veterinario_id');
            $table->index('fecha_hora');
            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('citas');
    }
};
