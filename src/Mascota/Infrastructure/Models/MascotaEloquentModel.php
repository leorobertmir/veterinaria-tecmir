<?php

namespace Src\Mascota\Infrastructure\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Src\Cliente\Infrastructure\Models\ClienteEloquentModel;

class MascotaEloquentModel extends Model
{
    use HasUuid;

    protected $table = 'mascotas';

    protected $fillable = [
        'id',
        'cliente_id',
        'nombre',
        'especie',
        'raza',
        'color',
        'sexo',
        'fecha_nacimiento',
        'peso',
        'microchip',
        'observaciones',
        'activo'
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'peso' => 'decimal:2',
        'activo' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(ClienteEloquentModel::class, 'cliente_id', 'id');
    }

    public function citas(): HasMany
    {
        return $this->hasMany(\Src\Cita\Infrastructure\Models\CitaEloquentModel::class, 'mascota_id', 'id');
    }

    public function historiasClinicas(): HasMany
    {
        return $this->hasMany(\Src\HistoriaClinica\Infrastructure\Models\HistoriaClinicaEloquentModel::class, 'mascota_id', 'id');
    }
}
