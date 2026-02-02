<?php

namespace Src\Mascota\Application\Controllers;

use App\Http\Controllers\Controller;
use Src\Mascota\Infrastructure\Models\MascotaEloquentModel;
use Src\Mascota\Infrastructure\Mappers\MascotaMapper;
use Src\Mascota\Infrastructure\Requests\StoreMascotaRequest;
use Src\Mascota\Infrastructure\Requests\UpdateMascotaRequest;
use Src\Cliente\Infrastructure\Models\ClienteEloquentModel;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use Exception;

class MascotaWebController extends Controller
{
    public function index(): Response
    {
        $mascotas = MascotaEloquentModel::with('cliente')->get();

        $mascotasData = $mascotas->map(function ($model) {
            $data = MascotaMapper::toDomain($model)->toArray();
            $data['cliente'] = $model->cliente ? [
                'id' => $model->cliente->id,
                'razonSocial' => $model->cliente->razon_social,
                'telefono' => $model->cliente->telefono,
            ] : null;
            return $data;
        })->toArray();

        return Inertia::render('Mascota/index', [
            'mascotas' => [
                'data' => $mascotasData,
                'meta' => [
                    'total' => count($mascotasData),
                ]
            ],
            'stats' => [
                'total' => count($mascotasData),
                'perros' => MascotaEloquentModel::where('especie', 'perro')->count(),
                'gatos' => MascotaEloquentModel::where('especie', 'gato')->count(),
                'otros' => MascotaEloquentModel::whereNotIn('especie', ['perro', 'gato'])->count(),
            ],
        ]);
    }

    public function create(): Response
    {
        $clientes = ClienteEloquentModel::all()->map(fn($c) => [
            'id' => $c->id,
            'razonSocial' => $c->razon_social,
            'numeroDocumento' => $c->numero_documento,
        ])->toArray();

        return Inertia::render('Mascota/create', [
            'clientes' => $clientes,
        ]);
    }

    public function store(StoreMascotaRequest $request): RedirectResponse
    {
        try {
            MascotaEloquentModel::create($request->validated());

            return redirect()
                ->route('mascotas.index')
                ->with('success', 'Mascota registrada exitosamente');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al registrar la mascota: ' . $e->getMessage());
        }
    }

    public function show(string $id): Response
    {
        $mascota = MascotaEloquentModel::with(['cliente', 'citas', 'historiasClinicas'])->findOrFail($id);

        $mascotaData = MascotaMapper::toDomain($mascota)->toArray();
        $mascotaData['cliente'] = $mascota->cliente ? [
            'id' => $mascota->cliente->id,
            'razonSocial' => $mascota->cliente->razon_social,
            'telefono' => $mascota->cliente->telefono,
            'email' => $mascota->cliente->email,
        ] : null;

        return Inertia::render('Mascota/show', [
            'mascota' => $mascotaData,
        ]);
    }

    public function edit(string $id): Response
    {
        $mascota = MascotaEloquentModel::findOrFail($id);

        $clientes = ClienteEloquentModel::all()->map(fn($c) => [
            'id' => $c->id,
            'razonSocial' => $c->razon_social,
            'numeroDocumento' => $c->numero_documento,
        ])->toArray();

        return Inertia::render('Mascota/edit', [
            'mascota' => MascotaMapper::toDomain($mascota)->toArray(),
            'clientes' => $clientes,
        ]);
    }

    public function update(UpdateMascotaRequest $request, string $id): RedirectResponse
    {
        try {
            $mascota = MascotaEloquentModel::findOrFail($id);
            $mascota->update($request->validated());

            return redirect()
                ->route('mascotas.index')
                ->with('success', 'Mascota actualizada exitosamente');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al actualizar la mascota: ' . $e->getMessage());
        }
    }

    public function destroy(string $id): RedirectResponse
    {
        $mascota = MascotaEloquentModel::find($id);

        if (!$mascota) {
            return redirect()
                ->back()
                ->with('error', 'Mascota no encontrada');
        }

        if ($mascota->citas()->exists()) {
            return redirect()
                ->back()
                ->with('error', 'No se puede eliminar esta mascota porque tiene citas asociadas');
        }

        if ($mascota->historiasClinicas()->exists()) {
            return redirect()
                ->back()
                ->with('error', 'No se puede eliminar esta mascota porque tiene historial clínico');
        }

        $mascota->delete();

        return redirect()
            ->route('mascotas.index')
            ->with('success', 'Mascota eliminada exitosamente');
    }
}
