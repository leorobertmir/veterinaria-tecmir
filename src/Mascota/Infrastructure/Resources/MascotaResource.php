<?php

namespace Src\Mascota\Infrastructure\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MascotaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'clienteId' => $this->cliente_id,
            'nombre' => $this->nombre,
            'especie' => $this->especie,
            'raza' => $this->raza,
            'color' => $this->color,
            'sexo' => $this->sexo,
            'fechaNacimiento' => $this->fecha_nacimiento?->format('Y-m-d'),
            'peso' => $this->peso,
            'microchip' => $this->microchip,
            'observaciones' => $this->observaciones,
            'activo' => $this->activo,
            'cliente' => $this->whenLoaded('cliente', fn() => [
                'id' => $this->cliente->id,
                'razonSocial' => $this->cliente->razon_social,
                'telefono' => $this->cliente->telefono,
            ]),
            'createdAt' => $this->created_at?->format('Y-m-d H:i:s'),
            'updatedAt' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
