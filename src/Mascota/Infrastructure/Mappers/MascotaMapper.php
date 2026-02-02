<?php

namespace Src\Mascota\Infrastructure\Mappers;

use DateTimeImmutable;
use Src\Mascota\Domain\Entities\Mascota;
use Src\Mascota\Infrastructure\Models\MascotaEloquentModel;

class MascotaMapper
{
    public static function toDomain(MascotaEloquentModel $model): Mascota
    {
        return new Mascota(
            id: $model->id,
            clienteId: $model->cliente_id,
            nombre: $model->nombre,
            especie: $model->especie,
            raza: $model->raza,
            color: $model->color,
            sexo: $model->sexo,
            fechaNacimiento: $model->fecha_nacimiento ? new DateTimeImmutable($model->fecha_nacimiento->toDateTimeString()) : null,
            peso: $model->peso ? (float) $model->peso : null,
            microchip: $model->microchip,
            observaciones: $model->observaciones,
            activo: $model->activo,
            createdAt: $model->created_at ? new DateTimeImmutable($model->created_at->toDateTimeString()) : null,
            updatedAt: $model->updated_at ? new DateTimeImmutable($model->updated_at->toDateTimeString()) : null
        );
    }

    public static function toEloquent(Mascota $mascota): MascotaEloquentModel
    {
        $model = new MascotaEloquentModel();
        $model->id = $mascota->getId();
        $model->cliente_id = $mascota->getClienteId();
        $model->nombre = $mascota->getNombre();
        $model->especie = $mascota->getEspecie();
        $model->raza = $mascota->getRaza();
        $model->color = $mascota->getColor();
        $model->sexo = $mascota->getSexo();
        $model->fecha_nacimiento = $mascota->getFechaNacimiento()?->format('Y-m-d');
        $model->peso = $mascota->getPeso();
        $model->microchip = $mascota->getMicrochip();
        $model->observaciones = $mascota->getObservaciones();
        $model->activo = $mascota->getActivo();
        return $model;
    }
}
