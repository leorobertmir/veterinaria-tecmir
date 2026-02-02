<?php

namespace Src\HistoriaClinica\Infrastructure\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HistoriaClinicaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'mascotaId' => $this->mascota_id,
            'citaId' => $this->cita_id,
            'veterinarioId' => $this->veterinario_id,
            'fecha' => $this->fecha?->format('Y-m-d'),
            'peso' => $this->peso,
            'temperatura' => $this->temperatura,
            'frecuenciaCardiaca' => $this->frecuencia_cardiaca,
            'frecuenciaRespiratoria' => $this->frecuencia_respiratoria,
            'anamnesis' => $this->anamnesis,
            'examenFisico' => $this->examen_fisico,
            'diagnostico' => $this->diagnostico,
            'tratamiento' => $this->tratamiento,
            'receta' => $this->receta,
            'observaciones' => $this->observaciones,
            'proximaVisita' => $this->proxima_visita?->format('Y-m-d'),
            'mascota' => $this->whenLoaded('mascota', fn() => [
                'id' => $this->mascota->id,
                'nombre' => $this->mascota->nombre,
                'especie' => $this->mascota->especie,
                'raza' => $this->mascota->raza,
                'cliente' => $this->mascota->cliente ? [
                    'id' => $this->mascota->cliente->id,
                    'razonSocial' => $this->mascota->cliente->razon_social,
                ] : null,
            ]),
            'veterinario' => $this->whenLoaded('veterinario', fn() => [
                'id' => $this->veterinario->id,
                'name' => $this->veterinario->name,
            ]),
            'cita' => $this->whenLoaded('cita', fn() => $this->cita ? [
                'id' => $this->cita->id,
                'fechaHora' => $this->cita->fecha_hora?->format('Y-m-d H:i:s'),
                'tipoConsulta' => $this->cita->tipo_consulta,
            ] : null),
            'createdAt' => $this->created_at?->format('Y-m-d H:i:s'),
            'updatedAt' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
