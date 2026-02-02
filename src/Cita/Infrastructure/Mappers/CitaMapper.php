<?php

namespace Src\Cita\Infrastructure\Mappers;

use DateTimeImmutable;
use Src\Cita\Domain\Entities\Cita;
use Src\Cita\Infrastructure\Models\CitaEloquentModel;

class CitaMapper
{
    public static function toDomain(CitaEloquentModel $model): Cita
    {
        return new Cita(
            id: $model->id,
            mascotaId: $model->mascota_id,
            veterinarioId: $model->veterinario_id,
            fechaHora: new DateTimeImmutable($model->fecha_hora->toDateTimeString()),
            motivo: $model->motivo,
            estado: $model->estado,
            fechaHoraFin: $model->fecha_hora_fin ? new DateTimeImmutable($model->fecha_hora_fin->toDateTimeString()) : null,
            observaciones: $model->observaciones,
            tipoConsulta: $model->tipo_consulta,
            createdAt: $model->created_at ? new DateTimeImmutable($model->created_at->toDateTimeString()) : null,
            updatedAt: $model->updated_at ? new DateTimeImmutable($model->updated_at->toDateTimeString()) : null
        );
    }

    public static function toEloquent(Cita $cita): CitaEloquentModel
    {
        $model = new CitaEloquentModel();
        $model->id = $cita->getId();
        $model->mascota_id = $cita->getMascotaId();
        $model->veterinario_id = $cita->getVeterinarioId();
        $model->fecha_hora = $cita->getFechaHora()->format('Y-m-d H:i:s');
        $model->fecha_hora_fin = $cita->getFechaHoraFin()?->format('Y-m-d H:i:s');
        $model->motivo = $cita->getMotivo();
        $model->estado = $cita->getEstado();
        $model->observaciones = $cita->getObservaciones();
        $model->tipo_consulta = $cita->getTipoConsulta();
        return $model;
    }
}
