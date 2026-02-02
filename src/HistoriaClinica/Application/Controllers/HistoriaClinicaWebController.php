<?php

namespace Src\HistoriaClinica\Application\Controllers;

use App\Http\Controllers\Controller;
use Src\HistoriaClinica\Infrastructure\Models\HistoriaClinicaEloquentModel;
use Src\HistoriaClinica\Infrastructure\Mappers\HistoriaClinicaMapper;
use Src\HistoriaClinica\Infrastructure\Requests\StoreHistoriaClinicaRequest;
use Src\HistoriaClinica\Infrastructure\Requests\UpdateHistoriaClinicaRequest;
use Src\Mascota\Infrastructure\Models\MascotaEloquentModel;
use Src\Cita\Infrastructure\Models\CitaEloquentModel;
use Src\Auth\Infrastructure\Models\UserEloquentModel;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Exception;

class HistoriaClinicaWebController extends Controller
{
    public function index(Request $request): Response
    {
        $query = HistoriaClinicaEloquentModel::with(['mascota.cliente', 'veterinario']);

        // Filtrar por mascota
        if ($request->has('mascota_id') && $request->mascota_id) {
            $query->where('mascota_id', $request->mascota_id);
        }

        // Filtrar por veterinario
        if ($request->has('veterinario_id') && $request->veterinario_id) {
            $query->where('veterinario_id', $request->veterinario_id);
        }

        // Buscar por diagnóstico
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('diagnostico', 'like', "%{$search}%")
                  ->orWhere('tratamiento', 'like', "%{$search}%")
                  ->orWhereHas('mascota', function ($mq) use ($search) {
                      $mq->where('nombre', 'like', "%{$search}%");
                  });
            });
        }

        $historias = $query->orderBy('fecha', 'desc')->get();

        $historiasData = $historias->map(function ($model) {
            $data = HistoriaClinicaMapper::toDomain($model)->toArray();
            $data['mascota'] = $model->mascota ? [
                'id' => $model->mascota->id,
                'nombre' => $model->mascota->nombre,
                'especie' => $model->mascota->especie,
                'raza' => $model->mascota->raza,
                'cliente' => $model->mascota->cliente ? [
                    'id' => $model->mascota->cliente->id,
                    'razonSocial' => $model->mascota->cliente->razon_social,
                ] : null,
            ] : null;
            $data['veterinario'] = $model->veterinario ? [
                'id' => $model->veterinario->id,
                'name' => $model->veterinario->name,
            ] : null;
            return $data;
        })->toArray();

        $mascotas = MascotaEloquentModel::where('activo', true)
            ->with('cliente')
            ->get()
            ->map(fn($m) => [
                'id' => $m->id,
                'nombre' => $m->nombre,
                'cliente' => $m->cliente ? $m->cliente->razon_social : null,
            ])->toArray();

        $veterinarios = UserEloquentModel::all()->map(fn($v) => [
            'id' => $v->id,
            'name' => $v->name,
        ])->toArray();

        return Inertia::render('HistoriaClinica/index', [
            'historias' => [
                'data' => $historiasData,
                'meta' => [
                    'total' => count($historiasData),
                ]
            ],
            'mascotas' => $mascotas,
            'veterinarios' => $veterinarios,
            'filters' => [
                'mascota_id' => $request->mascota_id ?? null,
                'veterinario_id' => $request->veterinario_id ?? null,
                'search' => $request->search ?? '',
            ],
            'stats' => [
                'total' => HistoriaClinicaEloquentModel::count(),
                'esteMes' => HistoriaClinicaEloquentModel::whereMonth('fecha', now()->month)->count(),
            ],
        ]);
    }

    public function create(Request $request): Response
    {
        $mascotas = MascotaEloquentModel::with('cliente')
            ->where('activo', true)
            ->get()
            ->map(fn($m) => [
                'id' => $m->id,
                'nombre' => $m->nombre,
                'especie' => $m->especie,
                'cliente' => $m->cliente ? [
                    'id' => $m->cliente->id,
                    'razonSocial' => $m->cliente->razon_social,
                ] : null,
            ])->toArray();

        $veterinarios = UserEloquentModel::all()->map(fn($v) => [
            'id' => $v->id,
            'name' => $v->name,
        ])->toArray();

        // Si viene desde una cita
        $citaId = $request->cita_id;
        $mascotaId = $request->mascota_id;
        $cita = null;

        if ($citaId) {
            $citaModel = CitaEloquentModel::with('mascota')->find($citaId);
            if ($citaModel) {
                $cita = [
                    'id' => $citaModel->id,
                    'mascotaId' => $citaModel->mascota_id,
                    'tipoConsulta' => $citaModel->tipo_consulta,
                    'motivo' => $citaModel->motivo,
                ];
                $mascotaId = $citaModel->mascota_id;
            }
        }

        return Inertia::render('HistoriaClinica/create', [
            'mascotas' => $mascotas,
            'veterinarios' => $veterinarios,
            'cita' => $cita,
            'selectedMascotaId' => $mascotaId,
        ]);
    }

    public function store(StoreHistoriaClinicaRequest $request): RedirectResponse
    {
        try {
            $data = $request->validated();

            // Si no se especifica veterinario, usar el usuario actual
            if (!isset($data['veterinario_id'])) {
                $data['veterinario_id'] = $request->user()->id;
            }

            $historia = HistoriaClinicaEloquentModel::create($data);

            // Si viene de una cita, actualizar su estado a completada
            if (isset($data['cita_id']) && $data['cita_id']) {
                CitaEloquentModel::where('id', $data['cita_id'])
                    ->update(['estado' => 'completada']);
            }

            // Actualizar peso de la mascota si se proporcionó
            if (isset($data['peso']) && $data['peso']) {
                MascotaEloquentModel::where('id', $data['mascota_id'])
                    ->update(['peso' => $data['peso']]);
            }

            return redirect()
                ->route('historias-clinicas.show', $historia->id)
                ->with('success', 'Historia clínica registrada exitosamente');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al registrar la historia clínica: ' . $e->getMessage());
        }
    }

    public function show(string $id): Response
    {
        $historia = HistoriaClinicaEloquentModel::with(['mascota.cliente', 'veterinario', 'cita'])->findOrFail($id);

        $historiaData = HistoriaClinicaMapper::toDomain($historia)->toArray();
        $historiaData['mascota'] = $historia->mascota ? [
            'id' => $historia->mascota->id,
            'nombre' => $historia->mascota->nombre,
            'especie' => $historia->mascota->especie,
            'raza' => $historia->mascota->raza,
            'sexo' => $historia->mascota->sexo,
            'fechaNacimiento' => $historia->mascota->fecha_nacimiento?->format('Y-m-d'),
            'cliente' => $historia->mascota->cliente ? [
                'id' => $historia->mascota->cliente->id,
                'razonSocial' => $historia->mascota->cliente->razon_social,
                'telefono' => $historia->mascota->cliente->telefono,
                'email' => $historia->mascota->cliente->email,
            ] : null,
        ] : null;
        $historiaData['veterinario'] = $historia->veterinario ? [
            'id' => $historia->veterinario->id,
            'name' => $historia->veterinario->name,
        ] : null;
        $historiaData['cita'] = $historia->cita ? [
            'id' => $historia->cita->id,
            'fechaHora' => $historia->cita->fecha_hora?->format('Y-m-d H:i'),
            'tipoConsulta' => $historia->cita->tipo_consulta,
            'motivo' => $historia->cita->motivo,
        ] : null;

        // Historial previo de la mascota
        $historialPrevio = HistoriaClinicaEloquentModel::where('mascota_id', $historia->mascota_id)
            ->where('id', '!=', $id)
            ->orderBy('fecha', 'desc')
            ->limit(5)
            ->get()
            ->map(fn($h) => [
                'id' => $h->id,
                'fecha' => $h->fecha->format('Y-m-d'),
                'diagnostico' => $h->diagnostico,
            ])->toArray();

        return Inertia::render('HistoriaClinica/show', [
            'historia' => $historiaData,
            'historialPrevio' => $historialPrevio,
        ]);
    }

    public function edit(string $id): Response
    {
        $historia = HistoriaClinicaEloquentModel::findOrFail($id);

        $mascotas = MascotaEloquentModel::with('cliente')
            ->where('activo', true)
            ->get()
            ->map(fn($m) => [
                'id' => $m->id,
                'nombre' => $m->nombre,
                'especie' => $m->especie,
                'cliente' => $m->cliente ? [
                    'id' => $m->cliente->id,
                    'razonSocial' => $m->cliente->razon_social,
                ] : null,
            ])->toArray();

        $veterinarios = UserEloquentModel::all()->map(fn($v) => [
            'id' => $v->id,
            'name' => $v->name,
        ])->toArray();

        return Inertia::render('HistoriaClinica/edit', [
            'historia' => HistoriaClinicaMapper::toDomain($historia)->toArray(),
            'mascotas' => $mascotas,
            'veterinarios' => $veterinarios,
        ]);
    }

    public function update(UpdateHistoriaClinicaRequest $request, string $id): RedirectResponse
    {
        try {
            $historia = HistoriaClinicaEloquentModel::findOrFail($id);
            $historia->update($request->validated());

            return redirect()
                ->route('historias-clinicas.show', $id)
                ->with('success', 'Historia clínica actualizada exitosamente');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al actualizar la historia clínica: ' . $e->getMessage());
        }
    }

    public function destroy(string $id): RedirectResponse
    {
        $historia = HistoriaClinicaEloquentModel::find($id);

        if (!$historia) {
            return redirect()
                ->back()
                ->with('error', 'Historia clínica no encontrada');
        }

        $historia->delete();

        return redirect()
            ->route('historias-clinicas.index')
            ->with('success', 'Historia clínica eliminada exitosamente');
    }

    public function porMascota(string $mascotaId): Response
    {
        $mascota = MascotaEloquentModel::with('cliente')->findOrFail($mascotaId);

        $historias = HistoriaClinicaEloquentModel::with('veterinario')
            ->where('mascota_id', $mascotaId)
            ->orderBy('fecha', 'desc')
            ->get();

        $historiasData = $historias->map(function ($model) {
            $data = HistoriaClinicaMapper::toDomain($model)->toArray();
            $data['veterinario'] = $model->veterinario ? [
                'id' => $model->veterinario->id,
                'name' => $model->veterinario->name,
            ] : null;
            return $data;
        })->toArray();

        return Inertia::render('HistoriaClinica/porMascota', [
            'mascota' => [
                'id' => $mascota->id,
                'nombre' => $mascota->nombre,
                'especie' => $mascota->especie,
                'raza' => $mascota->raza,
                'sexo' => $mascota->sexo,
                'fechaNacimiento' => $mascota->fecha_nacimiento?->format('Y-m-d'),
                'peso' => $mascota->peso,
                'cliente' => $mascota->cliente ? [
                    'id' => $mascota->cliente->id,
                    'razonSocial' => $mascota->cliente->razon_social,
                    'telefono' => $mascota->cliente->telefono,
                ] : null,
            ],
            'historias' => [
                'data' => $historiasData,
                'meta' => [
                    'total' => count($historiasData),
                ]
            ],
        ]);
    }
}
