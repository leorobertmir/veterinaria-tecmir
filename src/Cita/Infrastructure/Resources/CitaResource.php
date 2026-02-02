<?php

namespace Src\Cita\Infrastructure\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CitaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'mascotaId' => $this->mascota_id,
            'veterinarioId' => $this->veterinario_id,
            'fechaHora' => $this->fecha_hora?->format('Y-m-d H:i:s'),
            'fechaHoraFin' => $this->fecha_hora_fin?->format('Y-m-d H:i:s'),
            'motivo' => $this->motivo,
            'estado' => $this->estado,
            'observaciones' => $this->observaciones,
            'tipoConsulta' => $this->tipo_consulta,
            'mascota' => $this->whenLoaded('mascota', fn() => [
                'id' => $this->mascota->id,
                'nombre' => $this->mascota->nombre,
                'especie' => $this->mascota->especie,
                'raza' => $this->mascota->raza,
                'cliente' => $this->mascota->cliente ? [
                    'id' => $this->mascota->cliente->id,
                    'razonSocial' => $this->mascota->cliente->razon_social,
                    'telefono' => $this->mascota->cliente->telefono,
                ] : null,
            ]),
            'veterinario' => $this->whenLoaded('veterinario', fn() => [
                'id' => $this->veterinario->id,
                'name' => $this->veterinario->name,
                'email' => $this->veterinario->email,
            ]),
            'createdAt' => $this->created_at?->format('Y-m-d H:i:s'),
            'updatedAt' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
