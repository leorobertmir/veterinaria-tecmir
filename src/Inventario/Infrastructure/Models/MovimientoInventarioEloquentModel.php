<?php

namespace Src\Inventario\Infrastructure\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Src\Auth\Infrastructure\Models\UserEloquentModel;
use Src\Producto\Infrastructure\Models\ProductoEloquentModel;

class MovimientoInventarioEloquentModel extends Model
{
    use HasUuid;

    protected $table = 'movimientos_inventario';

    protected $fillable = [
        'id',
        'producto_id',
        'tipo',
        'cantidad',
        'motivo',
        'user_id'
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function producto(): BelongsTo
    {
        return $this->belongsTo(ProductoEloquentModel::class, 'producto_id', 'id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(UserEloquentModel::class, 'user_id', 'id');
    }
}
