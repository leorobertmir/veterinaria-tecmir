<?php

namespace Src\HistoriaClinica\Infrastructure\Mappers;

use DateTimeImmutable;
use Src\HistoriaClinica\Domain\Entities\HistoriaClinica;
use Src\HistoriaClinica\Infrastructure\Models\HistoriaClinicaEloquentModel;

class HistoriaClinicaMapper
{
    public static function toDomain(HistoriaClinicaEloquentModel $model): HistoriaClinica
    {
        return new HistoriaClinica(
            id: $model->id,
            mascotaId: $model->mascota_id,
            veterinarioId: $model->veterinario_id,
            fecha: new DateTimeImmutable($model->fecha->toDateTimeString()),
            citaId: $model->cita_id,
            peso: $model->peso ? (float) $model->peso : null,
            temperatura: $model->temperatura ? (float) $model->temperatura : null,
            frecuenciaCardiaca: $model->frecuencia_cardiaca,
            frecuenciaRespiratoria: $model->frecuencia_respiratoria,
            anamnesis: $model->anamnesis,
            examenFisico: $model->examen_fisico,
            diagnostico: $model->diagnostico,
            tratamiento: $model->tratamiento,
            receta: $model->receta,
            observaciones: $model->observaciones,
            proximaVisita: $model->proxima_visita ? new DateTimeImmutable($model->proxima_visita->toDateTimeString()) : null,
            createdAt: $model->created_at ? new DateTimeImmutable($model->created_at->toDateTimeString()) : null,
            updatedAt: $model->updated_at ? new DateTimeImmutable($model->updated_at->toDateTimeString()) : null
        );
    }

    public static function toEloquent(HistoriaClinica $historia): HistoriaClinicaEloquentModel
    {
        $model = new HistoriaClinicaEloquentModel();
        $model->id = $historia->getId();
        $model->mascota_id = $historia->getMascotaId();
        $model->cita_id = $historia->getCitaId();
        $model->veterinario_id = $historia->getVeterinarioId();
        $model->fecha = $historia->getFecha()->format('Y-m-d');
        $model->peso = $historia->getPeso();
        $model->temperatura = $historia->getTemperatura();
        $model->frecuencia_cardiaca = $historia->getFrecuenciaCardiaca();
        $model->frecuencia_respiratoria = $historia->getFrecuenciaRespiratoria();
        $model->anamnesis = $historia->getAnamnesis();
        $model->examen_fisico = $historia->getExamenFisico();
        $model->diagnostico = $historia->getDiagnostico();
        $model->tratamiento = $historia->getTratamiento();
        $model->receta = $historia->getReceta();
        $model->observaciones = $historia->getObservaciones();
        $model->proxima_visita = $historia->getProximaVisita()?->format('Y-m-d');
        return $model;
    }
}
