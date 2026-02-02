<?php

namespace Src\Cita\Domain\Entities;

use DateTimeImmutable;

class Cita
{
    private string $id;
    private string $mascotaId;
    private string $veterinarioId;
    private DateTimeImmutable $fechaHora;
    private ?DateTimeImmutable $fechaHoraFin;
    private string $motivo;
    private string $estado; // programada, confirmada, en_curso, completada, cancelada, no_asistio
    private ?string $observaciones;
    private ?string $tipoConsulta; // consulta_general, vacunacion, cirugia, emergencia, control, peluqueria
    private ?DateTimeImmutable $createdAt;
    private ?DateTimeImmutable $updatedAt;

    public function __construct(
        string $id,
        string $mascotaId,
        string $veterinarioId,
        DateTimeImmutable $fechaHora,
        string $motivo,
        string $estado = 'programada',
        ?DateTimeImmutable $fechaHoraFin = null,
        ?string $observaciones = null,
        ?string $tipoConsulta = 'consulta_general',
        ?DateTimeImmutable $createdAt = null,
        ?DateTimeImmutable $updatedAt = null
    ) {
        $this->id = $id;
        $this->mascotaId = $mascotaId;
        $this->veterinarioId = $veterinarioId;
        $this->fechaHora = $fechaHora;
        $this->fechaHoraFin = $fechaHoraFin;
        $this->motivo = $motivo;
        $this->estado = $estado;
        $this->observaciones = $observaciones;
        $this->tipoConsulta = $tipoConsulta;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getMascotaId(): string
    {
        return $this->mascotaId;
    }

    public function getVeterinarioId(): string
    {
        return $this->veterinarioId;
    }

    public function getFechaHora(): DateTimeImmutable
    {
        return $this->fechaHora;
    }

    public function getFechaHoraFin(): ?DateTimeImmutable
    {
        return $this->fechaHoraFin;
    }

    public function getMotivo(): string
    {
        return $this->motivo;
    }

    public function getEstado(): string
    {
        return $this->estado;
    }

    public function getObservaciones(): ?string
    {
        return $this->observaciones;
    }

    public function getTipoConsulta(): ?string
    {
        return $this->tipoConsulta;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'mascotaId' => $this->mascotaId,
            'veterinarioId' => $this->veterinarioId,
            'fechaHora' => $this->fechaHora->format('Y-m-d H:i:s'),
            'fechaHoraFin' => $this->fechaHoraFin?->format('Y-m-d H:i:s'),
            'motivo' => $this->motivo,
            'estado' => $this->estado,
            'observaciones' => $this->observaciones,
            'tipoConsulta' => $this->tipoConsulta,
            'createdAt' => $this->createdAt?->format('Y-m-d H:i:s'),
            'updatedAt' => $this->updatedAt?->format('Y-m-d H:i:s'),
        ];
    }
}
