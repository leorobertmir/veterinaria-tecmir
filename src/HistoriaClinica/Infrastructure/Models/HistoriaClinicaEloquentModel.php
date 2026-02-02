<?php

namespace Src\HistoriaClinica\Infrastructure\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Src\Mascota\Infrastructure\Models\MascotaEloquentModel;
use Src\Cita\Infrastructure\Models\CitaEloquentModel;
use Src\Auth\Infrastructure\Models\UserEloquentModel;

class HistoriaClinicaEloquentModel extends Model
{
    use HasUuid;

    protected $table = 'historias_clinicas';

    protected $fillable = [
        'id',
        'mascota_id',
        'cita_id',
        'veterinario_id',
        'fecha',
        'peso',
        'temperatura',
        'frecuencia_cardiaca',
        'frecuencia_respiratoria',
        'anamnesis',
        'examen_fisico',
        'diagnostico',
        'tratamiento',
        'receta',
        'observaciones',
        'proxima_visita'
    ];

    protected $casts = [
        'fecha' => 'date',
        'peso' => 'decimal:2',
        'temperatura' => 'decimal:1',
        'frecuencia_cardiaca' => 'integer',
        'frecuencia_respiratoria' => 'integer',
        'proxima_visita' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function mascota(): BelongsTo
    {
        return $this->belongsTo(MascotaEloquentModel::class, 'mascota_id', 'id');
    }

    public function cita(): BelongsTo
    {
        return $this->belongsTo(CitaEloquentModel::class, 'cita_id', 'id');
    }

    public function veterinario(): BelongsTo
    {
        return $this->belongsTo(UserEloquentModel::class, 'veterinario_id', 'id');
    }
}
