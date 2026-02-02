<?php

namespace Src\Cita\Infrastructure\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCitaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        // Convertir formato datetime-local (YYYY-MM-DDTHH:mm) a formato estándar
        $fechaHora = $this->fechaHora;
        if ($fechaHora && str_contains($fechaHora, 'T')) {
            $fechaHora = str_replace('T', ' ', $fechaHora) . ':00';
        }

        $fechaHoraFin = $this->fechaHoraFin;
        if ($fechaHoraFin && str_contains($fechaHoraFin, 'T')) {
            $fechaHoraFin = str_replace('T', ' ', $fechaHoraFin) . ':00';
        }

        $this->merge([
            'mascota_id' => $this->mascotaId,
            'veterinario_id' => $this->veterinarioId,
            'fecha_hora' => $fechaHora,
            'fecha_hora_fin' => $fechaHoraFin ?: null,
            'tipo_consulta' => $this->tipoConsulta,
        ]);
    }

    public function rules(): array
    {
        return [
            'mascota_id' => 'required|exists:mascotas,id',
            'veterinario_id' => 'required|exists:users,id',
            'fecha_hora' => 'required|date',
            'fecha_hora_fin' => 'nullable|date|after:fecha_hora',
            'motivo' => 'required|string|max:500',
            'estado' => 'required|string|in:programada,confirmada,en_curso,completada,cancelada,no_asistio',
            'observaciones' => 'nullable|string',
            'tipo_consulta' => 'required|string|in:consulta_general,vacunacion,cirugia,emergencia,control,peluqueria,desparasitacion'
        ];
    }

    public function attributes(): array
    {
        return [
            'mascota_id' => 'mascota',
            'veterinario_id' => 'veterinario',
            'fecha_hora' => 'fecha y hora',
            'fecha_hora_fin' => 'fecha y hora de fin',
            'motivo' => 'motivo',
            'estado' => 'estado',
            'observaciones' => 'observaciones',
            'tipo_consulta' => 'tipo de consulta',
        ];
    }

    public function messages(): array
    {
        return [
            'mascota_id.required' => 'La mascota es obligatoria',
            'mascota_id.exists' => 'La mascota seleccionada no existe',
            'veterinario_id.required' => 'El veterinario es obligatorio',
            'veterinario_id.exists' => 'El veterinario seleccionado no existe',
            'fecha_hora.required' => 'La fecha y hora son obligatorias',
            'fecha_hora_fin.after' => 'La fecha de fin debe ser posterior a la fecha de inicio',
            'motivo.required' => 'El motivo de la cita es obligatorio',
            'tipo_consulta.required' => 'El tipo de consulta es obligatorio',
            'tipo_consulta.in' => 'El tipo de consulta no es válido',
            'estado.required' => 'El estado es obligatorio',
            'estado.in' => 'El estado no es válido',
        ];
    }
}
