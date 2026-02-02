<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historias_clinicas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('mascota_id');
            $table->uuid('cita_id')->nullable();
            $table->uuid('veterinario_id');
            $table->date('fecha');
            $table->decimal('peso', 5, 2)->nullable(); // en kg
            $table->decimal('temperatura', 3, 1)->nullable(); // en °C
            $table->integer('frecuencia_cardiaca')->nullable(); // en bpm
            $table->integer('frecuencia_respiratoria')->nullable(); // en rpm
            $table->text('anamnesis')->nullable(); // historia contada por el dueño
            $table->text('examen_fisico')->nullable();
            $table->text('diagnostico')->nullable();
            $table->text('tratamiento')->nullable();
            $table->text('receta')->nullable();
            $table->text('observaciones')->nullable();
            $table->date('proxima_visita')->nullable();
            $table->timestamps();

            $table->foreign('mascota_id')
                ->references('id')
                ->on('mascotas')
                ->onDelete('restrict');

            $table->foreign('cita_id')
                ->references('id')
                ->on('citas')
                ->onDelete('set null');

            $table->foreign('veterinario_id')
                ->references('id')
                ->on('users')
                ->onDelete('restrict');

            $table->index('mascota_id');
            $table->index('veterinario_id');
            $table->index('fecha');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historias_clinicas');
    }
};
