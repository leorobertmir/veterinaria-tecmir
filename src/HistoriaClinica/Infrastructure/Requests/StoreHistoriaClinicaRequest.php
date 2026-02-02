<?php

namespace Src\HistoriaClinica\Infrastructure\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreHistoriaClinicaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'mascota_id' => $this->mascotaId,
            'cita_id' => $this->citaId,
            'veterinario_id' => $this->veterinarioId,
            'frecuencia_cardiaca' => $this->frecuenciaCardiaca,
            'frecuencia_respiratoria' => $this->frecuenciaRespiratoria,
            'examen_fisico' => $this->examenFisico,
            'proxima_visita' => $this->proximaVisita,
        ]);
    }

    public function rules(): array
    {
        return [
            'mascota_id' => 'required|exists:mascotas,id',
            'cita_id' => 'nullable|exists:citas,id',
            'veterinario_id' => 'required|exists:users,id',
            'fecha' => 'required|date',
            'peso' => 'nullable|numeric|min:0|max:999.99',
            'temperatura' => 'nullable|numeric|min:30|max:45',
            'frecuencia_cardiaca' => 'nullable|integer|min:20|max:300',
            'frecuencia_respiratoria' => 'nullable|integer|min:5|max:100',
            'anamnesis' => 'nullable|string',
            'examen_fisico' => 'nullable|string',
            'diagnostico' => 'nullable|string',
            'tratamiento' => 'nullable|string',
            'receta' => 'nullable|string',
            'observaciones' => 'nullable|string',
            'proxima_visita' => 'nullable|date|after:fecha',
        ];
    }

    public function attributes(): array
    {
        return [
            'mascota_id' => 'mascota',
            'cita_id' => 'cita',
            'veterinario_id' => 'veterinario',
            'fecha' => 'fecha',
            'peso' => 'peso',
            'temperatura' => 'temperatura',
            'frecuencia_cardiaca' => 'frecuencia cardíaca',
            'frecuencia_respiratoria' => 'frecuencia respiratoria',
            'anamnesis' => 'anamnesis',
            'examen_fisico' => 'examen físico',
            'diagnostico' => 'diagnóstico',
            'tratamiento' => 'tratamiento',
            'receta' => 'receta',
            'observaciones' => 'observaciones',
            'proxima_visita' => 'próxima visita',
        ];
    }

    public function messages(): array
    {
        return [
            'mascota_id.required' => 'La mascota es obligatoria',
            'mascota_id.exists' => 'La mascota seleccionada no existe',
            'veterinario_id.required' => 'El veterinario es obligatorio',
            'veterinario_id.exists' => 'El veterinario seleccionado no existe',
            'fecha.required' => 'La fecha es obligatoria',
            'temperatura.min' => 'La temperatura debe ser mayor a 30°C',
            'temperatura.max' => 'La temperatura debe ser menor a 45°C',
            'frecuencia_cardiaca.min' => 'La frecuencia cardíaca debe ser mayor a 20 bpm',
            'frecuencia_cardiaca.max' => 'La frecuencia cardíaca debe ser menor a 300 bpm',
            'frecuencia_respiratoria.min' => 'La frecuencia respiratoria debe ser mayor a 5 rpm',
            'frecuencia_respiratoria.max' => 'La frecuencia respiratoria debe ser menor a 100 rpm',
            'proxima_visita.after' => 'La próxima visita debe ser posterior a la fecha actual',
        ];
    }
}
