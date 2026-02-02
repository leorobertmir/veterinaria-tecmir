<?php

namespace Src\Mascota\Infrastructure\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMascotaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'cliente_id' => $this->clienteId,
            'fecha_nacimiento' => $this->fechaNacimiento,
        ]);
    }

    public function rules(): array
    {
        return [
            'cliente_id' => 'required|exists:clientes,id',
            'nombre' => 'required|string|max:255',
            'especie' => 'required|string|in:perro,gato,ave,roedor,reptil,otro',
            'raza' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:100',
            'sexo' => 'nullable|string|in:macho,hembra',
            'fecha_nacimiento' => 'nullable|date',
            'peso' => 'nullable|numeric|min:0|max:999.99',
            'microchip' => 'nullable|string|max:50|unique:mascotas,microchip',
            'observaciones' => 'nullable|string',
            'activo' => 'nullable|boolean'
        ];
    }

    public function attributes(): array
    {
        return [
            'cliente_id' => 'propietario',
            'nombre' => 'nombre',
            'especie' => 'especie',
            'raza' => 'raza',
            'color' => 'color',
            'sexo' => 'sexo',
            'fecha_nacimiento' => 'fecha de nacimiento',
            'peso' => 'peso',
            'microchip' => 'microchip',
            'observaciones' => 'observaciones',
        ];
    }

    public function messages(): array
    {
        return [
            'cliente_id.required' => 'El propietario es obligatorio',
            'cliente_id.exists' => 'El propietario seleccionado no existe',
            'nombre.required' => 'El nombre de la mascota es obligatorio',
            'especie.required' => 'La especie es obligatoria',
            'especie.in' => 'La especie debe ser: perro, gato, ave, roedor, reptil u otro',
            'sexo.in' => 'El sexo debe ser macho o hembra',
            'peso.numeric' => 'El peso debe ser un número',
            'peso.min' => 'El peso no puede ser negativo',
            'microchip.unique' => 'Este número de microchip ya está registrado',
        ];
    }
}
