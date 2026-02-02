<?php

namespace Src\HistoriaClinica\Domain\Entities;

use DateTimeImmutable;

class HistoriaClinica
{
    private string $id;
    private string $mascotaId;
    private ?string $citaId;
    private string $veterinarioId;
    private DateTimeImmutable $fecha;
    private ?float $peso;
    private ?float $temperatura;
    private ?int $frecuenciaCardiaca;
    private ?int $frecuenciaRespiratoria;
    private ?string $anamnesis;
    private ?string $examenFisico;
    private ?string $diagnostico;
    private ?string $tratamiento;
    private ?string $receta;
    private ?string $observaciones;
    private ?DateTimeImmutable $proximaVisita;
    private ?DateTimeImmutable $createdAt;
    private ?DateTimeImmutable $updatedAt;

    public function __construct(
        string $id,
        string $mascotaId,
        string $veterinarioId,
        DateTimeImmutable $fecha,
        ?string $citaId = null,
        ?float $peso = null,
        ?float $temperatura = null,
        ?int $frecuenciaCardiaca = null,
        ?int $frecuenciaRespiratoria = null,
        ?string $anamnesis = null,
        ?string $examenFisico = null,
        ?string $diagnostico = null,
        ?string $tratamiento = null,
        ?string $receta = null,
        ?string $observaciones = null,
        ?DateTimeImmutable $proximaVisita = null,
        ?DateTimeImmutable $createdAt = null,
        ?DateTimeImmutable $updatedAt = null
    ) {
        $this->id = $id;
        $this->mascotaId = $mascotaId;
        $this->citaId = $citaId;
        $this->veterinarioId = $veterinarioId;
        $this->fecha = $fecha;
        $this->peso = $peso;
        $this->temperatura = $temperatura;
        $this->frecuenciaCardiaca = $frecuenciaCardiaca;
        $this->frecuenciaRespiratoria = $frecuenciaRespiratoria;
        $this->anamnesis = $anamnesis;
        $this->examenFisico = $examenFisico;
        $this->diagnostico = $diagnostico;
        $this->tratamiento = $tratamiento;
        $this->receta = $receta;
        $this->observaciones = $observaciones;
        $this->proximaVisita = $proximaVisita;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getId(): string { return $this->id; }
    public function getMascotaId(): string { return $this->mascotaId; }
    public function getCitaId(): ?string { return $this->citaId; }
    public function getVeterinarioId(): string { return $this->veterinarioId; }
    public function getFecha(): DateTimeImmutable { return $this->fecha; }
    public function getPeso(): ?float { return $this->peso; }
    public function getTemperatura(): ?float { return $this->temperatura; }
    public function getFrecuenciaCardiaca(): ?int { return $this->frecuenciaCardiaca; }
    public function getFrecuenciaRespiratoria(): ?int { return $this->frecuenciaRespiratoria; }
    public function getAnamnesis(): ?string { return $this->anamnesis; }
    public function getExamenFisico(): ?string { return $this->examenFisico; }
    public function getDiagnostico(): ?string { return $this->diagnostico; }
    public function getTratamiento(): ?string { return $this->tratamiento; }
    public function getReceta(): ?string { return $this->receta; }
    public function getObservaciones(): ?string { return $this->observaciones; }
    public function getProximaVisita(): ?DateTimeImmutable { return $this->proximaVisita; }
    public function getCreatedAt(): ?DateTimeImmutable { return $this->createdAt; }
    public function getUpdatedAt(): ?DateTimeImmutable { return $this->updatedAt; }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'mascotaId' => $this->mascotaId,
            'citaId' => $this->citaId,
            'veterinarioId' => $this->veterinarioId,
            'fecha' => $this->fecha->format('Y-m-d'),
            'peso' => $this->peso,
            'temperatura' => $this->temperatura,
            'frecuenciaCardiaca' => $this->frecuenciaCardiaca,
            'frecuenciaRespiratoria' => $this->frecuenciaRespiratoria,
            'anamnesis' => $this->anamnesis,
            'examenFisico' => $this->examenFisico,
            'diagnostico' => $this->diagnostico,
            'tratamiento' => $this->tratamiento,
            'receta' => $this->receta,
            'observaciones' => $this->observaciones,
            'proximaVisita' => $this->proximaVisita?->format('Y-m-d'),
            'createdAt' => $this->createdAt?->format('Y-m-d H:i:s'),
            'updatedAt' => $this->updatedAt?->format('Y-m-d H:i:s'),
        ];
    }
}
