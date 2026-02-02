<?php

namespace Src\Cita\Infrastructure\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Src\Mascota\Infrastructure\Models\MascotaEloquentModel;
use Src\Auth\Infrastructure\Models\UserEloquentModel;

class CitaEloquentModel extends Model
{
    use HasUuid;

    protected $table = 'citas';

    protected $fillable = [
        'id',
        'mascota_id',
        'veterinario_id',
        'fecha_hora',
        'fecha_hora_fin',
        'motivo',
        'estado',
        'observaciones',
        'tipo_consulta'
    ];

    protected $casts = [
        'fecha_hora' => 'datetime',
        'fecha_hora_fin' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function mascota(): BelongsTo
    {
        return $this->belongsTo(MascotaEloquentModel::class, 'mascota_id', 'id');
    }

    public function veterinario(): BelongsTo
    {
        return $this->belongsTo(UserEloquentModel::class, 'veterinario_id', 'id');
    }

    public function historiaClinica(): HasOne
    {
        return $this->hasOne(\Src\HistoriaClinica\Infrastructure\Models\HistoriaClinicaEloquentModel::class, 'cita_id', 'id');
    }
}
