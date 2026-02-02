<?php

namespace Src\Inventario\Domain\Entities;

use DateTimeImmutable;

class MovimientoInventario
{
    private string $id;
    private string $productoId;
    private string $tipo;
    private int $cantidad;
    private ?string $motivo;
    private string $userId;
    private ?DateTimeImmutable $createdAt;
    private ?DateTimeImmutable $updatedAt;

    public function __construct(
        string $id,
        string $productoId,
        string $tipo,
        int $cantidad,
        ?string $motivo,
        string $userId,
        ?DateTimeImmutable $createdAt = null,
        ?DateTimeImmutable $updatedAt = null
    ) {
        $this->id = $id;
        $this->productoId = $productoId;
        $this->tipo = $tipo;
        $this->cantidad = $cantidad;
        $this->motivo = $motivo;
        $this->userId = $userId;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getProductoId(): string
    {
        return $this->productoId;
    }

    public function getTipo(): string
    {
        return $this->tipo;
    }

    public function getCantidad(): int
    {
        return $this->cantidad;
    }

    public function getMotivo(): ?string
    {
        return $this->motivo;
    }

    public function getUserId(): string
    {
        return $this->userId;
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
            'productoId' => $this->productoId,
            'tipo' => $this->tipo,
            'cantidad' => $this->cantidad,
            'motivo' => $this->motivo,
            'userId' => $this->userId,
            'createdAt' => $this->createdAt?->format('Y-m-d H:i:s'),
            'updatedAt' => $this->updatedAt?->format('Y-m-d H:i:s'),
        ];
    }
}
