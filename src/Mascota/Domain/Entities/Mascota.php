<?php

namespace Src\Mascota\Domain\Entities;

use DateTimeImmutable;

class Mascota
{
    private string $id;
    private string $clienteId;
    private string $nombre;
    private string $especie;
    private ?string $raza;
    private ?string $color;
    private ?string $sexo;
    private ?DateTimeImmutable $fechaNacimiento;
    private ?float $peso;
    private ?string $microchip;
    private ?string $observaciones;
    private bool $activo;
    private ?DateTimeImmutable $createdAt;
    private ?DateTimeImmutable $updatedAt;

    public function __construct(
        string $id,
        string $clienteId,
        string $nombre,
        string $especie,
        ?string $raza = null,
        ?string $color = null,
        ?string $sexo = null,
        ?DateTimeImmutable $fechaNacimiento = null,
        ?float $peso = null,
        ?string $microchip = null,
        ?string $observaciones = null,
        bool $activo = true,
        ?DateTimeImmutable $createdAt = null,
        ?DateTimeImmutable $updatedAt = null
    ) {
        $this->id = $id;
        $this->clienteId = $clienteId;
        $this->nombre = $nombre;
        $this->especie = $especie;
        $this->raza = $raza;
        $this->color = $color;
        $this->sexo = $sexo;
        $this->fechaNacimiento = $fechaNacimiento;
        $this->peso = $peso;
        $this->microchip = $microchip;
        $this->observaciones = $observaciones;
        $this->activo = $activo;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getClienteId(): string
    {
        return $this->clienteId;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function getEspecie(): string
    {
        return $this->especie;
    }

    public function getRaza(): ?string
    {
        return $this->raza;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function getSexo(): ?string
    {
        return $this->sexo;
    }

    public function getFechaNacimiento(): ?DateTimeImmutable
    {
        return $this->fechaNacimiento;
    }

    public function getPeso(): ?float
    {
        return $this->peso;
    }

    public function getMicrochip(): ?string
    {
        return $this->microchip;
    }

    public function getObservaciones(): ?string
    {
        return $this->observaciones;
    }

    public function getActivo(): bool
    {
        return $this->activo;
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
            'clienteId' => $this->clienteId,
            'nombre' => $this->nombre,
            'especie' => $this->especie,
            'raza' => $this->raza,
            'color' => $this->color,
            'sexo' => $this->sexo,
            'fechaNacimiento' => $this->fechaNacimiento?->format('Y-m-d'),
            'peso' => $this->peso,
            'microchip' => $this->microchip,
            'observaciones' => $this->observaciones,
            'activo' => $this->activo,
            'createdAt' => $this->createdAt?->format('Y-m-d H:i:s'),
            'updatedAt' => $this->updatedAt?->format('Y-m-d H:i:s'),
        ];
    }
}
