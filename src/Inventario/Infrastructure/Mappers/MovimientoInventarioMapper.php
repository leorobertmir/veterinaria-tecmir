<?php

namespace Src\Inventario\Infrastructure\Mappers;

use DateTimeImmutable;
use Src\Inventario\Domain\Entities\MovimientoInventario;
use Src\Inventario\Infrastructure\Models\MovimientoInventarioEloquentModel;

class MovimientoInventarioMapper
{
    public static function toDomain(MovimientoInventarioEloquentModel $model): MovimientoInventario
    {
        return new MovimientoInventario(
            id: $model->id,
            productoId: $model->producto_id,
            tipo: $model->tipo,
            cantidad: (int) $model->cantidad,
            motivo: $model->motivo,
            userId: $model->user_id,
            createdAt: $model->created_at ? new DateTimeImmutable($model->created_at->toDateTimeString()) : null,
            updatedAt: $model->updated_at ? new DateTimeImmutable($model->updated_at->toDateTimeString()) : null
        );
    }

    public static function toEloquent(MovimientoInventario $movimiento): array
    {
        return [
            'id' => $movimiento->getId(),
            'producto_id' => $movimiento->getProductoId(),
            'tipo' => $movimiento->getTipo(),
            'cantidad' => $movimiento->getCantidad(),
            'motivo' => $movimiento->getMotivo(),
            'user_id' => $movimiento->getUserId(),
        ];
    }
}
